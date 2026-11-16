<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('naskah', function (Blueprint $table) {
            if (Schema::hasColumn('naskah', 'abstrak')) {
                $table->dropColumn('abstrak');
            }
        });
    }

    public function down(): void
    {
        Schema::table('naskah', function (Blueprint $table) {
            if (!Schema::hasColumn('naskah', 'abstrak')) {
                $table->text('abstrak')->nullable()->after('kata_kunci');
            }
        });
    }
};
