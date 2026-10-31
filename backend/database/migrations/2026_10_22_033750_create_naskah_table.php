<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('naskah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kontributor')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_guest')->nullable()->constrained('guest')->nullOnDelete();
            $table->string('nama_kontributor', 50)->nullable();
            $table->string('nama_guest', 50)->nullable();
            $table->text('judul');
            $table->string('file_path', 255)->nullable();
            $table->string('kategori', 30)->default('Berita');
            $table->string('status_naskah', 20)->default('perlu_direview');
            $table->timestamp('waktu_kirim')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('naskah');
    }
};
