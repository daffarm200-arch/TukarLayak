<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat user dan kategori awal untuk pengujian
        $this->user = User::create([
            'name' => 'Budi Pengguna',
            'email' => 'budi@tukarlayak.id',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);

        $this->category = Category::create([
            'name' => 'Elektronik & Gadget',
            'slug' => 'elektronik-gadget',
            'description' => 'Kategori perangkat elektronik',
        ]);
    }

    /**
     * Uji 1: User yang login berhasil menambahkan barang baru ke katalog.
     */
    public function test_user_can_create_item_successfully(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('kipas.jpg', 600, 600);

        $response = $this->actingAs($this->user)->post('/items', [
            'title' => 'Kipas Angin Meja Miyako',
            'category_id' => $this->category->id,
            'description' => 'Kipas angin meja masih berfungsi normal dan angin kencang.',
            'condition' => 'Layak Pakai',
            'type' => 'exchange',
            'image' => $image,
        ]);

        $response->assertRedirect('/my-items');
        $response->assertSessionHas('success');

        // Pastikan barang tersimpan dengan pemilik yang sesuai (user_id tidak dimanipulasi)
        $this->assertDatabaseHas('items', [
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Kipas Angin Meja Miyako',
            'condition' => 'Layak Pakai',
            'type' => 'exchange',
            'status' => 'available',
        ]);

        $item = Item::where('title', 'Kipas Angin Meja Miyako')->first();
        $this->assertNotNull($item->image_path);
        Storage::disk('public')->assertExists($item->image_path);
    }

    /**
     * Uji 2: User berhasil mengedit barang miliknya sendiri.
     */
    public function test_user_can_edit_own_item(): void
    {
        $item = Item::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Buku Pemrograman PHP Lawas',
            'description' => 'Buku panduan dasar PHP.',
            'condition' => 'Layak Pakai',
            'type' => 'free',
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->user)->put("/items/{$item->id}", [
            'title' => 'Buku Pemrograman PHP Modern',
            'category_id' => $this->category->id,
            'description' => 'Buku sudah diperbarui deskripsinya.',
            'condition' => 'Seperti Baru',
            'type' => 'exchange',
            'status' => 'available',
        ]);

        $response->assertRedirect('/my-items');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'title' => 'Buku Pemrograman PHP Modern',
            'condition' => 'Seperti Baru',
            'type' => 'exchange',
        ]);
    }

    /**
     * Uji 3: User berhasil menghapus barang miliknya sendiri.
     */
    public function test_user_can_delete_own_item(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('hapus.jpg');
        $imagePath = $image->store('items', 'public');

        $item = Item::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Barang Mau Dihapus',
            'description' => 'Deskripsi barang.',
            'condition' => 'Layak Pakai',
            'type' => 'free',
            'image_path' => $imagePath,
            'status' => 'available',
        ]);

        Storage::disk('public')->assertExists($imagePath);

        $response = $this->actingAs($this->user)->delete("/items/{$item->id}");

        $response->assertRedirect('/my-items');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('items', [
            'id' => $item->id,
        ]);

        // File foto juga harus terhapus dari storage
        Storage::disk('public')->assertMissing($imagePath);
    }

    /**
     * Uji 4: User DILARANG mengedit barang milik orang lain (Mencegah IDOR).
     */
    public function test_user_cannot_edit_other_users_item(): void
    {
        $otherUser = User::create([
            'name' => 'Pengguna Lain',
            'email' => 'lain@tukarlayak.id',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);

        $item = Item::create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id,
            'title' => 'Barang Pengguna Lain',
            'description' => 'Tidak boleh diubah orang lain.',
            'condition' => 'Baru',
            'type' => 'exchange',
            'status' => 'available',
        ]);

        // Percobaan membuka halaman edit milik orang lain
        $responseForm = $this->actingAs($this->user)->get("/items/{$item->id}/edit");
        $responseForm->assertStatus(403);

        // Percobaan mengirim request update ke data milik orang lain
        $responseUpdate = $this->actingAs($this->user)->put("/items/{$item->id}", [
            'title' => 'Judul Dibajak',
            'category_id' => $this->category->id,
            'description' => 'Deskripsi diubah.',
            'condition' => 'Layak Pakai',
            'type' => 'free',
            'status' => 'available',
        ]);

        $responseUpdate->assertStatus(403);

        // Pastikan data di database tidak berubah
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'title' => 'Barang Pengguna Lain',
        ]);
    }

    /**
     * Uji 5: User DILARANG menghapus barang milik orang lain (Mencegah IDOR).
     */
    public function test_user_cannot_delete_other_users_item(): void
    {
        $otherUser = User::create([
            'name' => 'Pengguna Lain',
            'email' => 'lain@tukarlayak.id',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);

        $item = Item::create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id,
            'title' => 'Barang Aman Pengguna Lain',
            'description' => 'Tidak boleh dihapus sembarangan.',
            'condition' => 'Baru',
            'type' => 'exchange',
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->user)->delete("/items/{$item->id}");

        $response->assertStatus(403);

        // Pastikan barang tetap ada di database
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
        ]);
    }

    /**
     * Uji 6: Pengunjung tanpa login (guest) DILARANG mengakses rute kelola barang.
     */
    public function test_guest_cannot_access_item_management_routes(): void
    {
        $item = Item::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Barang Test Guest',
            'description' => 'Deskripsi.',
            'condition' => 'Layak Pakai',
            'type' => 'free',
            'status' => 'available',
        ]);

        // Coba akses halaman barang saya
        $this->get('/my-items')->assertRedirect('/login');

        // Coba akses form tambah barang
        $this->get('/items/create')->assertRedirect('/login');

        // Coba kirim data simpan barang
        $this->post('/items', [])->assertRedirect('/login');

        // Coba akses halaman edit
        $this->get("/items/{$item->id}/edit")->assertRedirect('/login');

        // Coba update
        $this->put("/items/{$item->id}", [])->assertRedirect('/login');

        // Coba delete
        $this->delete("/items/{$item->id}")->assertRedirect('/login');
    }

    /**
     * Uji 7: Upload file yang tidak valid (bukan gambar) harus ditolak.
     */
    public function test_invalid_file_upload_is_rejected(): void
    {
        $fakePdf = UploadedFile::fake()->create('dokumen_rahasia.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user)->post('/items', [
            'title' => 'Barang dengan File Palsu',
            'category_id' => $this->category->id,
            'description' => 'Mencoba upload PDF.',
            'condition' => 'Layak Pakai',
            'type' => 'free',
            'image' => $fakePdf,
        ]);

        $response->assertSessionHasErrors(['image']);
        $this->assertDatabaseMissing('items', [
            'title' => 'Barang dengan File Palsu',
        ]);
    }

    /**
     * Uji 8: Input form yang tidak valid harus ditolak oleh sistem validasi.
     */
    public function test_invalid_input_is_rejected(): void
    {
        // Request tanpa judul, category_id tidak ada di DB, kondisi invalid, type invalid
        $response = $this->actingAs($this->user)->post('/items', [
            'title' => '',
            'category_id' => 99999, // ID tidak ada
            'description' => '',
            'condition' => 'KondisiNgawur',
            'type' => 'bukan_tipe_valid',
        ]);

        $response->assertSessionHasErrors(['title', 'category_id', 'description', 'condition', 'type']);
    }

    /**
     * Uji 9: Publik (tamu/user) dapat melihat katalog barang dan detail barang.
     */
    public function test_public_can_view_items_catalog_and_detail(): void
    {
        $item = Item::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Sepeda Lipat Evergreen 20 Inch',
            'description' => 'Sepeda lipat warna hitam masih sangat mulus.',
            'condition' => 'Seperti Baru',
            'type' => 'exchange',
            'status' => 'available',
        ]);

        // Cek katalog umum
        $responseCatalog = $this->get('/items');
        $responseCatalog->assertStatus(200);
        $responseCatalog->assertSee('Sepeda Lipat Evergreen 20 Inch');

        // Cek halaman detail
        $responseDetail = $this->get("/items/{$item->id}");
        $responseDetail->assertStatus(200);
        $responseDetail->assertSee('Sepeda Lipat Evergreen 20 Inch');
        $responseDetail->assertSee('Budi Pengguna');
    }
}
