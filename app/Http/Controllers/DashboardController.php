<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama untuk pengguna biasa (role: user).
     */
    public function userDashboard(Request $request): View
    {
        $user = $request->user();

        // Hitung statistik barang dan permintaan pengguna
        $userItemIds = $user->items()->pluck('id');
        $incomingPendingCount = ItemRequest::whereIn('item_id', $userItemIds)->where('status', 'pending')->count();
        $myPendingCount = $user->sentRequests()->where('status', 'pending')->count();
        $completedExchangesCount = ItemRequest::where(function ($q) use ($user, $userItemIds) {
            $q->whereIn('item_id', $userItemIds)
              ->orWhere('requester_id', $user->id);
        })->where('status', 'completed')->count();

        return view('dashboard', [
            'user' => $user,
            'incomingPendingCount' => $incomingPendingCount,
            'myPendingCount' => $myPendingCount,
            'completedExchangesCount' => $completedExchangesCount,
        ]);
    }

    /**
     * Menampilkan dashboard khusus untuk administrator (role: admin).
     */
    public function adminDashboard(Request $request): View
    {
        $admin = $request->user();

        $totalUsers = User::count();
        $totalReguler = User::where('role', 'user')->count();
        $totalAdmin = User::where('role', 'admin')->count();
        $recentUsers = User::latest()->take(10)->get();

        return view('admin.dashboard', [
            'admin' => $admin,
            'totalUsers' => $totalUsers,
            'totalReguler' => $totalReguler,
            'totalAdmin' => $totalAdmin,
            'recentUsers' => $recentUsers,
        ]);
    }
}
