<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('review', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_admin')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_naskah')->constrained('naskah')->onDelete('cascade');
            $table->enum('keputusan', ['diterima', 'direvisi', 'ditolak']);
            $table->text('catatan')->nullable();
            $table->timestamp('waktu_review')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review');
    }
};
