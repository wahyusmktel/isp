<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        /* ───── A4 Page Setup ───── */
        @page {
            size: A4 portrait;
            margin: 0;
        }

        /* ───── Reset & Base ───── */
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

        /* ───── Top Accent Bar ───── */
        .accent-bar {
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, #16a34a 0%, #22c55e 50%, #4ade80 100%);
        }

        /* ───── Content Wrapper ───── */
        .content {
            padding: 32px 40px 20px 40px;
        }

        /* ───── Header ───── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .header-table td {
            vertical-align: top;
        }

        .company-name {
            font-size: 26px;
            font-weight: 800;
            color: #16a34a;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .company-tagline {
            font-size: 10px;
            color: #6b7280;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .company-contact {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.8;
        }
        .company-contact span {
            display: inline-block;
            width: 12px;
            color: #16a34a;
            font-weight: bold;
        }

        .invoice-badge {
            text-align: right;
        }
        .invoice-label {
            font-size: 38px;
            font-weight: 200;
            color: #e5e7eb;
            letter-spacing: 6px;
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: 14px;
        }

        /* ───── Invoice Meta ───── */
        .meta-table {
            border-collapse: collapse;
            margin-left: auto;
        }
        .meta-table td {
            padding: 3px 0;
            font-size: 11px;
            vertical-align: top;
        }
        .meta-label {
            color: #6b7280;
            padding-right: 14px;
            white-space: nowrap;
        }
        .meta-value {
            color: #111827;
            font-weight: 600;
            text-align: right;
        }

        /* ───── Status Badge ───── */
        .status-pill {
            display: inline-block;
            padding: 5px 16px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .status-paid      { background: #dcfce7; color: #15803d; }
        .status-unpaid     { background: #fef9c3; color: #a16207; }
        .status-overdue    { background: #fee2e2; color: #b91c1c; }
        .status-cancelled  { background: #f3f4f6; color: #6b7280; }

        /* ───── Divider ───── */
        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 24px 0;
        }
        .divider-thick {
            border: none;
            border-top: 2px solid #16a34a;
            margin: 24px 0;
        }

        /* ───── Client & Service Section ───── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table > tbody > tr > td {
            vertical-align: top;
            width: 50%;
        }

        .info-section-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #16a34a;
            margin-bottom: 10px;
        }
        .info-card {
            background: #f9fafb;
            border-radius: 8px;
            padding: 16px 18px;
            border: 1px solid #f3f4f6;
        }
        .info-name {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }
        .info-detail {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.8;
        }
        .info-detail strong {
            color: #374151;
        }

        /* ───── Items Table ───── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .items-table thead th {
            background: #f0fdf4;
            color: #15803d;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 16px;
            border-bottom: 2px solid #bbf7d0;
        }
        .items-table thead th:first-child {
            text-align: left;
            border-radius: 8px 0 0 0;
        }
        .items-table thead th:last-child {
            text-align: right;
            border-radius: 0 8px 0 0;
        }
        .items-table tbody td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 12px;
            color: #1f2937;
        }
        .items-table .item-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 3px;
        }
        .items-table .item-desc {
            font-size: 10px;
            color: #6b7280;
        }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        /* ───── Totals ───── */
        .totals-wrapper {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-wrapper > tbody > tr > td {
            vertical-align: top;
        }

        .totals-table {
            border-collapse: collapse;
            width: 100%;
        }
        .totals-table td {
            padding: 8px 16px;
            font-size: 11px;
        }
        .totals-table .row-label {
            color: #6b7280;
        }
        .totals-table .row-value {
            text-align: right;
            color: #374151;
            font-weight: 500;
        }
        .totals-table .grand-total td {
            border-top: 2px solid #16a34a;
            padding-top: 12px;
            padding-bottom: 12px;
        }
        .totals-table .grand-total .row-label {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }
        .totals-table .grand-total .row-value {
            font-size: 18px;
            font-weight: 800;
            color: #16a34a;
        }

        /* ───── Payment Info ───── */
        .payment-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 16px 18px;
        }
        .payment-box-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #15803d;
            margin-bottom: 8px;
        }
        .payment-box p {
            font-size: 11px;
            color: #374151;
            line-height: 1.7;
            margin: 0;
        }
        .payment-box .account {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #111827;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        .payment-warning {
            margin-top: 10px;
            font-size: 9px !important;
            color: #dc2626 !important;
            font-style: italic;
        }
        .payment-paid {
            color: #16a34a !important;
            font-weight: 600;
        }

        /* ───── Notes ───── */
        .notes-section {
            margin-top: 20px;
            padding: 14px 18px;
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 8px;
        }
        .notes-section p {
            font-size: 10px;
            color: #92400e;
            line-height: 1.6;
            margin: 0;
        }

        /* ───── Footer ───── */
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 0 40px 24px 40px;
        }
        .footer-inner {
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
            text-align: center;
        }
        .footer-inner p {
            font-size: 9px;
            color: #9ca3af;
            line-height: 1.7;
            margin: 0;
        }
        .footer-inner .footer-brand {
            color: #16a34a;
            font-weight: 700;
        }

        /* ───── Signature Area ───── */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }
        .sig-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 50px;
        }
        .sig-line {
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
            font-size: 11px;
            font-weight: 600;
            color: #374151;
        }
    </style>
