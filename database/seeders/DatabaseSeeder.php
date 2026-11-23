<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Card;
use App\Models\Menu;
use App\Models\Limit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ================= ADMIN =================
        $admin = User::create([
            'name' => 'Admin Kampus',
            'email' => 'admin@kantin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'approved'
        ]);

        // ================= WALI =================
        $wali1 = User::create([
            'name' => 'Bapak Budi (Wali Andi)',
            'email' => 'wali1@kantin.com',
            'password' => Hash::make('password'),
            'role' => 'wali',
            'status' => 'approved',
            'balance' => 250000,
        ]);

        $wali2 = User::create([
            'name' => 'Ibu Siti (Wali Budi)',
            'email' => 'wali2@kantin.com',
            'password' => Hash::make('password'),
            'role' => 'wali',
            'status' => 'approved',
            'balance' => 200000,
        ]);

        // ================= MAHASISWA =================
        $mahasiswa1 = User::create([
            'name' => 'Andi (Mahasiswa)',
            'email' => 'mahasiswa1@kantin.com',
            'nim' => '2101092001',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'status' => 'approved'
        ]);

        $mahasiswa2 = User::create([
            'name' => 'Budi (Mahasiswa)',
            'email' => 'mahasiswa2@kantin.com',
            'nim' => '2101092002',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'status' => 'approved'
        ]);

        // ================= PEDAGANG =================
        $pedagang1 = User::create([
            'name' => 'Ibu Kantin A (Nasi Goreng)',
            'email' => 'pedagang1@kantin.com',
            'password' => Hash::make('password'),
            'role' => 'pedagang',
            'status' => 'approved'
        ]);

        $pedagang2 = User::create([
            'name' => 'Bapak Kantin B (Minuman)',
            'email' => 'pedagang2@kantin.com',
            'password' => Hash::make('password'),
            'role' => 'pedagang',
            'status' => 'approved'
        ]);

        // ================= KARTU NFC =================
        Card::create([
            'user_id' => $mahasiswa1->id,
            'card_uid' => 'NFC-11111',
            'balance' => 50000,
            'status' => 'active'
        ]);

        Card::create([
            'user_id' => $mahasiswa2->id,
            'card_uid' => 'NFC-22222',
            'balance' => 30000,
            'status' => 'active'
        ]);

        // ================= LIMIT =================
        // Wali 1 set limit untuk Andi (Mahasiswa 1)
        Limit::create([
            'student_id' => $mahasiswa1->id,
            'parent_id' => $wali1->id,
            'daily_limit' => 20000
        ]);
        
        // Wali 2 set limit untuk Budi (Mahasiswa 2)
        Limit::create([
            'student_id' => $mahasiswa2->id,
            'parent_id' => $wali2->id,
            'daily_limit' => 15000
        ]);

        // ================= MENU =================
        // Menu Pedagang 1
        Menu::create([
            'pedagang_id' => $pedagang1->id,
            'name' => 'Nasi Goreng Spesial',
            'price' => 15000,
            'description' => 'Nasi goreng pakai telur dan sosis'
        ]);
        Menu::create([
            'pedagang_id' => $pedagang1->id,
            'name' => 'Mie Kuah Pedas',
            'price' => 12000,
            'description' => 'Mie kuah ekstra pedas'
        ]);

        // Menu Pedagang 2
        Menu::create([
            'pedagang_id' => $pedagang2->id,
            'name' => 'Es Teh Manis',
            'price' => 4000,
            'description' => 'Es teh manis dingin'
        ]);
        Menu::create([
            'pedagang_id' => $pedagang2->id,
            'name' => 'Jus Jeruk',
            'price' => 8000,
            'description' => 'Jus jeruk asli peras'
        ]);
    }
}
