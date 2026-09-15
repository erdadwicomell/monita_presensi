<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->foreignId('divisi_id')
                  ->nullable()
                  ->constrained('divisis')
                  ->nullOnDelete();

            $table->foreignId('teknisi_id')
                  ->nullable()
                  ->constrained('teknisis')
                  ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropForeign(['divisi_id']);
            $table->dropForeign(['teknisi_id']);

            $table->dropColumn([
                'divisi_id',
                'teknisi_id'
            ]);

        });
        
    }
};