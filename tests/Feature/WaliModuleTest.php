<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Card;
use App\Models\Limit;

class WaliModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $wali;
    private User $mahasiswa;
    private Card $card;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wali = User::create([
            'name' => 'Bapak Budi (Wali)',
            'email' => 'wali_budi@kantin.com',
            'password' => bcrypt('password123'),
            'role' => 'wali',
            'status' => 'approved',
            'balance' => 150000, // Saldo dompet wali Rp 150.000
        ]);

        $this->mahasiswa = User::create([
            'name' => 'Andi Mahasiswa',
            'email' => 'andi@kantin.com',
            'nim' => '2101092001',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
            'status' => 'approved',
        ]);

        $this->card = Card::create([
            'user_id' => $this->mahasiswa->id,
            'card_uid' => 'NFC-ANDI-01',
            'balance' => 20000,
            'status' => 'active',
        ]);
    }

    /**
     * TEST-06.1: Verifikasi Data Mahasiswa Menggunakan NIM Valid
     */
    public function test_wali_can_verify_student_via_valid_nim(): void
    {
        $response = $this->actingAs($this->wali)->getJson('/wali/check-student-nim?nim=2101092001');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'has_active_card' => true,
            'student' => [
                'name' => 'Andi Mahasiswa',
                'nim' => '2101092001',
            ],
            'card' => [
                'card_uid' => 'NFC-ANDI-01',
                'status' => 'active',
            ]
        ]);
    }

    /**
     * TEST-06.2: Verifikasi Data Mahasiswa dengan NIM Tidak Terdaftar
     */
    public function test_wali_verification_fails_with_invalid_nim(): void
    {
        $response = $this->actingAs($this->wali)->getJson('/wali/check-student-nim?nim=9999999999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * TEST-06.3: Pengaturan Batas Belanja / Limit Jajan Harian oleh Wali
     */
    public function test_wali_can_set_daily_spending_limit(): void
    {
        $response = $this->actingAs($this->wali)->post('/wali/limit', [
            'student_id' => $this->mahasiswa->id,
            'daily_limit' => 25000,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('limits', [
            'student_id' => $this->mahasiswa->id,
            'parent_id' => $this->wali->id,
            'daily_limit' => 25000,
        ]);
    }

    /**
     * TEST-06.4: Transfer Saldo dari Dompet Wali ke Kartu Mahasiswa (Berhasil)
     */
    public function test_wali_can_transfer_balance_to_student_card(): void
    {
        $response = $this->actingAs($this->wali)->postJson('/wali/transfer', [
            'nim' => '2101092001',
            'amount' => 50000,
            'notes' => 'Uang jajan kuliah minggu ini',
            'payment_method' => 'transfer_wali',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Saldo wali berkurang: 150.000 - 50.000 = 100.000
        $this->wali->refresh();
        $this->assertEquals(100000, $this->wali->balance);

        // Saldo kartu mahasiswa bertambah: 20.000 + 50.000 = 70.000
        $this->card->refresh();
        $this->assertEquals(70000, $this->card->balance);

        // Transaksi tercatat
        $this->assertDatabaseHas('transactions', [
            'card_id' => $this->card->id,
            'user_id' => $this->wali->id,
            'amount' => 50000,
            'type' => 'topup',
            'payment_method' => 'transfer_wali',
            'status' => 'success',
        ]);
    }

    /**
     * TEST-06.5: Penolakan Transfer Saldo Karena Dompet Wali Tidak Mencukupi
     */
    public function test_wali_transfer_fails_when_wallet_balance_is_insufficient(): void
    {
        // Saldo wali hanya 150.000, mencoba transfer 200.000
        $response = $this->actingAs($this->wali)->postJson('/wali/transfer', [
            'nim' => '2101092001',
            'amount' => 200000,
            'payment_method' => 'transfer_wali',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'insufficient_balance' => true,
        ]);

        // Saldo wali & kartu tetap utuh
        $this->wali->refresh();
        $this->assertEquals(150000, $this->wali->balance);

        $this->card->refresh();
        $this->assertEquals(20000, $this->card->balance);
    }
}
