@extends('layouts.app')

@section('title', 'Permintaan Saya')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 mb-1">
                📤 Permintaan Terkirim
            </span>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Permintaan Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pantau status permintaan barang yang pernah Anda ajukan ke pengguna lain</p>
        </div>
        <div>
            <a href="{{ route('items.index') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-sm transition">
                Cari Barang di Katalog &rarr;
            </a>
        </div>
    </div>

    <!-- Tabel Daftar Permintaan Saya -->
    @if ($requests->count() > 0)
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="py-4 px-6">Barang Diminta</th>
                            <th class="py-4 px-6">Pemilik Barang</th>
                            <th class="py-4 px-6">Barang yang Ditawarkan</th>
                            <th class="py-4 px-6">Status Permintaan</th>
                            <th class="py-4 px-6">Diajukan Pada</th>
                            <th class="py-4 px-6 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach ($requests as $req)
                            <tr class="hover:bg-slate-50/80 transition">

                                <!-- Barang Diminta -->
                                <td class="py-4 px-6">
                                    <a href="{{ route('items.show', $req->item) }}" class="font-bold text-slate-900 hover:text-brand-600 transition block line-clamp-1">
                                        {{ $req->item->title }}
                                    </a>
                                    <span class="text-xs text-slate-400">
                                        Kondisi: {{ $req->item->condition }}
                                    </span>
                                </td>

                                <!-- Pemilik Barang -->
                                <td class="py-4 px-6 font-medium text-slate-800">
                                    {{ $req->item->user->name }}
                                </td>

                                <!-- Barang yang Saya Tawarkan -->
                                <td class="py-4 px-6">
                                    @if ($req->offeredItem)
                                        <a href="{{ route('items.show', $req->offeredItem) }}" class="text-xs font-semibold text-blue-600 hover:underline block line-clamp-1">
                                            🔄 {{ $req->offeredItem->title }}
                                        </a>
                                        <span class="text-[11px] text-slate-400">Milik Saya</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            🎁 Permintaan Gratis
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="py-4 px-6">
                                    @if ($req->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            ● Menunggu Tanggapan
                                        </span>
                                    @elseif ($req->status === 'accepted')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            ✓ Disetujui Pemilik
                                        </span>
                                    @elseif ($req->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            ✕ Ditolak Pemilik
                                        </span>
                                    @elseif ($req->status === 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                            ✕ Dibatalkan
                                        </span>
                                    @elseif ($req->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            ✓ Pertukaran Selesai
                                        </span>
                                    @endif
                                </td>

                                <!-- Waktu -->
                                <td class="py-4 px-6 text-xs text-slate-500">
                                    {{ $req->created_at->translatedFormat('d M Y, H:i') }}
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a
                                            href="{{ route('requests.show', $req) }}"
                                            class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition"
                                        >
                                            Detail
                                        </a>

                                        @if ($req->isPending())
                                            <!-- Tombol Batalkan -->
                                            <form method="POST" action="{{ route('requests.cancel', $req) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permintaan ini?');" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-lg border border-red-200 transition">
                                                    Batalkan
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
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-3xl mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Anda belum mengajukan permintaan</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                Jelajahi katalog TukarLayak dan temukan barang yang Anda butuhkan untuk diajukan barter atau diambil gratis.
            </p>
            <div class="mt-6">
                <a href="{{ route('items.index') }}" class="inline-flex items-center px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl text-sm shadow-sm transition">
                    Jelajahi Katalog Barang
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
