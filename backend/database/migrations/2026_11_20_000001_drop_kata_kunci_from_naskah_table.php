<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('naskah', function (Blueprint $table) {
            if (Schema::hasColumn('naskah', 'kata_kunci')) {
                $table->dropColumn('kata_kunci');
            }
        });
    }

    public function down(): void
    {
        Schema::table('naskah', function (Blueprint $table) {
            if (!Schema::hasColumn('naskah', 'kata_kunci')) {
                $table->string('kata_kunci', 255)->nullable()->after('judul');
            }
        });
    }
};
