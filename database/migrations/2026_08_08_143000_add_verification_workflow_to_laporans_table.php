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
        Schema::table('laporans', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'disetujui', 'revisi'])
                  ->default('menunggu')
                  ->after('jam');

            $table->text('catatan_revisi')
                  ->nullable()
                  ->after('status');

            $table->foreignId('diverifikasi_oleh')
                  ->nullable()
                  ->after('catatan_revisi')
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('diverifikasi_pada')
                  ->nullable()
                  ->after('diverifikasi_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->dropForeign(['diverifikasi_oleh']);
            $table->dropColumn([
                'status',
                'catatan_revisi',
                'diverifikasi_oleh',
                'diverifikasi_pada',
            ]);
        });
    }
};
