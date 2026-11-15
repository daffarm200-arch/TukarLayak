@extends('layouts.app')

@section('title', 'Detail Permintaan #' . $requestItem->id)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Tombol Kembali -->
    <div class="flex items-center justify-between">
        <a href="{{ url()->previous() == route('requests.incoming') ? route('requests.incoming') : route('requests.my') }}" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-brand-600 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Permintaan
        </a>

        <!-- Badge Status -->
        <div>
            @if ($requestItem->status === 'pending')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                    ● Menunggu Persetujuan
                </span>
            @elseif ($requestItem->status === 'accepted')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300">
                    ✓ Permintaan Disetujui (Dipesan)
                </span>
            @elseif ($requestItem->status === 'rejected')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300">
                    ✕ Ditolak
                </span>
            @elseif ($requestItem->status === 'cancelled')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-300">
                    ✕ Dibatalkan oleh Pengaju
                </span>
            @elseif ($requestItem->status === 'completed')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                    ✓ Pertukaran Selesai
                </span>
            @endif
        </div>
    </div>

    <!-- Main Card Detail Permintaan -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">

        <div class="border-b border-slate-100 pb-4">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Detail Permintaan Pertukaran</h1>
            <p class="text-xs text-slate-400 mt-1">Diajukan pada {{ $requestItem->created_at->translatedFormat('l, d F Y - H:i') }} WIB</p>
        </div>

        <!-- Perbandingan Dua Barang (Barang Diminta vs Barang Ditawarkan) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Barang yang Diminta -->
            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 flex flex-col justify-between">
                <div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-brand-100 text-brand-800 mb-2">
                        Barang yang Diminta
                    </span>
                    <div class="flex items-start space-x-3 mt-1">
                        <div class="w-16 h-16 rounded-xl bg-slate-200 overflow-hidden flex-shrink-0 border border-slate-300 flex items-center justify-center">
                            @if ($requestItem->item->image_path)
                                <img src="{{ asset('storage/' . $requestItem->item->image_path) }}" alt="{{ $requestItem->item->title }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h2 class="font-bold text-slate-900 text-sm">
                                <a href="{{ route('items.show', $requestItem->item) }}" class="hover:text-brand-600 transition">
                                    {{ $requestItem->item->title }}
                                </a>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Kategori: {{ $requestItem->item->category->name }}</p>
                            <p class="text-xs text-slate-500">Kondisi: {{ $requestItem->item->condition }}</p>
                            <span class="inline-block mt-1 text-[11px] font-semibold text-slate-600">
                                Status: {{ ucfirst($requestItem->item->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-200/80 text-xs text-slate-500">
                    Pemilik: <strong class="text-slate-800">{{ $requestItem->item->user->name }}</strong>
                </div>
            </div>

            <!-- Barang yang Ditawarkan / Gratis -->
            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 flex flex-col justify-between">
                <div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-blue-800 mb-2">
                        Barang yang Ditawarkan (Barter)
                    </span>

                    @if ($requestItem->offeredItem)
                        <div class="flex items-start space-x-3 mt-1">
                            <div class="w-16 h-16 rounded-xl bg-slate-200 overflow-hidden flex-shrink-0 border border-slate-300 flex items-center justify-center">
                                @if ($requestItem->offeredItem->image_path)
                                    <img src="{{ asset('storage/' . $requestItem->offeredItem->image_path) }}" alt="{{ $requestItem->offeredItem->title }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <h2 class="font-bold text-slate-900 text-sm">
                                    <a href="{{ route('items.show', $requestItem->offeredItem) }}" class="hover:text-blue-600 transition">
                                        {{ $requestItem->offeredItem->title }}
                                    </a>
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">Kategori: {{ $requestItem->offeredItem->category->name }}</p>
                                <p class="text-xs text-slate-500">Kondisi: {{ $requestItem->offeredItem->condition }}</p>
                                <span class="inline-block mt-1 text-[11px] font-semibold text-slate-600">
                                    Status: {{ ucfirst($requestItem->offeredItem->status) }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-xl text-xs text-emerald-800">
                            🎁 <strong>Permintaan Hibah Gratis:</strong> Pengaju memohon barang ini tanpa menukar dengan barang lain.
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-200/80 text-xs text-slate-500">
                    Pengaju: <strong class="text-slate-800">{{ $requestItem->requester->name }}</strong>
                </div>
            </div>

        </div>

        <!-- Pesan Tambahan dari Pengaju -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Pesan dari Pengaju</h2>
            @if ($requestItem->message)
                <p class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">{{ $requestItem->message }}</p>
            @else
                <p class="text-slate-400 text-sm italic">Pengaju tidak menyertakan pesan tambahan.</p>
            @endif
        </div>

        <!-- Panel Tindakan Berdasarkan Peran & Status -->
        <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-end gap-3">

            <!-- Jika User adalah Pemilik Barang -->
            @if (Auth::id() === $requestItem->item->user_id)
                @if ($requestItem->isPending())
                    <form method="POST" action="{{ route('requests.reject', $requestItem) }}" onsubmit="return confirm('Apakah Anda yakin ingin menolak permintaan ini?');" class="inline">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-sm font-semibold border border-red-200 transition">
                            Tolak Permintaan
                        </button>
                    </form>

                    <form method="POST" action="{{ route('requests.accept', $requestItem) }}" onsubmit="return confirm('Setujui permintaan ini? Barang akan diubah statusnya menjadi Dipesan (Reserved).');" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                            ✓ Terima Permintaan
                        </button>
                    </form>
                @elseif ($requestItem->isAccepted())
                    <form method="POST" action="{{ route('requests.complete', $requestItem) }}" onsubmit="return confirm('Tandai pertukaran ini sebagai selesai? Barang akan diubah statusnya menjadi selesai.');" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                            ✓ Tandai Pertukaran Selesai
                        </button>
                    </form>
                @endif
            @endif

            <!-- Jika User adalah Pengaju Permintaan -->
            @if (Auth::id() === $requestItem->requester_id)
                @if ($requestItem->isPending())
                    <form method="POST" action="{{ route('requests.cancel', $requestItem) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan permintaan ini?');" class="inline">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-sm font-semibold border border-red-200 transition">
                            Batalkan Permintaan
                        </button>
                    </form>
                @endif
            @endif

        </div>

    </div>

</div>
@endsection
