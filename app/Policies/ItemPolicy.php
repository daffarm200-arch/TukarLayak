<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    /**
     * Tentukan apakah pengguna dapat memperbarui data barang.
     * Mencegah kerentanan IDOR: Hanya pemilik barang yang berhak mengedit.
     */
    public function update(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }

    /**
     * Tentukan apakah pengguna dapat menghapus data barang.
     * Mencegah kerentanan IDOR: Hanya pemilik barang (atau admin) yang berhak menghapus.
     */
    public function delete(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }
}
