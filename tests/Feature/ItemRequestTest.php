<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $requester;
    private User $stranger;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'name' => 'Budi Pemilik',
            'email' => 'budi_owner@tukarlayak.id',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);

        $this->requester = User::create([
            'name' => 'Siti Pemohon',
            'email' => 'siti_requester@tukarlayak.id',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);

        $this->stranger = User::create([
            'name' => 'Andi Orang Lain',
            'email' => 'andi_stranger@tukarlayak.id',
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
     * Uji 1: Pengguna bisa meminta barang gratis tanpa menawarkan barang lain.
     */
    public function test_user_can_request_free_item(): void
    {
        $item = Item::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Buku Ensiklopedia Gratis',
            'description' => 'Dibagikan gratis untuk yang membutuhkan.',
            'condition' => 'Layak Pakai',
            'type' => 'free',
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->requester)->post("/items/{$item->id}/request", [
            'message' => 'Halo, saya ingin meminta buku ini untuk adik saya.',
        ]);

        $response->assertRedirect('/requests/my');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('item_requests', [
            'item_id' => $item->id,
            'requester_id' => $this->requester->id,
            'offered_item_id' => null,
            'status' => 'pending',
        ]);
    }

    /**
     * Uji 2: Pengguna bisa mengajukan pertukaran dengan memilih barang miliknya yang tersedia.
     */
    public function test_user_can_request_exchange_with_own_available_item(): void
    {
        $targetItem = Item::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Kamera Analog Canon',
            'description' => 'Kamera jadul normal.',
            'condition' => 'Layak Pakai',
            'type' => 'exchange',
            'status' => 'available',
        ]);

        $myItem = Item::create([
            'user_id' => $this->requester->id,
            'category_id' => $this->category->id,
            'title' => 'Lensa 50mm f1.8',
            'description' => 'Lensa mulus no jamur.',
            'condition' => 'Seperti Baru',
            'type' => 'exchange',
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->requester)->post("/items/{$targetItem->id}/request", [
            'offered_item_id' => $myItem->id,
            'message' => 'Bisa barter dengan lensa saya?',
        ]);

        $response->assertRedirect('/requests/my');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('item_requests', [
            'item_id' => $targetItem->id,
            'requester_id' => $this->requester->id,
            'offered_item_id' => $myItem->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Uji 3: Pengguna tidak bisa meminta barang miliknya sendiri (403 Forbidden).
     */
    public function test_user_cannot_request_own_item(): void
    {
        $ownItem = Item::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Barang Punya Sendiri',
            'description' => 'Deskripsi.',
            'condition' => 'Baru',
            'type' => 'free',
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->owner)->post("/items/{$ownItem->id}/request", [
            'message' => 'Coba minta barang sendiri',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('item_requests', [
            'item_id' => $ownItem->id,
        ]);
    }

    /**
     * Uji 4: Pengguna tidak bisa menawarkan barang milik orang lain saat barter.
     */
    public function test_user_cannot_offer_item_belonging_to_someone_else(): void
    {
        $targetItem = Item::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Monitor Gaming 24 Inch',
            'description' => 'Monitor normal.',
            'condition' => 'Layak Pakai',
            'type' => 'exchange',
            'status' => 'available',
        ]);

        // Barang milik Andi (orang lain), bukan milik Siti (requester)
        $strangerItem = Item::create([
            'user_id' => $this->stranger->id,
            'category_id' => $this->category->id,
            'title' => 'Laptop Asus Milik Andi',
            'description' => 'Laptop.',
            'condition' => 'Layak Pakai',
            'type' => 'exchange',
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->requester)->post("/items/{$targetItem->id}/request", [
            'offered_item_id' => $strangerItem->id,
            'message' => 'Saya tawarkan laptop orang lain.',
        ]);

        $response->assertSessionHasErrors(['offered_item_id']);
        $this->assertDatabaseMissing('item_requests', [
            'item_id' => $targetItem->id,
            'requester_id' => $this->requester->id,
        ]);
    }

    /**
     * Uji 5: Pemilik bisa menerima permintaan (status menjadi accepted, barang reserved, permintaan lain ditolak).
     */
    public function test_owner_can_accept_request_and_reserve_items(): void
    {
        $item = Item::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Meja Belajar Kayu',
            'description' => 'Meja kokoh.',
            'condition' => 'Layak Pakai',
            'type' => 'exchange',
            'status' => 'available',
        ]);

        $offeredItem = Item::create([
            'user_id' => $this->requester->id,
            'category_id' => $this->category->id,
            'title' => 'Kursi Kerja Putar',
            'description' => 'Kursi empuk.',
            'condition' => 'Seperti Baru',
            'type' => 'exchange',
            'status' => 'available',
        ]);

        // Permintaan 1 dari Siti
        $req1 = ItemRequest::create([
            'item_id' => $item->id,
            'requester_id' => $this->requester->id,
            'offered_item_id' => $offeredItem->id,
            'status' => 'pending',
        ]);

        // Permintaan 2 dari Andi untuk barang yang sama
        $req2 = ItemRequest::create([
            'item_id' => $item->id,
            'requester_id' => $this->stranger->id,
            'offered_item_id' => null,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->owner)->post("/requests/{$req1->id}/accept");

        $response->assertSessionHas('success');

        // Permintaan 1 disetujui
        $this->assertEquals('accepted', $req1->fresh()->status);

        // Kedua barang berubah menjadi 'reserved'
        $this->assertEquals('reserved', $item->fresh()->status);
        $this->assertEquals('reserved', $offeredItem->fresh()->status);

        // Permintaan 2 otomatis ditolak
        $this->assertEquals('rejected', $req2->fresh()->status);
    }

    /**
     * Uji 6: Pemilik bisa menolak permintaan (barang tetap available).
     */
    public function test_owner_can_reject_request(): void
    {
        $item = Item::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Gitar Akustik Yamaha',
            'description' => 'Gitar mulus.',
            'condition' => 'Seperti Baru',
            'type' => 'free',
            'status' => 'available',
        ]);

        $req = ItemRequest::create([
            'item_id' => $item->id,
            'requester_id' => $this->requester->id,
            'offered_item_id' => null,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->owner)->post("/requests/{$req->id}/reject");

        $response->assertSessionHas('success');
        $this->assertEquals('rejected', $req->fresh()->status);
        $this->assertEquals('available', $item->fresh()->status);
    }

    /**
     * Uji 7: User lain tidak bisa menerima permintaan orang lain (403 Forbidden).
     */
    public function test_other_user_cannot_accept_request(): void
    {
        $item = Item::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Headphone Bluetooth',
            'description' => 'Headphone bas mantap.',
            'condition' => 'Layak Pakai',
            'type' => 'free',
            'status' => 'available',
        ]);

        $req = ItemRequest::create([
            'item_id' => $item->id,
            'requester_id' => $this->requester->id,
            'offered_item_id' => null,
            'status' => 'pending',
        ]);

        // Andi (stranger) mencoba menerima permintaan yang ditujukan ke Budi (owner)
        $response = $this->actingAs($this->stranger)->post("/requests/{$req->id}/accept");

        $response->assertStatus(403);
        $this->assertEquals('pending', $req->fresh()->status);
        $this->assertEquals('available', $item->fresh()->status);
    }

    /**
     * Uji 8: User lain tidak bisa membatalkan permintaan milik pengaju lain (403 Forbidden).
     */
    public function test_other_user_cannot_cancel_request(): void
    {
        $item = Item::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Rak Sepatu 4 Susun',
            'description' => 'Rak sepatu plastik.',
            'condition' => 'Layak Pakai',
            'type' => 'free',
            'status' => 'available',
        ]);

        $req = ItemRequest::create([
            'item_id' => $item->id,
            'requester_id' => $this->requester->id,
            'offered_item_id' => null,
            'status' => 'pending',
        ]);

        // Pemilik atau orang asing mencoba membatalkan permintaan milik Siti
        $response = $this->actingAs($this->stranger)->post("/requests/{$req->id}/cancel");

        $response->assertStatus(403);
        $this->assertEquals('pending', $req->fresh()->status);

        // Namun pengaju sendiri (Siti) BERHASIL membatalkan
        $responseRequester = $this->actingAs($this->requester)->post("/requests/{$req->id}/cancel");
        $responseRequester->assertSessionHas('success');
        $this->assertEquals('cancelled', $req->fresh()->status);
    }

    /**
     * Uji 9: Permintaan duplikat yang masih berstatus pending ditolak.
     */
    public function test_duplicate_pending_request_is_rejected(): void
    {
        $item = Item::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Dispenser Air Minum',
            'description' => 'Fungsi normal.',
            'condition' => 'Layak Pakai',
            'type' => 'free',
            'status' => 'available',
        ]);

        // Permintaan pertama
        ItemRequest::create([
            'item_id' => $item->id,
            'requester_id' => $this->requester->id,
            'offered_item_id' => null,
            'status' => 'pending',
        ]);

        // Permintaan kedua dari user yang sama
        $response = $this->actingAs($this->requester)->post("/items/{$item->id}/request", [
            'message' => 'Minta lagi untuk kedua kali',
        ]);

        $response->assertRedirect('/requests/my');
        $response->assertSessionHas('error');

        // Pastikan hanya ada 1 request di database
        $this->assertEquals(1, ItemRequest::where('item_id', $item->id)->where('requester_id', $this->requester->id)->count());
    }

    /**
     * Uji 10: Barang yang sudah reserved tidak bisa diminta lagi.
     */
    public function test_cannot_request_item_that_is_already_reserved(): void
    {
        $item = Item::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Jam Dinding Seiko',
            'description' => 'Jam dinding.',
            'condition' => 'Layak Pakai',
            'type' => 'free',
            'status' => 'reserved', // Sudah direservasi
        ]);

        $response = $this->actingAs($this->requester)->post("/items/{$item->id}/request", [
            'message' => 'Mau dong jam dindingnya',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('item_requests', [
            'item_id' => $item->id,
            'requester_id' => $this->requester->id,
        ]);
    }

    /**
     * Uji 11: Permintaan selesai memperbarui status kedua barang menjadi completed.
     */
    public function test_completing_request_updates_items_status_to_completed(): void
    {
        $item = Item::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Smartwatch Xiaomi',
            'description' => 'Baterai awet.',
            'condition' => 'Seperti Baru',
            'type' => 'exchange',
            'status' => 'reserved',
        ]);

        $offeredItem = Item::create([
            'user_id' => $this->requester->id,
            'category_id' => $this->category->id,
            'title' => 'Earphone TWS Baseus',
            'description' => 'Masih lengkap.',
            'condition' => 'Seperti Baru',
            'type' => 'exchange',
            'status' => 'reserved',
        ]);

        $req = ItemRequest::create([
            'item_id' => $item->id,
            'requester_id' => $this->requester->id,
            'offered_item_id' => $offeredItem->id,
            'status' => 'accepted', // Sudah accepted
        ]);

        $response = $this->actingAs($this->owner)->post("/requests/{$req->id}/complete");

        $response->assertSessionHas('success');

        // Status request dan kedua barang harus menjadi 'completed'
        $this->assertEquals('completed', $req->fresh()->status);
        $this->assertEquals('completed', $item->fresh()->status);
        $this->assertEquals('completed', $offeredItem->fresh()->status);
    }
}
