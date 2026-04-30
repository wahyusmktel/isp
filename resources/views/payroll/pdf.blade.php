<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $payroll->employee?->name }}</title>
    <style>
        @page { size: A4 portrait; margin: 0; }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.5;
            background: #fff;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 0;
            margin: 0 auto;
            position: relative;
        }

        /* ── Accent bar ─────────────────────────────────────── */
        .accent-bar {
            width: 100%;
            height: 7px;
            background: linear-gradient(90deg, #0284c7 0%, #38bdf8 60%, #7dd3fc 100%);
        }

        /* ── Content ────────────────────────────────────────── */
        .content { padding: 30px 40px 24px; }

        /* ── Header ─────────────────────────────────────────── */
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }

        .logo { max-height: 64px; max-width: 180px; }

        .slip-title {
            text-align: right;
        }
        .slip-word {
            font-size: 32px;
            font-weight: 200;
            color: #e2e8f0;
            letter-spacing: 8px;
            text-transform: uppercase;
            line-height: 1;
        }
        .slip-sub {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #0284c7;
            margin-top: 4px;
        }

        /* ── Divider ─────────────────────────────────────────── */
        .divider       { border: none; border-top: 1px solid #e5e7eb; margin: 18px 0; }
        .divider-blue  { border: none; border-top: 2px solid #0284c7; margin: 18px 0; }

        /* ── Period badge ────────────────────────────────────── */
        .period-badge {
            display: inline-block;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 11px;
            font-weight: 700;
            color: #1d4ed8;
            letter-spacing: 0.5px;
        }

        /* ── Employee card ───────────────────────────────────── */
        .emp-table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        .emp-table td { vertical-align: top; }

        .section-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #0284c7;
            margin-bottom: 8px;
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            padding: 14px 16px;
        }
        .emp-name {
            font-size: 17px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 5px;
        }
        .emp-detail {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.9;
        }
        .emp-detail strong { color: #374151; }

        /* ── Salary table ────────────────────────────────────── */
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }
        .salary-table thead th {
            background: #f0f9ff;
            color: #0369a1;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 16px;
            border-bottom: 2px solid #bae6fd;
        }
        .salary-table thead th:first-child { text-align: left; border-radius: 8px 0 0 0; }
        .salary-table thead th:last-child  { text-align: right; border-radius: 0 8px 0 0; }

        .salary-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 12px;
            color: #374151;
        }
        .salary-table tbody td:last-child { text-align: right; }
        .salary-table .row-desc { font-size: 10px; color: #9ca3af; }

        .row-subtotal td {
            background: #fafafa;
            font-weight: 600;
            font-size: 11px;
            color: #374151;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }
        .row-deduction td { color: #dc2626; }
        .row-deduction td:first-child { color: #374151; }

        .row-net td {
            background: #0284c7;
            color: #fff;
            font-weight: 800;
            font-size: 14px;
            border-radius: 0;
            padding-top: 14px;
            padding-bottom: 14px;
        }

        /* ── Status badge ────────────────────────────────────── */
        .status-pill {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .status-paid    { background: #dcfce7; color: #15803d; }
        .status-pending { background: #fef9c3; color: #a16207; }

        /* ── Notes ───────────────────────────────────────────── */
        .notes-box {
            margin-top: 18px;
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 10px;
            color: #92400e;
        }

        /* ── Signature ───────────────────────────────────────── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 36px;
        }
        .sig-table td {
            width: 33.333%;
            text-align: center;
            vertical-align: top;
            padding: 0 16px;
        }
        .sig-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 52px;
        }
        .sig-line {
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
            font-size: 11px;
            font-weight: 600;
            color: #374151;
        }

        /* ── Footer ──────────────────────────────────────────── */
        .footer {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            padding: 0 40px 20px;
        }
        .footer-inner {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            text-align: center;
        }
        .footer-inner p {
            font-size: 9px;
            color: #9ca3af;
            line-height: 1.7;
            margin: 0;
        }
        .footer-brand { color: #0284c7; font-weight: 700; }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

<div class="page">
    <div class="accent-bar"></div>

    <div class="content">

        {{-- ═══ HEADER ═══ --}}
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    @php $logoPath = public_path('logo.png'); @endphp
                    @if(file_exists($logoPath))
                        <img class="logo" src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Logo TiM7 NET">
                    @else
                        <div style="font-size:22px; font-weight:800; color:#0284c7;">TiM7 NET</div>
                    @endif
                    <div style="font-size:10px; color:#6b7280; margin-top:6px; line-height:1.7;">
                        Internet Service Provider<br>
                        Melayani dengan tulus, menghubungkan dengan bangga.
                    </div>
                </td>
                <td style="width: 50%;" class="slip-title">
                    <div class="slip-word">SLIP GAJI</div>
                    <div class="slip-sub">Bukti Pembayaran Gaji Karyawan</div>
                    <div style="margin-top: 10px;">
                        <span class="period-badge">
                            Periode: {{ $payroll->period?->translatedFormat('F Y') ?? $payroll->period?->format('M Y') }}
                        </span>
                    </div>
                    <div style="margin-top: 8px; text-align: right;">
                        <span class="status-pill {{ $payroll->status === 'paid' ? 'status-paid' : 'status-pending' }}">
                            {{ $payroll->status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar' }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <hr class="divider-blue">

        {{-- ═══ EMPLOYEE INFO ═══ --}}
        <table class="emp-table">
            <tr>
                <td style="padding-right: 16px; width: 50%;">
                    <div class="section-label">Data Karyawan</div>
                    <div class="info-card">
                        <div class="emp-name">{{ $payroll->employee?->name ?? '—' }}</div>
                        <div class="emp-detail">
                            No. Karyawan: <strong>{{ $payroll->employee?->employee_number ?? '—' }}</strong><br>
                            Jabatan: <strong>{{ $payroll->employee?->jabatan ?? '—' }}</strong><br>
                            Departemen: <strong>{{ $payroll->employee?->departemen_label ?? '—' }}</strong>
                            @if($payroll->employee?->email)
                                <br>Email: {{ $payroll->employee->email }}
                            @endif
                        </div>
                    </div>
                </td>
                <td style="padding-left: 16px; width: 50%;">
                    <div class="section-label">Informasi Pembayaran</div>
                    <div class="info-card">
                        <div class="emp-detail">
                            Tanggal Cetak: <strong>{{ now()->format('d M Y') }}</strong><br>
                            Periode Gaji: <strong>{{ $payroll->period?->format('F Y') }}</strong><br>
                            @if($payroll->paid_at)
                                Tanggal Bayar: <strong>{{ $payroll->paid_at->format('d M Y') }}</strong><br>
                            @endif
                            Status: <strong>{{ $payroll->status === 'paid' ? 'Lunas' : 'Belum Dibayar' }}</strong>
                            @if($payroll->notes)
                                <br>Catatan: {{ $payroll->notes }}
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- ═══ SALARY BREAKDOWN ═══ --}}
        <table class="salary-table" style="margin-top: 24px;">
            <thead>
                <tr>
                    <th style="text-align: left; width: 60%;">Komponen</th>
                    <th style="text-align: right; width: 40%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                {{-- PENDAPATAN --}}
                <tr>
                    <td colspan="2" style="background: #f8fafc; padding: 8px 16px;">
                        <span style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#0284c7;">Pendapatan</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>Gaji Pokok</div>
                        <div class="row-desc">Gaji dasar sesuai jabatan</div>
                    </td>
                    <td>Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>
                        <div>Tunjangan</div>
                        <div class="row-desc">Tunjangan jabatan / operasional</div>
                    </td>
                    <td>Rp {{ number_format($payroll->allowance, 0, ',', '.') }}</td>
                </tr>
                <tr class="row-subtotal">
                    <td>Total Pendapatan Kotor</td>
                    <td>Rp {{ number_format($payroll->base_salary + $payroll->allowance, 0, ',', '.') }}</td>
                </tr>

                {{-- POTONGAN --}}
                <tr>
                    <td colspan="2" style="background: #f8fafc; padding: 8px 16px;">
                        <span style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#dc2626;">Potongan</span>
                    </td>
                </tr>
                <tr class="row-deduction">
                    <td>
                        <div>Potongan</div>
                        <div class="row-desc">Ketidakhadiran / lain-lain</div>
                    </td>
                    <td>- Rp {{ number_format($payroll->deduction, 0, ',', '.') }}</td>
                </tr>

                {{-- GAJI BERSIH --}}
                <tr class="row-net">
                    <td style="border-radius: 0 0 0 8px;">GAJI BERSIH DITERIMA</td>
                    <td style="border-radius: 0 0 8px 0; font-size: 16px;">
                        Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- ═══ SIGNATURE ═══ --}}
        <table class="sig-table">
            <tr>
                <td>
                    <div class="sig-label">Diterima Oleh,</div>
                    <div class="sig-line">{{ $payroll->employee?->name ?? '—' }}</div>
                </td>
                <td>
                    <div class="sig-label">Dibuat Oleh,</div>
                    <div class="sig-line">HRD / Keuangan</div>
                </td>
                <td>
                    <div class="sig-label">Disetujui Oleh,</div>
                    <div class="sig-line">Direktur</div>
                </td>
            </tr>
        </table>

    </div>

    {{-- ═══ FOOTER ═══ --}}
    <div class="footer">
        <div class="footer-inner">
            <p>
                Dokumen ini merupakan slip gaji resmi yang diterbitkan oleh <span class="footer-brand">TiM7 NET</span>.<br>
                Slip ini sah tanpa tanda tangan basah jika dicetak dari sistem manajemen resmi perusahaan.<br>
                Dicetak pada: {{ now()->format('d M Y, H:i') }} WIB
            </p>
        </div>
    </div>
</div>

</body>
</html>
