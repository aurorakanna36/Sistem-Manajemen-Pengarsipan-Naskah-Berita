<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Naskah extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'kategori',
        'abstrak',
        'isi_naskah',
        'berkas_naskah',
        'author_type',
        'author_id',
        'tahap',
        'keputusan',
        'is_published',
        'alasan_penolakan',
    ];

    /**
     * Relasi polimorfik ke penulis (User atau Guest).
     */
    public function author()
    {
        return $this->morphTo();
    }

    /**
     * Relasi ke notifikasi terkait naskah ini.
     */
    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }
}
