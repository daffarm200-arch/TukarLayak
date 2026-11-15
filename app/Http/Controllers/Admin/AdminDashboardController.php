<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama administrator dengan data statistik riil dari database.
     */
    public function index(Request $request): View
    {
        $admin = $request->user();

        // 1. Statistik Pengguna
        $totalUsers = User::count();
        $totalReguler = User::where('role', 'user')->count();
        $totalAdmin = User::where('role', 'admin')->count();

        // 2. Statistik Barang
        $totalItems = Item::count();
        $itemsAvailable = Item::where('status', 'available')->count();
        $itemsReserved = Item::where('status', 'reserved')->count();
        $itemsCompleted = Item::where('status', 'completed')->count();

        // 3. Statistik Permintaan & Pertukaran
        $totalRequests = ItemRequest::count();
        $requestsPending = ItemRequest::where('status', 'pending')->count();
        $requestsAccepted = ItemRequest::where('status', 'accepted')->count();
        $requestsRejected = ItemRequest::where('status', 'rejected')->count();
        $exchangesCompleted = ItemRequest::where('status', 'completed')->count();

        // 4. Statistik Kategori
        $totalCategories = Category::count();

        // 5. Daftar Aktivitas Terbaru
        $recentUsers = User::latest()->take(5)->get();
        $recentItems = Item::with(['user', 'category'])->latest()->take(5)->get();
        $recentRequests = ItemRequest::with(['item', 'requester', 'offeredItem'])->latest()->take(6)->get();

        return view('admin.dashboard', [
            'admin' => $admin,
            'totalUsers' => $totalUsers,
            'totalReguler' => $totalReguler,
            'totalAdmin' => $totalAdmin,
            'totalItems' => $totalItems,
            'itemsAvailable' => $itemsAvailable,
            'itemsReserved' => $itemsReserved,
            'itemsCompleted' => $itemsCompleted,
            'totalRequests' => $totalRequests,
            'requestsPending' => $requestsPending,
            'requestsAccepted' => $requestsAccepted,
            'requestsRejected' => $requestsRejected,
            'exchangesCompleted' => $exchangesCompleted,
            'totalCategories' => $totalCategories,
            'recentUsers' => $recentUsers,
            'recentItems' => $recentItems,
            'recentRequests' => $recentRequests,
        ]);
    }
}
