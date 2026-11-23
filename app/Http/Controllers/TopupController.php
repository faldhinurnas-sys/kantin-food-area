<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TopupController extends Controller
{
    /**
     * Top-up saldo mandiri oleh Mahasiswa (Diteruskan ke Midtrans)
     */
    public function mahasiswaTopup(Request $request)
    {
        return app(MidtransController::class)->topup($request);
    }

    /**
     * Top-Up Saldo Dompet Wali Pribadi (Diteruskan ke Midtrans)
     */
    public function waliTopupWallet(Request $request)
    {
        return app(MidtransController::class)->topupWallet($request);
    }

    /**
     * Cek data mahasiswa berdasarkan NIM untuk keperluan transfer saldo
     */
    public function checkStudentNim(Request $request)
    {
        $nim = trim($request->get('nim', ''));
        if (empty($nim)) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan masukkan NIM mahasiswa.'
            ], 422);
        }

        // Cari mahasiswa berdasarkan NIM atau Email
        $student = User::where('role', 'mahasiswa')
            ->where(function ($q) use ($nim) {
                $q->where('nim', $nim)
                  ->orWhere('email', $nim);
            })
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa dengan NIM / Pengenal "' . $nim . '" tidak ditemukan di pangkalan data kampus.'
            ], 404);
        }

        $card = Card::where('user_id', $student->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$card) {
            return response()->json([
                'success' => true,
                'has_active_card' => false,
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nim' => $student->nim ?? $nim,
                    'email' => $student->email,
                    'avatar_url' => $student->avatar_url,
                ],
                'message' => 'Mahasiswa ditemukan (' . $student->name . '), namun belum memiliki kartu NFC aktif.'
            ]);
        }

        return response()->json([
            'success' => true,
            'has_active_card' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nim' => $student->nim ?? $nim,
                'email' => $student->email,
                'avatar_url' => $student->avatar_url,
            ],
            'card' => [
                'id' => $card->id,
                'card_uid' => $card->card_uid,
                'balance' => (float) $card->balance,
                'balance_formatted' => 'Rp ' . number_format($card->balance, 0, ',', '.'),
                'status' => $card->status,
            ],
            'message' => 'Mahasiswa terverifikasi: ' . $student->name . ' (NIM: ' . ($student->nim ?? $nim) . ').'
        ]);
    }

    /**
     * Kirim saldo dari Dompet Wali ke Mahasiswa / Anak (Bisa via Card ID atau NIM)
     */
    public function waliTransfer(Request $request)
    {
        $wali = Auth::user();
        if ($wali->role !== 'wali') {
            return back()->with('error', 'Aksi ini hanya dapat dilakukan oleh akun Wali.');
        }

        $request->validate([
            'card_id' => 'nullable|exists:cards,id',
            'nim'     => 'nullable|string|max:50',
            'amount'  => 'required|numeric|min:1000',
            'notes'   => 'nullable|string|max:255',
            'payment_method' => 'nullable|string',
        ], [
            'amount.required'  => 'Nominal kirim saldo wajib diisi.',
            'amount.min'       => 'Minimal pengiriman saldo adalah Rp 1.000.',
        ]);

        $card = null;
        $student = null;

        if ($request->filled('card_id')) {
            $card = Card::with('user')->find($request->card_id);
            if ($card) {
                $student = $card->user;
            }
        }

        // Jika card_id tidak ditemukan atau wali menginput lewat NIM
        if (!$card && $request->filled('nim')) {
            $nim = trim($request->nim);
            $student = User::where('role', 'mahasiswa')
                ->where(function ($q) use ($nim) {
                    $q->where('nim', $nim)->orWhere('email', $nim);
                })
                ->first();

            if (!$student) {
                $msg = 'Mahasiswa dengan NIM "' . $nim . '" tidak ditemukan.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg], 404);
                }
                return back()->with('error', $msg);
            }

            $card = Card::where('user_id', $student->id)->where('status', 'active')->latest()->first();
        }

        if (!$card) {
            $msg = 'Pilih kartu mahasiswa penerima atau masukkan NIM mahasiswa yang memiliki kartu aktif.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        if ($card->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Kartu NFC mahasiswa penerima tidak aktif.'
            ], 400);
        }

        $student = $student ?? $card->user;
        $amount = (float) $request->amount;
        $method = $request->payment_method ?? 'transfer_wali';
        $noteText = $request->filled('notes') ? ' - Pesan: ' . trim($request->notes) : '';
        $nimTag = ($student && $student->nim) ? ' [NIM: ' . $student->nim . ']' : '';

        // Validasi ketersediaan saldo di dompet wali jika menggunakan pemotongan saldo wali
        if ($method === 'transfer_wali') {
            if ($wali->balance < $amount) {
                $errorMsg = 'Saldo Dompet Wali Anda tidak mencukupi (Tersedia: Rp ' . number_format($wali->balance, 0, ',', '.') . '). Silakan lakukan Top Up Dompet Wali terlebih dahulu.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'insufficient_balance' => true,
                        'current_balance' => (float) $wali->balance,
                        'shortage' => (float) max(0, $amount - $wali->balance),
                        'message' => $errorMsg,
                    ], 422);
                }
                return back()->with('error', $errorMsg);
            }
        }

        DB::transaction(function () use ($card, $wali, $student, $amount, $method, $nimTag, $noteText) {
            // Potong saldo dompet wali
            if ($method === 'transfer_wali') {
                $wali->balance -= $amount;
                $wali->save();
            }

            // Tambah saldo ke kartu NFC anak/mahasiswa
            $card->balance += $amount;
            $card->save();

            $orderId = 'TRF-WALI-' . time() . '-' . rand(100, 999);

            Transaction::create([
                'user_id' => $wali->id,
                'card_id' => $card->id,
                'pedagang_id' => null,
                'amount' => $amount,
                'type' => 'topup',
                'payment_method' => $method,
                'order_id' => $orderId,
                'status' => 'success',
                'order_status' => 'selesai',
                'description' => 'Transfer dari Dompet Wali (' . $wali->name . ') ke ' . ($student->name ?? 'Mahasiswa') . $nimTag . $noteText,
            ]);
        });

        $studentName = $student ? $student->name : 'Mahasiswa';
        $message = 'Berhasil mengirim saldo sebesar Rp ' . number_format($amount, 0, ',', '.') . ' ke ' . $studentName . ($student && $student->nim ? ' (NIM: ' . $student->nim . ')' : '') . '. Sisa saldo dompet Anda Rp ' . number_format($wali->fresh()->balance, 0, ',', '.') . '.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'student_new_balance' => (float) $card->fresh()->balance,
                'wali_new_balance' => (float) $wali->fresh()->balance,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Fallback generic store method
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->role === 'wali') {
            return $this->waliTransfer($request);
        }
        return $this->mahasiswaTopup($request);
    }
}
