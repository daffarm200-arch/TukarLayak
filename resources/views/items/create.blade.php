@extends('layouts.app')

@section('title', 'Pasang Barang Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Tombol Kembali -->
    <div>
        <a href="{{ route('items.my') }}" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-brand-600 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Barang Saya
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">

        <div class="border-b border-slate-100 pb-5 mb-6">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pasang Barang Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Lengkapi data barang layak pakai yang ingin Anda tukar atau bagikan</p>
        </div>

        <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Judul Barang -->
            <div>
                <label for="title" class="block text-sm font-semibold text-slate-800 mb-1.5">Judul Barang</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    autofocus
                    placeholder="Contoh: Kipas Angin Meja Miyako 9 Inch"
                    class="w-full px-4 py-2.5 rounded-xl border @error('title') border-red-400 bg-red-50/50 @else border-slate-300 @enderror focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                >
                @error('title')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori & Kondisi (2 Kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <!-- Kategori -->
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-slate-800 mb-1.5">Kategori</label>
                    <select
                        id="category_id"
                        name="category_id"
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl border @error('category_id') border-red-400 bg-red-50/50 @else border-slate-300 @enderror focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm bg-white text-slate-700 transition"
                    >
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kondisi -->
                <div>
                    <label for="condition" class="block text-sm font-semibold text-slate-800 mb-1.5">Kondisi Barang</label>
                    <select
                        id="condition"
                        name="condition"
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl border @error('condition') border-red-400 bg-red-50/50 @else border-slate-300 @enderror focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm bg-white text-slate-700 transition"
                    >
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="Baru" {{ old('condition') == 'Baru' ? 'selected' : '' }}>Baru (Belum pernah dipakai)</option>
                        <option value="Seperti Baru" {{ old('condition') == 'Seperti Baru' ? 'selected' : '' }}>Seperti Baru (Mulus 95%+)</option>
                        <option value="Layak Pakai" {{ old('condition') == 'Layak Pakai' ? 'selected' : '' }}>Layak Pakai (Fungsi normal, ada bekas pemakaian)</option>
                        <option value="Butuh Perbaikan" {{ old('condition') == 'Butuh Perbaikan' ? 'selected' : '' }}>Butuh Perbaikan (Perlu servis ringan)</option>
                    </select>
                    @error('condition')
                        <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Jenis Penawaran (Bertukar atau Gratis) -->
            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-2">Jenis Penawaran</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    <label class="relative flex items-center p-3.5 rounded-2xl border cursor-pointer hover:bg-slate-50 transition border-slate-200">
                        <input
                            type="radio"
                            name="type"
                            value="exchange"
                            {{ old('type', 'exchange') === 'exchange' ? 'checked' : '' }}
                            class="w-4 h-4 text-brand-600 border-slate-300 focus:ring-brand-500"
                        >
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-slate-900">🔄 Siap Bertukar</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Ingin menukar dengan barang lain</span>
                        </div>
                    </label>

                    <label class="relative flex items-center p-3.5 rounded-2xl border cursor-pointer hover:bg-slate-50 transition border-slate-200">
                        <input
                            type="radio"
                            name="type"
                            value="free"
                            {{ old('type') === 'free' ? 'checked' : '' }}
                            class="w-4 h-4 text-brand-600 border-slate-300 focus:ring-brand-500"
                        >
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-slate-900">🎁 Berbagi Gratis</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Diberikan cuma-cuma kepada yang butuh</span>
                        </div>
                    </label>

                </div>
                @error('type')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Foto Barang -->
            <div>
                <label for="image" class="block text-sm font-semibold text-slate-800 mb-1">
                    Foto Barang <span class="text-xs font-normal text-slate-400">(Opsional, Maks. 2MB)</span>
                </label>
                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/png,image/jpg,image/webp"
                    class="w-full px-3.5 py-2 rounded-xl border @error('image') border-red-400 bg-red-50/50 @else border-slate-300 @enderror text-sm text-slate-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition"
                >
                <p class="text-[11px] text-slate-400 mt-1">Format yang didukung: JPG, PNG, atau WEBP.</p>
                @error('image')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi Barang -->
            <div>
                <label for="description" class="block text-sm font-semibold text-slate-800 mb-1.5">Deskripsi Lengkap</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    required
                    placeholder="Jelaskan spesifikasi barang, alasan dilepas, kelengkapan barang, serta preferensi barang barter yang diinginkan..."
                    class="w-full px-4 py-3 rounded-xl border @error('description') border-red-400 bg-red-50/50 @else border-slate-300 @enderror focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a
                    href="{{ route('items.my') }}"
                    class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-sm transition flex items-center"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan & Pasang Barang
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
