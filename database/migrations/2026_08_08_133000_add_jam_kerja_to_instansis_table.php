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
        Schema::table('instansis', function (Blueprint $table) {
            $table->time('jam_masuk_mulai')->default('07:00:00')->after('radius');
            $table->time('jam_masuk_batas')->default('09:00:00')->after('jam_masuk_mulai');
            $table->time('jam_pulang_mulai')->default('16:00:00')->after('jam_masuk_batas');
            $table->time('jam_pulang_batas')->default('18:00:00')->after('jam_pulang_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instansis', function (Blueprint $table) {
            $table->dropColumn([
                'jam_masuk_mulai',
                'jam_masuk_batas',
                'jam_pulang_mulai',
                'jam_pulang_batas',
            ]);
        });
    }
};
