<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - Tim-7 Net</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .glass-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); padding: 1.5rem; }
    </style>
</head>
<body class="text-gray-800 antialiased">
    
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="{{ asset('logo.png') }}" alt="Tim-7 Net" class="h-8 w-auto mr-3">
                    <span class="font-bold text-gray-900 text-lg hidden sm:block">Portal Pelanggan</span>
                </div>
                <div class="flex items-center gap-4">
                    <form action="{{ route('customer.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-600 transition-colors flex items-center gap-1.5 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg">
                            <i class="fas fa-sign-out-alt"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Welcome Hero -->
        <div class="bg-gradient-to-r from-sky-500 to-sky-600 rounded-3xl p-8 sm:p-10 text-white shadow-xl shadow-sky-500/20 mb-8 relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-3xl sm:text-4xl font-extrabold mb-2">Halo, {{ explode(' ', $customer->name)[0] }}! 👋</h1>
                <p class="text-sky-100 text-lg max-w-lg">Selamat datang di Portal Pelanggan Tim-7 Net. Kelola tagihan, cek layanan, dan pantau pemakaian internet Anda dengan mudah.</p>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-sky-300 opacity-20 rounded-full blur-xl"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Info Layanan -->
            <div class="glass-card md:col-span-2">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-wifi text-sky-500"></i> Informasi Layanan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 relative overflow-hidden">
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1 relative z-10">Status Koneksi</p>
                        <div class="flex items-center gap-2 relative z-10">
                            @if($customer->status === 'Aktif')
                                <span class="w-3 h-3 bg-green-500 rounded-full shadow-[0_0_8px_rgba(34,197,94,0.6)] animate-pulse"></span>
                                <span class="font-bold text-gray-900">Aktif</span>
                            @else
                                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                                <span class="font-bold text-gray-900">{{ $customer->status }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">ID Pelanggan</p>
                        <p class="font-bold text-gray-900 font-mono">{{ $customer->customer_number }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Paket Berlangganan</p>
                        <p class="font-bold text-gray-900">{{ $customer->package?->name ?? 'Belum ada paket' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Tanggal Tagihan</p>
                        <p class="font-bold text-gray-900">Tiap tgl {{ $customer->billing_date }}</p>
                    </div>
                </div>
            </div>

            <!-- Bantuan -->
            <div class="glass-card bg-gradient-to-br from-gray-50 to-white">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-headset text-orange-500"></i> Butuh Bantuan?</h2>
                <p class="text-sm text-gray-600 mb-6">Tim dukungan kami siap membantu Anda 24/7 jika mengalami kendala jaringan atau pertanyaan seputar tagihan.</p>
                <a href="https://wa.me/6282279122727?text=Halo%20Tim-7%20Net%2C%20saya%20pelanggan%20dengan%20ID%20{{ $customer->customer_number }}%20membutuhkan%20bantuan." target="_blank" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-green-500/30 transition-all hover:shadow-green-500/50 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fab fa-whatsapp text-lg"></i> Hubungi via WhatsApp
                </a>
            </div>
        </div>

        <!-- Tagihan Terakhir -->
        <div class="mt-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4 ml-1 flex items-center gap-2"><i class="fas fa-file-invoice-dollar text-indigo-500"></i> Riwayat Tagihan</h2>
            <div class="glass-card p-0 overflow-hidden">
                @php
                    $invoices = \App\Models\Invoice::where('customer_id', $customer->id)->latest()->take(5)->get();
                @endphp
                
                @if($invoices->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                                    <th class="py-3 px-4 font-semibold">No Tagihan</th>
                                    <th class="py-3 px-4 font-semibold">Periode</th>
                                    <th class="py-3 px-4 font-semibold">Nominal</th>
                                    <th class="py-3 px-4 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($invoices as $inv)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3 px-4 font-mono text-sm font-semibold">{{ $inv->invoice_number }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $inv->billing_period ? $inv->billing_period->format('F Y') : '-' }}</td>
                                    <td class="py-3 px-4 text-sm font-bold text-gray-900">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4">
                                        @if($inv->status === 'paid')
                                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">LUNAS</span>
                                        @elseif($inv->status === 'unpaid')
                                            <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-full">BELUM DIBAYAR</span>
                                        @else
                                            <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-full uppercase">{{ $inv->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-10 text-center text-gray-500 flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-receipt text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-sm font-medium">Belum ada riwayat tagihan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
