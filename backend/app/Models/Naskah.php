<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Naskah extends Model
{
    use HasFactory;

    protected $table = 'naskah';

    protected $fillable = [
        'id_kontributor',
        'id_guest',
        'nama_kontributor',
        'nama_guest',
        'judul',
        'file_path',
        'kategori',
        'status_naskah',
        'waktu_kirim',
    ];

    protected $casts = [
        'waktu_kirim' => 'datetime',
    ];

    public function kontributor()
    {
        return $this->belongsTo(User::class, 'id_kontributor');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'id_guest');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_naskah');
    }

    public function publikasi()
    {
        return $this->hasOne(Publikasi::class, 'id_naskah');
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatus::class, 'id_naskah');
    }
}
