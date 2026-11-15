<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Menampilkan daftar seluruh pengguna dengan fitur pencarian.
     */
    public function index(Request $request): View
    {
        $query = User::withCount(['items', 'sentRequests'])->latest();

        // Fitur pencarian berdasarkan nama atau alamat email
        if ($request->filled('q')) {
            $search = trim($request->query('q'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->filled('role') && in_array($request->query('role'), ['admin', 'user'])) {
            $query->where('role', $request->query('role'));
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $request->query('q'),
            'selectedRole' => $request->query('role'),
        ]);
    }

    /**
     * Menampilkan detail informasi profil pengguna beserta riwayat barang dan permintaannya.
     * Informasi sensitif (seperti password hash & remember token) dilindungi dan tidak ditampilkan.
     */
    public function show(User $user): View
    {
        $userItems = $user->items()->with('category')->latest()->paginate(10, ['*'], 'items_page');
        $userRequests = $user->sentRequests()->with(['item', 'offeredItem'])->latest()->paginate(10, ['*'], 'requests_page');

        $totalItems = $user->items()->count();
        $totalSentRequests = $user->sentRequests()->count();
        $userItemIds = $user->items()->pluck('id');
        $totalReceivedRequests = \App\Models\ItemRequest::whereIn('item_id', $userItemIds)->count();

        return view('admin.users.show', [
            'user' => $user,
            'userItems' => $userItems,
            'userRequests' => $userRequests,
            'totalItems' => $totalItems,
            'totalSentRequests' => $totalSentRequests,
            'totalReceivedRequests' => $totalReceivedRequests,
        ]);
    }
}
