<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'foto_identitas')) {
                $table->string('foto_identitas', 255)->nullable()->after('instansi');
            }
            if (!Schema::hasColumn('users', 'tanda_tangan')) {
                $table->string('tanda_tangan', 255)->nullable()->after('file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'foto_identitas')) {
                $table->dropColumn('foto_identitas');
            }
            if (Schema::hasColumn('users', 'tanda_tangan')) {
                $table->dropColumn('tanda_tangan');
            }
        });
    }
};
