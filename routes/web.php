<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| SUPER ADMIN
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\SuperAdmin\InstansiController;
use App\Http\Controllers\SuperAdmin\AdminInstansiController;

/*
|--------------------------------------------------------------------------
| ADMIN INSTANSI
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\AdminInstansi\PesertaController;
use App\Http\Controllers\AdminInstansi\DivisiController;
use App\Http\Controllers\AdminInstansi\TeknisiController;
use App\Http\Controllers\AdminInstansi\AdminPerizinanController;
use App\Http\Controllers\AdminInstansi\AdminLaporanController;
use App\Http\Controllers\AdminInstansi\AdminPembimbingController;
use App\Http\Controllers\AdminInstansi\AuditLogController;
use App\Http\Controllers\AdminInstansi\OnboardingInstansiController;
use App\Http\Controllers\NotifikasiController;

/*
|--------------------------------------------------------------------------
| AUTH & OTP FLOWS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Auth\AdminRegisterController;
use App\Http\Controllers\Auth\OtpVerificationController;

/*
|--------------------------------------------------------------------------
| PEMBIMBING INSTANSI
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\PembimbingInstansi\PembimbingDashboardController;
use App\Http\Controllers\PembimbingInstansi\PembimbingLaporanController;

/*
|--------------------------------------------------------------------------
| PESERTA
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Peserta\AbsensiController;
use App\Http\Controllers\Peserta\LaporanController;
use App\Http\Controllers\Peserta\PerizinanController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| REGISTRASI ADMIN INSTANSI & VERIFIKASI OTP (PUBLIK)
|--------------------------------------------------------------------------
*/
Route::get('/register/admin-instansi', [AdminRegisterController::class, 'showRegistrationForm'])
    ->name('register.admin.form');

Route::post('/register/admin-instansi', [AdminRegisterController::class, 'register'])
    ->name('register.admin.submit');

Route::get('/verify-otp', [OtpVerificationController::class, 'showVerifyForm'])
    ->name('otp.verify.form');

Route::post('/verify-otp', [OtpVerificationController::class, 'verify'])
    ->name('otp.verify.submit');

Route::post('/verify-otp/resend', [OtpVerificationController::class, 'resend'])
    ->name('otp.verify.resend');

Route::get('/set-password', [OtpVerificationController::class, 'showSetPasswordForm'])
    ->name('otp.set-password.form');

