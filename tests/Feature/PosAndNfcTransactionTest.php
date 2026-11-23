<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Card;
use App\Models\Menu;
use App\Models\Limit;
use App\Models\Transaction;

class PosAndNfcTransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $pedagang;
    private User $mahasiswa;
    private Card $card;
    private Menu $menu1;
    private Menu $menu2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pedagang = User::create([
            'name' => 'Stan Pedagang POS',
            'email' => 'pedagang_pos@kantin.com',
            'password' => bcrypt('password123'),
            'role' => 'pedagang',
            'status' => 'approved',
        ]);

        $this->mahasiswa = User::create([
            'name' => 'Fajar Mahasiswa',
            'email' => 'fajar@kantin.com',
            'nim' => '2101092055',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
            'status' => 'approved',
        ]);

        $this->card = Card::create([
            'user_id' => $this->mahasiswa->id,
            'card_uid' => 'NFC-FAJAR-1',
            'balance' => 50000,
            'status' => 'active',
        ]);

        $this->menu1 = Menu::create([
            'pedagang_id' => $this->pedagang->id,
            'name' => 'Nasi Ayam Geprek',
            'price' => 15000,
            'category' => 'makanan',
            'is_available' => true,
        ]);

        $this->menu2 = Menu::create([
            'pedagang_id' => $this->pedagang->id,
            'name' => 'Es Jeruk Peras',
            'price' => 5000,
            'category' => 'minuman',
            'is_available' => true,
        ]);
    }

    /**
     * TEST-05.1: Simpan Pesanan Kasir POS (Store Order)
     */
    public function test_pedagang_can_store_order(): void
    {
        $response = $this->actingAs($this->pedagang)->post('/pedagang/transaction/store-order', [
            'items' => [
                ['menu_id' => $this->menu1->id, 'quantity' => 1],
                ['menu_id' => $this->menu2->id, 'quantity' => 2],
            ]
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('transactions', [
            'pedagang_id' => $this->pedagang->id,
            'amount' => 25000, // 15.000 + (5.000 * 2)
            'status' => 'pending',
            'order_status' => 'menunggu',
        ]);
    }

    /**
     * TEST-05.2: Pembayaran NFC Berhasil dengan Saldo Mencukupi
     */
    public function test_nfc_payment_succeeds_with_sufficient_balance(): void
    {
        $response = $this->actingAs($this->pedagang)->postJson('/pedagang/transaction/nfc', [
            'card_uid' => 'NFC-FAJAR-1',
            'items' => [
                ['menu_id' => $this->menu1->id, 'quantity' => 1], // 15.000
                ['menu_id' => $this->menu2->id, 'quantity' => 1], // 5.000 (Total: 20.000)
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Saldo awal 50.000 - 20.000 = 30.000
        $this->card->refresh();
        $this->assertEquals(30000, $this->card->balance);

        $this->assertDatabaseHas('transactions', [
            'card_id' => $this->card->id,
            'pedagang_id' => $this->pedagang->id,
            'amount' => 20000,
            'status' => 'success',
            'payment_method' => 'nfc',
        ]);
    }

    /**
     * TEST-05.3: Penolakan Pembayaran NFC Karena Saldo Kurang (Insufficient Balance)
     */
    public function test_nfc_payment_fails_when_balance_is_insufficient(): void
    {
        $this->card->update(['balance' => 5000]); // Saldo hanya 5.000

        $response = $this->actingAs($this->pedagang)->postJson('/pedagang/transaction/nfc', [
            'card_uid' => 'NFC-FAJAR-1',
            'items' => [
                ['menu_id' => $this->menu1->id, 'quantity' => 1], // Butuh 15.000
            ]
        ]);

        $response->assertJson(['success' => false]);
        $response->assertJsonFragment(['message' => 'Transaksi gagal: Saldo tidak mencukupi. (Butuh Rp 15.000)']);

        // Saldo tetap utuh
        $this->card->refresh();
        $this->assertEquals(5000, $this->card->balance);
    }

    /**
     * TEST-05.4: Penolakan Pembayaran NFC Karena Melebihi Batas Limit Jajan Harian
     */
    public function test_nfc_payment_fails_when_exceeding_daily_limit(): void
    {
        $wali = User::create([
            'name' => 'Wali Fajar',
            'email' => 'wali_fajar@kantin.com',
            'password' => bcrypt('password123'),
            'role' => 'wali',
            'status' => 'approved',
        ]);

        Limit::create([
            'student_id' => $this->mahasiswa->id,
            'parent_id' => $wali->id,
            'daily_limit' => 10000, // Limit belanja harian disetel Rp 10.000
        ]);

        // Belanja Rp 15.000 (melebihi limit harian Rp 10.000)
        $response = $this->actingAs($this->pedagang)->postJson('/pedagang/transaction/nfc', [
            'card_uid' => 'NFC-FAJAR-1',
            'items' => [
                ['menu_id' => $this->menu1->id, 'quantity' => 1],
            ]
        ]);

        $response->assertJson(['success' => false]);
        $this->assertStringContainsString('Melebihi limit harian', $response->json('message'));

        // Saldo tidak terpotong
        $this->card->refresh();
        $this->assertEquals(50000, $this->card->balance);
    }

    /**
     * TEST-05.5: Penolakan Pembayaran Kartu NFC yang Belum Di-ACC / Pending
     */
    public function test_nfc_payment_fails_when_card_is_pending(): void
    {
        $this->card->update(['status' => 'pending']);

        $response = $this->actingAs($this->pedagang)->postJson('/pedagang/transaction/nfc', [
            'card_uid' => 'NFC-FAJAR-1',
            'items' => [
                ['menu_id' => $this->menu2->id, 'quantity' => 1],
            ]
        ]);

        $response->assertJson(['success' => false]);
        $this->assertStringContainsString('tidak terdaftar atau tidak aktif', $response->json('message'));
    }

    /**
     * TEST-05.6: Penolakan Pembayaran dengan UID Kartu Tidak Terdaftar
     */
    public function test_nfc_payment_fails_with_unregistered_uid(): void
    {
        $response = $this->actingAs($this->pedagang)->postJson('/pedagang/transaction/nfc', [
            'card_uid' => 'NFC-UNKNOWN-999',
            'items' => [
                ['menu_id' => $this->menu2->id, 'quantity' => 1],
            ]
        ]);

        $response->assertJson(['success' => false]);
        $this->assertStringContainsString('tidak terdaftar atau tidak aktif', $response->json('message'));
    }

    /**
     * TEST-05.7: Pembaruan Status Pesanan oleh Pedagang
     */
    public function test_pedagang_can_update_order_status(): void
    {
        $transaction = Transaction::create([
            'pedagang_id' => $this->pedagang->id,
            'card_id' => $this->card->id,
            'amount' => 15000,
            'type' => 'payment',
            'status' => 'success',
            'order_status' => 'diproses',
            'description' => 'Pesanan Uji Status'
        ]);

        $response = $this->actingAs($this->pedagang)->put("/pedagang/transaction/{$transaction->id}/status", [
            'order_status' => 'selesai'
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'order_status' => 'selesai',
        ]);
    }
}
