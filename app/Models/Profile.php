<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    // app/Models/Profile.php
    protected $fillable = [
        'user_id',
        'photo',
        'school_name', // <-- TAMBAHKAN INI
        'phone_number',
        'address',
        'bio',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
