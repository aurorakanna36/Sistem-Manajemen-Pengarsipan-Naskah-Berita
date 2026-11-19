<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('kategori')) {
            Schema::create('kategori', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kategori', 50)->unique();
                $table->string('slug', 60)->unique();
                $table->timestamps();
            });
        }

        $defaultCategories = [
            'Berita',
            'Nasional',
            'Pendidikan',
            'Teknologi',
            'Bisnis',
            'Daerah',
            'Ekonomi',
            'Kriminal',
            'Olahraga',
            'Opini',
            'Otomotif',
            'Politik',
            'Uncategorized',
        ];

        $now = now();
        foreach ($defaultCategories as $catName) {
            DB::table('kategori')->updateOrInsert(
                ['slug' => Str::slug($catName)],
                [
                    'nama_kategori' => $catName,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        if (!Schema::hasTable('naskah_kategori')) {
            Schema::create('naskah_kategori', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_naskah')->constrained('naskah')->onDelete('cascade');
                $table->foreignId('id_kategori')->constrained('kategori')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['id_naskah', 'id_kategori']);
            });
        }

        Schema::table('naskah', function (Blueprint $table) {
            if (!Schema::hasColumn('naskah', 'kata_kunci')) {
                $table->string('kata_kunci', 255)->nullable()->after('judul');
            }
            if (!Schema::hasColumn('naskah', 'abstrak')) {
                $table->text('abstrak')->nullable()->after('kata_kunci');
            }
            if (!Schema::hasColumn('naskah', 'isi')) {
                $table->longText('isi')->nullable()->after('abstrak');
            }
            if (!Schema::hasColumn('naskah', 'gambar_sampul')) {
                $table->string('gambar_sampul', 255)->nullable()->after('isi');
            }
            if (Schema::hasColumn('naskah', 'kategori')) {
                $table->string('kategori', 255)->nullable()->default('Berita')->change();
            }
        });

        Schema::table('riwayat_status', function (Blueprint $table) {
            if (!Schema::hasColumn('riwayat_status', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('catatan');
            }
        });

        // Sinkronisasi data naskah lama yang sudah ada ke tabel pivot naskah_kategori
        $kategoriMap = DB::table('kategori')->pluck('id', 'nama_kategori');
        $existingNaskahs = DB::table('naskah')->get();

        foreach ($existingNaskahs as $naskah) {
            if (!empty($naskah->kategori)) {
                $names = array_map('trim', explode(',', $naskah->kategori));
                foreach ($names as $name) {
                    $katId = $kategoriMap[$name] ?? $kategoriMap['Berita'] ?? null;
                    if ($katId) {
                        DB::table('naskah_kategori')->updateOrInsert(
                            [
                                'id_naskah' => $naskah->id,
                                'id_kategori' => $katId,
                            ],
                            [
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]
                        );
                    }
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('naskah_kategori');
        Schema::dropIfExists('kategori');

        Schema::table('naskah', function (Blueprint $table) {
            $cols = array_filter(
                ['kata_kunci', 'abstrak', 'isi', 'gambar_sampul'],
                fn($c) => Schema::hasColumn('naskah', $c)
            );
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('riwayat_status', function (Blueprint $table) {
            if (Schema::hasColumn('riwayat_status', 'is_read')) {
                $table->dropColumn('is_read');
            }
        });
    }
};
