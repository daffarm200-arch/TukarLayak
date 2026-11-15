<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Mengeluarkan pengguna dan membersihkan sesi.
     */
    public function logout(Request $request): RedirectResponse
    {
        // 1. Keluarkan pengguna dari autentikasi Laravel
        Auth::logout();

        // 2. Batalkan (invalidate) data sesi yang lama
        $request->session()->invalidate();

        // 3. Regenerasi CSRF Token untuk mencegah serangan CSRF
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari akun.');
    }
}
