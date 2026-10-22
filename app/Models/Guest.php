<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'instansi',
    ];

    /**
     * Relasi ke naskah yang dikirim oleh tamu (Guest).
     */
    public function naskahs()
    {
        return $this->morphMany(Naskah::class, 'author');
    }
}
