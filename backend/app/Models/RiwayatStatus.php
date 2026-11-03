<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStatus extends Model
{
    use HasFactory;

    protected $table = 'riwayat_status';

    protected $fillable = [
        'id_kontributor',
        'id_naskah',
        'status',
        'catatan',
        'is_read',
        'waktu_status',
    ];

    protected $casts = [
        'waktu_status' => 'datetime',
        'is_read' => 'boolean',
    ];

    public function kontributor()
    {
        return $this->belongsTo(User::class, 'id_kontributor');
    }

    public function naskah()
    {
        return $this->belongsTo(Naskah::class, 'id_naskah');
    }
}
