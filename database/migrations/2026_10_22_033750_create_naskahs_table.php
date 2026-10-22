<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('naskahs', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('kategori')->default('Berita');
            $table->text('abstrak')->nullable();
            $table->longText('isi_naskah')->nullable();
            $table->string('berkas_naskah')->nullable();
            
            // Relasi polimorfik ke User atau Guest
            $table->morphs('author');

            // 3 Tahapan Utama
            $table->enum('tahap', ['pengarsipan', 'verifikasi', 'selesai'])->default('pengarsipan');

            // Keputusan Redaksi
            $table->enum('keputusan', ['menunggu', 'diterima', 'revisi', 'ditolak'])->default('menunggu');
            $table->boolean('is_published')->default(false);
            $table->text('alasan_penolakan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('naskahs');
    }
};
