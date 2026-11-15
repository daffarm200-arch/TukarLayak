<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ItemRequestController extends Controller
{
    /**
     * Menampilkan formulir pengajuan permintaan barang.
     */
    public function create(Item $item): View|RedirectResponse
    {
        // 1. User tidak boleh meminta barang miliknya sendiri
        if ($item->user_id === Auth::id()) {
            abort(403, 'Anda tidak dapat mengajukan permintaan untuk barang milik Anda sendiri.');
        }

        // 2. Barang harus berstatus tersedia
        if (! $item->isAvailable()) {
            return redirect()->route('items.show', $item)
                ->with('error', 'Barang ini sedang tidak berstatus tersedia.');
        }

        // 3. User tidak boleh membuat permintaan duplikat yang masih berstatus pending
        $hasPending = ItemRequest::where('item_id', $item->id)
            ->where('requester_id', Auth::id())
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return redirect()->route('requests.my')
                ->with('error', 'Anda sudah memiliki permintaan yang masih diproses untuk barang ini.');
        }

        // 4. Jika jenis barang adalah pertukaran, ambil daftar barang milik user yang masih available
        $myAvailableItems = [];
        if ($item->isExchange()) {
            $myAvailableItems = Auth::user()->items()->available()->get();
        }

        return view('requests.create', [
            'item' => $item,
            'myAvailableItems' => $myAvailableItems,
        ]);
    }

    /**
     * Menyimpan pengajuan permintaan barang ke database.
     */
    public function store(Request $request, Item $item): RedirectResponse
    {
        // 1. User tidak boleh meminta barang miliknya sendiri
        if ($item->user_id === Auth::id()) {
            abort(403, 'Anda tidak dapat mengajukan permintaan untuk barang milik Anda sendiri.');
        }

        // 2. Pastikan barang masih berstatus tersedia
        if (! $item->isAvailable()) {
            return redirect()->route('items.show', $item)
                ->with('error', 'Maaf, barang ini sudah tidak berstatus tersedia.');
        }

        // 3. Cegah permintaan duplikat yang masih berstatus pending
        $hasPending = ItemRequest::where('item_id', $item->id)
            ->where('requester_id', Auth::id())
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return redirect()->route('requests.my')
                ->with('error', 'Anda sudah memiliki permintaan pending untuk barang ini.');
        }

        // 4. Validasi berdasarkan jenis penawaran barang
        if ($item->isExchange()) {
            // Wajib memilih barang yang ditawarkan
            $validated = $request->validate([
                'offered_item_id' => ['required', 'integer', 'exists:items,id'],
                'message' => ['nullable', 'string', 'max:1000'],
            ], [
                'offered_item_id.required' => 'Anda wajib memilih salah satu barang milik Anda untuk ditukarkan.',
                'offered_item_id.exists' => 'Barang yang dipilih tidak valid.',
                'message.max' => 'Pesan maksimal 1000 karakter.',
            ]);

            // Pastikan barang yang ditawarkan benar-benar milik user login dan berstatus tersedia
            $offeredItem = Item::where('id', $validated['offered_item_id'])
                ->where('user_id', Auth::id())
                ->where('status', 'available')
                ->first();

            if (! $offeredItem) {
                return back()->withErrors([
                    'offered_item_id' => 'Barang yang ditawarkan harus milik Anda dan masih berstatus tersedia.',
                ])->withInput();
            }

            $offeredItemId = $offeredItem->id;
        } else {
            // Barang gratis: offered_item_id harus kosong
            $validated = $request->validate([
                'message' => ['nullable', 'string', 'max:1000'],
            ], [
                'message.max' => 'Pesan maksimal 1000 karakter.',
            ]);

            $offeredItemId = null;
        }

        // 5. Simpan permintaan dengan identitas requester dari sesi login
        ItemRequest::create([
            'item_id' => $item->id,
            'requester_id' => Auth::id(),
            'offered_item_id' => $offeredItemId,
            'message' => trim($validated['message'] ?? ''),
            'status' => 'pending',
        ]);

        return redirect()->route('requests.my')
            ->with('success', 'Permintaan berhasil dikirimkan ke pemilik barang!');
    }

    /**
     * Menampilkan daftar permintaan yang masuk untuk barang-barang milik user.
     */
    public function incoming(Request $request): View
    {
        $userItemIds = $request->user()->items()->pluck('id');

        $requests = ItemRequest::with(['item', 'requester', 'offeredItem'])
            ->whereIn('item_id', $userItemIds)
            ->latest()
            ->paginate(10);

        return view('requests.incoming', [
            'requests' => $requests,
        ]);
    }

    /**
     * Menampilkan daftar permintaan yang pernah diajukan oleh user.
     */
    public function myRequests(Request $request): View
    {
        $requests = $request->user()
            ->sentRequests()
            ->with(['item.user', 'offeredItem'])
            ->latest()
            ->paginate(10);

        return view('requests.my', [
            'requests' => $requests,
        ]);
    }

    /**
     * Menampilkan detail informasi suatu permintaan.
     */
    public function show(ItemRequest $itemRequest): View
    {
        Gate::authorize('view', $itemRequest);

        $itemRequest->load(['item.user', 'requester', 'offeredItem']);

        return view('requests.show', [
            'requestItem' => $itemRequest,
        ]);
    }

    /**
     * Pemilik barang menerima permintaan yang masih pending.
     */
    public function accept(ItemRequest $itemRequest): RedirectResponse
    {
        Gate::authorize('accept', $itemRequest);

        try {
            DB::transaction(function () use ($itemRequest) {
                // Kunci baris barang yang diminta untuk mencegah race condition (dobel accepted)
                $item = Item::where('id', $itemRequest->item_id)->lockForUpdate()->first();

                if (! $item || $item->status !== 'available') {
                    throw new \Exception('Barang ini sudah tidak berstatus tersedia.');
                }

                // Ubah status permintaan menjadi accepted
                $itemRequest->update(['status' => 'accepted']);

                // Ubah status barang utama menjadi reserved
                $item->update(['status' => 'reserved']);

                // Jika ada barang yang ditawarkan, ubah juga menjadi reserved
                if ($itemRequest->offered_item_id) {
                    $offeredItem = Item::where('id', $itemRequest->offered_item_id)->lockForUpdate()->first();
                    if ($offeredItem && $offeredItem->status === 'available') {
                        $offeredItem->update(['status' => 'reserved']);
                    }
                }

                // Otomatis tolak permintaan pending lainnya untuk barang yang sama
                ItemRequest::where('item_id', $item->id)
                    ->where('id', '!=', $itemRequest->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);

                // Jika ada barang yang ditawarkan, tolak juga permintaan pending lain yang mengarah padanya
                if ($itemRequest->offered_item_id) {
                    ItemRequest::where('item_id', $itemRequest->offered_item_id)
                        ->where('id', '!=', $itemRequest->id)
                        ->where('status', 'pending')
                        ->update(['status' => 'rejected']);
                }
            });

            return back()->with('success', 'Permintaan telah disetujui! Barang kini berstatus Dipesan (Reserved).');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Pemilik barang menolak permintaan yang masih pending.
     */
    public function reject(ItemRequest $itemRequest): RedirectResponse
    {
        Gate::authorize('reject', $itemRequest);

        $itemRequest->update(['status' => 'rejected']);

        return back()->with('success', 'Permintaan telah ditolak.');
    }

    /**
     * Pengaju membatalkan permintaannya sendiri yang masih pending.
     */
    public function cancel(ItemRequest $itemRequest): RedirectResponse
    {
        Gate::authorize('cancel', $itemRequest);

        $itemRequest->update(['status' => 'cancelled']);

        return back()->with('success', 'Permintaan berhasil dibatalkan.');
    }

    /**
     * Pemilik barang menandai transaksi pertukaran/pemberian sebagai selesai (completed).
     */
    public function complete(ItemRequest $itemRequest): RedirectResponse
    {
        Gate::authorize('complete', $itemRequest);

        try {
            DB::transaction(function () use ($itemRequest) {
                // Perbarui status permintaan menjadi completed
                $itemRequest->update(['status' => 'completed']);

                // Perbarui status barang utama menjadi completed
                $itemRequest->item()->update(['status' => 'completed']);

                // Jika ada barang yang ditawarkan, perbarui juga statusnya menjadi completed
                if ($itemRequest->offered_item_id) {
                    $itemRequest->offeredItem()->update(['status' => 'completed']);
                }
            });

            return back()->with('success', 'Pertukaran barang telah selesai! Terima kasih telah menggunakan TukarLayak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
