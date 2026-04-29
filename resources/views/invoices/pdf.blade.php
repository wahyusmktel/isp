<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        /* Base Styles */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            width: 100%;
            border-bottom: 2px solid #16a34a; /* Green brand color */
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header td {
            vertical-align: top;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #16a34a;
            margin: 0 0 5px 0;
            letter-spacing: -0.5px;
        }
        .company-details {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }
        .invoice-title {
            font-size: 36px;
            font-weight: 300;
            color: #111827;
            text-align: right;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-meta p {
            margin: 3px 0;
            font-size: 13px;
        }
        .invoice-meta strong {
            display: inline-block;
            width: 90px;
            color: #374151;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
            float: right;
        }
        .status-paid { background: #dcfce7; color: #16a34a; }
        .status-unpaid { background: #fef3c7; color: #d97706; }
        .status-overdue { background: #fee2e2; color: #dc2626; }
        .status-cancelled { background: #f3f4f6; color: #4b5563; }

        /* Client Details */
        .client-section {
            width: 100%;
            margin-bottom: 40px;
        }
        .client-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .client-section td {
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .client-info p {
            margin: 3px 0;
            font-size: 14px;
            color: #374151;
        }
        .client-name {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 5px;
        }

        /* Table Items */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f9fafb;
            color: #374151;
            font-size: 12px;
            text-transform: uppercase;
            padding: 12px 15px;
            text-align: left;
            border-bottom: 2px solid #e5e7eb;
        }
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
            font-size: 14px;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .item-desc {
            font-weight: 500;
            margin: 0 0 4px 0;
        }
        .item-sub {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }

        /* Totals */
        .totals-table {
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 10px 15px;
            font-size: 14px;
            color: #374151;
        }
        .totals-table .total-row td {
            border-top: 2px solid #111827;
            font-weight: bold;
            color: #111827;
            font-size: 18px;
        }

        /* Footer Notes */
        .notes-box {
            background-color: #f9fafb;
            border-left: 4px solid #16a34a;
            padding: 15px;
            border-radius: 0 4px 4px 0;
        }
        .notes-box p {
            margin: 0;
            font-size: 13px;
            color: #4b5563;
            line-height: 1.6;
        }
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* Footer */
        .footer {
            margin-top: 80px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

<div class="container">
    {{-- Header --}}
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="company-name">ISP PROVIDER</h1>
                    <div class="company-details">
                        <p style="margin:0;">Jl. Teknologi Digital No. 99, Kota Informatika</p>
                        <p style="margin:0;">Telp: (021) 1234-5678 | Email: cs@ispprovider.com</p>
                        <p style="margin:0;">Web: www.ispprovider.com</p>
                    </div>
                </td>
                <td style="text-align: right;">
                    <h2 class="invoice-title">INVOICE</h2>
                    <div class="invoice-meta">
                        <p><strong>No. Tagihan:</strong> {{ $invoice->invoice_number }}</p>
                        <p><strong>Tgl. Terbit:</strong> {{ $invoice->created_at->format('d M Y') }}</p>
                        <p><strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d M Y') }}</p>
                    </div>
                    @php
                        $statusClass = match($invoice->status) {
                            'paid' => 'status-paid',
                            'unpaid' => 'status-unpaid',
                            'overdue' => 'status-overdue',
                            'cancelled' => 'status-cancelled',
                            default => ''
                        };
                    @endphp
                    <div class="status-badge {{ $statusClass }}">
                        {{ $invoice->status_label }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Client Details --}}
    <div class="client-section">
        <table>
            <tr>
                <td>
                    <div class="section-title">Tagihan Kepada:</div>
                    <div class="client-info">
                        <h3 class="client-name">{{ $invoice->customer->name }}</h3>
                        <p>{{ $invoice->customer->address ?? 'Alamat tidak tersedia' }}</p>
                        <p>Telp/WA: {{ $invoice->customer->phone }}</p>
                        @if($invoice->customer->email)
                            <p>Email: {{ $invoice->customer->email }}</p>
                        @endif
                    </div>
                </td>
                <td style="text-align: right;">
                    <div class="section-title">Informasi Layanan:</div>
                    <div class="client-info" style="text-align: right;">
                        <p><strong>Periode:</strong> {{ $invoice->billing_period->format('F Y') }}</p>
                        <p><strong>Paket:</strong> {{ $invoice->customer->package?->name ?? 'Layanan Internet' }}</p>
                        @if($invoice->customer->ip_address)
                            <p><strong>IP:</strong> {{ $invoice->customer->ip_address }}</p>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th>Deskripsi Layanan</th>
                <th class="text-center" style="width: 15%;">Jumlah</th>
                <th class="text-right" style="width: 25%;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <p class="item-desc">Langganan Internet - Periode {{ $invoice->billing_period->format('M Y') }}</p>
                    <p class="item-sub">Paket: {{ $invoice->customer->package?->name ?? 'Layanan Standar' }}</p>
                </td>
                <td class="text-center">1</td>
                <td class="text-right">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Totals and Notes --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr>
            <td style="width: 55%; vertical-align: top; padding-right: 20px;">
                <div class="section-title">Metode Pembayaran</div>
                <div class="notes-box">
                    @if($invoice->status === 'paid')
                        <p style="color: #16a34a; font-weight: bold; margin-bottom: 5px;">Telah dibayar melalui {{ $invoice->payment_method ?? 'Sistem' }}</p>
                        <p>Terima kasih atas pembayaran Anda. Layanan Anda tetap aktif.</p>
                    @else
                        @if($invoice->payment_method === 'Tunai')
                            <p style="font-weight: bold; margin-bottom: 5px;">Pembayaran Tunai:</p>
                            <p>Silakan melakukan pembayaran tagihan secara tunai melalui loket / kantor resmi kami.</p>
                        @elseif($invoice->payment_method === 'E-Wallet')
                            <p style="font-weight: bold; margin-bottom: 5px;">Pembayaran E-Wallet / QRIS:</p>
                            <p>OVO / Dana / Gopay: <strong>0812-3456-7890</strong> a/n PT ISP Provider</p>
                        @else
                            <p style="font-weight: bold; margin-bottom: 5px;">Transfer Rekening Bank:</p>
                            <p>BCA: <strong>1234-5678-90</strong> a/n PT ISP Provider</p>
                            <p>Mandiri: <strong>0987-6543-21</strong> a/n PT ISP Provider</p>
                        @endif
                        <p style="margin-top: 8px; font-size: 11px; color: #dc2626;">* Lakukan pembayaran sebelum tanggal jatuh tempo untuk menghindari denda/pemutusan.</p>
                    @endif
                    @if($invoice->notes)
                        <p style="margin-top: 10px; font-style: italic;">Catatan: {{ $invoice->notes }}</p>
                    @endif
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <table class="totals-table" style="width: 100%; float: none;">
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-right">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Pajak / PPN</td>
                        <td class="text-right">Rp 0</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total Tagihan</td>
                        <td class="text-right">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini adalah bukti tagihan resmi dari ISP PROVIDER. Jika ada pertanyaan terkait tagihan ini, silakan hubungi tim dukungan kami.</p>
        <p style="font-weight: bold; margin-top: 5px;">Terima kasih atas kepercayaan Anda menggunakan layanan kami!</p>
    </div>
</div>

</body>
</html>
