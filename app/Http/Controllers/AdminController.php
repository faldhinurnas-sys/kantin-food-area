<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Card;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $pendingUsers = User::where('status', 'pending')->get();
        $users = User::where('role', '!=', 'admin')->get();
        $pendingCards = Card::where('status', 'pending')->with('user')->get();
        $allCards = Card::with('user')->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'active' THEN 1 ELSE 2 END")->orderBy('created_at', 'desc')->get();
        
        $transactions = \App\Models\Transaction::with(['pedagang', 'card.user', 'items.menu'])->orderBy('created_at', 'desc')->get();
        $totalSales = \App\Models\Transaction::where('status', 'success')->where('type', 'payment')->sum('amount');
        $students = User::where('role', 'mahasiswa')->get();
        
        return view('dashboard.admin', compact('pendingUsers', 'users', 'students', 'pendingCards', 'allCards', 'transactions', 'totalSales'));
    }

    public function approveUser($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        
        $user = User::findOrFail($id);
        $user->status = 'approved';
        $user->save();

        return back()->with('success', 'User approved successfully.');
    }

    public function rejectUser($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        
        $user = User::findOrFail($id);
        $user->status = 'rejected';
        $user->save();

        return back()->with('success', 'User rejected successfully.');
    }

    public function destroyUser($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        if (Auth::id() == $id) {
            return back()->with('error', 'Tidak dapat menghapus akun admin Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $name = $user->name;

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Putuskan relasi kartu & transaksi milik user
        foreach ($user->cards as $c) {
            Transaction::where('card_id', $c->id)->update(['card_id' => null]);
            $c->delete();
        }

        // Hapus batas limit saldo
        \App\Models\Limit::where('student_id', $user->id)->orWhere('parent_id', $user->id)->delete();

        $user->delete();

        return back()->with('success', "Akun pengguna {$name} berhasil dihapus permanen.");
    }

    public function approveCard($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        
        $card = Card::with('user')->findOrFail($id);

        // Nonaktifkan kartu aktif lain milik mahasiswa ini jika ada
        Card::where('user_id', $card->user_id)
            ->where('id', '!=', $card->id)
            ->where('status', 'active')
            ->update(['status' => 'inactive']);

        $card->status = 'active';
        $card->save();

        $userName = $card->user->name ?? 'Mahasiswa';
        return back()->with('success', "Kartu NFC {$card->card_uid} milik {$userName} berhasil di-ACC dan diaktifkan.");
    }

    public function rejectCard($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        
        $card = Card::findOrFail($id);
        $card->status = 'inactive';
        $card->save();

        return back()->with('success', "Kartu NFC {$card->card_uid} berhasil ditolak / dinonaktifkan.");
    }

    public function destroyCard($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $card = Card::findOrFail($id);
        $cardUid = $card->card_uid;
        $studentName = $card->user->name ?? 'Mahasiswa';

        // Putuskan relasi transaksi agar riwayat transaksi tetap aman
        Transaction::where('card_id', $card->id)->update(['card_id' => null]);

        $card->delete();

        return back()->with('success', "Kartu NFC {$cardUid} milik {$studentName} berhasil dihapus permanen.");
    }

    public function storeCard(Request $request)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'card_uid' => 'required|string|unique:cards,card_uid',
            'balance' => 'nullable|numeric|min:0'
        ], [
            'card_uid.required' => 'UID Kartu NFC wajib diisi / ditempelkan.',
            'card_uid.unique' => 'UID kartu ini sudah terdaftar di sistem.'
        ]);

        $card = Card::create([
            'user_id' => $request->user_id,
            'card_uid' => strtoupper(trim($request->card_uid)),
            'balance' => $request->balance ?? 0,
            'status' => 'pending'
        ]);

        $userName = $card->user->name ?? 'Mahasiswa';
        return back()->with('success', "Kartu NFC {$card->card_uid} milik {$userName} berhasil didaftarkan! Status saat ini: MENUNGGU ACC / VALIDASI ADMIN.");
    }

    public function replaceCard(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'new_card_uid' => 'required|string|unique:cards,card_uid',
            'reason' => 'nullable|string|max:255'
        ], [
            'new_card_uid.required' => 'UID kartu pengganti wajib diisi / ditempelkan.',
            'new_card_uid.unique' => 'UID kartu ini sudah terdaftar di sistem. Gunakan kartu baru lainnya.'
        ]);

        $oldCard = Card::findOrFail($id);
        $migratedBalance = $oldCard->balance;
        $reason = $request->reason ?: 'Kartu fisik rusak/hilang';

        \Illuminate\Support\Facades\DB::transaction(function () use ($oldCard, $request, $migratedBalance, $reason) {
            // 1. Nonaktifkan kartu lama & kosongkan saldo agar tidak ganda
            $oldCard->status = 'inactive';
            $oldCard->balance = 0;
            $oldCard->save();

            // 2. Buat kartu baru dengan saldo yang dimigrasikan
            $newCard = Card::create([
                'user_id' => $oldCard->user_id,
                'card_uid' => strtoupper(trim($request->new_card_uid)),
                'balance' => $migratedBalance,
                'status' => 'active'
            ]);

            // 3. Catat riwayat log migrasi saldo (jika ada saldo yang dipindahkan)
            if ($migratedBalance > 0) {
                \App\Models\Transaction::create([
                    'card_id' => $newCard->id,
                    'pedagang_id' => null,
                    'amount' => $migratedBalance,
                    'type' => 'topup',
                    'payment_method' => 'migrasi_kartu',
                    'status' => 'success',
                    'order_status' => 'selesai',
                    'description' => "Penggantian kartu ({$reason}) dari UID {$oldCard->card_uid} ke UID {$newCard->card_uid}"
                ]);
            }
        });

        return back()->with('success', 'Kartu berhasil diganti! Sisa saldo Rp ' . number_format($migratedBalance, 0, ',', '.') . ' telah berhasil dipindahkan ke kartu baru.');
    }

    public function overrideLimit(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        return back()->with('success', 'Limit override approved.');
    }

    public function showUser($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $user = User::findOrFail($id);
        
        $transactions = [];
        $children = collect();
        $cards = collect();
        $totalSalesOrSpent = 0;
        
        if ($user->role == 'pedagang') {
            $transactions = \App\Models\Transaction::with(['card.user'])->where('pedagang_id', $user->id)->orderBy('created_at', 'desc')->get();
            $totalSalesOrSpent = $transactions->where('status', 'success')->where('type', 'payment')->sum('amount');
        } elseif ($user->role == 'mahasiswa') {
            $cards = \App\Models\Card::where('user_id', $user->id)->get();
            $cardIds = $cards->pluck('id');
            $transactions = \App\Models\Transaction::with(['pedagang'])->whereIn('card_id', $cardIds)->orderBy('created_at', 'desc')->get();
            $totalSalesOrSpent = $transactions->where('status', 'success')->where('type', 'payment')->sum('amount');
        } elseif ($user->role == 'wali') {
            $limits = \App\Models\Limit::where('parent_id', $user->id)->get();
            $childrenIds = $limits->pluck('student_id');
            $children = User::whereIn('id', $childrenIds)->get();
            $cards = \App\Models\Card::whereIn('user_id', $childrenIds)->get();
            $cardIds = $cards->pluck('id');
            $transactions = \App\Models\Transaction::with(['pedagang', 'card.user'])->whereIn('card_id', $cardIds)->orderBy('created_at', 'desc')->get();
            $totalSalesOrSpent = $transactions->where('status', 'success')->where('type', 'payment')->sum('amount');
        }

        return view('dashboard.admin_user_detail', compact('user', 'transactions', 'children', 'cards', 'totalSalesOrSpent'));
    }

    public function updateRent(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        
        $request->validate([
            'rent_due_date' => 'required|date'
        ]);
        
        $user = User::findOrFail($id);
        $user->rent_due_date = $request->rent_due_date;
        $user->save();

        return back()->with('success', 'Tempo sewa lapak berhasil diperbarui.');
    }

    public function updateUser(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'avatar_cropped' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ];

        if ($user->role === 'mahasiswa') {
            $rules['nim'] = 'nullable|string|max:30|unique:users,nim,' . $user->id;
        }

        $request->validate($rules, [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email telah digunakan pengguna lain.',
            'nim.unique' => 'NIM telah terdaftar untuk mahasiswa lain.',
            'avatar.image' => 'File foto profil harus berupa file gambar.',
            'avatar.mimes' => 'Format foto profil harus JPG, PNG, GIF, atau WEBP.',
            'avatar.max' => 'Ukuran file foto profil maksimal 5 MB.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user->name = trim($request->name);
        $user->email = trim($request->email);
        if ($user->role === 'mahasiswa' && $request->has('nim')) {
            $user->nim = $request->filled('nim') ? trim($request->nim) : null;
        }

        if ($request->filled('avatar_cropped') && preg_match('/^data:image\/(\w+);base64,/', $request->avatar_cropped, $matches)) {
            $imageFormat = strtolower($matches[1]);
            if (!in_array($imageFormat, ['jpeg', 'jpg', 'png', 'webp'])) {
                $imageFormat = 'jpg';
            }
            $imageData = substr($request->avatar_cropped, strpos($request->avatar_cropped, ',') + 1);
            $decoded = base64_decode($imageData);
            if ($decoded !== false) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $filename = 'avatars/' . \Illuminate\Support\Str::random(40) . '.' . ($imageFormat === 'jpeg' ? 'jpg' : $imageFormat);
                Storage::disk('public')->put($filename, $decoded);
                $user->avatar = $filename;
            }
        } elseif ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
        }

        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Data profil pengguna (' . $user->name . ') berhasil diperbarui.');
    }
}
