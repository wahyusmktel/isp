@extends('layouts.app')
@section('title', 'Pegawai')
@section('page-title', 'Pegawai')

@php
$departemenOptions = [
    'manajemen'    => 'Manajemen',
    'teknis'       => 'Teknis',
    'noc'          => 'NOC',
    'keuangan'     => 'Keuangan',
    'cs'           => 'Customer Service',
    'administrasi' => 'Administrasi',
];
$departemenColors = [
    'manajemen'    => 'bg-violet-50 text-violet-700',
    'teknis'       => 'bg-blue-50 text-blue-700',
    'noc'          => 'bg-cyan-50 text-cyan-700',
    'keuangan'     => 'bg-emerald-50 text-emerald-700',
    'cs'           => 'bg-orange-50 text-orange-700',
    'administrasi' => 'bg-gray-100 text-gray-600',
];
$jabatanOptions = [
    'CEO', 'Direktur', 'Manajer', 'Supervisor',
    'Admin', 'Keuangan', 'Customer Service',
    'NOC Engineer', 'Teknisi', 'Lainnya',
];
$avatarColors = [
    'bg-gradient-to-br from-violet-500 to-purple-600',
    'bg-gradient-to-br from-blue-500 to-cyan-500',
    'bg-gradient-to-br from-emerald-500 to-teal-500',
    'bg-gradient-to-br from-orange-500 to-amber-500',
    'bg-gradient-to-br from-rose-500 to-pink-500',
    'bg-gradient-to-br from-indigo-500 to-blue-600',
];
@endphp

@section('content')

