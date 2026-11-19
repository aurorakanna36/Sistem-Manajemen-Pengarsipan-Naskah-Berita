<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publikasi extends Model
{
    use HasFactory;

    protected $table = 'publikasi';

    protected $fillable = [
        'id_admin',
        'id_naskah',
        'url_berita',
        'waktu_terbit',
    ];

    protected $casts = [
        'waktu_terbit' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }

    public function naskah()
    {
        return $this->belongsTo(Naskah::class, 'id_naskah');
    }
}
