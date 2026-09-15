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
        // 1. Tambah kolom tipe_penempatan pada tabel users (Default mengikuti jenis instansi)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'tipe_penempatan')) {
                $table->enum('tipe_penempatan', ['kantor', 'lapangan'])
                      ->default('kantor')
                      ->after('role');
            }
        });

        // 2. Tambah kolom teknisi_id pada tabel absensis (Untuk mencatat teknisi pendamping harian)
        Schema::table('absensis', function (Blueprint $table) {
            if (!Schema::hasColumn('absensis', 'teknisi_id')) {
                $table->foreignId('teknisi_id')
                      ->nullable()
                      ->after('id_perizinan')
                      ->constrained('teknisis')
                      ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['teknisi_id']);
            $table->dropColumn('teknisi_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tipe_penempatan');
        });
    }
};
