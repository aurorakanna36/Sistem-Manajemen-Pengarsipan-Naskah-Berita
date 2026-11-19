<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guest', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->string('email', 50);
            $table->string('nomot_Whatsapp', 20)->nullable();
            $table->string('instansi', 50)->nullable();
            $table->string('sosial_media', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest');
    }
};
