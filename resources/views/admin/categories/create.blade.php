@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="max-w-xl mx-auto">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-4">
        <a href="{{ route('admin.categories.index') }}" class="hover:text-green-600">Kategori</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Tambah Baru</span>
    </nav>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h1 class="text-xl font-bold text-gray-800 mb-6">Tambah Kategori Baru</h1>

        <form method="POST" action="{{ route('admin.categories.store') }}" novalidate>
            @csrf

            {{-- Nama Kategori --}}
            <div class="mb-5">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    maxlength="100"
                    placeholder="Contoh: Elektronik, Pakaian, Buku..."
                    class="w-full border rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent
                           {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                    required
                >
                @error('name')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="mb-6">
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Deskripsi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    maxlength="500"
                    placeholder="Deskripsikan jenis barang dalam kategori ini..."
                    class="w-full border rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent resize-none
                           {{ $errors->has('description') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs mt-1">Maksimal 500 karakter.</p>
            </div>

            {{-- Tombol --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Simpan Kategori
                </button>
                <a href="{{ route('admin.categories.index') }}"
                   class="text-gray-500 hover:text-gray-700 text-sm px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
