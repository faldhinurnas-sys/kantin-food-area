<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Menu;
use App\Models\Transaction;
use App\Models\TransactionItem;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TEST-01.1: Akses Halaman Utama (/)
     */
    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Polbeng Food Area');
        $response->assertSee('Politeknik Negeri Bengkalis');
    }

    /**
     * TEST-01.2: Tampilan Menu Terlaris di Landing Page
     */
    public function test_landing_page_displays_top_selling_menus(): void
    {
        $pedagang = User::factory()->create([
            'role' => 'pedagang',
            'status' => 'approved'
        ]);

        $menu1 = Menu::create([
            'pedagang_id' => $pedagang->id,
            'name' => 'Nasi Goreng Spesial Polbeng',
            'price' => 15000,
            'category' => 'makanan',
            'is_available' => true,
        ]);

        $menu2 = Menu::create([
            'pedagang_id' => $pedagang->id,
            'name' => 'Jus Mangga Segar',
            'price' => 8000,
            'category' => 'minuman',
            'is_available' => true,
        ]);

        $transaction = Transaction::create([
            'pedagang_id' => $pedagang->id,
            'amount' => 38000,
            'type' => 'payment',
            'status' => 'success',
            'order_status' => 'selesai',
        ]);

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'menu_id' => $menu1->id,
            'quantity' => 2,
            'price' => 15000
        ]);

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'menu_id' => $menu2->id,
            'quantity' => 1,
            'price' => 8000
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Nasi Goreng Spesial Polbeng');
        $response->assertSee('Jus Mangga Segar');
        $response->assertSee('porsi terjual');
    }

    /**
     * TEST-01.3: Akses Sematan Peta Google Maps Interaktif
     */
    public function test_landing_page_contains_interactive_google_maps_embed(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('google.com/maps/embed', false);
        $response->assertSee('Rusunawa Politeknik Negeri Bengkalis');
    }

    /**
     * TEST-01.4: Navigasi Anchor Section Landing Page
     */
    public function test_landing_page_contains_smooth_navigation_anchors(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('href="#layanan"', false);
        $response->assertSee('href="#katalog-menu"', false);
        $response->assertSee('href="#lokasi"', false);
    }

    /**
     * TEST-01.5: Tautan Rute Eksternal Google Maps
     */
    public function test_landing_page_has_direct_google_maps_route_link(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('https://share.google/jY8dDF9yt6caXMZql');
    }
}
