@php
$fmt = fn($value, $fallback = '-') => filled($value ?? null) ? $value : $fallback;
$moneyDate = now()->format('d M Y H:i');
$rxStyle = function ($category) {
    return match($category) {
        'excellent' => 'background:#15803d;color:#ffffff;font-weight:bold;',
        'good' => 'background:#dcfce7;color:#166534;font-weight:bold;',
        'critical' => 'background:#fee2e2;color:#b91c1c;font-weight:bold;',
        default => 'background:#f3f4f6;color:#4b5563;',
    };
};
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color: #111827; }
        table { border-collapse: collapse; width: 100%; }
        .title { background: #0f172a; color: #ffffff; font-size: 18px; font-weight: bold; height: 34px; }
        .subtitle { background: #e5e7eb; color: #374151; font-size: 11px; height: 24px; }
        .kpi-label { background: #f8fafc; color: #64748b; font-size: 10px; font-weight: bold; text-transform: uppercase; border: 1px solid #dbe3ef; }
        .kpi-value { background: #ffffff; color: #111827; font-size: 16px; font-weight: bold; border: 1px solid #dbe3ef; }
        .legend { font-size: 11px; font-weight: bold; border: 1px solid #d1d5db; }
        .head { background: #166534; color: #ffffff; font-weight: bold; font-size: 11px; border: 1px solid #14532d; }
        .cell { font-size: 11px; border: 1px solid #d1d5db; vertical-align: top; }
        .mono { font-family: Consolas, monospace; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
<table>
    <tr>
        <td colspan="16" class="title">Laporan Monitoring OLT</td>
    </tr>
    <tr>
        <td colspan="16" class="subtitle">Source: {{ $result['base_url'] ?? '-' }} | Export: {{ $moneyDate }}</td>
    </tr>
    <tr><td colspan="16">&nbsp;</td></tr>
    <tr>
        <td class="kpi-label">Total ONU</td>
        <td class="kpi-label">Online</td>
        <td class="kpi-label">Offline</td>
        <td class="kpi-label">Mapped</td>
        <td class="kpi-label">Excellent</td>
        <td class="kpi-label">Good</td>
        <td class="kpi-label">Critical</td>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td class="kpi-value">{{ $stats['total'] }}</td>
        <td class="kpi-value">{{ $stats['online'] }}</td>
        <td class="kpi-value">{{ $stats['offline'] }}</td>
        <td class="kpi-value">{{ $stats['mapped'] }}</td>
        <td class="kpi-value">{{ $stats['rx_excellent'] }}</td>
        <td class="kpi-value">{{ $stats['rx_good'] }}</td>
        <td class="kpi-value">{{ $stats['rx_critical'] }}</td>
        <td colspan="9"></td>
    </tr>
    <tr><td colspan="16">&nbsp;</td></tr>
    <tr>
        <td class="legend" style="background:#15803d;color:#fff;">Excellent -15 s/d -22 dBm</td>
        <td class="legend" style="background:#dcfce7;color:#166534;">Good &lt; -22 s/d -25 dBm</td>
        <td class="legend" style="background:#fee2e2;color:#b91c1c;">Critical &lt; -25 dBm</td>
        <td colspan="13"></td>
    </tr>
    <tr><td colspan="16">&nbsp;</td></tr>
    <tr>
        <td class="head">No</td>
        <td class="head">Port</td>
        <td class="head">ONU ID</td>
        <td class="head">ONU Name</td>
        <td class="head">Pelanggan</td>
        <td class="head">PPPoE</td>
        <td class="head">IP</td>
        <td class="head">MAC Address</td>
        <td class="head">Status</td>
        <td class="head">Kategori Rx</td>
        <td class="head">Rx Power</td>
        <td class="head">Tx Power</td>
        <td class="head">Temperature</td>
        <td class="head">Register Time</td>
        <td class="head">Deregister / Reason</td>
        <td class="head">Source</td>
    </tr>
    @foreach($clients as $index => $client)
    @php $customer = $client['_customer'] ?? null; @endphp
    <tr>
        <td class="cell">{{ $index + 1 }}</td>
        <td class="cell mono">{{ $fmt($client['_port_label'] ?? null) }}</td>
        <td class="cell mono">{{ $fmt($client['id'] ?? null) }}</td>
        <td class="cell">{{ $fmt($client['name'] ?? null, 'Tanpa nama') }}</td>
        <td class="cell">{{ $customer['name'] ?? 'Belum mapped' }}</td>
        <td class="cell mono">{{ $customer['pppoe_user'] ?? '-' }}</td>
        <td class="cell mono">{{ $customer['ip_address'] ?? '-' }}</td>
        <td class="cell mono">{{ $fmt($client['mac_address'] ?? $client['macaddress'] ?? null) }}</td>
        <td class="cell">{{ $fmt($client['status'] ?? null) }}</td>
        <td class="cell" style="{{ $rxStyle($client['_rx_category'] ?? 'unknown') }}">{{ $fmt($client['_rx_label'] ?? null) }}</td>
        <td class="cell mono">{{ $fmt($client['rx_power'] ?? null) }}</td>
        <td class="cell mono">{{ $fmt($client['tx_power'] ?? null) }}</td>
        <td class="cell mono">{{ $fmt($client['temperature'] ?? null) }}</td>
        <td class="cell">{{ $fmt($client['register_time'] ?? null) }}</td>
        <td class="cell">{{ $fmt($client['deregister_time'] ?? null) }} / {{ $fmt($client['offline_reason'] ?? null) }}</td>
        <td class="cell mono">{{ $fmt($client['_source_url'] ?? null) }}</td>
    </tr>
    @endforeach
</table>
</body>
</html>
