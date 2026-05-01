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
                            @if(strtolower($customer->status) === 'aktif')
                                <span class="w-3 h-3 bg-green-500 rounded-full shadow-[0_0_8px_rgba(34,197,94,0.6)] animate-pulse"></span>
                                <span class="font-bold text-green-600">Aktif</span>
                            @else
                                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                                <span class="font-bold text-red-600">{{ ucfirst($customer->status) }}</span>
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

        <!-- Tiket Pengaduan -->
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4 ml-1">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-ticket-alt text-violet-500"></i> Tiket Pengaduan
                </h2>
                <button onclick="openTicketModal()"
                        class="inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors shadow-lg shadow-violet-500/20">
                    <i class="fas fa-plus text-xs"></i> Buat Tiket
                </button>
            </div>

            @php
                $myTickets = \App\Models\Ticket::where('customer_id', $customer->id)->latest()->get();
            @endphp

            <div class="glass-card p-0 overflow-hidden">
                @if($myTickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                                <th class="py-3 px-4 font-semibold">No Tiket</th>
                                <th class="py-3 px-4 font-semibold">Subjek</th>
                                <th class="py-3 px-4 font-semibold hidden sm:table-cell">Kategori</th>
                                <th class="py-3 px-4 font-semibold">Status</th>
                                <th class="py-3 px-4 font-semibold hidden md:table-cell">Tanggal</th>
                                <th class="py-3 px-4 font-semibold text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="ticket-tbody">
                            @foreach($myTickets as $tkt)
                            <tr class="hover:bg-gray-50/50 transition-colors" data-id="{{ $tkt->id }}">
                                <td class="py-3 px-4 font-mono text-xs font-bold text-gray-700">{{ $tkt->ticket_number }}</td>
                                <td class="py-3 px-4 text-sm font-medium text-gray-800 max-w-[180px] truncate">{{ $tkt->subject }}</td>
                                <td class="py-3 px-4 text-xs text-gray-500 hidden sm:table-cell">{{ $tkt->category_label }}</td>
                                <td class="py-3 px-4">
                                    @php
                                        $sc = match($tkt->status) {
                                            'open'        => 'bg-red-100 text-red-700',
                                            'in_progress' => 'bg-amber-100 text-amber-700',
                                            'resolved'    => 'bg-green-100 text-green-700',
                                            default       => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $sc }}">{{ $tkt->status_label }}</span>
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-500 hidden md:table-cell">{{ $tkt->created_at->format('d M Y') }}</td>
                                <td class="py-3 px-4 text-center">
                                    <button onclick="viewTicketDetail({{ json_encode(['ticket_number' => $tkt->ticket_number, 'subject' => $tkt->subject, 'category_label' => $tkt->category_label, 'priority_label' => $tkt->priority_label, 'status_label' => $tkt->status_label, 'status' => $tkt->status, 'description' => $tkt->description, 'admin_notes' => $tkt->admin_notes, 'created_at' => $tkt->created_at->format('d M Y H:i')]) }})"
                                            class="text-violet-600 hover:text-violet-800 text-xs font-semibold hover:underline">Lihat</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div id="ticket-empty" class="py-10 text-center text-gray-500 flex flex-col items-center">
                    <div class="w-16 h-16 bg-violet-50 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-ticket-alt text-2xl text-violet-300"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Belum ada tiket pengaduan.</p>
                    <p class="text-xs text-gray-400 mt-1">Klik "Buat Tiket" untuk melaporkan gangguan.</p>
                </div>
                <table class="w-full text-left border-collapse hidden" id="ticket-table-new">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="py-3 px-4 font-semibold">No Tiket</th>
                            <th class="py-3 px-4 font-semibold">Subjek</th>
                            <th class="py-3 px-4 font-semibold hidden sm:table-cell">Kategori</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 font-semibold hidden md:table-cell">Tanggal</th>
                            <th class="py-3 px-4 font-semibold text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody id="ticket-tbody-new" class="divide-y divide-gray-100"></tbody>
                </table>
                @endif
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
    <!-- Modal Buat Tiket -->
    <div id="modal-ticket" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeTicketModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white rounded-t-3xl flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Buat Tiket Pengaduan</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tim kami akan merespons dalam 1×24 jam</p>
                </div>
                <button onclick="closeTicketModal()" class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <form id="form-ticket" onsubmit="submitTicket(event)" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori Pengaduan <span class="text-red-500">*</span></label>
                    <select id="t-category" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent bg-gray-50">
                        <option value="">— Pilih Kategori —</option>
                        <option value="gangguan_jaringan">Gangguan Jaringan</option>
                        <option value="lambat">Internet Lambat</option>
                        <option value="tidak_bisa_akses">Tidak Bisa Akses</option>
                        <option value="billing">Masalah Tagihan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Prioritas <span class="text-red-500">*</span></label>
                    <select id="t-priority" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent bg-gray-50">
                        <option value="rendah">Rendah — gangguan kecil, tidak mendesak</option>
                        <option value="sedang" selected>Sedang — layanan terganggu sebagian</option>
                        <option value="tinggi">Tinggi — layanan tidak bisa digunakan</option>
                        <option value="kritis">Kritis — mendesak, perlu ditangani segera</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Subjek <span class="text-red-500">*</span></label>
                    <input type="text" id="t-subject" required maxlength="255"
                           placeholder="Contoh: Internet mati sejak pagi ini"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                    <textarea id="t-description" required rows="4" maxlength="2000"
                              placeholder="Ceritakan detail masalah yang Anda alami: kapan terjadi, gejala apa, sudah dicoba apa, dll."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent bg-gray-50 resize-none"></textarea>
                </div>
                <div id="ticket-form-error" class="hidden bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-600"></div>
                <button type="submit" id="btn-submit-ticket"
                        class="w-full bg-violet-600 hover:bg-violet-500 text-white font-semibold py-3 rounded-xl transition-colors flex items-center justify-center gap-2 shadow-lg shadow-violet-500/20">
                    <i class="fas fa-paper-plane text-sm"></i> Kirim Tiket
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Detail Tiket -->
    <div id="modal-ticket-detail" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeTicketDetail()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white rounded-t-3xl flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
                <div>
                    <p id="dtl-number" class="font-mono text-xs font-bold text-gray-400"></p>
                    <h3 id="dtl-subject" class="text-base font-bold text-gray-900 leading-snug mt-0.5"></h3>
                </div>
                <button onclick="closeTicketDetail()" class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Kategori</p>
                        <p id="dtl-category" class="text-sm font-semibold text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Prioritas</p>
                        <p id="dtl-priority" class="text-sm font-semibold text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Status</p>
                        <p id="dtl-status" class="text-sm font-semibold text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Dibuat</p>
                        <p id="dtl-created" class="text-sm font-semibold text-gray-800"></p>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Deskripsi Pengaduan</p>
                    <div id="dtl-description" class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap"></div>
                </div>
                <div id="dtl-notes-wrap" class="hidden">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Balasan dari Tim Kami</p>
                    <div id="dtl-notes" class="bg-green-50 border border-green-100 rounded-xl p-4 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap"></div>
                </div>
                <div id="dtl-pending" class="hidden bg-amber-50 border border-amber-100 rounded-xl p-4 text-sm text-amber-700 flex items-center gap-2">
                    <i class="fas fa-clock"></i>
                    <span>Tiket Anda sedang menunggu ditangani oleh tim kami.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="cust-toast" class="fixed bottom-6 right-6 z-[999] hidden">
        <div id="cust-toast-inner" class="flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl text-sm font-medium text-white min-w-[260px] max-w-sm">
            <span id="cust-toast-msg"></span>
        </div>
    </div>

    <script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}';

    function showCustToast(msg, type = 'success') {
        const el = document.getElementById('cust-toast');
        document.getElementById('cust-toast-msg').textContent = msg;
        document.getElementById('cust-toast-inner').className = `flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl text-sm font-medium text-white min-w-[260px] max-w-sm ${type === 'success' ? 'bg-green-600' : 'bg-red-500'}`;
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 4000);
    }

    function openTicketModal() {
        document.getElementById('modal-ticket').classList.remove('hidden');
        document.getElementById('modal-ticket').classList.add('flex');
    }

    function closeTicketModal() {
        document.getElementById('modal-ticket').classList.add('hidden');
        document.getElementById('modal-ticket').classList.remove('flex');
    }

    function viewTicketDetail(t) {
        document.getElementById('dtl-number').textContent      = '#' + t.ticket_number;
        document.getElementById('dtl-subject').textContent     = t.subject;
        document.getElementById('dtl-category').textContent    = t.category_label;
        document.getElementById('dtl-priority').textContent    = t.priority_label;
        document.getElementById('dtl-status').textContent      = t.status_label;
        document.getElementById('dtl-created').textContent     = t.created_at;
        document.getElementById('dtl-description').textContent = t.description;

        const notesWrap = document.getElementById('dtl-notes-wrap');
        const pending   = document.getElementById('dtl-pending');
        if (t.admin_notes) {
            document.getElementById('dtl-notes').textContent = t.admin_notes;
            notesWrap.classList.remove('hidden');
            pending.classList.add('hidden');
        } else {
            notesWrap.classList.add('hidden');
            pending.classList.remove('hidden');
        }

        document.getElementById('modal-ticket-detail').classList.remove('hidden');
        document.getElementById('modal-ticket-detail').classList.add('flex');
    }

    function closeTicketDetail() {
        document.getElementById('modal-ticket-detail').classList.add('hidden');
        document.getElementById('modal-ticket-detail').classList.remove('flex');
    }

    async function submitTicket(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-submit-ticket');
        const errEl = document.getElementById('ticket-form-error');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i> Mengirim...';
        errEl.classList.add('hidden');

        try {
            const res = await fetch('{{ route("customer.tickets.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    category:    document.getElementById('t-category').value,
                    priority:    document.getElementById('t-priority').value,
                    subject:     document.getElementById('t-subject').value,
                    description: document.getElementById('t-description').value,
                }),
            });
            const data = await res.json();

            if (!data.success) {
                errEl.textContent = data.message || 'Terjadi kesalahan.';
                errEl.classList.remove('hidden');
                return;
            }

            showCustToast(data.message);
            closeTicketModal();
            document.getElementById('form-ticket').reset();

            // Append row to table
            const t = data.ticket;
            const statusColor = t.status === 'open' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700';
            const row = `<tr class="hover:bg-gray-50/50 transition-colors">
                <td class="py-3 px-4 font-mono text-xs font-bold text-gray-700">${t.ticket_number}</td>
                <td class="py-3 px-4 text-sm font-medium text-gray-800 max-w-[180px] truncate">${t.subject}</td>
                <td class="py-3 px-4 text-xs text-gray-500 hidden sm:table-cell">${t.category_label}</td>
                <td class="py-3 px-4"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${statusColor}">${t.status_label}</span></td>
                <td class="py-3 px-4 text-xs text-gray-500 hidden md:table-cell">${t.created_at.split(' ')[0]}</td>
                <td class="py-3 px-4 text-center">
                    <button onclick="viewTicketDetail(${JSON.stringify(t).replace(/"/g, '&quot;')})"
                            class="text-violet-600 hover:text-violet-800 text-xs font-semibold hover:underline">Lihat</button>
                </td>
            </tr>`;

            // Show table if empty state is visible
            const emptyEl = document.getElementById('ticket-empty');
            if (emptyEl) {
                emptyEl.classList.add('hidden');
                const newTable = document.getElementById('ticket-table-new');
                newTable.classList.remove('hidden');
                document.getElementById('ticket-tbody-new').insertAdjacentHTML('afterbegin', row);
            } else {
                const tbody = document.getElementById('ticket-tbody');
                if (tbody) tbody.insertAdjacentHTML('afterbegin', row);
            }
        } catch (err) {
            errEl.textContent = 'Gagal mengirim tiket: ' + err.message;
            errEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane text-sm"></i> Kirim Tiket';
        }
    }
    </script>
</body>
</html>
