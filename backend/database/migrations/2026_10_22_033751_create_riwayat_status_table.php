<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kontributor')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_naskah')->constrained('naskah')->onDelete('cascade');
            $table->string('status', 30);
            $table->text('catatan')->nullable();
            $table->timestamp('waktu_status')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status');
    }
};
