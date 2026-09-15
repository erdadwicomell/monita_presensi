<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teknisis', function (Blueprint $table) {

            $table->id();

            $table->string('nama');

            $table->string('email');

            $table->string('no_hp');

            $table->string('nik');

            $table->text('alamat_kerja');

            $table->foreignId('instansi_id')
                ->constrained()
                ->onDelete('cascade');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teknisis');
    }
};