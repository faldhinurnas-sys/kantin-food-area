<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;
use App\Models\Transaction;
use App\Models\Card;
use App\Models\Limit;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function pedagang()

    {
        $user = Auth::user();
        $menus = Menu::where('pedagang_id', $user->id)->get();
        $transactions = Transaction::where('pedagang_id', $user->id)->with('card.user')->latest()->get();
        
        $activeOrders = Transaction::where('pedagang_id', $user->id)
            ->where(function ($q) {
                $q->where('status', 'pending')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'success')->where('order_status', 'diproses');
                  });
            })
            ->with('items.menu')
            ->orderBy('created_at', 'asc')
            ->get();
        
        $totalPendapatan = Transaction::where('pedagang_id', $user->id)->where('status', 'success')->sum('amount');
        $activeCards = Card::where('status', 'active')->with('user')->get();

        return view('dashboard.pedagang', compact('menus', 'transactions', 'totalPendapatan', 'activeOrders', 'activeCards'));
    }

    public function mahasiswa()
    {
        $user = Auth::user();
        // Prioritaskan kartu aktif terbaru, fallback ke kartu terakhir
        $card = Card::where('user_id', $user->id)->where('status', 'active')->latest()->first()
            ?? Card::where('user_id', $user->id)->latest()->first();

        $cardIds = Card::where('user_id', $user->id)->pluck('id');
        $transactions = Transaction::whereIn('card_id', $cardIds)->with('pedagang')->latest()->get();
        $limit = Limit::where('student_id', $user->id)->first();
        $topupHistory = Transaction::whereIn('card_id', $cardIds)->where('type', 'topup')->latest()->get();

        return view('dashboard.mahasiswa', compact('card', 'transactions', 'limit', 'topupHistory'));
    }

    public function wali(Request $request)
    {
        $wali = Auth::user();
        $linkedStudentIds = Limit::where('parent_id', $wali->id)->pluck('student_id');
        $allMahasiswa = User::where('role', 'mahasiswa')
            ->with(['cards' => function ($q) {
                $q->where('status', 'active')->latest();
            }])
            ->orderBy('name')
            ->get();

        $selectedStudentId = $request->query('student_id');
        $mahasiswa = null;

        if ($selectedStudentId) {
            $mahasiswa = User::where('role', 'mahasiswa')->find($selectedStudentId);
        }

        if (!$mahasiswa && $linkedStudentIds->isNotEmpty()) {
            $mahasiswa = User::find($linkedStudentIds->first());
        }

        if (!$mahasiswa) {
            $mahasiswa = $allMahasiswa->first();
        }

        $card = null;
        $transactions = [];
        $limit = null;
        $transferHistory = collect();

        if ($mahasiswa) {
            $card = Card::where('user_id', $mahasiswa->id)->where('status', 'active')->latest()->first()
                ?? Card::where('user_id', $mahasiswa->id)->latest()->first();

            $limit = Limit::firstOrCreate(
                ['student_id' => $mahasiswa->id, 'parent_id' => $wali->id],
                ['daily_limit' => 50000, 'per_transaction_limit' => 20000]
            );

            $cardIds = Card::where('user_id', $mahasiswa->id)->pluck('id');
            $transactions = Transaction::whereIn('card_id', $cardIds)->with('pedagang')->latest()->get();
            $transferHistory = Transaction::whereIn('card_id', $cardIds)->where('type', 'topup')->latest()->get();
        }

        $walletTransactions = Transaction::where('user_id', $wali->id)->latest()->get();

        return view('dashboard.wali', compact('mahasiswa', 'card', 'transactions', 'limit', 'allMahasiswa', 'transferHistory', 'walletTransactions'));
    }

    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'avatar_cropped' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ];

        if ($user->role === 'mahasiswa') {
            $rules['nim'] = 'nullable|string|max:30|unique:users,nim,' . $user->id;
        }

        $request->validate($rules, [
            'name.required' => 'Nama lengkap / stan wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut telah digunakan oleh akun lain.',
            'nim.unique' => 'NIM tersebut sudah terdaftar untuk mahasiswa lain.',
            'avatar.image' => 'File foto profil harus berupa file gambar.',
            'avatar.mimes' => 'Format foto profil harus JPG, PNG, GIF, atau WEBP.',
            'avatar.max' => 'Ukuran file foto profil maksimal 5 MB.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->name = trim($request->name);
        $user->email = trim($request->email);
        if ($user->role === 'mahasiswa' && $request->has('nim')) {
            $user->nim = $request->filled('nim') ? trim($request->nim) : null;
        }

        // Upload Avatar baru (Mendukung base64 dari Cropper atau file upload langsung)
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
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil dan foto berhasil diperbarui.');
    }
}
