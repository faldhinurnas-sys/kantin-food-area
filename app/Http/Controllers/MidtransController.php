<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', true));
        \Midtrans\Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', env('MIDTRANS_IS_SANITIZED', true));
        \Midtrans\Config::$is3ds = (bool) config('services.midtrans.is_3ds', env('MIDTRANS_IS_3DS', true));
    }

    /**
     * Memproses inisialisasi Top Up Saldo Kartu NFC via Midtrans Snap Token
     */
    public function topup(Request $request)
    {
        $request->validate([
            'card_id' => 'nullable|exists:cards,id',
            'nim'     => 'nullable|string|max:50',
            'amount'  => 'required|numeric|min:1000',
            'notes'   => 'nullable|string|max:255',
        ], [
            'amount.required'  => 'Nominal top up wajib diisi.',
            'amount.min'       => 'Minimal nominal top up adalah Rp 1.000.',
        ]);

        $card = null;
        if ($request->filled('card_id')) {
            $card = Card::with('user')->find($request->card_id);
        }

        if (!$card && $request->filled('nim')) {
            $nim = trim($request->nim);
            $student = User::where('role', 'mahasiswa')
                ->where(function ($q) use ($nim) {
                    $q->where('nim', $nim)->orWhere('email', $nim);
                })
                ->first();
            if ($student) {
                $card = Card::with('user')->where('user_id', $student->id)->where('status', 'active')->latest()->first();
            }
        }

        if (!$card) {
            return response()->json(['error' => 'Kartu NFC penerima tidak ditemukan atau belum aktif.'], 400);
        }

        $user = Auth::user();
        $isWali = $user && $user->role === 'wali';

        if ($card->status !== 'active') {
            return response()->json(['error' => 'Kartu NFC tidak dalam status aktif.'], 400);
        }

        $orderId = 'TOPUP-' . time() . '-' . rand(100, 999);
        $note = $request->filled('notes') ? ' - ' . trim($request->notes) : '';

        $description = $isWali
            ? 'Top Up via Midtrans oleh Wali (' . $user->name . ') ke ' . ($card->user->name ?? 'Mahasiswa') . $note
            : 'Top Up Saldo Mandiri via Midtrans (' . ($user->name ?? 'Mahasiswa') . ')' . $note;

        // Buat record pending transaction
        $transaction = Transaction::create([
            'user_id' => $user ? $user->id : null,
            'card_id' => $card->id,
            'pedagang_id' => null,
            'amount' => (float) $request->amount,
            'type' => 'topup',
            'payment_method' => 'midtrans',
            'order_id' => $orderId,
            'status' => 'pending',
            'order_status' => 'menunggu',
            'description' => $description
        ]);

        $serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        if (empty($serverKey) || str_contains($serverKey, '...')) {
            return response()->json([
                'error' => 'MIDTRANS_SERVER_KEY di .env belum dikonfigurasi dengan Server Key yang valid.'
            ], 422);
        }

        $payerName = $user ? $user->name : ($card->user->name ?? 'Mahasiswa');
        $payerEmail = $user ? $user->email : ($card->user->email ?? 'mahasiswa@example.com');

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $request->amount,
            ],
            'customer_details' => [
                'first_name' => $payerName,
                'email' => $payerEmail,
            ],
            'item_details' => [
                [
                    'id' => 'TOPUP-' . $card->id,
                    'price' => (int) $request->amount,
                    'quantity' => 1,
                    'name' => 'Top Up Saldo Kartu NFC ' . $card->card_uid,
                ]
            ]
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'amount' => $request->amount
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans getSnapToken error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menghubungkan ke Midtrans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memproses Top Up Saldo Dompet Wali Pribadi via Midtrans Snap Token
     */
    public function topupWallet(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'wali') {
            return response()->json(['error' => 'Akses hanya untuk akun Wali.'], 403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ], [
            'amount.required' => 'Nominal pengisian saldo wajib diisi.',
            'amount.min'      => 'Minimal isi saldo adalah Rp 1.000.',
        ]);

        $orderId = 'TOPUP-WALI-' . time() . '-' . rand(100, 999);

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'card_id' => null,
            'pedagang_id' => null,
            'amount' => (float) $request->amount,
            'type' => 'topup',
            'payment_method' => 'midtrans',
            'order_id' => $orderId,
            'status' => 'pending',
            'order_status' => 'menunggu',
            'description' => 'Top Up Saldo Dompet Wali via Midtrans (' . $user->name . ')'
        ]);

        $serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        if (empty($serverKey) || str_contains($serverKey, '...')) {
            return response()->json([
                'error' => 'MIDTRANS_SERVER_KEY di .env belum dikonfigurasi dengan Server Key yang valid.'
            ], 422);
        }

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $request->amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => [
                [
                    'id' => 'WALLET-' . $user->id,
                    'price' => (int) $request->amount,
                    'quantity' => 1,
                    'name' => 'Top Up Saldo Dompet Wali',
                ]
            ]
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'amount' => $request->amount
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans getSnapToken wallet error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menghubungkan ke Midtrans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Konfirmasi pembayaran Midtrans langsung dari return callback Snap (Frontend)
     */
    public function finishPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $transaction = Transaction::where('order_id', $request->order_id)->first();
        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
        }

        $this->successTransaction($transaction);

        $newBalance = 0;
        if ($transaction->card_id) {
            $card = Card::find($transaction->card_id);
            $newBalance = $card ? (float) $card->balance : 0;
        } elseif ($transaction->user_id) {
            $user = User::find($transaction->user_id);
            $newBalance = $user ? (float) $user->balance : 0;
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikonfirmasi dan saldo telah ditambahkan!',
            'new_balance' => $newBalance
        ]);
    }

    /**
     * Membatalkan transaksi pembayaran Midtrans (Manual atau saat popup ditutup/dibatalkan)
     */
    public function cancelPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $user = Auth::user();
        $transaction = Transaction::where('order_id', $request->order_id)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.'
            ], 404);
        }

        if ($user && $user->role !== 'admin') {
            $isOwner = false;
            if ($transaction->user_id && $transaction->user_id === $user->id) {
                $isOwner = true;
            } elseif ($transaction->card_id) {
                $card = Card::find($transaction->card_id);
                if ($card && ($card->user_id === $user->id || ($user->role === 'wali' && $user->student_id === $card->user_id))) {
                    $isOwner = true;
                }
            }
            if (!$isOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak untuk membatalkan transaksi ini.'
                ], 403);
            }
        }

        if ($transaction->status === 'success') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi yang sudah berhasil tidak dapat dibatalkan.'
            ], 400);
        }

        // Coba batalkan ke Midtrans API jika transaksi aktif
        try {
            \Midtrans\Transaction::cancel($transaction->order_id);
        } catch (\Exception $e) {
            Log::info('Midtrans remote cancel info: ' . $e->getMessage());
        }

        $transaction->update([
            'status' => 'failed',
            'order_status' => 'batal'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran Midtrans berhasil dibatalkan.'
        ]);
    }

    /**
     * Webhook Midtrans (Notifikasi Otomatis Server-to-Server)
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload);

        if (!$notification || !isset($notification->order_id)) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        if (!empty($serverKey)) {
            $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . $serverKey);
            if (!isset($notification->signature_key) || $notification->signature_key != $validSignatureKey) {
                return response()->json(['message' => 'Invalid signature'], 403);
            }
        }

        $transaction = Transaction::where('order_id', $notification->order_id)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transactionStatus = $notification->transaction_status ?? '';
        $fraudStatus = $notification->fraud_status ?? '';

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                // Pending
            } else if ($fraudStatus == 'accept') {
                $this->successTransaction($transaction);
            }
        } else if ($transactionStatus == 'settlement') {
            $this->successTransaction($transaction);
        } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $transaction->update(['status' => 'failed', 'order_status' => 'batal']);
        }

        return response()->json(['message' => 'Success']);
    }

    /**
     * Helper untuk mengubah transaksi pending menjadi success dan menambah saldo kartu atau dompet user
     */
    public function successTransaction($transaction)
    {
        if ($transaction->status === 'success') {
            return;
        }

        DB::transaction(function () use ($transaction) {
            $orderStatus = $transaction->type === 'topup' ? 'selesai' : 'diproses';

            $transaction->update([
                'status' => 'success',
                'order_status' => $orderStatus
            ]);

            if ($transaction->type === 'topup') {
                if ($transaction->card_id) {
                    $card = Card::find($transaction->card_id);
                    if ($card) {
                        $card->balance += (float) $transaction->amount;
                        $card->save();
                    }
                } elseif ($transaction->user_id) {
                    $user = User::find($transaction->user_id);
                    if ($user) {
                        $user->balance += (float) $transaction->amount;
                        $user->save();
                    }
                }
            }
        });
    }
}
