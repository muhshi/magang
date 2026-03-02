<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'internship_id',
        'certificate_number',
        'program_studi',
        'fakultas',
        'nim',
        'predikat',
        'certificate_date',
    ];

    protected $casts = [
        'certificate_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Certificate $certificate) {
            if (empty($certificate->uuid)) {
                $certificate->uuid = (string) Str::uuid();
            }
        });
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }
}
