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
        Schema::table('divisis', function (Blueprint $table) {
            if (!Schema::hasColumn('divisis', 'kode_divisi')) {
                $table->string('kode_divisi', 10)->nullable()->after('nama_divisi');
            }
            if (!Schema::hasColumn('divisis', 'kepala_divisi_id')) {
                $table->foreignId('kepala_divisi_id')
                    ->nullable()
                    ->after('kode_divisi')
                    ->constrained('users')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('divisis', 'lokasi_ruangan')) {
                $table->string('lokasi_ruangan', 100)->nullable()->after('kepala_divisi_id');
            }
            if (!Schema::hasColumn('divisis', 'kuota_maksimal')) {
                $table->integer('kuota_maksimal')->default(5)->after('lokasi_ruangan');
            }
            if (!Schema::hasColumn('divisis', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('kuota_maksimal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('divisis', function (Blueprint $table) {
            if (Schema::hasColumn('divisis', 'kepala_divisi_id')) {
                $table->dropForeign(['kepala_divisi_id']);
                $table->dropColumn('kepala_divisi_id');
            }
            if (Schema::hasColumn('divisis', 'kode_divisi')) {
                $table->dropColumn('kode_divisi');
            }
            if (Schema::hasColumn('divisis', 'lokasi_ruangan')) {
                $table->dropColumn('lokasi_ruangan');
            }
            if (Schema::hasColumn('divisis', 'kuota_maksimal')) {
                $table->dropColumn('kuota_maksimal');
            }
        });
    }
};
