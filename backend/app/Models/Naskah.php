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
        'isi',
        'gambar_sampul',
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

    public function kategoris()
    {
        return $this->belongsToMany(Kategori::class, 'naskah_kategori', 'id_naskah', 'id_kategori')
            ->withTimestamps();
    }

    /**
     * Helper statis untuk menormalkan URL gambar sampul.
     * Membersihkan duplikasi storage/ dan mendukung fallback gambar pertama dari isi.
     */
    public static function resolveGambarSampulUrl(?string $gambarSampul, ?string $isi = null): ?string
    {
        if (empty($gambarSampul)) {
            if (!empty($isi) && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $isi, $matches)) {
                $gambarSampul = $matches[1];
            } else {
                return null;
            }
        }

        // Jika sudah URL http/https lengkap atau data URL
        if (preg_match('/^(https?:\/\/|data:)/i', $gambarSampul)) {
            return preg_replace('/(\/storage)+\/storage\//', '/storage/', $gambarSampul);
        }

        $clean = ltrim($gambarSampul, '/');
        if (str_starts_with($clean, 'storage/')) {
            $clean = substr($clean, 8);
        }
        if (!str_contains($clean, '/')) {
            $clean = 'sampul_naskah/' . $clean;
        }

        return asset('storage/' . $clean);
    }

    public function getGambarSampulUrlAttribute(): ?string
    {
        return self::resolveGambarSampulUrl($this->gambar_sampul, $this->isi);
    }
}
