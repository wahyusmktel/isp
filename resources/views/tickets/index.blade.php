@extends('layouts.app')
@section('title', 'Tiket Pengaduan')
@section('page-title', 'Tiket Pengaduan')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Tiket Pengaduan</h1>
        <p class="text-sm text-gray-400 mt-0.5">Kelola semua tiket gangguan dan pengaduan dari pelanggan</p>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('tickets.index', ['status' => 'open']) }}"
       class="bg-white rounded-2xl border {{ $status === 'open' ? 'border-red-300 ring-2 ring-red-100' : 'border-gray-100' }} p-5 flex items-center gap-4 hover:border-red-200 transition-colors">
        <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stats['open'] }}</p>
            <p class="text-xs text-gray-500">Dibuka</p>
        </div>
    </a>
    <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}"
       class="bg-white rounded-2xl border {{ $status === 'in_progress' ? 'border-amber-300 ring-2 ring-amber-100' : 'border-gray-100' }} p-5 flex items-center gap-4 hover:border-amber-200 transition-colors">
        <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stats['in_progress'] }}</p>
            <p class="text-xs text-gray-500">Diproses</p>
        </div>
    </a>
    <a href="{{ route('tickets.index', ['status' => 'resolved']) }}"
       class="bg-white rounded-2xl border {{ $status === 'resolved' ? 'border-green-300 ring-2 ring-green-100' : 'border-gray-100' }} p-5 flex items-center gap-4 hover:border-green-200 transition-colors">
        <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stats['resolved'] }}</p>
            <p class="text-xs text-gray-500">Selesai</p>
        </div>
    </a>
    <a href="{{ route('tickets.index', ['status' => 'closed']) }}"
       class="bg-white rounded-2xl border {{ $status === 'closed' ? 'border-gray-400 ring-2 ring-gray-100' : 'border-gray-100' }} p-5 flex items-center gap-4 hover:border-gray-300 transition-colors">
        <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stats['closed'] }}</p>
            <p class="text-xs text-gray-500">Ditutup</p>
        </div>
    </a>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl border border-gray-100 px-4 py-3 mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-3">
    <form method="GET" action="{{ route('tickets.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full" id="filter-form">
        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-72">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari tiket atau pelanggan..."
                   class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none flex-1">
        </div>
        <div class="flex items-center gap-2 ml-auto flex-wrap">
            <select name="status" onchange="this.form.submit()" class="inp text-xs py-2 px-3">
                <option value="">Semua Status</option>
                <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Dibuka</option>
                <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>Diproses</option>
                <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Selesai</option>
                <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Ditutup</option>
            </select>
            <select name="category" onchange="this.form.submit()" class="inp text-xs py-2 px-3">
                <option value="">Semua Kategori</option>
                <option value="gangguan_jaringan" {{ $category === 'gangguan_jaringan' ? 'selected' : '' }}>Gangguan Jaringan</option>
                <option value="lambat" {{ $category === 'lambat' ? 'selected' : '' }}>Internet Lambat</option>
                <option value="tidak_bisa_akses" {{ $category === 'tidak_bisa_akses' ? 'selected' : '' }}>Tidak Bisa Akses</option>
                <option value="billing" {{ $category === 'billing' ? 'selected' : '' }}>Masalah Tagihan</option>
                <option value="lainnya" {{ $category === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            @if($search || $status || $category)
            <a href="{{ route('tickets.index') }}" class="text-xs text-gray-500 hover:text-gray-700 px-2 py-2 rounded-lg hover:bg-gray-100 transition-colors">Reset</a>
            @endif
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-3 py-2 rounded-xl transition-colors">Cari</button>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-3">No Tiket</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Pelanggan</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden md:table-cell">Kategori</th>
                    <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">Prioritas</th>
                    <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Status</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">Tanggal</th>
                    <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3.5">
                        <p class="font-mono text-xs font-bold text-gray-800">{{ $ticket->ticket_number }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5 md:hidden">{{ $ticket->category_label }}</p>
                    </td>
                    <td class="px-4 py-3.5">
                        <p class="font-semibold text-gray-900 text-sm">{{ $ticket->customer?->name ?? '—' }}</p>
                        <p class="text-[10px] text-gray-400 font-mono">{{ $ticket->customer?->customer_number }}</p>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <span class="text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded-lg">{{ $ticket->category_label }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-center hidden lg:table-cell">
                        @php
                            $pClass = match($ticket->priority) {
                                'kritis' => 'bg-red-100 text-red-700',
                                'tinggi' => 'bg-orange-100 text-orange-700',
                                'sedang' => 'bg-amber-100 text-amber-700',
                                default  => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full {{ $pClass }}">{{ $ticket->priority_label }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @php
                            $sClass = match($ticket->status) {
                                'open'        => 'bg-red-100 text-red-700',
                                'in_progress' => 'bg-amber-100 text-amber-700',
                                'resolved'    => 'bg-green-100 text-green-700',
                                default       => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="inline-block text-[10px] font-bold px-2.5 py-0.5 rounded-full {{ $sClass }}">{{ $ticket->status_label }}</span>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        <p class="text-xs text-gray-600">{{ $ticket->created_at->format('d M Y') }}</p>
                        <p class="text-[10px] text-gray-400">{{ $ticket->created_at->format('H:i') }}</p>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="openDetail({{ json_encode($ticket->toJsonData()) }})"
                                    class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition-colors" title="Detail & Balas">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button onclick="deleteTicket({{ $ticket->id }}, '{{ $ticket->ticket_number }}')"
                                    class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center">
                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada tiket pengaduan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $tickets->links() }}
    </div>
    @endif
</div>

{{-- Detail / Respond Modal --}}
<div id="modal-detail" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDetail()"></div>
    <div class="absolute right-0 top-0 h-full w-full sm:max-w-lg bg-white shadow-2xl flex flex-col">
        {{-- Modal Header --}}
        <div class="flex items-start justify-between p-6 border-b border-gray-100 flex-shrink-0">
            <div>
                <p id="md-number" class="font-mono text-xs font-bold text-gray-400 mb-0.5"></p>
                <h3 id="md-subject" class="text-base font-bold text-gray-900 leading-snug"></h3>
            </div>
            <button onclick="closeDetail()" class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0 ml-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Scrollable body --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-5">
            {{-- Customer & meta info --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Pelanggan</p>
                    <p id="md-customer" class="text-sm font-semibold text-gray-800"></p>
                    <p id="md-customer-num" class="text-[10px] font-mono text-gray-400"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Tanggal Dibuat</p>
                    <p id="md-created" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Kategori</p>
                    <p id="md-category" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Prioritas</p>
                    <p id="md-priority" class="text-sm font-semibold text-gray-800"></p>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Deskripsi Pengaduan</p>
                <div id="md-description" class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap"></div>
            </div>

            {{-- Existing admin notes --}}
            <div id="md-existing-notes-wrap" class="hidden">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Balasan Sebelumnya</p>
                <div id="md-existing-notes" class="bg-green-50 border border-green-100 rounded-xl p-4 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap"></div>
            </div>

            {{-- Admin respond form --}}
            <form id="form-respond" onsubmit="submitRespond(event)">
                <input type="hidden" id="respond-id">
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Update Status</label>
                    <select id="respond-status" class="inp w-full">
                        <option value="open">Dibuka</option>
                        <option value="in_progress">Diproses</option>
                        <option value="resolved">Selesai</option>
                        <option value="closed">Ditutup</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan / Balasan Admin</label>
                    <textarea id="respond-notes" rows="4" placeholder="Tulis balasan atau catatan penanganan..."
                              class="inp w-full resize-none"></textarea>
                </div>
                <button type="submit" id="btn-respond"
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast" class="fixed bottom-5 right-5 z-[999] hidden">
    <div id="toast-inner" class="flex items-center gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm font-medium text-white min-w-[240px] max-w-sm">
        <span id="toast-msg"></span>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function showToast(msg, type = 'success') {
    const el = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    document.getElementById('toast-inner').className = `flex items-center gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm font-medium text-white min-w-[240px] max-w-sm ${type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 3500);
}

function openDetail(t) {
    document.getElementById('md-number').textContent      = '#' + t.ticket_number;
    document.getElementById('md-subject').textContent     = t.subject;
    document.getElementById('md-customer').textContent    = t.customer_name || '—';
    document.getElementById('md-customer-num').textContent= t.customer_number || '';
    document.getElementById('md-created').textContent     = t.created_at;
    document.getElementById('md-category').textContent    = t.category_label;
    document.getElementById('md-priority').textContent    = t.priority_label;
    document.getElementById('md-description').textContent = t.description;

    const notesWrap = document.getElementById('md-existing-notes-wrap');
    const notesEl   = document.getElementById('md-existing-notes');
    if (t.admin_notes) {
        notesEl.textContent = t.admin_notes;
        notesWrap.classList.remove('hidden');
    } else {
        notesWrap.classList.add('hidden');
    }

    document.getElementById('respond-id').value       = t.id;
    document.getElementById('respond-status').value   = t.status;
    document.getElementById('respond-notes').value    = t.admin_notes || '';

    document.getElementById('modal-detail').classList.remove('hidden');
}

function closeDetail() {
    document.getElementById('modal-detail').classList.add('hidden');
}

async function submitRespond(e) {
    e.preventDefault();
    const id  = document.getElementById('respond-id').value;
    const btn = document.getElementById('btn-respond');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    try {
        const res = await fetch(`/tickets/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                status:      document.getElementById('respond-status').value,
                admin_notes: document.getElementById('respond-notes').value,
            }),
        });
        const data = await res.json();
        if (!data.success) { showToast(data.message, 'error'); return; }
        showToast(data.message);
        closeDetail();
        setTimeout(() => location.reload(), 800);
    } catch (err) {
        showToast('Gagal: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Simpan`;
    }
}

async function deleteTicket(id, num) {
    if (!confirm(`Hapus tiket #${num}? Tindakan ini tidak dapat dibatalkan.`)) return;
    try {
        const res = await fetch(`/tickets/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (!data.success) { showToast(data.message, 'error'); return; }
        showToast(data.message);
        setTimeout(() => location.reload(), 800);
    } catch (err) {
        showToast('Gagal: ' + err.message, 'error');
    }
}
</script>

@endsection
