@extends('layouts.app')
@section('title', 'PPPoE Mapping')
@section('page-title', 'PPPoE Mapping')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">PPPoE Mapping</h1>
        <p class="text-sm text-gray-400 mt-0.5">Hubungkan akun PPPoE dari router Mikrotik dengan data pelanggan</p>
    </div>
</div>

{{-- Step 1: Router Selector --}}
<div class="bg-white rounded-2xl border border-gray-100 p-5 mb-5">
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">1. Pilih Router</p>
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <select id="router-select" class="inp w-full sm:w-72">
            <option value="">— Pilih Router —</option>
            @foreach($routers as $r)
            <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->host }})</option>
            @endforeach
        </select>
        <button onclick="fetchSecrets()" id="btn-fetch"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors disabled:opacity-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span id="btn-fetch-label">Ambil Data PPPoE</span>
        </button>
        <div id="fetch-status" class="text-sm text-gray-400 hidden"></div>
    </div>
</div>

{{-- Stats --}}
<div id="stats-panel" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5 hidden">
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
        <div><p class="text-xl font-bold text-gray-900" id="stat-total">0</p><p class="text-xs text-gray-500">Total PPPoE</p></div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div><p class="text-xl font-bold text-gray-900" id="stat-online">0</p><p class="text-xs text-gray-500">Online</p></div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center"><svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg></div>
        <div><p class="text-xl font-bold text-gray-900" id="stat-mapped">0</p><p class="text-xs text-gray-500">Sudah Mapped</p></div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center"><svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg></div>
        <div><p class="text-xl font-bold text-gray-900" id="stat-unmapped">0</p><p class="text-xs text-gray-500">Belum Mapped</p></div>
    </div>
</div>

{{-- Step 2: Mapping Table --}}
<div id="mapping-panel" class="hidden">
    {{-- Toolbar --}}
    <div class="bg-white rounded-2xl border border-gray-100 px-4 py-3 mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-72">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="pppoe-search" placeholder="Cari username PPPoE..." class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none flex-1" oninput="filterTable()">
        </div>
        <div class="flex items-center gap-2 ml-auto">
            <select id="filter-status" onchange="filterTable()" class="inp text-xs py-2 px-3">
                <option value="all">Semua</option>
                <option value="mapped">Sudah Mapped</option>
                <option value="unmapped">Belum Mapped</option>
                <option value="online">Online</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="mapping-table">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-3">Username PPPoE</th>
                        <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden md:table-cell">Profile</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">IP / Uptime</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">MAC ONT</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Pelanggan</th>
                        <th class="text-right text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="mapping-tbody">
                    <tr><td colspan="7" class="px-5 py-16 text-center text-gray-400">Pilih router dan klik "Ambil Data PPPoE" untuk memulai</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Empty state (before fetch) --}}
<div id="empty-state" class="bg-white rounded-2xl border border-gray-100 p-16">
    <div class="flex flex-col items-center gap-3 text-center">
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center">
            <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
        </div>
        <p class="font-semibold text-gray-700">PPPoE Mapping</p>
        <p class="text-sm text-gray-400 max-w-sm">Pilih router di atas untuk mengambil daftar akun PPPoE, lalu hubungkan dengan pelanggan yang sudah terdaftar.</p>
    </div>
</div>

{{-- Toast --}}
<div id="toast" class="fixed bottom-5 right-5 z-[999] hidden">
    <div id="toast-inner" class="flex items-center gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm font-medium text-white min-w-[240px] max-w-sm">
        <svg id="toast-icon" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"></svg>
        <span id="toast-msg"></span>
    </div>
</div>

{{-- Data --}}
<script>
const CUSTOMERS = @json($customers->map(fn($c) => $c->toJsonData()));
const CSRF = '{{ csrf_token() }}';
let SECRETS = [];
let currentRouterId = null;

