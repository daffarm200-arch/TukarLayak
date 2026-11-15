@extends('layouts.app')

@section('title', 'Masuk ke Akun')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">

        <!-- Header Form -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-brand-100 text-brand-700 rounded-2xl mx-auto flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Selamat Datang Kembali</h1>
            <p class="text-sm text-slate-500 mt-1">Masuk untuk melanjutkan ke akun TukarLayak Anda</p>
        </div>

        <!-- Form Login -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Alamat Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="nama@email.com"
                    class="w-full px-3.5 py-2.5 rounded-xl border @error('email') border-red-400 bg-red-50/50 @else border-slate-300 @enderror focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                >
                @error('email')
                    <p class="text-xs text-red-600 mt-1.5 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                </div>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    placeholder="Masukkan password Anda"
                    class="w-full px-3.5 py-2.5 rounded-xl border @error('password') border-red-400 bg-red-50/50 @else border-slate-300 @enderror focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                >
                @error('password')
                    <p class="text-xs text-red-600 mt-1.5 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Ingat Saya -->
            <div class="flex items-center">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    class="w-4 h-4 text-brand-600 border-slate-300 rounded focus:ring-brand-500"
                >
                <label for="remember" class="ml-2 text-sm text-slate-600 select-none">
                    Ingat saya di perangkat ini
                </label>
            </div>

            <!-- Tombol Submit -->
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full py-3 px-4 bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-semibold rounded-xl shadow-sm transition duration-150 flex items-center justify-center space-x-2"
                >
                    <span>Masuk ke Akun</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </form>

        <!-- Link ke Halaman Registrasi -->
        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-600">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:text-brand-700 transition">
                    Daftar sekarang
                </a>
            </p>
        </div>

    </div>
</div>
@endsection