{{-- ===== Header ===== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Data Pegawai</h1>
        <p class="text-sm text-gray-400 mt-0.5">Kelola seluruh data karyawan perusahaan</p>
    </div>
    @if(auth()->user()->role === 'admin')
    <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
        </svg>
        Tambah Pegawai
    </button>
    @endif
</div>

{{-- ===== Stats ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @php
    $statCards = [
        ['label' => 'Total Pegawai', 'value' => $stats['total'],  'color' => 'bg-blue-50 text-blue-600',    'ico' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label' => 'Aktif',         'value' => $stats['aktif'],  'color' => 'bg-green-50 text-green-600',  'ico' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Cuti',          'value' => $stats['cuti'],   'color' => 'bg-amber-50 text-amber-600',  'ico' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Resign',        'value' => $stats['resign'], 'color' => 'bg-red-50 text-red-500',      'ico' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
    ];
    @endphp
    @foreach($statCards as $sc)
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl {{ $sc['color'] }} bg-opacity-60 flex items-center justify-center flex-shrink-0" style="background: none;">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ explode(' ', $sc['color'])[0] }}">
                <svg class="w-5 h-5 {{ explode(' ', $sc['color'])[1] }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sc['ico'] }}"/>
                </svg>
            </div>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $sc['value'] }}</p>
            <p class="text-xs text-gray-500">{{ $sc['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ===== Toolbar ===== --}}
<form method="GET" action="{{ route('employees.index') }}"
      class="bg-white rounded-2xl border border-gray-100 px-4 py-3 mb-5 flex flex-col sm:flex-row items-start sm:items-center gap-3">
    {{-- Search --}}
    <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-64">
        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / jabatan..."
               class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none w-full">
    </div>
    {{-- Departemen filter --}}
    <select name="departemen" onchange="this.form.submit()"
            class="bg-white border border-gray-200 text-gray-600 text-sm px-3 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
        <option value="">Semua Departemen</option>
        @foreach($departemenOptions as $val => $lbl)
            <option value="{{ $val }}" {{ $departemen === $val ? 'selected' : '' }}>{{ $lbl }}</option>
        @endforeach
    </select>
    {{-- Status filter --}}
    <select name="status" onchange="this.form.submit()"
            class="bg-white border border-gray-200 text-gray-600 text-sm px-3 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
        <option value="">Semua Status</option>
        <option value="aktif"  {{ $status === 'aktif'  ? 'selected' : '' }}>Aktif</option>
        <option value="cuti"   {{ $status === 'cuti'   ? 'selected' : '' }}>Cuti</option>
        <option value="resign" {{ $status === 'resign' ? 'selected' : '' }}>Resign</option>
    </select>
    <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
        Cari
    </button>
    @if($search || $departemen || $status)
    <a href="{{ route('employees.index') }}" class="text-sm text-red-400 hover:text-red-600 transition-colors">Reset</a>
    @endif
    <p class="ml-auto text-xs text-gray-400">{{ $employees->count() }} pegawai ditampilkan</p>
</form>

{{-- ===== Employee Cards Grid ===== --}}
@if($employees->isEmpty())
<div class="bg-white rounded-2xl border border-gray-100 py-16 text-center">
    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    <p class="text-gray-400 text-sm font-medium">Belum ada data pegawai</p>
    <p class="text-gray-300 text-xs mt-1">Klik "Tambah Pegawai" untuk menambahkan data pertama</p>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach($employees as $emp)
    @php $color = $avatarColors[$emp->id % count($avatarColors)]; @endphp
    <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-shadow group relative">

        {{-- Status dot + ID Card button --}}
        <div class="absolute top-4 right-4 flex items-center gap-1.5">
            @if($emp->status === 'aktif')
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-50 text-green-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif
                </span>
            @elseif($emp->status === 'cuti')
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>Cuti
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>Resign
                </span>
            @endif
            <a href="{{ route('employees.idcard', $emp->id) }}" target="_blank"
               title="Cetak ID Card"
               class="w-6 h-6 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-500 flex items-center justify-center transition-colors flex-shrink-0">
                {{-- ID Card icon --}}
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <circle cx="8" cy="10" r="2"/>
                    <path stroke-linecap="round" d="M14 9h4M14 12h4M6 16h12"/>
                </svg>
            </a>
        </div>

        {{-- Avatar --}}
        <div class="w-14 h-14 rounded-2xl {{ $color }} flex items-center justify-center text-white text-xl font-bold mb-3 shadow-sm">
            {{ strtoupper(mb_substr($emp->name, 0, 1)) }}
        </div>

        {{-- Info --}}
        <p class="font-semibold text-gray-900 text-sm leading-tight">{{ $emp->name }}</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ $emp->jabatan }}</p>

        <div class="mt-2.5 flex items-center gap-1.5 flex-wrap">
            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $departemenColors[$emp->departemen] ?? 'bg-gray-100 text-gray-600' }}">
                {{ $emp->departemen_label }}
            </span>
            <span class="text-[10px] text-gray-400">{{ $emp->employee_number }}</span>
        </div>

        <div class="mt-3 pt-3 border-t border-gray-50 space-y-1">
            @if($emp->phone)
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <svg class="w-3 h-3 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                {{ $emp->phone }}
            </div>
            @endif
            @if($emp->email)
            <div class="flex items-center gap-2 text-xs text-gray-500 truncate">
                <svg class="w-3 h-3 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="truncate">{{ $emp->email }}</span>
            </div>
            @endif
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <svg class="w-3 h-3 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Bergabung {{ $emp->join_date?->format('d M Y') ?? '—' }}
            </div>
        </div>

        {{-- Actions --}}
        @if(auth()->user()->role === 'admin')
        <div class="mt-3 pt-3 border-t border-gray-50 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
            <button onclick='openModal(@json($emp->toJsonData()))'
                    class="flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-semibold transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </button>
            <button onclick="confirmDelete({{ $emp->id }}, '{{ addslashes($emp->name) }}')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 text-xs font-semibold transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus
            </button>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endif

{{-- ===== Employee Form Modal ===== --}}
<div id="emp-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg my-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900" id="modal-title">Tambah Pegawai</h3>
                <button onclick="closeModal()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                <input type="hidden" id="emp-id">

                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="f-name" placeholder="Nama lengkap pegawai"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan <span class="text-red-500">*</span></label>
                        <select id="f-jabatan"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($jabatanOptions as $j)
                            <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Departemen <span class="text-red-500">*</span></label>
                        <select id="f-departemen"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            @foreach($departemenOptions as $val => $lbl)
                            <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Telepon</label>
                        <input type="text" id="f-phone" placeholder="08xxxxxxxxxx"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                        <input type="email" id="f-email" placeholder="email@perusahaan.com"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Bergabung <span class="text-red-500">*</span></label>
                        <input type="date" id="f-join-date"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                        <select id="f-status"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="aktif">Aktif</option>
                            <option value="cuti">Cuti</option>
                            <option value="resign">Resign</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat</label>
                        <textarea id="f-address" rows="2" placeholder="Alamat lengkap..."
                                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"></textarea>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                        <textarea id="f-notes" rows="2" placeholder="Catatan tambahan (opsional)..."
                                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"></textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100">
                <button onclick="closeModal()"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 transition-colors">
                    Batal
                </button>
                <button onclick="submitEmployee()" id="btn-save"
                        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===== Delete Confirm Modal ===== --}}
