<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'role',
        'status_verifikasi',
        'nomor_whatsapp',
        'instansi',
        'file_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function kontributor()
    {
        return $this->hasOne(Kontributor::class, 'user_id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id');
    }

    public function naskah()
    {
        return $this->hasMany(Naskah::class, 'id_kontributor');
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatus::class, 'id_kontributor');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_admin');
    }

    public function publikasis()
    {
        return $this->hasMany(Publikasi::class, 'id_admin');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
