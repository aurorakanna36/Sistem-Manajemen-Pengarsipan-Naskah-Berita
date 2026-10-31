<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $table = 'guest';

    protected $fillable = [
        'nama',
        'email',
        'nomot_Whatsapp',
        'instansi',
        'sosial_media',
    ];

    public function naskah()
    {
        return $this->hasMany(Naskah::class, 'id_guest');
    }
}
