<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses percobaan login pengguna dengan rate limiting.
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. Kunci unik rate limiter berdasarkan email dan IP pengguna
        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        // 2. Batasi maksimal 5 percobaan login gagal dalam 1 menit
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        // 3. Validasi input email dan password
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        // 4. Verifikasi kredensial ke database
        if (Auth::attempt($credentials, $remember)) {
            // Bersihkan riwayat percobaan login saat berhasil
            RateLimiter::clear($throttleKey);

            // Regenerasi Session ID untuk mencegah Session Fixation Attack
            $request->session()->regenerate();

            // Arahkan ke dashboard sesuai dengan peran (role) pengguna
            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang di Dashboard Administrator.');
            }

            return redirect()->intended(route('dashboard'))->with('success', 'Login berhasil! Selamat datang kembali.');
        }

        // 5. Jika gagal, tambahkan hit ke rate limiter
        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }
}
