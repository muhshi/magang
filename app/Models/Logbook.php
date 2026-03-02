<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Logbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_id',
        'source',
        'tanggal_pengisian',
        'nama_pegawai',
        'deskripsi_tugas',
        'status',
        'lampiran',
    ];

    protected $casts = [
        'tanggal_pengisian' => 'date',
    ];

    // Pemilik logbook
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Ticket asal (jika source = system)
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    // Yang ditugaskan (pivot)
    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'logbook_users');
    }
}
