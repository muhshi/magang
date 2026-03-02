<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aturan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'aturan',
        'mulai',
        'selesai',
        'alasan',
    ];

    protected $casts = [
        'mulai'   => 'date',
        'selesai' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: hanya aturan yang aktif hari ini.
     */
    public function scopeActive($query)
    {
        $today = now()->toDateString();
        return $query->where('mulai', '<=', $today)
                     ->where('selesai', '>=', $today);
    }

    /**
     * Cek apakah user punya aturan aktif tertentu.
     */
    public static function hasActive(int $userId, string $jenis): bool
    {
        return static::where('user_id', $userId)
            ->where('aturan', $jenis)
            ->active()
            ->exists();
    }
}