Route::post('/set-password', [OtpVerificationController::class, 'setPassword'])
    ->name('otp.set-password.submit');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ONBOARDING DATA INSTANSI (ADMIN INSTANSI BARU)
    |--------------------------------------------------------------------------
    */
    Route::get('/onboarding/instansi', [OnboardingInstansiController::class, 'create'])
        ->name('admin.onboarding.create');

    Route::post('/onboarding/instansi', [OnboardingInstansiController::class, 'store'])
        ->name('admin.onboarding.store');

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN
    |--------------------------------------------------------------------------
    */

    // CRUD INSTANSI
    Route::resource('instansi', InstansiController::class);

    // CRUD ADMIN INSTANSI
    Route::resource('admin-instansi', AdminInstansiController::class);

    /*
    |--------------------------------------------------------------------------
    | ADMIN INSTANSI
    |--------------------------------------------------------------------------
    */

    // CRUD PESERTA
    Route::resource('peserta', PesertaController::class);

    /*
    |--------------------------------------------------------------------------
    | REKAP ABSENSI
    |--------------------------------------------------------------------------
    */

    // HALAMAN REKAP ABSENSI
    Route::get('/rekap-absensi', [PesertaController::class, 'rekapAbsensi'])
        ->name('rekap.absensi');

    // EXPORT PDF REKAP ABSENSI
    Route::get('/rekap-absensi/export-pdf', [PesertaController::class, 'exportPdf'])
        ->name('rekap.absensi.pdf');

    // EXPORT CSV / EXCEL REKAP ABSENSI
    Route::get('/rekap-absensi/export-csv', [PesertaController::class, 'exportCsv'])
        ->name('rekap.absensi.csv');

    /*
    |--------------------------------------------------------------------------
    | DIVISI
    |--------------------------------------------------------------------------
    */

    Route::resource('divisi', DivisiController::class);

    /*
    |--------------------------------------------------------------------------
    | TEKNISI
    |--------------------------------------------------------------------------
    */

    Route::resource('teknisi', TeknisiController::class);

    /*
    |--------------------------------------------------------------------------
    | DATA PERIZINAN
    |--------------------------------------------------------------------------
    */

    Route::get('/AdminInstansi/perizinan', [AdminPerizinanController::class, 'index'])
        ->name('admin.perizinan.index');

    Route::get('/AdminInstansi/perizinan/{id}', [AdminPerizinanController::class, 'show'])
        ->name('admin.perizinan.show');

    Route::put('/AdminInstansi/perizinan/{id}/setujui', [AdminPerizinanController::class, 'setujui'])
        ->name('admin.perizinan.setujui');

    Route::put('/AdminInstansi/perizinan/{id}/tolak', [AdminPerizinanController::class, 'tolak'])
        ->name('admin.perizinan.tolak');

    /*
    |--------------------------------------------------------------------------
    | DATA LAPORAN KEGIATAN (ADMIN INSTANSI / PEMBIMBING)
    |--------------------------------------------------------------------------
    */

    Route::get('/AdminInstansi/laporan', [AdminLaporanController::class, 'index'])
        ->name('admin.laporan.index');

    Route::get('/AdminInstansi/laporan/{id}', [AdminLaporanController::class, 'show'])
        ->name('admin.laporan.show');

    Route::put('/AdminInstansi/laporan/{id}/setujui', [AdminLaporanController::class, 'setujui'])
        ->name('admin.laporan.setujui');

    Route::put('/AdminInstansi/laporan/{id}/revisi', [AdminLaporanController::class, 'revisi'])
        ->name('admin.laporan.revisi');

    /*
    |--------------------------------------------------------------------------
    | DATA PEMBIMBING INSTANSI (ADMIN INSTANSI)
    |--------------------------------------------------------------------------
    */
    Route::get('/AdminInstansi/pembimbing', [AdminPembimbingController::class, 'index'])
        ->name('admin.pembimbing.index');

    Route::get('/AdminInstansi/pembimbing/create', [AdminPembimbingController::class, 'create'])
        ->name('admin.pembimbing.create');

    Route::post('/AdminInstansi/pembimbing', [AdminPembimbingController::class, 'store'])
        ->name('admin.pembimbing.store');

    Route::get('/AdminInstansi/pembimbing/{id}/edit', [AdminPembimbingController::class, 'edit'])
        ->name('admin.pembimbing.edit');

    Route::put('/AdminInstansi/pembimbing/{id}', [AdminPembimbingController::class, 'update'])
        ->name('admin.pembimbing.update');

    Route::delete('/AdminInstansi/pembimbing/{id}', [AdminPembimbingController::class, 'destroy'])
        ->name('admin.pembimbing.destroy');

    /*
    |--------------------------------------------------------------------------
    | PEMBIMBING INSTANSI WORKFLOW (DASHBOARD & VALIDASI LAPORAN BIMBINGAN)
    |--------------------------------------------------------------------------
    */
    Route::get('/Pembimbing/dashboard', [PembimbingDashboardController::class, 'index'])
        ->name('pembimbing.dashboard');

    Route::get('/Pembimbing/laporan', [PembimbingLaporanController::class, 'index'])
        ->name('pembimbing.laporan.index');

    Route::get('/Pembimbing/laporan/{id}', [PembimbingLaporanController::class, 'show'])
        ->name('pembimbing.laporan.show');

    Route::put('/Pembimbing/laporan/{id}/setujui', [PembimbingLaporanController::class, 'setujui'])
        ->name('pembimbing.laporan.setujui');

    Route::put('/Pembimbing/laporan/{id}/revisi', [PembimbingLaporanController::class, 'revisi'])
        ->name('pembimbing.laporan.revisi');

    /*
    |--------------------------------------------------------------------------
    | AUDIT LOG KEAMANAN (SUPER ADMIN)
    |--------------------------------------------------------------------------
    */
    Route::get('/AdminInstansi/audit-log', [AuditLogController::class, 'index'])
        ->middleware('role:super_admin')
        ->name('admin.audit.log');

    /*
    |--------------------------------------------------------------------------
    | SISTEM NOTIFIKASI
    |--------------------------------------------------------------------------
    */
    Route::get('/notifikasi/latest', [NotifikasiController::class, 'getLatest'])
        ->name('notifikasi.latest');

    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])
        ->name('notifikasi.read');

    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllAsRead'])
        ->name('notifikasi.readAll');

    /*
    |--------------------------------------------------------------------------
    | PESERTA
    |--------------------------------------------------------------------------
    */

    // HALAMAN ABSENSI
    Route::get('/absensi', [AbsensiController::class, 'index'])
        ->name('absensi.index');

    // SIMPAN ABSENSI
    Route::post('/absensi', [AbsensiController::class, 'store'])
        ->name('absensi.store');

    // RIWAYAT ABSENSI
    Route::get('/riwayat-absensi', [AbsensiController::class, 'riwayat'])
        ->name('absensi.riwayat');


    /*
    |--------------------------------------------------------------------------
    | PERIZINAN
    |--------------------------------------------------------------------------
    */

    // HALAMAN PERIZINAN
    Route::get('/perizinan', [PerizinanController::class, 'index'])
        ->name('perizinan.index');

    // FORM AJUKAN PERIZINAN
    Route::get('/perizinan/create', [PerizinanController::class, 'create'])
        ->name('perizinan.create');

    // SIMPAN PERIZINAN
    Route::post('/perizinan', [PerizinanController::class, 'store'])
        ->name('perizinan.store');

    // DETAIL PERIZINAN
    Route::get('/perizinan/{id}', [PerizinanController::class, 'show'])
        ->name('perizinan.show');

    // EDIT PERIZINAN
    Route::get('/perizinan/{id}/edit', [PerizinanController::class, 'edit'])
        ->name('perizinan.edit');

    // UPDATE PERIZINAN
    Route::put('/perizinan/{id}', [PerizinanController::class, 'update'])
        ->name('perizinan.update');

    // BATALKAN / HAPUS PERIZINAN
    Route::delete('/perizinan/{id}', [PerizinanController::class, 'destroy'])
        ->name('perizinan.destroy');


    /*
    |--------------------------------------------------------------------------
    | LAPORAN KEGIATAN (PESERTA)
    |--------------------------------------------------------------------------
    */

    // HALAMAN LAPORAN
    Route::get('/laporan', [LaporanController::class, 'index'])
        ->name('laporan.index');

    // SIMPAN LAPORAN
    Route::post('/laporan', [LaporanController::class, 'store'])
        ->name('laporan.store');

    // DETAIL LAPORAN
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])
        ->name('laporan.show');

    // EDIT LAPORAN (JIKA STATUS REVISI/MENUNGGU)
    Route::get('/laporan/{id}/edit', [LaporanController::class, 'edit'])
        ->name('laporan.edit');

    // UPDATE LAPORAN
    Route::put('/laporan/{id}', [LaporanController::class, 'update'])
        ->name('laporan.update');

    /*
    |--------------------------------------------------------------------------
    | DATA USER
    |--------------------------------------------------------------------------
    */

    Route::get('/users', function () {

        $users = \App\Models\User::latest()->get();

        return view('users.index', compact('users'));

    })->name('users.index');

});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

