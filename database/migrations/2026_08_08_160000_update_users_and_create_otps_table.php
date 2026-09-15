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
        // 1. Update Users Table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nomor_telepon')) {
                $table->string('nomor_telepon', 30)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'alamat')) {
                $table->text('alamat')->nullable()->after('nomor_telepon');
            }
            if (!Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan')->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('users', 'nip')) {
                $table->string('nip', 50)->nullable()->after('jabatan');
            }
            if (!Schema::hasColumn('users', 'asal_sekolah_pt')) {
                $table->string('asal_sekolah_pt')->nullable()->after('nip');
            }
            if (!Schema::hasColumn('users', 'nim_nisn')) {
                $table->string('nim_nisn', 50)->nullable()->after('asal_sekolah_pt');
            }
            if (!Schema::hasColumn('users', 'divisi_id')) {
                $table->foreignId('divisi_id')->nullable()->after('nim_nisn')->constrained('divisis')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'teknisi_id')) {
                $table->foreignId('teknisi_id')->nullable()->after('divisi_id')->constrained('teknisis')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('teknisi_id');
            }
            if (!Schema::hasColumn('users', 'otp_verified_at')) {
                $table->timestamp('otp_verified_at')->nullable()->after('is_active');
            }
        });

        // 2. Create OTPs Table
        if (!Schema::hasTable('otps')) {
            Schema::create('otps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
                $table->string('email')->index();
                $table->string('otp_code', 10);
                $table->string('tipe', 50); // registrasi_admin, aktivasi_peserta, aktivasi_pembimbing
                $table->boolean('is_used')->default(false);
                $table->timestamp('expires_at');
                $table->timestamps();
            });
        }

        // 3. Create Pembimbing Peserta Pivot Table
        if (!Schema::hasTable('pembimbing_peserta')) {
            Schema::create('pembimbing_peserta', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pembimbing_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('peserta_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('instansi_id')->constrained('instansis')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['pembimbing_id', 'peserta_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembimbing_peserta');
        Schema::dropIfExists('otps');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['divisi_id']);
            $table->dropForeign(['teknisi_id']);
            $table->dropColumn([
                'nomor_telepon',
                'alamat',
                'jabatan',
                'nip',
                'asal_sekolah_pt',
                'nim_nisn',
                'divisi_id',
                'teknisi_id',
                'is_active',
                'otp_verified_at'
            ]);
        });
    }
};
