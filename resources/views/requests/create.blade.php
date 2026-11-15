@extends('layouts.app')

@section('title', 'Ajukan Permintaan - ' . $item->title)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Tombol Kembali -->
    <div>
        <a href="{{ route('items.show', $item) }}" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-brand-600 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Detail Barang
        </a>
    </div>

    <!-- Ringkasan Barang yang Diminta -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm flex items-center space-x-4">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200 flex items-center justify-center">
            @if ($item->image_path)
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
            @else
                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            @endif
        </div>
        <div>
            <div class="flex items-center gap-2 mb-1">
                @if ($item->isFree())
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">
                        🎁 Berbagi Gratis
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-800">
                        🔄 Siap Bertukar
                    </span>
                @endif
                <span class="text-xs text-slate-400">Pemilik: {{ $item->user->name }}</span>
            </div>
            <h2 class="text-base font-bold text-slate-900">{{ $item->title }}</h2>
            <p class="text-xs text-slate-500">Kondisi: {{ $item->condition }}</p>
        </div>
    </div>

    <!-- Form Pengajuan -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">

        <div class="border-b border-slate-100 pb-4 mb-6">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Formulir Pengajuan Permintaan</h1>
            <p class="text-xs text-slate-500 mt-1">
                @if ($item->isFree())
                    Barang ini dibagikan secara gratis. Anda dapat menyertakan pesan permohonan kepada pemilik barang.
                @else
                    Pilih salah satu barang milik Anda yang masih tersedia untuk diajukan sebagai pertukaran (barter).
                @endif
            </p>
        </div>

        <form method="POST" action="{{ route('requests.store', $item) }}" class="space-y-6">
            @csrf

            @if ($item->isExchange())
                <!-- Pilihan Barang yang Ditawarkan -->
                <div>
                    <label for="offered_item_id" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Barang Milik Anda untuk Ditukarkan <span class="text-red-500">*</span>
                    </label>

                    @if ($myAvailableItems->count() > 0)
                        <select
                            id="offered_item_id"
                            name="offered_item_id"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border @error('offered_item_id') border-red-400 bg-red-50/50 @else border-slate-300 @enderror focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm bg-white text-slate-800 transition"
                        >
                            <option value="">-- Pilih Barang yang Ingin Anda Tukarkan --</option>
                            @foreach ($myAvailableItems as $myItem)
                                <option value="{{ $myItem->id }}" {{ old('offered_item_id') == $myItem->id ? 'selected' : '' }}>
                                    {{ $myItem->title }} (Kondisi: {{ $myItem->condition }})
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-amber-800 text-sm space-y-2">
                            <p class="font-medium">⚠️ Anda belum memiliki barang berstatus tersedia untuk ditukarkan.</p>
                            <p class="text-xs text-amber-700">Untuk mengajukan pertukaran, Anda perlu memasang barang milik Anda terlebih dahulu ke katalog.</p>
                            <a href="{{ route('items.create') }}" class="inline-block mt-2 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold">
                                + Pasang Barang Sekarang
                            </a>
                        </div>
                    @endif

                    @error('offered_item_id')
                        <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <!-- Pesan untuk Pemilik Barang -->
            <div>
                <label for="message" class="block text-sm font-semibold text-slate-800 mb-1.5">
                    Pesan untuk Pemilik <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                </label>
                <textarea
                    id="message"
                    name="message"
                    rows="4"
                    placeholder="Contoh: Halo, saya tertarik dengan barang ini untuk keperluan kuliah/kerja. Apakah bisa janjian serah terima di area sekitar kampus?"
                    class="w-full px-4 py-3 rounded-xl border @error('message') border-red-400 bg-red-50/50 @else border-slate-300 @enderror focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                >{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Submit -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a
                    href="{{ route('items.show', $item) }}"
                    class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    @if ($item->isExchange() && $myAvailableItems->count() === 0) disabled @endif
                    class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl shadow-sm transition flex items-center"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Kirim Permintaan
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
