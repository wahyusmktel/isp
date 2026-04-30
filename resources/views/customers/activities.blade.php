@extends('layouts.app')
@section('title', 'Aktifitas Pelanggan')
@section('page-title', 'Aktifitas Pelanggan')

@section('content')

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
