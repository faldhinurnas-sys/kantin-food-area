<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthAndRbacTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TEST-02.1: Login Valid Akun Admin
     */
    public function test_admin_can_login_and_redirects_to_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin Kampus',
            'email' => 'admin@kantin.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@kantin.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * TEST-02.2: Login Valid Akun Pedagang
     */
    public function test_pedagang_can_login_and_redirects_to_pedagang_dashboard(): void
    {
        $pedagang = User::create([
            'name' => 'Ibu Kantin',
            'email' => 'pedagang@kantin.com',
            'password' => Hash::make('password123'),
            'role' => 'pedagang',
            'status' => 'approved',
        ]);

        $response = $this->post('/login', [
            'email' => 'pedagang@kantin.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/pedagang/dashboard');
        $this->assertAuthenticatedAs($pedagang);
    }

    /**
     * TEST-02.3: Login Valid Akun Mahasiswa
     */
    public function test_mahasiswa_can_login_and_redirects_to_mahasiswa_dashboard(): void
    {
        $mahasiswa = User::create([
            'name' => 'Andi Mahasiswa',
            'email' => 'mahasiswa@kantin.com',
            'nim' => '2101092001',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'status' => 'approved',
        ]);

        $response = $this->post('/login', [
            'email' => 'mahasiswa@kantin.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/mahasiswa/dashboard');
        $this->assertAuthenticatedAs($mahasiswa);
    }

    /**
     * TEST-02.4: Login Valid Akun Wali Murid
     */
    public function test_wali_can_login_and_redirects_to_wali_dashboard(): void
    {
        $wali = User::create([
            'name' => 'Bapak Budi (Wali)',
            'email' => 'wali@kantin.com',
            'password' => Hash::make('password123'),
            'role' => 'wali',
            'status' => 'approved',
            'balance' => 200000,
        ]);

        $response = $this->post('/login', [
            'email' => 'wali@kantin.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/wali/dashboard');
        $this->assertAuthenticatedAs($wali);
    }

    /**
     * TEST-02.5: Login Gagal dengan Password Salah
     */
    public function test_login_fails_with_invalid_password(): void
    {
        User::create([
            'name' => 'User Test',
            'email' => 'user@kantin.com',
            'password' => Hash::make('passwordBenar'),
            'role' => 'mahasiswa',
            'status' => 'approved',
        ]);

        $response = $this->post('/login', [
            'email' => 'user@kantin.com',
            'password' => 'passwordSalah123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * TEST-02.6: Penolakan Login Pengguna Berstatus Belum Disetujui (Pending)
     */
    public function test_unapproved_user_cannot_login(): void
    {
        User::create([
            'name' => 'Pending User',
            'email' => 'pending@kantin.com',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'status' => 'pending',
        ]);

        $response = $this->post('/login', [
            'email' => 'pending@kantin.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * TEST-02.7: Pencegahan Akses Dashboard Langsung Tanpa Sesi Login (Guest)
     */
    public function test_guest_is_redirected_when_accessing_protected_dashboards(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/pedagang/dashboard')->assertRedirect('/login');
        $this->get('/mahasiswa/dashboard')->assertRedirect('/login');
        $this->get('/wali/dashboard')->assertRedirect('/login');
    }

    /**
     * TEST-02.8: Pencegahan Eskalasi Hak Akses (Role Privilege Isolation)
     */
    public function test_mahasiswa_cannot_access_admin_dashboard(): void
    {
        $mahasiswa = User::create([
            'name' => 'Mahasiswa Test',
            'email' => 'mhs@kantin.com',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($mahasiswa)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * TEST-02.9: Proses Logout Berhasil Menghapus Sesi
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::create([
            'name' => 'User Logout',
            'email' => 'logout@kantin.com',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
