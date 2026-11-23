<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Card;
use App\Models\Transaction;

class MidtransIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $mahasiswa;
    private Card $card;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mahasiswa = User::create([
            'name' => 'Bagus Mahasiswa',
            'email' => 'bagus@kantin.com',
            'nim' => '2101092077',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
            'status' => 'approved',
        ]);

        $this->card = Card::create([
            'user_id' => $this->mahasiswa->id,
            'card_uid' => 'NFC-BAGUS-01',
            'balance' => 15000,
            'status' => 'active',
        ]);
    }

    /**
     * TEST-07.1: Callback Finish Payment Midtrans Berhasil Menambah Saldo Kartu NFC
     */
    public function test_midtrans_finish_payment_increments_card_balance(): void
    {
        $transaction = Transaction::create([
            'user_id' => $this->mahasiswa->id,
            'card_id' => $this->card->id,
            'amount' => 50000,
            'type' => 'topup',
            'payment_method' => 'midtrans',
            'order_id' => 'TOPUP-TEST-FINISH-01',
            'status' => 'pending',
            'order_status' => 'menunggu',
            'description' => 'Top Up Mandiri via Midtrans'
        ]);

        $response = $this->actingAs($this->mahasiswa)->postJson('/midtrans/finish', [
            'order_id' => 'TOPUP-TEST-FINISH-01'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'new_balance' => 65000, // 15.000 + 50.000 = 65.000
        ]);

        // Verifikasi saldo kartu di database
        $this->card->refresh();
        $this->assertEquals(65000, $this->card->balance);

        // Verifikasi status transaksi
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'success',
            'order_status' => 'selesai',
        ]);
    }

    /**
     * TEST-07.2: Webhook Server-to-Server Midtrans Status Settlement
     */
    public function test_midtrans_webhook_settlement_increments_card_balance(): void
    {
        $transaction = Transaction::create([
            'user_id' => $this->mahasiswa->id,
            'card_id' => $this->card->id,
            'amount' => 100000,
            'type' => 'topup',
            'payment_method' => 'midtrans',
            'order_id' => 'TOPUP-WEBHOOK-SETTLEMENT-01',
            'status' => 'pending',
            'order_status' => 'menunggu',
            'description' => 'Top Up Saldo via Webhook'
        ]);

        $serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', ''));
        $signatureKey = hash("sha512", 'TOPUP-WEBHOOK-SETTLEMENT-01' . '200' . '100000.00' . $serverKey);

        $payload = [
            'order_id' => 'TOPUP-WEBHOOK-SETTLEMENT-01',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
        ];

        $response = $this->postJson('/midtrans/webhook', $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Success']);

        // Saldo bertambah 15.000 + 100.000 = 115.000
        $this->card->refresh();
        $this->assertEquals(115000, $this->card->balance);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'success',
            'order_status' => 'selesai',
        ]);
    }

    /**
     * TEST-07.3: Webhook Midtrans Status Expire / Cancel
     */
    public function test_midtrans_webhook_expire_marks_transaction_as_failed(): void
    {
        $transaction = Transaction::create([
            'user_id' => $this->mahasiswa->id,
            'card_id' => $this->card->id,
            'amount' => 50000,
            'type' => 'topup',
            'payment_method' => 'midtrans',
            'order_id' => 'TOPUP-WEBHOOK-EXPIRE-01',
            'status' => 'pending',
            'order_status' => 'menunggu',
        ]);

        $serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', ''));
        $signatureKey = hash("sha512", 'TOPUP-WEBHOOK-EXPIRE-01' . '200' . '50000.00' . $serverKey);

        $payload = [
            'order_id' => 'TOPUP-WEBHOOK-EXPIRE-01',
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'signature_key' => $signatureKey,
            'transaction_status' => 'expire',
        ];

        $response = $this->postJson('/midtrans/webhook', $payload);

        $response->assertStatus(200);

        // Saldo tidak berubah
        $this->card->refresh();
        $this->assertEquals(15000, $this->card->balance);

        // Status transaksi gagal / batal
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'failed',
            'order_status' => 'batal',
        ]);
    }

    /**
     * TEST-07.4: Pembatalan Pembayaran Midtrans Secara Mandiri oleh User
     */
    public function test_user_can_cancel_pending_midtrans_payment(): void
    {
        $transaction = Transaction::create([
            'user_id' => $this->mahasiswa->id,
            'card_id' => $this->card->id,
            'amount' => 20000,
            'type' => 'topup',
            'payment_method' => 'midtrans',
            'order_id' => 'TOPUP-CANCEL-01',
            'status' => 'pending',
            'order_status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->mahasiswa)->postJson('/midtrans/cancel', [
            'order_id' => 'TOPUP-CANCEL-01'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Pembayaran Midtrans berhasil dibatalkan.',
        ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'failed',
            'order_status' => 'batal',
        ]);
    }
}