// Toast
let toastTimer;
function showToast(msg, type = 'success') {
    clearTimeout(toastTimer);
    const el = document.getElementById('toast');
    const inner = document.getElementById('toast-inner');
    const icon = document.getElementById('toast-icon');
    document.getElementById('toast-msg').textContent = msg;
    inner.className = `flex items-center gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm font-medium text-white min-w-[240px] max-w-sm ${type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
    icon.innerHTML = type === 'success'
        ? '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        : '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>';
    el.classList.remove('hidden');
    toastTimer = setTimeout(() => el.classList.add('hidden'), 3500);
}

// Fetch PPPoE secrets from selected router
async function fetchSecrets() {
    const routerId = document.getElementById('router-select').value;
    if (!routerId) { showToast('Pilih router terlebih dahulu', 'error'); return; }

    const btn = document.getElementById('btn-fetch');
    const label = document.getElementById('btn-fetch-label');
    btn.disabled = true;
    label.textContent = 'Mengambil data...';

    try {
        const res = await fetch(`/pppoe-mapping/${routerId}/secrets`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();

        if (!data.success) { showToast(data.message, 'error'); return; }

        currentRouterId = routerId;
        SECRETS = data.secrets;
        renderTable();
        updateStats();

        document.getElementById('empty-state').classList.add('hidden');
        document.getElementById('mapping-panel').classList.remove('hidden');
        document.getElementById('stats-panel').classList.remove('hidden');

        showToast(`${SECRETS.length} akun PPPoE ditemukan dari ${data.router_name}`, 'success');
    } catch (err) {
        showToast('Gagal mengambil data: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        label.textContent = 'Ambil Data PPPoE';
    }
}

function getMappedCustomer(username) {
    return CUSTOMERS.find(c => c.pppoe_user === username);
}

function renderTable() {
    const tbody = document.getElementById('mapping-tbody');
    if (!SECRETS.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-16 text-center text-gray-400">Tidak ada akun PPPoE di router ini</td></tr>';
        return;
    }

    tbody.innerHTML = SECRETS.map((s, i) => {
        const mapped = getMappedCustomer(s.username);
        const onlineClass = s.online
            ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500';
        const onlineDot = s.online ? 'bg-green-500' : 'bg-gray-400';
        const onlineLabel = s.online ? 'Online' : 'Offline';

        const customerCell = mapped
            ? `<div class="flex items-center gap-2">
                   <div class="w-7 h-7 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center flex-shrink-0">
                       <span class="text-white text-[10px] font-bold">${mapped.name.charAt(0).toUpperCase()}</span>
                   </div>
                   <div>
                       <p class="font-semibold text-gray-900 text-xs">${mapped.name}</p>
                       <p class="text-[10px] text-gray-400">${mapped.package_name}</p>
                   </div>
               </div>`
            : `<select id="cust-sel-${i}" class="inp text-xs py-1.5 px-2 w-full max-w-[200px]"
                       onchange="onCustSelect(${i})">
                   <option value="">— Pilih Pelanggan —</option>
                   ${CUSTOMERS.filter(c => !c.pppoe_user).map(c =>
                       `<option value="${c.id}">${c.name}</option>`
                   ).join('')}
               </select>`;

        const actionCell = mapped
            ? `<button onclick="unmapCustomer(${mapped.id}, '${s.username}')" title="Hapus Mapping"
                       class="p-1.5 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                   </svg>
               </button>`
            : `<button onclick="mapSelected(${i})" id="map-btn-${i}" title="Map ke Pelanggan" disabled
                       class="p-1.5 rounded-lg text-gray-400 hover:bg-green-50 hover:text-green-600 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                   </svg>
               </button>`;

        return `<tr class="pppoe-row border-b border-gray-50 hover:bg-gray-50/50 transition-colors"
                    data-username="${s.username.toLowerCase()}" data-mapped="${mapped ? '1' : '0'}" data-online="${s.online ? '1' : '0'}">
            <td class="px-5 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl ${mapped ? 'bg-green-50' : 'bg-gray-100'} flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 ${mapped ? 'text-green-600' : 'text-gray-400'}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-mono font-semibold text-gray-900 text-sm">${s.username}</p>
                        ${s.comment ? `<p class="text-[10px] text-gray-400">${s.comment}</p>` : ''}
                    </div>
                </div>
            </td>
            <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ${onlineClass}">
                    <span class="w-1.5 h-1.5 rounded-full ${onlineDot}"></span>${onlineLabel}
                </span>
            </td>
            <td class="px-4 py-3 hidden md:table-cell"><span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-lg font-medium">${s.profile || '—'}</span></td>
            <td class="px-4 py-3 hidden lg:table-cell">
                ${s.online ? `<p class="font-mono text-xs text-gray-700">${s.ip}</p><p class="text-[10px] text-gray-400">${s.uptime}</p>` : '<span class="text-xs text-gray-400">—</span>'}
            </td>
            <td class="px-4 py-3 hidden lg:table-cell">
                ${s.mac
                    ? `<div class="flex items-center gap-1.5">
                           <span class="font-mono text-xs text-gray-700">${s.mac}</span>
                           <button type="button" onclick="copyMac('${s.mac}', this)" title="Copy MAC"
                                   class="w-6 h-6 rounded-md bg-gray-100 hover:bg-indigo-50 hover:text-indigo-600 text-gray-400 flex items-center justify-center transition-colors flex-shrink-0">
                               <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                               </svg>
                           </button>
                       </div>`
                    : '<span class="text-xs text-gray-400">—</span>'}
            </td>
            <td class="px-4 py-3">${customerCell}</td>
            <td class="px-5 py-3"><div class="flex items-center justify-end gap-1">${actionCell}</div></td>
        </tr>`;
    }).join('');
}

function onCustSelect(idx) {
    const sel = document.getElementById('cust-sel-' + idx);
    const btn = document.getElementById('map-btn-' + idx);
    if (btn) btn.disabled = !sel.value;
}

async function mapSelected(idx) {
    const sel = document.getElementById('cust-sel-' + idx);
    if (!sel || !sel.value) return;

    const secret = SECRETS[idx];
    try {
        const res = await fetch('/pppoe-mapping/map', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                customer_id: parseInt(sel.value),
                pppoe_user: secret.username,
                ip_address: secret.ip || null,
            })
        });
        const data = await res.json();
        if (!data.success) { showToast(data.message, 'error'); return; }

        // Update local CUSTOMERS data
        const ci = CUSTOMERS.findIndex(c => c.id == sel.value);
        if (ci > -1) {
            CUSTOMERS[ci].pppoe_user = secret.username;
            if (secret.ip) CUSTOMERS[ci].ip_address = secret.ip;
        }

        showToast(data.message, 'success');
        renderTable();
        updateStats();
    } catch (err) {
        showToast('Gagal mapping: ' + err.message, 'error');
    }
}

async function unmapCustomer(customerId, username) {
    if (!confirm(`Hapus mapping PPPoE "${username}"?`)) return;

    try {
        const res = await fetch(`/pppoe-mapping/unmap/${customerId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (!data.success) { showToast(data.message, 'error'); return; }

        const ci = CUSTOMERS.findIndex(c => c.id == customerId);
        if (ci > -1) CUSTOMERS[ci].pppoe_user = '';

        showToast(data.message, 'success');
        renderTable();
        updateStats();
    } catch (err) {
        showToast('Gagal menghapus mapping', 'error');
    }
}

