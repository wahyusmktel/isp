@php
$brand = $settings['brand_name'] ?? $settings['company_name'] ?? 'TIM-7 Net';
$bankName = $settings['isolation_bank_name'] ?? '';
$bankAccount = $settings['isolation_bank_account'] ?? '';
$accountName = $settings['isolation_account_name'] ?? '';
$cashNote = $settings['isolation_cash_note'] ?? 'Pembayaran tunai dapat dilakukan langsung ke kantor atau petugas resmi.';
$whatsapp = $settings['whatsapp'] ?? $settings['phone'] ?? '';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Layanan Diisolir - {{ $brand }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0b1220] text-white antialiased">
    <main class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-[1.1fr_.9fr] gap-6">
            <section class="rounded-3xl bg-white text-gray-950 p-7 lg:p-9 shadow-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-red-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-red-600">Akses internet sementara dibatasi</p>
                        <h1 class="text-2xl lg:text-3xl font-black mt-1">{{ $brand }}</h1>
                    </div>
                </div>

                <div class="mt-8">
                    <h2 class="text-xl font-bold text-gray-950">Tagihan Anda belum terselesaikan.</h2>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Layanan akan aktif kembali setelah pembayaran diverifikasi oleh admin. Jika sudah melakukan pembayaran, hubungi layanan pelanggan dan sertakan bukti transfer.
                    </p>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                        <p class="text-[11px] text-gray-400 font-semibold uppercase">Pelanggan</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $customer?->name ?? 'Tidak terdeteksi' }}</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                        <p class="text-[11px] text-gray-400 font-semibold uppercase">IP / User</p>
                        <p class="text-sm font-mono font-bold text-gray-900 mt-1">{{ $customer?->ip_address ?? $ip }}</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                        <p class="text-[11px] text-gray-400 font-semibold uppercase">Tagihan</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">
                            {{ $invoice ? 'Rp '.number_format($invoice->amount, 0, ',', '.') : 'Hubungi admin' }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-bold text-amber-900">Pembayaran tunai</p>
                    <p class="text-sm text-amber-800 mt-1 leading-relaxed">{{ $cashNote }}</p>
                </div>
            </section>

            <aside class="rounded-3xl bg-slate-900 border border-white/10 p-7 lg:p-8 shadow-2xl">
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-300">Instruksi pembayaran</p>
                <h2 class="text-2xl font-black mt-2">Transfer Bank</h2>

                <div class="mt-6 space-y-3">
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                        <p class="text-xs text-slate-400">Nama Bank</p>
                        <p class="text-lg font-bold mt-1">{{ $bankName ?: '-' }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                        <p class="text-xs text-slate-400">Nomor Rekening</p>
                        <p class="text-2xl font-black font-mono mt-1 tracking-wide">{{ $bankAccount ?: '-' }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                        <p class="text-xs text-slate-400">Atas Nama</p>
                        <p class="text-lg font-bold mt-1">{{ $accountName ?: '-' }}</p>
                    </div>
                </div>

                @if($whatsapp)
                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" class="mt-6 w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold px-4 py-3 transition-colors">
                    Hubungi Admin
                </a>
                @endif

                <p class="text-xs text-slate-400 leading-relaxed mt-5">
                    Setelah pembayaran dikonfirmasi, admin akan membuka isolir dan koneksi internet Anda akan kembali normal.
                </p>
            </aside>
        </div>
    </main>
</body>
</html>
