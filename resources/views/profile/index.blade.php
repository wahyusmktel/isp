@extends('layouts.app')
@section('title', 'Profil & Pengaturan')
@section('page-title', 'Profil Akun')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Detail Profil (Read Only) --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 h-fit">
        <h2 class="text-lg font-bold text-gray-900 mb-1">Informasi Profil</h2>
        <p class="text-sm text-gray-500 mb-5">Informasi dasar akun Anda (hanya lihat).</p>

        <div class="space-y-5">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input type="text" value="{{ auth()->user()->name }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 text-sm focus:outline-none" disabled>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <input type="email" value="{{ auth()->user()->email }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 text-sm focus:outline-none" disabled>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Role Sistem</label>
                <div class="mt-1">
                    <span class="inline-flex px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-lg text-xs font-bold uppercase">{{ auth()->user()->role }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Ganti Password --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 h-fit">
        <h2 class="text-lg font-bold text-gray-900 mb-1">Ganti Password</h2>
        <p class="text-sm text-gray-500 mb-5">Pastikan akun Anda menggunakan password yang panjang dan acak untuk tetap aman.</p>

        @if (session('status') === 'password-updated')
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Password berhasil diperbarui.
            </div>
        @endif

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Saat Ini</label>
                <input type="password" name="current_password" required class="w-full px-4 py-2 bg-white border {{ $errors->has('current_password') ? 'border-red-300 focus:ring-red-500' : 'border-gray-200 focus:ring-green-500 focus:border-green-500' }} rounded-xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-opacity-20">
                @if($errors->has('current_password'))
                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('current_password') }}</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                <input type="password" name="password" required class="w-full px-4 py-2 bg-white border {{ $errors->has('password') ? 'border-red-300 focus:ring-red-500' : 'border-gray-200 focus:ring-green-500 focus:border-green-500' }} rounded-xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-opacity-20">
                @if($errors->has('password'))
                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('password') }}</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:ring-opacity-20">
            </div>

            <div class="pt-2">
                <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                    Simpan Password
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
