@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="max-w-xl mx-auto">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-4">
        <a href="{{ route('admin.categories.index') }}" class="hover:text-green-600">Kategori</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Edit: {{ $category->name }}</span>
    </nav>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h1 class="text-xl font-bold text-gray-800 mb-6">Edit Kategori</h1>

        <form method="POST" action="{{ route('admin.categories.update', $category) }}" novalidate>
            @csrf
            @method('PUT')

            {{-- Nama Kategori --}}
            <div class="mb-5">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $category->name) }}"
                    maxlength="100"
                    placeholder="Nama kategori..."
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
                    placeholder="Deskripsi kategori..."
                    class="w-full border rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent resize-none
                           {{ $errors->has('description') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                >{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs mt-1">Maksimal 500 karakter.</p>
            </div>

            {{-- Info Barang Terkait --}}
            @php $itemsCount = $category->items()->count(); @endphp
            @if($itemsCount > 0)
            <div class="mb-5 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-sm text-amber-700">
                <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Kategori ini digunakan oleh <strong>{{ $itemsCount }} barang</strong>. Anda hanya dapat mengedit nama dan deskripsinya.
            </div>
            @endif

            {{-- Tombol --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Perbarui Kategori
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
