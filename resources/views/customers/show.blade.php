@extends('layouts.app')
@section('title', 'Detail Pelanggan')
@section('page-title', 'Detail Pelanggan')

@section('content')

{{-- Header --}}
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('customers.index') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-500 hover:text-gray-900 shadow-sm border border-gray-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ $customer->name }}</h1>
        <p class="text-sm text-gray-400 mt-0.5">ID: {{ $customer->customer_number ?? '-' }} | Pemantauan Trafik dan Detail Pelanggan</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kolom Kiri: Info Pelanggan --}}
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Informasi Identitas</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Status Layanan</p>
                    @if($customer->status === 'aktif')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                        </span>
                    @elseif($customer->status === 'suspend')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Suspend
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Terminate
                        </span>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Email</p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Nomor Telepon / WhatsApp</p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->phone }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Alamat Pemasangan</p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->address }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Tanggal Bergabung</p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->join_date?->format('d M Y') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Layanan Jaringan</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Paket Internet</p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->package?->name ?? 'Belum ada paket' }}</p>
                </div>
                @if($customer->package)
                <div>
                    <p class="text-xs text-gray-400 mb-1">Kecepatan</p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->package->speed_download }} Mbps (DL) / {{ $customer->package->speed_upload }} Mbps (UL)</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-400 mb-1">IP Address</p>
                    <p class="text-sm font-medium text-gray-900 font-mono">{{ $customer->ip_address ?? 'Dynamic/DHCP' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">PPPoE Username</p>
                    <p class="text-sm font-medium text-gray-900 font-mono">{{ $customer->pppoe_user ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">ONU / SN / Modem ID</p>
                    <p class="text-sm font-medium text-gray-900 font-mono">{{ $customer->onu_id ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">ACS Device ID</p>
                    <p class="text-sm font-medium text-gray-900 font-mono break-all">{{ $customer->acs_device_id ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">SSID WiFi Tercatat</p>
                    <p id="wifi-ssid-display" class="text-sm font-medium text-gray-900">{{ $customer->wifi_ssid ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Uptime Koneksi</p>
                    <p id="uptime-display" class="text-sm font-medium text-gray-900">
                        @if(empty($customer->pppoe_user))
                            <span class="text-gray-400">—</span>
                        @else
                            <span class="text-gray-400">Memuat...</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Manajemen WiFi ONT</h3>
            @if(empty($customer->acs_device_id))
                <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl p-4">
                    Isi ACS Device ID pelanggan ini dari halaman edit pelanggan sebelum mengirim perintah TR-069.
                </div>
            @endif
            <form id="wifi-form" class="space-y-4 mt-{{ empty($customer->acs_device_id) ? '4' : '0' }}">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama WiFi / SSID</label>
                    <input type="text" name="ssid" value="{{ $customer->wifi_ssid ?? '' }}" maxlength="32" required
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password WiFi Baru</label>
                    <input type="password" name="password" minlength="8" maxlength="63" required
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                    <p class="text-[10px] text-gray-400 mt-1">WPA/WPA2 umumnya membutuhkan 8-63 karakter.</p>
                </div>
                <button type="submit" id="wifi-save-btn"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-500 rounded-xl transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                        {{ empty($customer->acs_device_id) ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                    <span id="wifi-save-text">Kirim Perintah Ubah WiFi</span>
                </button>
                <p id="wifi-message" class="hidden text-xs rounded-xl px-3 py-2"></p>
            </form>
        </div>
    </div>

    {{-- Kolom Kanan: Monitoring Chart --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Realtime Traffic Monitoring --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Monitoring Trafik Realtime</h3>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-600 uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Live
                </span>
            </div>
            @if(empty($customer->pppoe_user))
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded-lg p-4 mb-4">
                    <strong>Perhatian:</strong> Pelanggan ini belum dimaping PPPoE akun nya. Tidak dapat menampilkan traffic real-time dari router.
                </div>
            @endif
            <div class="h-64 w-full relative">
                <canvas id="trafficChart"></canvas>
            </div>
            <div class="flex gap-6 mt-4 pt-4 border-t border-gray-50 justify-center">
                <div class="text-center">
                    <p class="text-xs text-gray-400 mb-1">Download</p>
                    <p class="text-lg font-bold text-green-500" id="current-rx">0.0 Mbps</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-400 mb-1">Upload</p>
                    <p class="text-lg font-bold text-blue-500" id="current-tx">0.0 Mbps</p>
                </div>
            </div>
        </div>

        {{-- Uptime Data Usage --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Penggunaan Data Selama Uptime</h3>
            <div class="h-64 w-full relative">
                <canvas id="usageChart"></canvas>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function formatUptime(raw) {
    if (!raw) return '—';
    const m = raw.match(/(?:(\d+)w)?(?:(\d+)d)?(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?/);
    if (!m) return raw;
    const parts = [];
    if (m[1]) parts.push(m[1] + ' minggu');
    if (m[2]) parts.push(m[2] + ' hari');
    if (m[3]) parts.push(m[3] + ' jam');
    if (m[4]) parts.push(m[4] + ' menit');
    if (m[5]) parts.push(m[5] + ' detik');
    return parts.length ? parts.join(' ') : '—';
}

document.addEventListener("DOMContentLoaded", function() {
    const wifiForm = document.getElementById('wifi-form');
    const wifiMessage = document.getElementById('wifi-message');
    const wifiBtn = document.getElementById('wifi-save-btn');
    const wifiBtnText = document.getElementById('wifi-save-text');

    function showWifiMessage(type, message) {
        wifiMessage.textContent = message;
        wifiMessage.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'bg-red-50', 'text-red-700');
        if (type === 'success') {
            wifiMessage.classList.add('bg-green-50', 'text-green-700');
        } else {
            wifiMessage.classList.add('bg-red-50', 'text-red-700');
        }
    }

    wifiForm?.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!wifiBtn || wifiBtn.disabled) return;

        wifiBtn.disabled = true;
        wifiBtnText.textContent = 'Mengirim ke GenieACS...';
        wifiMessage.classList.add('hidden');

        try {
            const response = await fetch('{{ route('customers.wifi.update', $customer->id) }}', {
                method: 'POST',
                body: new FormData(wifiForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showWifiMessage('success', data.message);
                document.getElementById('wifi-ssid-display').textContent = data.wifi_ssid || '-';
                wifiForm.querySelector('[name="password"]').value = '';
            } else {
                showWifiMessage('error', data.message || 'Gagal mengirim perintah ubah WiFi.');
            }
        } catch (err) {
            showWifiMessage('error', 'Koneksi ke aplikasi bermasalah.');
        } finally {
            wifiBtn.disabled = {{ empty($customer->acs_device_id) ? 'true' : 'false' }};
            wifiBtnText.textContent = 'Kirim Perintah Ubah WiFi';
        }
    });
    // ─── Setup Traffic Chart (Live Simulation) ───
    const ctxTraffic = document.getElementById('trafficChart').getContext('2d');
    
    // Gradient configs
    const gradientRx = ctxTraffic.createLinearGradient(0, 0, 0, 300);
    gradientRx.addColorStop(0, 'rgba(34, 197, 94, 0.4)');
    gradientRx.addColorStop(1, 'rgba(34, 197, 94, 0.0)');
    
    const gradientTx = ctxTraffic.createLinearGradient(0, 0, 0, 300);
    gradientTx.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
    gradientTx.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    const maxSpeedDL = {{ $customer->package ? $customer->package->speed_download : 20 }};
    const maxSpeedUL = {{ $customer->package ? $customer->package->speed_upload : 10 }};
    
    let labels = Array.from({length: 20}, (_, i) => i);
    let dataRx = Array.from({length: 20}, () => 0);
    let dataTx = Array.from({length: 20}, () => 0);

    const trafficChart = new Chart(ctxTraffic, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Download (Mbps)',
                    data: dataRx,
                    borderColor: '#22c55e',
                    backgroundColor: gradientRx,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                },
                {
                    label: 'Upload (Mbps)',
                    data: dataTx,
                    borderColor: '#3b82f6',
                    backgroundColor: gradientTx,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 500,
                easing: 'linear'
            },
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleFont: { size: 11 },
                    bodyFont: { size: 12 },
                    padding: 10,
                    displayColors: true,
                }
            },
            scales: {
                x: {
                    display: false, // hide x axis ticks to simulate moving window easily
                },
                y: {
                    beginAtZero: true,
                    max: maxSpeedDL > maxSpeedUL ? maxSpeedDL + 5 : maxSpeedUL + 5,
                    grid: { color: '#f3f4f6' },
                    border: { display: false },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 10 }
                    }
                }
            }
        }
    });

    let usageChartInstance = null;

    async function fetchLiveTraffic() {
        try {
            const res = await fetch(`/customers/{{ $customer->id }}/live-traffic`);
            const data = await res.json();
            
            if (!data.success) {
                document.getElementById('current-rx').textContent = '-';
                document.getElementById('current-tx').textContent = '-';
                document.getElementById('uptime-display').textContent = '—';
                return;
            }

            // Update uptime
            const uptimeEl = document.getElementById('uptime-display');
            if (data.uptime) {
                uptimeEl.textContent = formatUptime(data.uptime);
            } else {
                uptimeEl.textContent = 'Offline';
            }

            const rx = data.rx;
            const tx = data.tx;
            
            document.getElementById('current-rx').textContent = rx + ' Mbps';
            document.getElementById('current-tx').textContent = tx + ' Mbps';

            trafficChart.data.datasets[0].data.push(rx);
            trafficChart.data.datasets[1].data.push(tx);
            trafficChart.data.datasets[0].data.shift();
            trafficChart.data.datasets[1].data.shift();
            trafficChart.update('quiet');

            // Update usage chart data if not already set or we want to re-render
            if (!usageChartInstance && data.usage) {
                usageChartInstance = new Chart(document.getElementById('usageChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Download (GB)', 'Upload (GB)'],
                        datasets: [{
                            data: [data.usage.download_gb, data.usage.upload_gb],
                            backgroundColor: ['#22c55e', '#3b82f6'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { font: { size: 12 } } },
                            tooltip: { callbacks: { label: (c) => c.label + ': ' + c.raw + ' GB' } }
                        },
                        cutout: '70%'
                    }
                });
            } else if (usageChartInstance && data.usage) {
                usageChartInstance.data.datasets[0].data = [data.usage.download_gb, data.usage.upload_gb];
                usageChartInstance.update('quiet');
            }
        } catch (err) {
            console.error('Failed to fetch traffic data', err);
        }
    }

    @if(!empty($customer->pppoe_user))
        // Fetch API every 2 seconds if PPPoE is mapped
        setInterval(fetchLiveTraffic, 2000);
        fetchLiveTraffic();
    @endif
});
</script>
@endpush
