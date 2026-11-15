<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminItemController extends Controller
{
    /**
     * Menampilkan seluruh barang yang dipasang oleh pengguna beserta fitur pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $categories = Category::all();

        $query = Item::with(['user', 'category'])->latest();

        // Pencarian berdasarkan judul barang atau nama pemilik
        if ($request->filled('q')) {
            $search = trim($request->query('q'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->query('category'));
        }

        // Filter berdasarkan status (available, reserved, completed)
        if ($request->filled('status') && in_array($request->query('status'), ['available', 'reserved', 'completed'])) {
            $query->where('status', $request->query('status'));
        }

        // Filter berdasarkan jenis penawaran (free, exchange)
        if ($request->filled('type') && in_array($request->query('type'), ['free', 'exchange'])) {
            $query->where('type', $request->query('type'));
        }

        $items = $query->paginate(15)->withQueryString();

        return view('admin.items.index', [
            'items' => $items,
            'categories' => $categories,
            'search' => $request->query('q'),
            'selectedCategory' => $request->query('category'),
            'selectedStatus' => $request->query('status'),
            'selectedType' => $request->query('type'),
        ]);
    }
}
