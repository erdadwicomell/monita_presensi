<?php

namespace App\Http\Controllers\AdminInstansi;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Proteksi Level Controller: Khusus Super Admin
        if (!$user || $user->role !== 'super_admin') {
            abort(403, 'Akses Ditolak: Halaman Audit Log GPS hanya dapat diakses oleh Super Admin.');
        }

        $instansiId = $user->instansi_id;

        $query = AuditLog::with(['user', 'instansi']);
        if ($instansiId) {
            $query->where(function ($q) use ($instansiId) {
                $q->where('instansi_id', $instansiId)
                  ->orWhereNull('instansi_id');
            });
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Tingkat Risiko
        if ($request->filled('tingkat_risiko')) {
            $query->where('tingkat_risiko', $request->tingkat_risiko);
        }

        // Filter Peserta
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter Rentang Tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        $auditLogs = $query->latest()->paginate(20)->withQueryString();

        // Kartu Statistik
        $totalLogsQuery     = AuditLog::query();
        $totalBahayaQuery   = AuditLog::where('tingkat_risiko', 'bahaya');
        $totalSpoofingQuery = AuditLog::where('kategori', 'gps_spoofing');

        if ($instansiId) {
            $totalLogsQuery->where('instansi_id', $instansiId);
            $totalBahayaQuery->where('instansi_id', $instansiId);
            $totalSpoofingQuery->where('instansi_id', $instansiId);
        }

        $totalLogs     = $totalLogsQuery->count();
        $totalBahaya   = $totalBahayaQuery->count();
        $totalSpoofing = $totalSpoofingQuery->count();

        $pesertasQuery = User::where('role', 'peserta');
        if ($instansiId) {
            $pesertasQuery->where('instansi_id', $instansiId);
        }
        $pesertas = $pesertasQuery->orderBy('name')->get();

        return view('admin_instansi.audit_log', compact(
            'auditLogs',
            'totalLogs',
            'totalBahaya',
            'totalSpoofing',
            'pesertas'
        ));
    }
}

