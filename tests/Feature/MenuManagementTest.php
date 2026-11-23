<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Menu;

class MenuManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $pedagang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pedagang = User::create([
            'name' => 'Ibu Warung Kantin',
            'email' => 'warung@kantin.com',
            'password' => bcrypt('password123'),
            'role' => 'pedagang',
            'status' => 'approved',
        ]);
    }

    /**
     * TEST-04.1: Tambah Menu Makanan Baru
     */
    public function test_pedagang_can_store_new_menu(): void
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('soto.jpg');

        $response = $this->actingAs($this->pedagang)->post('/pedagang/menu', [
            'name' => 'Soto Ayam Lamongan',
            'category' => 'makanan',
            'price' => 12000,
            'description' => 'Soto ayam gurih hangat lengkap dengan koya',
            'image' => $image,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('menus', [
            'pedagang_id' => $this->pedagang->id,
            'name' => 'Soto Ayam Lamongan',
            'category' => 'makanan',
            'price' => 12000,
        ]);
    }

    /**
     * TEST-04.2: Validasi Format File Gambar Menu
     */
    public function test_menu_creation_fails_with_invalid_file_type(): void
    {
        Storage::fake('public');
        $invalidFile = UploadedFile::fake()->create('dokumen.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->pedagang)->post('/pedagang/menu', [
            'name' => 'Menu Uji',
            'category' => 'makanan',
            'price' => 10000,
            'image' => $invalidFile,
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('menus', [
            'name' => 'Menu Uji'
        ]);
    }

    /**
     * TEST-04.3: Ubah Data Menu Hidangan
     */
    public function test_pedagang_can_update_menu(): void
    {
        $menu = Menu::create([
            'pedagang_id' => $this->pedagang->id,
            'name' => 'Es Teh Manis',
            'category' => 'minuman',
            'price' => 3000,
            'is_available' => true,
        ]);

        $response = $this->actingAs($this->pedagang)->put("/pedagang/menu/{$menu->id}", [
            'name' => 'Es Teh Manis Jumbo',
            'category' => 'minuman',
            'price' => 5000,
            'description' => 'Porsi jumbo lebih segar',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'name' => 'Es Teh Manis Jumbo',
            'price' => 5000,
        ]);
    }

    /**
     * TEST-04.4: Hapus Menu Makanan
     */
    public function test_pedagang_can_delete_menu(): void
    {
        $menu = Menu::create([
            'pedagang_id' => $this->pedagang->id,
            'name' => 'Kwetiau Goreng',
            'category' => 'makanan',
            'price' => 13000,
            'is_available' => true,
        ]);

        $response = $this->actingAs($this->pedagang)->delete("/pedagang/menu/{$menu->id}");

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('menus', [
            'id' => $menu->id,
        ]);
    }
}
