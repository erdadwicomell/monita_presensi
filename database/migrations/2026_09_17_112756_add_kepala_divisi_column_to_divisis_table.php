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
            if (!Schema::hasColumn('divisis', 'kepala_divisi')) {
                $table->string('kepala_divisi')->nullable()->after('nama_divisi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('divisis', function (Blueprint $table) {
            if (Schema::hasColumn('divisis', 'kepala_divisi')) {
                $table->dropColumn('kepala_divisi');
            }
        });
    }
};
