<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>ID Card - {{ $employee->name }}</title>
    <style>
        /* CR80 standard: 85.6mm × 54mm — portrait */
        @page {
            size: 54mm 85.6mm;
            margin: 0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            width: 54mm;
            height: 85.6mm;
            font-family: Arial, Helvetica, sans-serif;
            background: #ffffff;
            overflow: hidden;
        }

        .card {
            width: 54mm;
            height: 85.6mm;
            position: relative;
            overflow: hidden;
        }

        /* ── Header (blue top section) ─────── */
        .card-header {
            width: 54mm;
            height: 28mm;
            background-color: {{ $brandColor }};
            text-align: center;
            padding-top: 3mm;
            position: relative;
        }

        /* subtle diagonal stripe pattern */
        .card-header::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: repeating-linear-gradient(
                135deg,
                rgba(255,255,255,0.04) 0px,
                rgba(255,255,255,0.04) 2px,
                transparent 2px,
                transparent 10px
            );
        }

        .logo-wrap {
            height: 10mm;
            display: block;
            text-align: center;
            margin-bottom: 2mm;
            position: relative;
            z-index: 1;
        }

        .logo-img {
            height: 10mm;
            max-width: 32mm;
        }

        .id-label {
            font-size: 5.5pt;
            color: rgba(255,255,255,0.75);
            letter-spacing: 2px;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
        }

        /* ── Avatar ring sits at bottom of header ── */
        .avatar-ring {
            position: absolute;
            bottom: -7mm;
            left: 50%;
            margin-left: -8mm;
            width: 16mm;
            height: 16mm;
            border-radius: 8mm;
            background-color: #ffffff;
            z-index: 10;
            /* ring shadow */
            box-shadow: 0 1mm 4mm rgba(0,0,0,0.18);
        }

        .avatar-inner {
            width: 13mm;
            height: 13mm;
            border-radius: 6.5mm;
            background-color: {{ $avatarBg }};
            margin: 1.5mm auto 0;
            text-align: center;
            line-height: 13mm;
            font-size: 13pt;
            font-weight: 900;
            color: #ffffff;
        }

        /* ── Body ─────────────────────────────── */
        .card-body {
            background: #ffffff;
            padding: 10mm 4mm 3mm;
            text-align: center;
        }

        .emp-name {
            font-size: 9pt;
            font-weight: 900;
            color: #111827;
            line-height: 1.2;
            margin-bottom: 1mm;
        }

        .emp-jabatan {
            font-size: 7pt;
            color: #6b7280;
            margin-bottom: 2mm;
        }

        .dept-badge {
            display: inline-block;
            padding: 0.6mm 3mm;
            border-radius: 3mm;
            font-size: 6pt;
            font-weight: 700;
            background-color: {{ $deptBg }};
            color: {{ $deptColor }};
            letter-spacing: 0.3px;
        }

        /* ── Divider ──────────────────────────── */
        .divider {
            border: none;
            border-top: 0.2mm solid #f1f5f9;
            margin: 3mm 0;
        }

        /* ── Contact row ─────────────────────── */
        .contact-row {
            font-size: 6pt;
            color: #6b7280;
            margin-bottom: 1mm;
            text-align: left;
        }

        /* ── Employee number block ───────────── */
        .emp-number-block {
            background: #f0f9ff;
            border: 0.3mm solid #bae6fd;
            border-radius: 2mm;
            padding: 1.5mm 3mm;
            margin-top: 2mm;
            text-align: left;
        }

        .emp-number-label {
            font-size: 5.5pt;
            color: {{ $brandColor }};
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .emp-number-value {
            font-size: 9.5pt;
            font-weight: 900;
            color: #0c4a6e;
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 0.5px;
        }

        /* ── Footer strip ────────────────────── */
        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 54mm;
            height: 7mm;
            background-color: {{ $brandColor }};
            padding: 0 4mm;
        }

        .footer-text {
            font-size: 5.5pt;
            color: rgba(255,255,255,0.8);
            letter-spacing: 0.4px;
            line-height: 7mm;
        }

        .footer-dot {
            color: rgba(255,255,255,0.5);
        }
    </style>
</head>
<body>

@php
$logoPath = public_path('logo.png');
@endphp

<div class="card">

    {{-- ── HEADER ── --}}
    <div class="card-header">
        <div class="logo-wrap">
            @if(file_exists($logoPath))
                <img class="logo-img" src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="TiM7 NET">
            @else
                <span style="font-size:12pt; font-weight:900; color:#fff; letter-spacing:-0.5px;">TiM7 NET</span>
            @endif
        </div>
        <div class="id-label">ID Karyawan</div>

        {{-- Avatar ring at bottom of header --}}
        <div class="avatar-ring">
            <div class="avatar-inner">
                {{ strtoupper(mb_substr($employee->name, 0, 1)) }}
            </div>
        </div>
    </div>

    {{-- ── BODY ── --}}
    <div class="card-body">
        <div class="emp-name">{{ $employee->name }}</div>
        <div class="emp-jabatan">{{ $employee->jabatan }}</div>
        <span class="dept-badge">{{ $employee->departemen_label }}</span>

        <hr class="divider">

        {{-- Contact info --}}
        @if($employee->phone)
        <div class="contact-row">
            <span style="color:{{ $brandColor }}; font-weight:700;">&#9742;</span>&nbsp;{{ $employee->phone }}
        </div>
        @endif
        @if($employee->email)
        <div class="contact-row" style="overflow:hidden;">
            <span style="color:{{ $brandColor }}; font-weight:700;">@</span>&nbsp;{{ Str::limit($employee->email, 26) }}
        </div>
        @endif

        {{-- Employee number --}}
        <div class="emp-number-block">
            <div class="emp-number-label">No. Karyawan</div>
            <div class="emp-number-value">{{ $employee->employee_number }}</div>
        </div>

        @if($employee->join_date)
        <div style="font-size:5.5pt; color:#9ca3af; margin-top:1.5mm; text-align:center;">
            Bergabung sejak {{ $employee->join_date->format('d M Y') }}
        </div>
        @endif
    </div>

    {{-- ── FOOTER STRIP ── --}}
    <div class="card-footer">
        <span class="footer-text">
            TiM7 NET <span class="footer-dot">&#9679;</span> Internet Service Provider
        </span>
    </div>

</div>

</body>
</html>