</head>
<body>

<div class="page">
    {{-- Green accent bar at top --}}
    <div class="accent-bar"></div>

    <div class="content">

        {{-- ═══ HEADER ═══ --}}
        <table class="header-table">
            <tr>
                <td style="width: 55%;">
                    <div class="company-name">ISP PROVIDER</div>
                    <div class="company-tagline">Internet Service Provider</div>
                    <div class="company-contact">
                        <span>●</span> Jl. Teknologi Digital No. 99, Kota Informatika<br>
                        <span>●</span> Telp: (021) 1234-5678<br>
                        <span>●</span> Email: cs@ispprovider.com<br>
                        <span>●</span> Web: www.ispprovider.com
                    </div>
                </td>
                <td style="width: 45%;" class="invoice-badge">
                    <div class="invoice-label">INVOICE</div>
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">No. Tagihan</td>
                            <td class="meta-value">{{ $invoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Tanggal Terbit</td>
                            <td class="meta-value">{{ $invoice->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Jatuh Tempo</td>
                            <td class="meta-value">{{ $invoice->due_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Status</td>
                            <td class="meta-value">
                                @php
                                    $statusClass = match($invoice->status) {
                                        'paid'      => 'status-paid',
                                        'unpaid'    => 'status-unpaid',
                                        'overdue'   => 'status-overdue',
                                        'cancelled' => 'status-cancelled',
                                        default     => ''
                                    };
                                @endphp
                                <span class="status-pill {{ $statusClass }}">{{ $invoice->status_label }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <hr class="divider-thick">

        {{-- ═══ CLIENT & SERVICE INFO ═══ --}}
        <table class="info-table">
            <tr>
                <td style="padding-right: 16px;">
                    <div class="info-section-label">Tagihan Kepada</div>
                    <div class="info-card">
                        <div class="info-name">{{ $invoice->customer->name }}</div>
                        <div class="info-detail">
                            {{ $invoice->customer->address ?? 'Alamat tidak tersedia' }}<br>
                            Telp/WA: <strong>{{ $invoice->customer->phone }}</strong>
                            @if($invoice->customer->email)
                                <br>Email: {{ $invoice->customer->email }}
                            @endif
                        </div>
                    </div>
                </td>
                <td style="padding-left: 16px;">
                    <div class="info-section-label">Informasi Layanan</div>
                    <div class="info-card">
                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <td style="padding: 3px 0; font-size: 11px; color: #6b7280; width: 80px;">Periode</td>
                                <td style="padding: 3px 0; font-size: 11px; font-weight: 600; color: #111827;">{{ $invoice->billing_period->translatedFormat('F Y') }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 3px 0; font-size: 11px; color: #6b7280;">Paket</td>
                                <td style="padding: 3px 0; font-size: 11px; font-weight: 600; color: #111827;">{{ $invoice->customer->package?->name ?? 'Layanan Internet' }}</td>
                            </tr>
                            @if($invoice->customer->package)
                            <tr>
                                <td style="padding: 3px 0; font-size: 11px; color: #6b7280;">Kecepatan</td>
                                <td style="padding: 3px 0; font-size: 11px; font-weight: 600; color: #111827;">{{ $invoice->customer->package->speed_download }} / {{ $invoice->customer->package->speed_upload }} Mbps</td>
                            </tr>
                            @endif
                            @if($invoice->customer->ip_address)
                            <tr>
                                <td style="padding: 3px 0; font-size: 11px; color: #6b7280;">IP Address</td>
                                <td style="padding: 3px 0; font-size: 11px; font-weight: 600; color: #111827; font-family: 'Courier New', monospace;">{{ $invoice->customer->ip_address }}</td>
                            </tr>
                            @endif
                            @if($invoice->customer->pppoe_user)
                            <tr>
                                <td style="padding: 3px 0; font-size: 11px; color: #6b7280;">PPPoE User</td>
                                <td style="padding: 3px 0; font-size: 11px; font-weight: 600; color: #111827; font-family: 'Courier New', monospace;">{{ $invoice->customer->pppoe_user }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <hr class="divider">

        {{-- ═══ ITEMS TABLE ═══ --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="text-align: left; width: 10%;">No</th>
                    <th style="text-align: left; width: 50%;">Deskripsi Layanan</th>
                    <th class="text-center" style="width: 15%;">Qty</th>
                    <th class="text-right" style="width: 25%;">Nominal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left; color: #6b7280;">1</td>
                    <td>
                        <div class="item-name">Langganan Internet — {{ $invoice->billing_period->translatedFormat('F Y') }}</div>
                        <div class="item-desc">
                            Paket {{ $invoice->customer->package?->name ?? 'Standar' }}
                            @if($invoice->customer->package)
                                ({{ $invoice->customer->package->speed_download }}/{{ $invoice->customer->package->speed_upload }} Mbps)
                            @endif
                        </div>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right" style="font-weight: 600;">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ═══ TOTALS + PAYMENT ═══ --}}
        <table class="totals-wrapper">
            <tr>
                {{-- Payment Info (Left) --}}
                <td style="width: 50%; padding-right: 16px;">
                    <div class="payment-box">
                        @if($invoice->status === 'paid')
                            <div class="payment-box-title">✓ Pembayaran Diterima</div>
                            <p class="payment-paid">
                                Telah dibayar melalui {{ $invoice->payment_method ?? 'Sistem' }}
                                @if($invoice->paid_at)
                                    <br>pada {{ $invoice->paid_at->format('d M Y, H:i') }} WIB
                                @endif
                            </p>
                            <p style="margin-top: 6px;">Terima kasih atas pembayaran Anda. Layanan internet Anda tetap aktif.</p>
                        @else
                            <div class="payment-box-title">Metode Pembayaran</div>
                            @if($invoice->payment_method === 'Tunai')
                                <p><strong>Pembayaran Tunai</strong></p>
                                <p>Silakan melakukan pembayaran tagihan secara tunai melalui loket / kantor resmi kami.</p>
                            @elseif($invoice->payment_method === 'E-Wallet')
                                <p><strong>E-Wallet / QRIS</strong></p>
                                <p>OVO / Dana / Gopay:<br><span class="account">0812-3456-7890</span> a/n PT ISP Provider</p>
                            @else
                                <p><strong>Transfer Rekening Bank</strong></p>
                                <p>BCA: <span class="account">1234-5678-90</span> a/n PT ISP Provider</p>
                                <p>Mandiri: <span class="account">0987-6543-21</span> a/n PT ISP Provider</p>
                            @endif
                            <p class="payment-warning">* Bayar sebelum tanggal jatuh tempo untuk menghindari denda/pemutusan layanan.</p>
                        @endif
                    </div>
                </td>

                {{-- Total Summary (Right) --}}
                <td style="width: 50%; padding-left: 16px;">
                    <table class="totals-table">
                        <tr>
                            <td class="row-label">Subtotal</td>
                            <td class="row-value">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="row-label">Pajak / PPN</td>
                            <td class="row-value">Rp 0</td>
                        </tr>
                        <tr>
                            <td class="row-label">Diskon</td>
                            <td class="row-value">Rp 0</td>
                        </tr>
                        <tr class="grand-total">
                            <td class="row-label">Total Tagihan</td>
                            <td class="row-value">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- ═══ NOTES ═══ --}}
        @if($invoice->notes)
        <div class="notes-section">
            <p><strong>Catatan:</strong> {{ $invoice->notes }}</p>
        </div>
        @endif

        {{-- ═══ SIGNATURE ═══ --}}
        <table class="signature-table">
            <tr>
                <td>
                    <div class="sig-label">Pelanggan</div>
                    <div class="sig-line">{{ $invoice->customer->name }}</div>
                </td>
                <td>
                    <div class="sig-label">Hormat Kami,</div>
                    <div class="sig-line">ISP PROVIDER</div>
                </td>
            </tr>
        </table>

    </div>

    {{-- ═══ FOOTER ═══ --}}
    <div class="footer">
        <div class="footer-inner">
            <p>
                Dokumen ini adalah bukti tagihan resmi yang diterbitkan oleh <span class="footer-brand">ISP PROVIDER</span>.<br>
                Untuk pertanyaan terkait tagihan, hubungi tim dukungan kami di <strong>(021) 1234-5678</strong> atau <strong>cs@ispprovider.com</strong>.<br>
                Terima kasih atas kepercayaan Anda menggunakan layanan kami.
            </p>
        </div>
    </div>
</div>

</body>
</html>
