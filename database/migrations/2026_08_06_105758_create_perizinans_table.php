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
        Schema::create('perizinans', function (Blueprint $table) {

            $table->id('id_perizinan');

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('instansi_id')
                ->constrained('instansis')
                ->cascadeOnDelete();

            $table->enum('jenis_izin', [
                'terlambat',
                'tidak_hadir',
                'pulang_awal'
            ]);

            $table->date('tanggal');

            $table->time('jam_mulai_izin')->nullable();

            $table->time('jam_selesai_izin')->nullable();

            $table->text('alasan');

            $table->string('bukti')->nullable();

            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak'
            ])->default('menunggu');

            $table->foreignId('disetujui_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinans');
    }
};