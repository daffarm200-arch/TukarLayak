<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    /**
     * Menampilkan halaman laporan aktivitas dengan filter tanggal, status, rekapitulasi, dan grafik bulanan.
     */
    public function index(Request $request): View
    {
        // 1. Filter Tanggal & Status
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $query = ItemRequest::with(['item.user', 'requester', 'offeredItem'])->latest();

        if ($startDate) {
            $query->whereDate('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        if ($status && in_array($status, ['pending', 'accepted', 'rejected', 'cancelled', 'completed'])) {
            $query->where('status', $status);
        }

        // 2. Statistik Ringkasan (Berdasarkan rentang filter jika ada)
        $statQuery = ItemRequest::query();
        if ($startDate) {
            $statQuery->whereDate('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $statQuery->whereDate('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $totalRequests = (clone $statQuery)->count();
        $totalFreeRequests = (clone $statQuery)->whereNull('offered_item_id')->count();
        $totalExchangeRequests = (clone $statQuery)->whereNotNull('offered_item_id')->count();
        $totalPending = (clone $statQuery)->where('status', 'pending')->count();
        $totalAccepted = (clone $statQuery)->where('status', 'accepted')->count();
        $totalRejected = (clone $statQuery)->where('status', 'rejected')->count();
        $totalCancelled = (clone $statQuery)->where('status', 'cancelled')->count();
        $totalCompleted = (clone $statQuery)->where('status', 'completed')->count();

        // Total barang yang ditawarkan (seluruh barang di database)
        $totalItemsOffered = Item::count();

        // 3. Data Rekapitulasi Aktivitas Bulanan (6 Bulan Terakhir)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $yearMonth = $month->format('Y-m');
            $monthLabel = $month->translatedFormat('M Y');

            $reqCount = ItemRequest::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $completedCount = ItemRequest::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'completed')
                ->count();

            $monthlyStats[] = [
                'label' => $monthLabel,
                'requests' => $reqCount,
                'completed' => $completedCount,
            ];
        }

        // 4. Data Tabel Transaksi Berfilter
        $requests = $query->paginate(20)->withQueryString();

        return view('admin.reports.index', [
            'requests' => $requests,
            'totalItemsOffered' => $totalItemsOffered,
            'totalRequests' => $totalRequests,
            'totalFreeRequests' => $totalFreeRequests,
            'totalExchangeRequests' => $totalExchangeRequests,
            'totalPending' => $totalPending,
            'totalAccepted' => $totalAccepted,
            'totalRejected' => $totalRejected,
            'totalCancelled' => $totalCancelled,
            'totalCompleted' => $totalCompleted,
            'monthlyStats' => $monthlyStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedStatus' => $status,
        ]);
    }

    /**
     * Mengekspor data laporan aktivitas ke format CSV.
     * Dilengkapi perlindungan terhadap kerentanan CSV Formula Injection.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $query = ItemRequest::with(['item.user', 'requester', 'offeredItem'])->latest();

        if ($startDate) {
            $query->whereDate('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        if ($status && in_array($status, ['pending', 'accepted', 'rejected', 'cancelled', 'completed'])) {
            $query->where('status', $status);
        }

        $filename = 'laporan-aktivitas-tukarlayak-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Tambahkan UTF-8 BOM agar Excel menampilkan aksen dan karakter bahasa dengan benar
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header kolom CSV
            fputcsv($handle, [
                'ID Permintaan',
                'Tanggal Pengajuan',
                'Nama Pengaju',
                'Email Pengaju',
                'Barang yang Diminta',
                'Pemilik Barang',
                'Jenis Permintaan',
                'Barang Barter yang Ditawarkan',
                'Status Transaksi',
                'Pesan Tambahan',
            ]);

            // Ekspor baris per baris dengan sanitasi CSV Formula Injection
            $query->chunk(100, function ($batch) use ($handle) {
                foreach ($batch as $row) {
                    $itemType = $row->offered_item_id ? 'Barter' : 'Gratis';
                    $offeredTitle = $row->offeredItem ? $row->offeredItem->title : '-';

                    fputcsv($handle, [
                        $this->sanitizeCsvCell($row->id),
                        $this->sanitizeCsvCell($row->created_at->format('Y-m-d H:i:s')),
                        $this->sanitizeCsvCell($row->requester->name ?? '-'),
                        $this->sanitizeCsvCell($row->requester->email ?? '-'),
                        $this->sanitizeCsvCell($row->item->title ?? '-'),
                        $this->sanitizeCsvCell($row->item->user->name ?? '-'),
                        $this->sanitizeCsvCell($itemType),
                        $this->sanitizeCsvCell($offeredTitle),
                        $this->sanitizeCsvCell(strtoupper($row->status)),
                        $this->sanitizeCsvCell($row->message ?? '-'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Mencegah serangan CSV Formula Injection (CWE-1236).
     * Jika sebuah sel teks diawali karakter formula berbahaya (=, +, -, @, tab, enter),
     * tambahkan tanda petik tunggal (') di awal agar aplikasi spreadsheet
     * (Excel, Calc, Google Sheets) membacanya murni sebagai teks biasa.
     */
    private function sanitizeCsvCell(mixed $value): string
    {
        $string = (string) $value;

        if ($string === '') {
            return '';
        }

        // Karakter picu formula berbahaya
        $dangerousChars = ['=', '+', '-', '@', "\t", "\r"];

        if (in_array($string[0], $dangerousChars, true)) {
            return "'" . $string;
        }

        return $string;
    }
}
