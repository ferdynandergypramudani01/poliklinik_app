<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'id_periksa',
        'bukti_pembayaran',
        'status',
        'catatan_admin',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function periksa()
    {
        return $this->belongsTo(Periksa::class, 'id_periksa');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
