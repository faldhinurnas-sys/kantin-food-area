<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Card;

class AdminModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Kampus',
            'email' => 'admin@kantin.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $this->mahasiswa = User::create([
            'name' => 'Rian Mahasiswa',
            'email' => 'rian@kantin.com',
            'nim' => '2101092099',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
            'status' => 'approved',
        ]);
    }

    /**
     * TEST-03.1: Registrasi Kartu NFC Baru oleh Admin
     */
    public function test_admin_can_store_new_nfc_card(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/cards', [
            'user_id' => $this->mahasiswa->id,
            'card_uid' => 'NFC-TEST-999',
            'balance' => 25000,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cards', [
            'card_uid' => 'NFC-TEST-999',
            'user_id' => $this->mahasiswa->id,
            'status' => 'pending',
            'balance' => 25000,
        ]);
    }

    /**
     * TEST-03.2: Persetujuan (Approval) Kartu NFC Mahasiswa
     */
    public function test_admin_can_approve_pending_nfc_card(): void
    {
        $card = Card::create([
            'user_id' => $this->mahasiswa->id,
            'card_uid' => 'NFC-APPROVE-1',
            'balance' => 50000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/cards/{$card->id}/approve");

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'status' => 'active',
        ]);
    }

    /**
     * TEST-03.3: Penolakan / Penonaktifan Kartu NFC Mahasiswa
     */
    public function test_admin_can_reject_nfc_card(): void
    {
        $card = Card::create([
            'user_id' => $this->mahasiswa->id,
            'card_uid' => 'NFC-REJECT-1',
            'balance' => 10000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/cards/{$card->id}/reject");

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'status' => 'inactive',
        ]);
    }

    /**
     * TEST-03.4: Penggantian Kartu Hilang (Replace Card) dengan Migrasi Saldo
     */
    public function test_admin_can_replace_card_and_migrates_balance(): void
    {
        $oldCard = Card::create([
            'user_id' => $this->mahasiswa->id,
            'card_uid' => 'NFC-OLD-111',
            'balance' => 75000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/cards/{$oldCard->id}/replace", [
            'new_card_uid' => 'NFC-NEW-222',
            'reason' => 'Kartu fisik hilang di lab',
        ]);

        $response->assertSessionHas('success');

        // Kartu lama menjadi non-aktif dengan saldo 0
        $this->assertDatabaseHas('cards', [
            'id' => $oldCard->id,
            'status' => 'inactive',
            'balance' => 0,
        ]);

        // Kartu baru aktif dengan saldo 75000
        $this->assertDatabaseHas('cards', [
            'card_uid' => 'NFC-NEW-222',
            'user_id' => $this->mahasiswa->id,
            'status' => 'active',
            'balance' => 75000,
        ]);

        // Transaksi migrasi tercatat
        $this->assertDatabaseHas('transactions', [
            'payment_method' => 'migrasi_kartu',
            'amount' => 75000,
            'status' => 'success',
        ]);
    }

    /**
     * TEST-03.5: Pembaruan Jatuh Tempo Sewa Stan Pedagang
     */
    public function test_admin_can_update_pedagang_rent_due_date(): void
    {
        $pedagang = User::create([
            'name' => 'Stan Gorengan Pak Dul',
            'email' => 'pakdul@kantin.com',
            'password' => bcrypt('password123'),
            'role' => 'pedagang',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/users/{$pedagang->id}/rent", [
            'rent_due_date' => '2026-12-31',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $pedagang->id,
            'rent_due_date' => '2026-12-31',
        ]);
    }
}