function updateStats() {
    const total = SECRETS.length;
    const online = SECRETS.filter(s => s.online).length;
    const mapped = SECRETS.filter(s => getMappedCustomer(s.username)).length;
    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-online').textContent = online;
    document.getElementById('stat-mapped').textContent = mapped;
    document.getElementById('stat-unmapped').textContent = total - mapped;
}

async function copyMac(mac, btn) {
    try {
        await navigator.clipboard.writeText(mac);
        // Swap icon to checkmark briefly
        const origHTML = btn.innerHTML;
        btn.innerHTML = `<svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>`;
        btn.classList.add('bg-green-50', 'text-green-600');
        showToast(`MAC ${mac} berhasil disalin ke clipboard`, 'success');
        setTimeout(() => {
            btn.innerHTML = origHTML;
            btn.classList.remove('bg-green-50', 'text-green-600');
        }, 2000);
    } catch {
        showToast('Gagal menyalin MAC ke clipboard', 'error');
    }
}

function filterTable() {
    const q = document.getElementById('pppoe-search').value.toLowerCase();
    const status = document.getElementById('filter-status').value;
    document.querySelectorAll('.pppoe-row').forEach(r => {
        const matchQ = r.dataset.username.includes(q);
        let matchS = true;
        if (status === 'mapped') matchS = r.dataset.mapped === '1';
        else if (status === 'unmapped') matchS = r.dataset.mapped === '0';
        else if (status === 'online') matchS = r.dataset.online === '1';
        r.style.display = matchQ && matchS ? '' : 'none';
    });
}
</script>

@endsection
