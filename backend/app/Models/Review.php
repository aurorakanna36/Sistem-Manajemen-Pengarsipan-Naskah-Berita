<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'review';

    protected $fillable = [
        'id_admin',
        'id_naskah',
        'keputusan',
        'catatan',
        'waktu_review',
    ];

    protected $casts = [
        'waktu_review' => 'datetime',
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
