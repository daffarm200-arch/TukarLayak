<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'requester_id',
        'offered_item_id',
        'message',
        'status',
    ];

    /**
     * Relasi ke barang yang diminta.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Relasi ke pengguna yang mengajukan permintaan.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Relasi ke barang yang ditawarkan untuk pertukaran (jika ada).
     */
    public function offeredItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'offered_item_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
