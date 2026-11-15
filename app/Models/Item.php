<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'condition',
        'type',
        'image_path',
        'status',
    ];

    /**
     * Relasi ke pemilik barang (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke kategori barang.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke daftar permintaan yang masuk untuk barang ini.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(ItemRequest::class, 'item_id');
    }

    /**
     * Scope query untuk hanya mengambil barang yang berstatus tersedia ('available').
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isReserved(): bool
    {
        return $this->status === 'reserved';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFree(): bool
    {
        return $this->type === 'free';
    }

    public function isExchange(): bool
    {
        return $this->type === 'exchange';
    }

    /**
     * Helper untuk mendapatkan URL gambar atau fallback placeholder.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return null;
    }
}