<div id="delete-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">Hapus Pegawai</h3>
            <p class="text-sm text-gray-500 mb-5">Hapus data "<span id="del-name" class="font-medium text-gray-700"></span>"? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-2">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 text-sm font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-colors">Batal</button>
                <button onclick="doDelete()" class="flex-1 px-4 py-2 text-sm font-semibold bg-red-500 hover:bg-red-600 text-white rounded-xl transition-colors">Hapus</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let deleteId = null;

// ─── Modal ─────────────────────────────────────────────────────────────────
function openModal(data = null) {
    document.getElementById('modal-title').textContent = data ? 'Edit Pegawai' : 'Tambah Pegawai';
    document.getElementById('emp-id').value       = data ? data.id : '';
    document.getElementById('f-name').value        = data ? data.name : '';
    document.getElementById('f-jabatan').value     = data ? data.jabatan : '';
    document.getElementById('f-departemen').value  = data ? data.departemen : 'administrasi';
    document.getElementById('f-phone').value       = data ? data.phone : '';
    document.getElementById('f-email').value       = data ? data.email : '';
    document.getElementById('f-join-date').value   = data ? data.join_date : '{{ now()->format("Y-m-d") }}';
    document.getElementById('f-status').value      = data ? data.status : 'aktif';
    document.getElementById('f-address').value     = data ? data.address : '';
    document.getElementById('f-notes').value       = data ? data.notes : '';
    document.getElementById('emp-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('emp-modal').classList.add('hidden');
}

async function submitEmployee() {
    const id  = document.getElementById('emp-id').value;
    const btn = document.getElementById('btn-save');
    const body = {
        name:        document.getElementById('f-name').value.trim(),
        jabatan:     document.getElementById('f-jabatan').value.trim(),
        departemen:  document.getElementById('f-departemen').value,
        phone:       document.getElementById('f-phone').value.trim(),
        email:       document.getElementById('f-email').value.trim(),
        join_date:   document.getElementById('f-join-date').value,
        status:      document.getElementById('f-status').value,
        address:     document.getElementById('f-address').value.trim(),
        notes:       document.getElementById('f-notes').value.trim(),
    };

    if (!body.name || !body.jabatan || !body.join_date) {
        showToast('Mohon isi semua field yang wajib diisi.', 'error');
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    const url    = id ? `/employees/${id}` : '/employees';
    const method = id ? 'PUT' : 'POST';

    try {
        const res  = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();

        if (data.success) {
            showToast(data.message, 'success');
            closeModal();
            setTimeout(() => location.reload(), 800);
        } else {
            const errs = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Terjadi kesalahan.');
            showToast(errs, 'error');
        }
    } catch {
        showToast('Gagal menghubungi server.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Simpan';
    }
}

// ─── Delete ─────────────────────────────────────────────────────────────────
function confirmDelete(id, name) {
    deleteId = id;
    document.getElementById('del-name').textContent = name;
    document.getElementById('delete-modal').classList.remove('hidden');
}
function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    deleteId = null;
}
async function doDelete() {
    if (!deleteId) return;
    try {
        const res  = await fetch(`/employees/${deleteId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeDeleteModal();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'Gagal menghapus.', 'error');
        }
    } catch {
        showToast('Gagal menghubungi server.', 'error');
    }
}

// ─── Toast ───────────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.className = `fixed bottom-5 right-5 z-[9999] px-5 py-3 rounded-xl text-sm font-semibold shadow-lg
        ${type === 'success' ? 'bg-green-600 text-white' : 'bg-red-500 text-white'}`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}
</script>
@endpush
