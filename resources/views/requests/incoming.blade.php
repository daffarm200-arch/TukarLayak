@extends('layouts.app')

@section('title', 'Permintaan Masuk')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-100 text-brand-800 border border-brand-200 mb-1">
                📥 Kotak Masuk Permintaan
            </span>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Permintaan Masuk</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftar pengguna yang ingin menukar atau meminta barang milik Anda</p>
        </div>
        <div>
            <a href="{{ route('requests.my') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition">
                Lihat Permintaan Saya &rarr;
            </a>
        </div>
    </div>

    <!-- Tabel Daftar Permintaan Masuk -->
    @if ($requests->count() > 0)
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="py-4 px-6">Barang Saya</th>
                            <th class="py-4 px-6">Pengaju</th>
                            <th class="py-4 px-6">Barang Barter</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6">Waktu</th>
                            <th class="py-4 px-6 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach ($requests as $req)
                            <tr class="hover:bg-slate-50/80 transition">

                                <!-- Barang Saya -->
                                <td class="py-4 px-6">
                                    <a href="{{ route('items.show', $req->item) }}" class="font-bold text-slate-900 hover:text-brand-600 transition block line-clamp-1">
                                        {{ $req->item->title }}
                                    </a>
                                    <span class="text-xs text-slate-400">
                                        {{ $req->item->category->name }}
                                    </span>
                                </td>

                                <!-- Pengaju -->
                                <td class="py-4 px-6 font-medium text-slate-800">
                                    {{ $req->requester->name }}
                                </td>

                                <!-- Barang yang Ditawarkan -->
                                <td class="py-4 px-6">
                                    @if ($req->offeredItem)
                                        <a href="{{ route('items.show', $req->offeredItem) }}" class="text-xs font-semibold text-blue-600 hover:underline block line-clamp-1">
                                            🔄 {{ $req->offeredItem->title }}
                                        </a>
                                        <span class="text-[11px] text-slate-400">Kondisi: {{ $req->offeredItem->condition }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            🎁 Permintaan Hibah Gratis
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="py-4 px-6">
                                    @if ($req->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            ● Menunggu Persetujuan
                                        </span>
                                    @elseif ($req->status === 'accepted')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            ✓ Disetujui (Dipesan)
                                        </span>
                                    @elseif ($req->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            ✕ Ditolak
                                        </span>
                                    @elseif ($req->status === 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                            ✕ Dibatalkan Pengaju
                                        </span>
                                    @elseif ($req->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            ✓ Pertukaran Selesai
                                        </span>
                                    @endif
                                </td>

                                <!-- Waktu -->
                                <td class="py-4 px-6 text-xs text-slate-500">
                                    {{ $req->created_at->diffForHumans() }}
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a
                                            href="{{ route('requests.show', $req) }}"
                                            class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition"
                                            title="Lihat Detail Permintaan"
                                        >
                                            Detail
                                        </a>

                                        @if ($req->isPending())
                                            <!-- Tombol Terima -->
                                            <form method="POST" action="{{ route('requests.accept', $req) }}" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui permintaan ini? Barang akan direservasi.');" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                                    Terima
                                                </button>
                                            </form>

                                            <!-- Tombol Tolak -->
                                            <form method="POST" action="{{ route('requests.reject', $req) }}" onsubmit="return confirm('Apakah Anda yakin ingin menolak permintaan ini?');" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-lg border border-red-200 transition">
                                                    Tolak
                                                </button>
                                            </form>
                                        @elseif ($req->isAccepted())
                                            <!-- Tombol Tandai Selesai -->
                                            <form method="POST" action="{{ route('requests.complete', $req) }}" onsubmit="return confirm('Tandai pertukaran ini sebagai selesai? Barang akan diubah statusnya menjadi selesai.');" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                                    Tandai Selesai
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $requests->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto shadow-sm">
            <div class="w-16 h-16 bg-brand-50 text-brand-600 rounded-3xl mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Belum ada permintaan masuk</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                Saat pengguna lain tertarik untuk meminta atau menukar barang yang Anda pasang, permintaannya akan muncul di sini.
            </p>
        </div>
    @endif

</div>
@endsection
