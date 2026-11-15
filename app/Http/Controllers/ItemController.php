<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * Menampilkan katalog seluruh barang yang tersedia.
     */
    public function index(Request $request): View
    {
        $categories = Category::all();

        // Query barang dengan eager loading untuk mencegah masalah N+1 Query
        $query = Item::with(['user', 'category'])
            ->where('status', 'available')
            ->latest();

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->query('category'));
        }

        // Filter berdasarkan jenis penawaran (gratis atau bertukar)
        if ($request->filled('type') && in_array($request->query('type'), ['free', 'exchange'])) {
            $query->where('type', $request->query('type'));
        }

        // Pencarian berdasarkan judul atau deskripsi
        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(12)->withQueryString();

        return view('items.index', [
            'items' => $items,
            'categories' => $categories,
            'selectedCategory' => $request->query('category'),
            'selectedType' => $request->query('type'),
            'search' => $request->query('q'),
        ]);
    }

    /**
     * Menampilkan detail informasi barang tertentu.
     */
    public function show(Item $item): View
    {
        $item->load(['user', 'category']);

        return view('items.show', [
            'item' => $item,
        ]);
    }

    /**
     * Menampilkan daftar barang milik pengguna yang sedang login.
     */
    public function myItems(Request $request): View
    {
        $items = $request->user()
            ->items()
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('items.my-items', [
            'items' => $items,
        ]);
    }

    /**
     * Menampilkan formulir tambah barang baru.
     */
    public function create(): View
    {
        $categories = Category::all();

        return view('items.create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Menyimpan barang baru ke database dengan validasi ketat & upload foto aman.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi input formulir
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:3000'],
            'condition' => ['required', 'string', 'in:Baru,Seperti Baru,Layak Pakai,Butuh Perbaikan'],
            'type' => ['required', 'string', 'in:free,exchange'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Maks 2MB
        ], [
            'title.required' => 'Judul barang wajib diisi.',
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'description.required' => 'Deskripsi barang wajib diisi.',
            'condition.required' => 'Kondisi barang wajib dipilih.',
            'condition.in' => 'Pilihan kondisi barang tidak valid.',
            'type.required' => 'Jenis penawaran wajib dipilih.',
            'type.in' => 'Jenis penawaran harus berupa gratis atau bertukar.',
            'image.image' => 'File yang diunggah harus berupa file gambar valid.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'image.max' => 'Ukuran foto maksimal adalah 2MB.',
        ]);

        // 2. Simpan file gambar dengan aman ke storage/app/public/items
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        // 3. Simpan data menggunakan relasi user yang sedang login (mencegah manipulasi user_id)
        $request->user()->items()->create([
            'category_id' => $validated['category_id'],
            'title' => trim($validated['title']),
            'description' => trim($validated['description']),
            'condition' => $validated['condition'],
            'type' => $validated['type'],
            'image_path' => $imagePath,
            'status' => 'available',
        ]);

        return redirect()->route('items.my')->with('success', 'Barang berhasil ditambahkan ke katalog TukarLayak!');
    }

    /**
     * Menampilkan formulir edit barang milik sendiri.
     */
    public function edit(Item $item): View
    {
        // Mencegah kerentanan IDOR: Hanya pemilik yang diizinkan mengedit
        Gate::authorize('update', $item);

        $categories = Category::all();

        return view('items.edit', [
            'item' => $item,
            'categories' => $categories,
        ]);
    }

    /**
     * Memperbarui data barang milik sendiri.
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        // Mencegah kerentanan IDOR: Pastikan pemohon adalah pemilik barang
        Gate::authorize('update', $item);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:3000'],
            'condition' => ['required', 'string', 'in:Baru,Seperti Baru,Layak Pakai,Butuh Perbaikan'],
            'type' => ['required', 'string', 'in:free,exchange'],
            'status' => ['required', 'string', 'in:available,completed'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'title.required' => 'Judul barang wajib diisi.',
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'description.required' => 'Deskripsi barang wajib diisi.',
            'condition.required' => 'Kondisi barang wajib dipilih.',
            'condition.in' => 'Pilihan kondisi barang tidak valid.',
            'type.required' => 'Jenis penawaran wajib dipilih.',
            'status.required' => 'Status barang wajib dipilih.',
            'image.image' => 'File yang diunggah harus berupa file gambar valid.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'image.max' => 'Ukuran foto maksimal adalah 2MB.',
        ]);

        $imagePath = $item->image_path;

        // Jika pengguna mengunggah foto baru, hapus foto lama dan simpan yang baru
        if ($request->hasFile('image')) {
            if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
                Storage::disk('public')->delete($item->image_path);
            }
            $imagePath = $request->file('image')->store('items', 'public');
        }

        $item->update([
            'category_id' => $validated['category_id'],
            'title' => trim($validated['title']),
            'description' => trim($validated['description']),
            'condition' => $validated['condition'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('items.my')->with('success', 'Data barang berhasil diperbarui!');
    }

    /**
     * Menghapus barang milik sendiri beserta file fotonya.
     */
    public function destroy(Item $item): RedirectResponse
    {
        // Mencegah kerentanan IDOR: Pastikan pemohon adalah pemilik barang
        Gate::authorize('delete', $item);

        // Hapus file foto dari disk penyimpanan jika ada
        if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        return redirect()->route('items.my')->with('success', 'Barang berhasil dihapus.');
    }
}
