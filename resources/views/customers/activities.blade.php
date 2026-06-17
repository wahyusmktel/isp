@extends('layouts.app')
@section('title', 'Aktifitas Pelanggan')
@section('page-title', 'Aktifitas Pelanggan')

@section('content')

<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-5">
    <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
            <div>
                <h3 class="font-bold text-gray-900">Paling Sering Putus Nyambung</h3>
                <p class="text-xs text-gray-500">Pelanggan dengan disconnect terbanyak dalam {{ $disconnectSummary['window_hours'] }} jam terakhir</p>
            </div>
            <span class="inline-flex w-fit items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                {{ $disconnectSummary['total_disconnects'] }} kejadian
            </span>
        </div>

        <div class="divide-y divide-gray-50">
            @forelse($topDisconnectCustomers as $item)
            <div class="px-6 py-4 flex items-center justify-between gap-4 hover:bg-gray-50 transition-colors">
                <div class="min-w-0">
                    @if($item['customer'])
                        <a href="{{ route('customers.show', $item['customer']->id) }}" class="font-semibold text-blue-600 hover:underline truncate block">
                            {{ $item['customer']->name }}
                        </a>
                    @else
                        <p class="font-semibold text-gray-900 truncate">{{ $item['pppoe_user'] ?? 'PPPoE tidak dikenal' }}</p>
                    @endif
                    <p class="text-xs text-gray-400 font-mono truncate">{{ $item['pppoe_user'] ?? '-' }}</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-xl font-bold text-gray-900">{{ $item['disconnect_count'] }}x</p>
                    <p class="text-xs text-gray-400">Terakhir {{ $item['last_seen']->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center">
                <p class="text-sm font-medium text-gray-700">Belum ada disconnect terbaru</p>
                <p class="text-xs text-gray-400 mt-1">Data akan muncul saat webhook mencatat pelanggan terputus.</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M12 22a10 10 0 110-20 10 10 0 010 20z"/>
            </svg>
        </div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Rata-rata Waktu Kejadian</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $disconnectSummary['avg_duration'] ?? '-' }}</p>
        <p class="text-sm text-gray-500 mt-2">
            Rata-rata durasi dari pelanggan terputus sampai terhubung kembali dalam {{ $disconnectSummary['window_hours'] }} jam terakhir.
        </p>

        <div class="grid grid-cols-2 gap-3 mt-5">
            <div class="rounded-xl bg-gray-50 p-3">
                <p class="text-lg font-bold text-gray-900">{{ $disconnectSummary['affected_customers'] }}</p>
                <p class="text-xs text-gray-500">Pelanggan terdampak</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-3">
                <p class="text-lg font-bold text-gray-900">{{ $disconnectSummary['paired_events'] }}</p>
                <p class="text-xs text-gray-500">Event lengkap</p>
            </div>
        </div>

        @unless($disconnectSummary['avg_duration'])
        <p class="text-xs text-amber-700 bg-amber-50 rounded-xl px-3 py-2 mt-4">
            Butuh pasangan event terputus dan terhubung untuk menghitung rata-rata waktu.
        </p>
        @endunless
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-bold text-gray-900">Riwayat Koneksi PPPoE</h3>
        <p class="text-xs text-gray-500">Menampilkan status login/logout pelanggan</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[700px]">
            <thead>
                <tr class="bg-gray-50/60 border-b border-gray-100">
                    <th class="py-3 px-6 text-xs font-semibold text-gray-500">Waktu</th>
                    <th class="py-3 px-6 text-xs font-semibold text-gray-500">Pelanggan</th>
                    <th class="py-3 px-6 text-xs font-semibold text-gray-500">IP Address</th>
                    <th class="py-3 px-6 text-xs font-semibold text-gray-500">Aksi / Status</th>
                    <th class="py-3 px-6 text-xs font-semibold text-gray-500">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($activities as $act)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-6 text-gray-500 whitespace-nowrap">{{ $act->created_at->format('d M Y, H:i:s') }}</td>
                    <td class="py-3 px-6">
                        @if($act->customer)
                            <a href="{{ route('customers.show', $act->customer->id) }}" class="font-medium text-blue-600 hover:underline">
                                {{ $act->customer->name }}
                            </a>
                            <span class="text-xs text-gray-400 block font-mono">{{ $act->pppoe_user }}</span>
                        @else
                            <span class="font-medium text-gray-900">{{ $act->pppoe_user }}</span>
                            <span class="text-xs text-gray-400 block">Pelanggan tidak ditemukan</span>
                        @endif
                    </td>
                    <td class="py-3 px-6 font-mono text-gray-600">{{ $act->ip_address ?? '-' }}</td>
                    <td class="py-3 px-6">
                        @if(strtolower($act->action) === 'connected' || strtolower($act->action) === 'logged in')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Terhubung
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Terputus
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-6 text-gray-500 max-w-xs truncate" title="{{ $act->description }}">
                        {{ $act->description ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-500 text-sm">
                        Belum ada aktifitas yang terekam.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($activities->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
        {{ $activities->links() }}
    </div>
    @endif
</div>

@endsection
