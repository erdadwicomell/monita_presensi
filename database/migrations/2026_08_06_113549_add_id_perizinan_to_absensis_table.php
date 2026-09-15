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
        Schema::table('absensis', function (Blueprint $table) {

            $table->unsignedBigInteger('id_perizinan')
                  ->nullable()
                  ->after('user_id');

            $table->foreign('id_perizinan')
                  ->references('id_perizinan')
                  ->on('perizinans')
                  ->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {

            $table->dropForeign(['id_perizinan']);

            $table->dropColumn('id_perizinan');

        });
    }
};