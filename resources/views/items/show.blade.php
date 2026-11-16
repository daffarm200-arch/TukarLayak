@extends('layouts.app')

@section('title', $item->title . ' - Detail Barang')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Tombol Kembali -->
    <div>
        <a href="{{ route('items.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-brand-600 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Katalog
        </a>
    </div>

    <!-- Main Card Detail Barang -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm grid grid-cols-1 md:grid-cols-12">

        <!-- Bagian Foto Barang -->
        <div class="md:col-span-6 bg-slate-100 flex items-center justify-center p-6 min-h-[350px]">
            @if ($item->image_path)
                <img
                    src="{{ asset('storage/' . $item->image_path) }}"
                    alt="{{ $item->title }}"
                    class="w-full h-auto max-h-[480px] object-contain rounded-2xl shadow-sm"
                >
            @else
                <div class="text-center text-slate-400 p-8">
                    <svg class="w-16 h-16 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm font-medium">Pemilik tidak menyertakan foto barang</p>
                </div>
            @endif
        </div>

        <!-- Bagian Informasi & Aksi -->
        <div class="md:col-span-6 p-6 sm:p-8 flex flex-col justify-between">

            <div class="space-y-4">

                <!-- Badge Kategori & Jenis -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                        📁 {{ $item->category->name }}
                    </span>

                    @if ($item->type === 'free')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            🎁 Berbagi Gratis
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                            🔄 Siap Bertukar
                        </span>
                    @endif

                    @if ($item->status === 'available')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200">
                            ● Tersedia
                        </span>
                    @elseif ($item->status === 'reserved')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                            ⏳ Dipesan (Reserved)
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-300">
                            ✓ Selesai
                        </span>
                    @endif
                </div>

                <!-- Judul Barang -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $item->title }}
                </h1>

                <!-- Detail Kondisi -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80">
                    <div class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Kondisi Barang</div>
                    <div class="font-bold text-slate-800 text-base mt-0.5">{{ $item->condition }}</div>
                </div>

                <!-- Deskripsi Lengkap -->
                <div>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Barang</h2>
                    <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-line bg-white border border-slate-100 rounded-2xl p-4">
                        {{ $item->description }}
                    </div>
                </div>

            </div>

            <!-- Bagian Pemilik & Aksi Pengajuan/Kelola -->
            <div class="pt-6 mt-6 border-t border-slate-100">

                <!-- Pemilik Barang -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-800 font-bold flex items-center justify-center text-sm">
                            {{ strtoupper(substr($item->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">Pemilik Barang:</div>
                            <div class="font-semibold text-slate-900 text-sm">{{ $item->user->name }}</div>
                        </div>
                    </div>
                    <div class="text-right text-xs text-slate-400">
                        Diunggah pada:<br>
                        <span class="font-medium text-slate-600">{{ $item->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                </div>

                <!-- Aksi: Pemilik vs Calon Pengaju -->
                @auth
                    @if (Auth::id() === $item->user_id)
                        <!-- Kontrol Pemilik Barang -->
                        <div class="mt-6 pt-4 border-t border-dashed border-slate-200 flex items-center gap-3">
                            <a
                                href="{{ route('items.edit', $item) }}"
                                class="flex-1 text-center py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold rounded-xl text-xs transition flex items-center justify-center gap-1.5"
                            >
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Barang Ini
                            </a>

                            <form method="POST" action="{{ route('items.destroy', $item) }}" data-app-confirm data-confirm-title="Hapus Barang?" data-confirm-message="Barang ini akan dihapus dari TukarLayak. Tindakan ini tidak dapat dibatalkan." data-confirm-item="{{ $item->title }}" data-confirm-kind="Barang" data-confirm-button="Ya, Hapus" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="py-2.5 px-4 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl text-xs transition flex items-center gap-1.5 border border-red-200"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Tombol Ajukan Permintaan untuk Pengguna Lain -->
                        <div class="mt-6 pt-4 border-t border-slate-100">
                            @if ($item->isAvailable())
                                <a
                                    href="{{ route('requests.create', $item) }}"
                                    class="w-full py-3 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-2xl shadow-sm transition flex items-center justify-center space-x-2"
                                >
                                    @if ($item->type === 'free')
                                        <span>🎁 Ajukan Permintaan Barang Gratis</span>
                                    @else
                                        <span>🔄 Ajukan Pertukaran Barang</span>
                                    @endif
                                </a>
                            @elseif ($item->isReserved())
                                <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl text-center text-amber-800 text-sm font-semibold">
                                    ⏳ Barang ini sedang dipesan (Reserved) dalam proses pertukaran.
                                </div>
                            @else
                                <div class="p-3 bg-slate-100 border border-slate-200 rounded-2xl text-center text-slate-500 text-sm font-semibold">
                                    ✓ Barang ini telah selesai ditukar / dibagikan.
                                </div>
                            @endif
                        </div>
                    @endif
                @else
                    <!-- Jika Tamu Belum Login -->
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a
                            href="{{ route('login') }}"
                            class="w-full py-3 px-4 bg-slate-100 hover:bg-brand-50 hover:text-brand-700 text-slate-700 font-bold rounded-2xl transition flex items-center justify-center space-x-2 border border-slate-200"
                        >
                            <span>Masuk untuk Mengajukan Permintaan</span>
                        </a>
                    </div>
                @endauth

            </div>

        </div>

    </div>

</div>
@endsection
