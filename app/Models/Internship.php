<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Internship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'birth_place',
        'birth_date',
        'gender',
        'address',
        'phone',
        'email',
        'school_name',
        'education_level',
        'start_date',
        'end_date',
        'letter_file',
        'photo_file',
        'motivation',
        'skills',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
