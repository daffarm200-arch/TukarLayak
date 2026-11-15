<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori beserta jumlah barang terkait.
     */
    public function index(): View
    {
        $categories = Category::withCount('items')->latest()->paginate(15);

        return view('admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Menampilkan form penambahan kategori baru.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori ini sudah terdaftar sebelumnya.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
        ]);

        $slug = Str::slug($validated['name']);

        // Pastikan slug unik jika ada kemiripan penamaan
        $originalSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        Category::create([
            'name' => trim($validated['name']),
            'slug' => $slug,
            'description' => trim($validated['description'] ?? ''),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Menampilkan formulir edit kategori.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Memperbarui data kategori.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori ini sudah digunakan oleh kategori lain.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
        ]);

        $slug = Str::slug($validated['name']);

        $category->update([
            'name' => trim($validated['name']),
            'slug' => $slug,
            'description' => trim($validated['description'] ?? ''),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    /**
     * Menghapus kategori HANYA jika tidak ada barang yang terkait.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $itemsCount = $category->items()->count();

        // Aturan: Jangan mengizinkan penghapusan kategori jika masih memiliki barang terkait
        if ($itemsCount > 0) {
            return back()->with('error', "Gagal menghapus! Kategori '{$category->name}' tidak dapat dihapus karena masih digunakan oleh {$itemsCount} barang.");
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$categoryName}' berhasil dihapus.");
    }
}
