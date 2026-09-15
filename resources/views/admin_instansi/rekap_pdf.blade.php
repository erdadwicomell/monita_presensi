<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Presensi Peserta Magang</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 11px;
            line-height: 1.4;
            padding: 30px;
        }

        /* HEADER DOKUMEN (TANPA KOP SURAT) */
        .doc-header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .doc-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .doc-subtitle {
            font-size: 12px;
            font-weight: 600;
            color: #4b5563;
        }

        /* META INFO */
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .meta-table td {
            padding: 3px 0;
            font-size: 10.5px;
        }

        .meta-label {
            width: 130px;
            color: #6b7280;
            font-weight: 600;
        }

        .meta-value {
            color: #111827;
            font-weight: bold;
        }

        /* STATS BOX */
        .stats-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 18px;
            width: 100%;
        }

        .stats-item {
            display: inline-block;
            width: 24%;
            text-align: center;
        }

        .stats-number {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
        }

        .stats-text {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* DATA TABLE */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 7px;
            font-size: 9.5px;
            text-align: left;
        }

        table.data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5px;
            letter-spacing: 0.3px;
        }

        table.data-table tr:nth-child(even) td {
            background-color: #fafafa;
        }

        .badge-hadir {
            color: #065f46;
            font-weight: bold;
        }

        .badge-telat {
            color: #b45309;
            font-weight: bold;
        }

        .badge-masuk {
            color: #1d4ed8;
            font-weight: bold;
        }

        .badge-pulang {
            color: #7e22ce;
            font-weight: bold;
        }

        /* TANDA TANGAN */
        .signature-table {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            font-size: 10.5px;
        }

        .sign-box {
            text-align: center;
        }

        .sign-space {
            height: 60px;
        }

        .sign-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .sign-role {
            font-size: 9.5px;
            color: #6b7280;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <!-- DOCUMENT HEADER -->
    <div class="doc-header">
        <div class="doc-title">Rekapitulasi Presensi Kehadiran Peserta Magang</div>
        <div class="doc-subtitle">{{ $instansi->nama_instansi ?? 'Sistem Monitoring Magang (MONITA)' }}</div>
    </div>

    <!-- META INFORMATION -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Periode Laporan</td>
            <td>: <span class="meta-value">{{ $periodeText }}</span></td>
            <td class="meta-label">Dicetak Pada</td>
            <td>: <span class="meta-value">{{ now()->format('d F Y, H:i') }} WIB</span></td>
        </tr>
        <tr>
            <td class="meta-label">Target Peserta</td>
            <td>: <span class="meta-value">{{ $selectedPeserta ? $selectedPeserta->name . ' (NIM: ' . ($selectedPeserta->nim ?? '-') . ')' : 'Semua Peserta Magang' }}</span></td>
            <td class="meta-label">Petugas Pencetak</td>
            <td>: <span class="meta-value">{{ $user->name ?? 'Admin Instansi' }}</span></td>
        </tr>
    </table>

    <!-- STATISTIC SUMMARY BOX -->
    <div class="stats-box">
        <div class="stats-item">
            <div class="stats-number">{{ $totalData }}</div>
            <div class="stats-text">Total Presensi</div>
        </div>
        <div class="stats-item">
            <div class="stats-number" style="color: #059669;">{{ $totalHadir }}</div>
            <div class="stats-text">Hadir Tepat Waktu</div>
        </div>
        <div class="stats-item">
            <div class="stats-number" style="color: #d97706;">{{ $totalTelat }}</div>
            <div class="stats-text">Terlambat</div>
        </div>
        <div class="stats-item">
            <div class="stats-number" style="color: #2563eb;">
                {{ $totalData > 0 ? round(($totalHadir / $totalData) * 100, 1) : 0 }}%
            </div>
            <div class="stats-text">Tingkat Ketepatan</div>
        </div>
    </div>

    <!-- DATA TABLE -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th>Nama Peserta</th>
                <th style="width: 65px;">Tanggal</th>
                <th style="width: 45px;">Jam</th>
                <th style="width: 45px;">Sesi</th>
                <th style="width: 45px;">Status</th>
                <th>Keterangan Izin</th>
                <th style="width: 45px;">Radius</th>
                <th style="width: 50px;">HMAC</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap as $item)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $item->user->name ?? '-' }}</strong>
                        @if($item->user && $item->user->nim)
                            <br><span style="color: #64748b; font-size: 8px;">NIM: {{ $item->user->nim }}</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ substr($item->jam, 0, 5) }}</td>
                    <td>
                        @if($item->tipe_absensi === 'masuk')
                            <span class="badge-masuk">Masuk</span>
                        @else
                            <span class="badge-pulang">Pulang</span>
                        @endif
                    </td>
                    <td>
                        @if($item->status === 'hadir')
                            <span class="badge-hadir">Hadir</span>
                        @else
                            <span class="badge-telat">Telat</span>
                        @endif
                    </td>
                    <td>
                        @if($item->perizinan)
                            <span style="color: #047857; font-weight: 600;">
                                Izin {{ ucfirst(str_replace('_', ' ', $item->perizinan->jenis_izin)) }}
                            </span>
                            <br><span style="color: #64748b; font-size: 8px;">{{ $item->perizinan->alasan }}</span>
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </td>
                    <td>{{ round($item->jarak, 1) }}m</td>
                    <td>
                        @if($item->hmac_signature)
                            <span style="color: #059669; font-weight: bold;">Valid</span>
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #9ca3af; padding: 20px;">
                        Tidak ada data presensi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SIGNATURE SECTION -->
    <table class="signature-table">
        <tr>
            <td class="sign-box">
                <p>Mengetahui,</p>
                <p class="sign-role">Peserta Magang Terkait</p>
                <div class="sign-space"></div>
                <p class="sign-name">{{ $selectedPeserta ? $selectedPeserta->name : '( ........................................ )' }}</p>
                <p class="sign-role">{{ $selectedPeserta && $selectedPeserta->nim ? 'NIM: ' . $selectedPeserta->nim : 'Peserta' }}</p>
            </td>
            <td class="sign-box">
                <p>{{ $instansi->nama_instansi ?? 'Instansi' }}, {{ now()->format('d F Y') }}</p>
                <p class="sign-role">Pembimbing / Admin Instansi</p>
                <div class="sign-space"></div>
                <p class="sign-name">{{ $user->name ?? 'Pembimbing Instansi' }}</p>
                <p class="sign-role">NIP / Identitas Pembimbing</p>
            </td>
        </tr>
    </table>

</body>
</html>
