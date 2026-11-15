@extends('layouts.app')

@section('title', 'Laporan Aktivitas — Admin')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Aktivitas</h1>
            <p class="text-sm text-gray-500 mt-1">Rekapitulasi seluruh permintaan dan pertukaran di TukarLayak.</p>
        </div>
        <a href="{{ route('admin.reports.export', request()->query()) }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export CSV
        </a>
    </div>

    {{-- Filter Tanggal & Status --}}
    <form method="GET" action="{{ route('admin.reports.index') }}"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status Permintaan</label>
                <select name="status"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    <option value="">Semua Status</option>
                    <option value="pending"   {{ $selectedStatus === 'pending'   ? 'selected' : '' }}>Menunggu</option>
                    <option value="accepted"  {{ $selectedStatus === 'accepted'  ? 'selected' : '' }}>Diterima</option>
                    <option value="rejected"  {{ $selectedStatus === 'rejected'  ? 'selected' : '' }}>Ditolak</option>
                    <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    Terapkan Filter
                </button>
                @if($startDate || $endDate || $selectedStatus)
                <a href="{{ route('admin.reports.index') }}"
                   class="text-gray-500 text-sm px-3 py-2 rounded-lg border border-gray-200 hover:border-gray-300 transition whitespace-nowrap">
                    Reset
                </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Statistik Ringkasan --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
        $stats = [
            ['label' => 'Total Barang',    'value' => $totalItemsOffered,    'color' => 'green',  'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ['label' => 'Total Permintaan','value' => $totalRequests,        'color' => 'blue',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label' => 'Permintaan Gratis','value' => $totalFreeRequests,   'color' => 'emerald','icon' => 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12z'],
            ['label' => 'Permintaan Barter','value' => $totalExchangeRequests,'color' => 'purple','icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
            ['label' => 'Menunggu',        'value' => $totalPending,         'color' => 'amber',  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Diterima',        'value' => $totalAccepted,        'color' => 'teal',   'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Ditolak',         'value' => $totalRejected,        'color' => 'red',    'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Selesai',         'value' => $totalCompleted,       'color' => 'gray',   'icon' => 'M5 13l4 4L19 7'],
        ];
        $colorMap = [
            'green'   => ['bg' => 'bg-green-50',   'text' => 'text-green-700',  'icon' => 'text-green-500'],
            'blue'    => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',   'icon' => 'text-blue-500'],
            'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700','icon' => 'text-emerald-500'],
            'purple'  => ['bg' => 'bg-purple-50',  'text' => 'text-purple-700', 'icon' => 'text-purple-500'],
            'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',  'icon' => 'text-amber-500'],
            'teal'    => ['bg' => 'bg-teal-50',    'text' => 'text-teal-700',   'icon' => 'text-teal-500'],
            'red'     => ['bg' => 'bg-red-50',     'text' => 'text-red-700',    'icon' => 'text-red-500'],
            'gray'    => ['bg' => 'bg-gray-100',   'text' => 'text-gray-700',   'icon' => 'text-gray-500'],
        ];
        @endphp

        @foreach($stats as $stat)
        @php $c = $colorMap[$stat['color']]; @endphp
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="p-2 rounded-lg {{ $c['bg'] }}">
                <svg class="w-5 h-5 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                </svg>
            </div>
            <div>
                <div class="text-xl font-bold {{ $c['text'] }}">{{ number_format($stat['value']) }}</div>
                <div class="text-xs text-gray-500">{{ $stat['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Grafik Aktivitas Bulanan (HTML Bar Chart) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-bold text-gray-800 mb-4">Aktivitas 6 Bulan Terakhir</h2>

        @php
            $maxVal = max(array_merge([1], array_column($monthlyStats, 'requests')));
        @endphp

        <div class="flex items-end gap-4 h-40">
            @foreach($monthlyStats as $month)
            @php
                $reqH    = $maxVal > 0 ? round(($month['requests']  / $maxVal) * 100) : 0;
                $compH   = $maxVal > 0 ? round(($month['completed'] / $maxVal) * 100) : 0;
            @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="w-full flex items-end gap-1 h-32">
                    {{-- Bar: Total permintaan --}}
                    <div class="flex-1 bg-green-200 rounded-t transition-all"
                         style="height: {{ $reqH }}%"
                         title="Total permintaan: {{ $month['requests'] }}">
                    </div>
                    {{-- Bar: Selesai --}}
                    <div class="flex-1 bg-green-600 rounded-t transition-all"
                         style="height: {{ $compH }}%"
                         title="Selesai: {{ $month['completed'] }}">
                    </div>
                </div>
                <div class="text-xs text-gray-500 text-center leading-tight whitespace-nowrap">
                    {{ $month['label'] }}
                </div>
                <div class="text-xs text-gray-400 text-center">{{ $month['requests'] }}</div>
            </div>
            @endforeach
        </div>

        <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
            <span class="inline-flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded bg-green-200"></span> Total Permintaan
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded bg-green-600"></span> Selesai
            </span>
        </div>
    </div>

    {{-- Tabel Data Transaksi --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-100 flex items-center justify-between">
            <span class="text-sm text-gray-500">
                Menampilkan <strong class="text-gray-800">{{ $requests->total() }}</strong> permintaan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-left text-gray-600 uppercase text-xs tracking-wide">
                        <th class="px-5 py-3">#</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Pengaju</th>
                        <th class="px-5 py-3">Barang Diminta</th>
                        <th class="px-5 py-3 text-center">Jenis</th>
                        <th class="px-5 py-3">Barang Ditawarkan</th>
                        <th class="px-5 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($requests as $req)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-gray-400 text-xs">#{{ $req->id }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs whitespace-nowrap">
                            {{ $req->created_at->format('d M Y') }}<br>
                            <span class="text-gray-400">{{ $req->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-800">{{ $req->requester->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $req->requester->email ?? '' }}</div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-800 max-w-[160px] truncate">
                                {{ $req->item->title ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-400">{{ $req->item->user->name ?? '' }}</div>
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($req->offered_item_id)
                                <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded-full">Barter</span>
                            @else
                                <span class="inline-block bg-emerald-100 text-emerald-700 text-xs font-semibold px-2 py-0.5 rounded-full">Gratis</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs max-w-[140px] truncate">
                            {{ $req->offeredItem->title ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $badge = match($req->status) {
                                    'pending'   => 'bg-amber-100 text-amber-700',
                                    'accepted'  => 'bg-teal-100 text-teal-700',
                                    'rejected'  => 'bg-red-100 text-red-700',
                                    'cancelled' => 'bg-gray-200 text-gray-600',
                                    'completed' => 'bg-green-100 text-green-700',
                                    default     => 'bg-gray-100 text-gray-500',
                                };
                                $label = match($req->status) {
                                    'pending'   => 'Menunggu',
                                    'accepted'  => 'Diterima',
                                    'rejected'  => 'Ditolak',
                                    'cancelled' => 'Dibatalkan',
                                    'completed' => 'Selesai',
                                    default     => ucfirst($req->status),
                                };
                            @endphp
                            <span class="inline-block {{ $badge }} text-xs font-semibold px-2 py-0.5 rounded-full">
                                {{ $label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="font-medium">Tidak ada data permintaan.</p>
                            @if($startDate || $endDate || $selectedStatus)
                                <a href="{{ route('admin.reports.index') }}" class="text-green-600 hover:underline text-sm mt-1 inline-block">
                                    Hapus filter
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $requests->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
