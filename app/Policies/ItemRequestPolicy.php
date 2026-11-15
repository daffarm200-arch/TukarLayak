<?php

namespace App\Policies;

use App\Models\ItemRequest;
use App\Models\User;

class ItemRequestPolicy
{
    /**
     * Tentukan apakah pengguna dapat melihat detail permintaan.
     * Hanya pengaju atau pemilik barang yang berhak melihat.
     */
    public function view(User $user, ItemRequest $itemRequest): bool
    {
        return $user->id === $itemRequest->requester_id
            || $user->id === $itemRequest->item->user_id;
    }

    /**
     * Tentukan apakah pengguna berhak menerima permintaan.
     * Hanya pemilik barang yang boleh menerima, dan status permintaan harus 'pending'.
     */
    public function accept(User $user, ItemRequest $itemRequest): bool
    {
        return $user->id === $itemRequest->item->user_id
            && $itemRequest->isPending();
    }

    /**
     * Tentukan apakah pengguna berhak menolak permintaan.
     * Hanya pemilik barang yang boleh menolak, dan status permintaan harus 'pending'.
     */
    public function reject(User $user, ItemRequest $itemRequest): bool
    {
        return $user->id === $itemRequest->item->user_id
            && $itemRequest->isPending();
    }

    /**
     * Tentukan apakah pengguna berhak membatalkan permintaan.
     * Hanya pengaju yang boleh membatalkan, dan status permintaan harus 'pending'.
     */
    public function cancel(User $user, ItemRequest $itemRequest): bool
    {
        return $user->id === $itemRequest->requester_id
            && $itemRequest->isPending();
    }

    /**
     * Tentukan apakah pengguna berhak menandai transaksi pertukaran sebagai selesai.
     * Hanya pemilik barang yang boleh menyelesaikan, dan status harus 'accepted'.
     */
    public function complete(User $user, ItemRequest $itemRequest): bool
    {
        return $user->id === $itemRequest->item->user_id
            && $itemRequest->isAccepted();
    }
}
