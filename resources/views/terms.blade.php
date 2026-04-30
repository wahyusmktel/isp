<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Syarat dan Ketentuan layanan internet Tim-7 Net, termasuk kebijakan pembayaran melalui Midtrans, kebijakan pengembalian dana, dan perlindungan data pribadi.">
    <title>Syarat & Ketentuan – Tim-7 Net</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='50' fill='%230ea5e9'/><text y='.9em' font-size='70' x='12' fill='white' font-family='Arial' font-weight='bold'>T7</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary:    #0ea5e9;
            --primary-dk: #0284c7;
            --primary-lt: #38bdf8;
            --accent:     #f97316;
            --dark:       #0a0f1e;
            --dark-2:     #0f172a;
            --dark-3:     #1e293b;
            --dark-4:     #334155;
            --text:       #f1f5f9;
            --text-muted: #94a3b8;
            --border:     #1e293b;
            --success:    #22c55e;
            --radius:     12px;
            --transition: .25s ease;
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--dark); color: var(--text); line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--dark-2); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 99px; }

        /* ── NAVBAR ── */
        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(10,15,30,.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
        }
        .nav-inner { max-width: 1200px; margin: 0 auto; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .nav-logo { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 1.2rem; }
        .logo-icon { width: 38px; height: 38px; border-radius: 9px; background: linear-gradient(135deg, var(--primary), var(--primary-dk)); display: flex; align-items: center; justify-content: center; font-size: .8rem; font-weight: 900; color: #fff; box-shadow: 0 4px 14px rgba(14,165,233,.4); flex-shrink: 0; }
        .logo-text span { color: var(--primary); }
        .nav-back { display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: .85rem; font-weight: 500; padding: 8px 14px; border-radius: 8px; border: 1px solid var(--border); transition: all var(--transition); }
        .nav-back:hover { color: var(--primary); border-color: var(--primary); background: rgba(14,165,233,.06); }

        /* ── LAYOUT ── */
        .page-wrap { max-width: 1200px; margin: 0 auto; padding: 48px 24px 80px; display: grid; grid-template-columns: 260px 1fr; gap: 48px; align-items: start; }

        /* ── SIDEBAR TOC ── */
        .toc {
            position: sticky; top: 80px;
            background: var(--dark-2); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 24px 20px;
        }
        .toc h3 { font-size: .82rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 16px; }
        .toc ul { list-style: none; display: flex; flex-direction: column; gap: 2px; }
        .toc ul a {
            display: flex; align-items: center; gap: 10px;
            padding: 7px 10px; border-radius: 7px; font-size: .82rem; color: var(--text-muted);
            transition: all var(--transition);
        }
        .toc ul a:hover, .toc ul a.active { color: var(--primary); background: rgba(14,165,233,.08); }
        .toc ul a .toc-num { font-weight: 700; font-size: .74rem; color: var(--primary); width: 18px; flex-shrink: 0; }
        .toc-meta { margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border); }
        .toc-meta p { font-size: .75rem; color: var(--text-muted); line-height: 1.6; }
        .toc-meta .updated { display: inline-flex; align-items: center; gap: 5px; font-size: .74rem; background: rgba(34,197,94,.1); color: var(--success); border: 1px solid rgba(34,197,94,.25); border-radius: 6px; padding: 3px 8px; margin-bottom: 8px; }

        /* ── CONTENT ── */
        .tnc-content {}
        .tnc-header { margin-bottom: 40px; }
        .tnc-header .label {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(14,165,233,.12); border: 1px solid rgba(14,165,233,.28);
            color: var(--primary-lt); border-radius: 99px;
            font-size: .73rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase;
            padding: 4px 12px; margin-bottom: 14px;
        }
        .tnc-header h1 { font-size: clamp(1.8rem, 3.5vw, 2.6rem); font-weight: 900; line-height: 1.2; }
        .tnc-header h1 span { background: linear-gradient(135deg, var(--primary-lt), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .tnc-header .subtitle { color: var(--text-muted); font-size: .95rem; margin-top: 10px; line-height: 1.7; }
        .intro-box {
            background: var(--dark-3); border: 1px solid var(--border);
            border-left: 4px solid var(--primary);
            border-radius: var(--radius); padding: 20px 22px;
            margin-bottom: 36px; font-size: .88rem; color: var(--text-muted); line-height: 1.8;
        }
        .intro-box strong { color: var(--text); }
        .midtrans-notice {
            display: flex; gap: 14px; align-items: flex-start;
            background: rgba(14,165,233,.07); border: 1px solid rgba(14,165,233,.22);
            border-radius: var(--radius); padding: 18px 20px; margin-bottom: 36px;
        }
        .midtrans-notice .icon { color: var(--primary); font-size: 1.3rem; flex-shrink: 0; margin-top: 2px; }
        .midtrans-notice p { font-size: .86rem; color: var(--text-muted); line-height: 1.75; }
        .midtrans-notice p strong { color: var(--text); }

        /* ── SECTIONS ── */
        .tnc-section { margin-bottom: 48px; scroll-margin-top: 90px; }
        .tnc-section-header {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 20px; padding-bottom: 14px;
            border-bottom: 1px solid var(--border);
        }
        .sec-num {
            width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
            background: linear-gradient(135deg, var(--primary), var(--primary-dk));
            display: flex; align-items: center; justify-content: center;
            font-size: .82rem; font-weight: 800; color: #fff;
            box-shadow: 0 4px 14px rgba(14,165,233,.3);
        }
        .tnc-section h2 { font-size: 1.15rem; font-weight: 700; }
        .tnc-body { font-size: .9rem; color: var(--text-muted); line-height: 1.85; }
        .tnc-body p { margin-bottom: 12px; }
        .tnc-body p:last-child { margin-bottom: 0; }
        .tnc-body h3 { font-size: .95rem; font-weight: 700; color: var(--text); margin: 18px 0 8px; }
        .tnc-body ul, .tnc-body ol { padding-left: 22px; display: flex; flex-direction: column; gap: 7px; margin: 8px 0 12px; }
        .tnc-body ul li, .tnc-body ol li { color: var(--text-muted); font-size: .88rem; line-height: 1.75; }
        .tnc-body strong { color: var(--text); font-weight: 600; }
        .tnc-body a { color: var(--primary); }
        .tnc-body a:hover { text-decoration: underline; }
        .tnc-body .highlight {
            background: var(--dark-3); border: 1px solid var(--border);
            border-radius: 9px; padding: 16px 18px; margin: 12px 0;
        }
        .tnc-body .warn {
            background: rgba(249,115,22,.07); border: 1px solid rgba(249,115,22,.22);
            border-left: 3px solid var(--accent);
            border-radius: 9px; padding: 14px 18px; margin: 12px 0;
            font-size: .86rem;
        }
        .tnc-body .warn strong { color: var(--accent); }
        .tnc-body table { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: .84rem; }
        .tnc-body table th { background: var(--dark-3); padding: 10px 14px; text-align: left; font-weight: 600; color: var(--text); border: 1px solid var(--border); }
        .tnc-body table td { padding: 10px 14px; border: 1px solid var(--border); color: var(--text-muted); vertical-align: top; }
        .tnc-body table tr:hover td { background: rgba(255,255,255,.02); }

        /* ── FOOTER ── */
        .tnc-footer {
            border-top: 1px solid var(--border);
            padding: 40px 24px; max-width: 1200px; margin: 0 auto;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 16px;
        }
        .tnc-footer p { font-size: .82rem; color: var(--text-muted); }
        .midtrans-badge { display: flex; align-items: center; gap: 8px; background: var(--dark-3); border: 1px solid var(--border); border-radius: 8px; padding: 7px 14px; font-size: .78rem; color: var(--text-muted); }
        .midtrans-badge i { color: var(--success); }

        @media (max-width: 900px) {
            .page-wrap { grid-template-columns: 1fr; }
            .toc { position: static; }
        }
        @media (max-width: 600px) {
            .page-wrap { padding: 32px 16px 60px; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-inner">
        <a href="{{ url('/') }}" class="nav-logo">
            <div class="logo-icon">T7</div>
            <span class="logo-text">Tim-7 <span>Net</span></span>
        </a>
        <a href="{{ url('/') }}" class="nav-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>
</nav>

<div class="page-wrap">

    <!-- ══ SIDEBAR: TABLE OF CONTENTS ══ -->
    <aside class="toc">
        <h3><i class="fas fa-list-ul" style="margin-right:6px"></i> Daftar Isi</h3>
        <ul>
            <li><a href="#pasal-1"><span class="toc-num">1</span> Definisi</a></li>
            <li><a href="#pasal-2"><span class="toc-num">2</span> Ruang Lingkup Layanan</a></li>
            <li><a href="#pasal-3"><span class="toc-num">3</span> Pendaftaran & Aktivasi</a></li>
            <li><a href="#pasal-4"><span class="toc-num">4</span> Biaya & Tagihan</a></li>
            <li><a href="#pasal-5"><span class="toc-num">5</span> Sistem Pembayaran Midtrans</a></li>
            <li><a href="#pasal-6"><span class="toc-num">6</span> Kebijakan Pengembalian Dana</a></li>
            <li><a href="#pasal-7"><span class="toc-num">7</span> Kewajiban Pelanggan</a></li>
            <li><a href="#pasal-8"><span class="toc-num">8</span> Larangan Penggunaan</a></li>
            <li><a href="#pasal-9"><span class="toc-num">9</span> Keamanan Transaksi</a></li>
            <li><a href="#pasal-10"><span class="toc-num">10</span> Kerahasiaan & Data Pribadi</a></li>
            <li><a href="#pasal-11"><span class="toc-num">11</span> Jaminan Layanan (SLA)</a></li>
            <li><a href="#pasal-12"><span class="toc-num">12</span> Penangguhan & Pemutusan</a></li>
            <li><a href="#pasal-13"><span class="toc-num">13</span> Batasan Tanggung Jawab</a></li>
            <li><a href="#pasal-14"><span class="toc-num">14</span> Penyelesaian Perselisihan</a></li>
            <li><a href="#pasal-15"><span class="toc-num">15</span> Hukum yang Berlaku</a></li>
            <li><a href="#pasal-16"><span class="toc-num">16</span> Perubahan Ketentuan</a></li>
            <li><a href="#pasal-17"><span class="toc-num">17</span> Informasi Perusahaan</a></li>
        </ul>
        <div class="toc-meta">
            <div class="updated"><i class="fas fa-check-circle"></i> Berlaku efektif</div>
            <p>Terakhir diperbarui:<br><strong style="color:var(--text)">April 2026</strong></p>
            <p style="margin-top:8px">Pertanyaan tentang S&K ini?<br>
            <a href="mailto:admin@tim-7.net" style="color:var(--primary)">admin@tim-7.net</a></p>
        </div>
    </aside>

    <!-- ══ MAIN CONTENT ══ -->
    <main class="tnc-content">

        <div class="tnc-header">
            <div class="label"><i class="fas fa-file-contract"></i> Dokumen Legal</div>
            <h1>Syarat &amp; Ketentuan <span>Tim-7 Net</span></h1>
            <p class="subtitle">
                Syarat dan Ketentuan ini mengatur hubungan antara <strong>Tim-7 Net</strong> selaku penyedia layanan internet
                dan Pelanggan. Layanan pembayaran diproses melalui <strong>Midtrans</strong> (PT Midtrans),
                penyedia jasa pembayaran berlisensi Bank Indonesia.
            </p>
        </div>

        <div class="intro-box">
            <strong>Penting:</strong> Dengan mendaftar, mengaktifkan, atau menggunakan layanan Tim-7 Net,
            Pelanggan dianggap telah membaca, memahami, dan <strong>menyetujui seluruh</strong> Syarat &amp; Ketentuan ini
            beserta kebijakan privasi yang berlaku. Jika Anda tidak menyetujui ketentuan ini, harap tidak melanjutkan
            penggunaan layanan.
        </div>

        <div class="midtrans-notice">
            <i class="fas fa-shield-alt icon"></i>
            <p>
                <strong>Pembayaran diproses oleh Midtrans.</strong> PT Midtrans adalah perusahaan teknologi keuangan
                yang menyediakan layanan pemrosesan pembayaran online. Midtrans telah memperoleh izin dari Bank Indonesia
                sebagai Penyelenggara Jasa Sistem Pembayaran (PJSP) dengan nomor izin
                <strong>18/196/DKSP/68 tanggal 6 September 2016</strong>. Tim-7 Net tidak menyimpan data
                kartu kredit atau rekening bank Pelanggan.
            </p>
        </div>

        <!-- ═══ PASAL 1 ═══ -->
        <div class="tnc-section" id="pasal-1">
            <div class="tnc-section-header">
                <div class="sec-num">1</div>
                <h2>Definisi</h2>
            </div>
            <div class="tnc-body">
                <p>Dalam Syarat &amp; Ketentuan ini, istilah-istilah berikut memiliki pengertian sebagai berikut:</p>
                <ul>
                    <li><strong>"Tim-7 Net" / "Perusahaan" / "Kami"</strong> — Penyedia layanan akses internet berbasis fiber optik yang berdomisili dan beroperasi di Lampung, Indonesia.</li>
                    <li><strong>"Pelanggan" / "Anda"</strong> — Perseorangan atau badan hukum yang mendaftarkan diri dan menggunakan layanan internet Tim-7 Net.</li>
                    <li><strong>"Layanan"</strong> — Seluruh produk dan jasa yang disediakan Tim-7 Net kepada Pelanggan, mencakup akses internet, instalasi, pemeliharaan jaringan, dan layanan pendukung lainnya.</li>
                    <li><strong>"Midtrans"</strong> — PT Midtrans, perusahaan penyedia layanan pemrosesan pembayaran elektronik yang berlisensi dari Bank Indonesia, yang digunakan Tim-7 Net untuk memproses transaksi pembayaran Pelanggan.</li>
                    <li><strong>"Transaksi"</strong> — Setiap kegiatan pembayaran yang dilakukan Pelanggan kepada Tim-7 Net melalui platform Midtrans, termasuk namun tidak terbatas pada pembayaran tagihan bulanan, biaya instalasi, dan biaya tambahan lainnya.</li>
                    <li><strong>"Tagihan"</strong> — Dokumen atau notifikasi elektronik yang diterbitkan Tim-7 Net yang menyatakan jumlah kewajiban pembayaran Pelanggan untuk periode layanan tertentu.</li>
                    <li><strong>"Perangkat"</strong> — Peralatan jaringan yang dipasang Tim-7 Net di lokasi Pelanggan, termasuk Optical Network Unit (ONU/ONT), kabel fiber optik drop, dan aksesori terkait, yang tetap menjadi milik Tim-7 Net selama masa berlangganan.</li>
                    <li><strong>"SLA" (Service Level Agreement)</strong> — Perjanjian tingkat layanan yang mendefinisikan standar kualitas dan ketersediaan layanan yang dijamin Tim-7 Net kepada Pelanggan.</li>
                    <li><strong>"Force Majeure"</strong> — Kejadian di luar kendali Tim-7 Net yang secara langsung menghalangi pemenuhan kewajiban layanan, meliputi bencana alam, pemadaman listrik massal, kerusuhan sipil, kebijakan pemerintah, atau kejadian luar biasa lainnya.</li>
                </ul>
            </div>
        </div>

        <!-- ═══ PASAL 2 ═══ -->
        <div class="tnc-section" id="pasal-2">
            <div class="tnc-section-header">
                <div class="sec-num">2</div>
                <h2>Ruang Lingkup Layanan</h2>
            </div>
            <div class="tnc-body">
                <p>Tim-7 Net menyediakan layanan-layanan berikut kepada Pelanggan berdasarkan paket yang dipilih:</p>
                <ul>
                    <li>Penyediaan akses internet melalui teknologi jaringan fiber optik GPON (Gigabit Passive Optical Network).</li>
                    <li>Pengadaan, pemasangan, dan konfigurasi perangkat CPE (<em>Customer Premises Equipment</em>) di lokasi Pelanggan.</li>
                    <li>Pemeliharaan dan perbaikan infrastruktur jaringan Tim-7 Net.</li>
                    <li>Dukungan teknis (<em>technical support</em>) selama masa berlangganan aktif.</li>
                    <li>Layanan pelanggan melalui saluran komunikasi resmi (WhatsApp, telepon, email).</li>
                </ul>
                <h3>Batasan Layanan</h3>
                <p>Tim-7 Net <strong>tidak</strong> bertanggung jawab atas:</p>
                <ul>
                    <li>Konten internet yang diakses Pelanggan melalui jaringan Tim-7 Net.</li>
                    <li>Keamanan perangkat milik Pelanggan (komputer, router pribadi, smartphone, dll).</li>
                    <li>Layanan pihak ketiga (platform digital, aplikasi, streaming) yang menggunakan koneksi Tim-7 Net.</li>
                    <li>Gangguan pada jaringan backbone internasional yang di luar infrastruktur Tim-7 Net.</li>
                </ul>
                <p>Tim-7 Net berhak mengubah, menambah, atau menghentikan fitur layanan dengan memberikan pemberitahuan minimal <strong>14 (empat belas) hari kalender</strong> kepada Pelanggan melalui media komunikasi resmi.</p>
            </div>
        </div>

        <!-- ═══ PASAL 3 ═══ -->
        <div class="tnc-section" id="pasal-3">
            <div class="tnc-section-header">
                <div class="sec-num">3</div>
                <h2>Pendaftaran &amp; Aktivasi Layanan</h2>
            </div>
            <div class="tnc-body">
                <h3>3.1 Persyaratan Pendaftaran</h3>
                <p>Untuk mendaftar sebagai Pelanggan Tim-7 Net, Anda wajib:</p>
                <ul>
                    <li>Berusia minimal 17 (tujuh belas) tahun atau merupakan badan hukum yang sah di Indonesia.</li>
                    <li>Menyediakan data identitas yang valid: nama lengkap sesuai KTP/KK, nomor KTP, alamat domisili lengkap, dan nomor telepon aktif.</li>
                    <li>Berlokasi di wilayah yang terjangkau jaringan Tim-7 Net.</li>
                    <li>Menyetujui Syarat &amp; Ketentuan ini secara penuh.</li>
                </ul>
                <h3>3.2 Proses Verifikasi</h3>
                <p>Tim-7 Net berhak melakukan verifikasi data pendaftaran. Pendaftaran dinyatakan <strong>sah dan mengikat</strong> setelah:</p>
                <ol>
                    <li>Data pendaftaran diverifikasi oleh tim Tim-7 Net.</li>
                    <li>Survey lokasi dilakukan dan dinyatakan layak secara teknis.</li>
                    <li>Pembayaran biaya pertama (tagihan perdana dan/atau biaya instalasi) diterima dan dikonfirmasi melalui sistem Midtrans.</li>
                </ol>
                <h3>3.3 Waktu Aktivasi</h3>
                <p>Layanan diaktifkan dalam <strong>1–3 hari kerja</strong> setelah konfirmasi pembayaran diterima, tergantung jadwal ketersediaan teknisi. Tim-7 Net akan menghubungi Pelanggan untuk menjadwalkan instalasi.</p>
                <div class="warn">
                    <strong>Perhatian:</strong> Tim-7 Net berhak menolak pendaftaran tanpa kewajiban memberikan alasan jika lokasi Pelanggan berada di luar coverage area, terdapat hambatan teknis yang tidak dapat diatasi, atau jika data yang diberikan terbukti tidak valid.
                </div>
            </div>
        </div>

        <!-- ═══ PASAL 4 ═══ -->
        <div class="tnc-section" id="pasal-4">
            <div class="tnc-section-header">
                <div class="sec-num">4</div>
                <h2>Biaya &amp; Tagihan</h2>
            </div>
            <div class="tnc-body">
                <h3>4.1 Komponen Biaya</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Komponen</th>
                            <th>Keterangan</th>
                            <th>Dapat Dikembalikan?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Biaya Berlangganan Bulanan</strong></td>
                            <td>Dibayarkan setiap bulan sesuai paket yang dipilih</td>
                            <td>Tidak (setelah layanan berjalan)</td>
                        </tr>
                        <tr>
                            <td><strong>Biaya Instalasi</strong></td>
                            <td>Biaya pemasangan perangkat dan konfigurasi awal (jika berlaku)</td>
                            <td>Tidak (setelah instalasi selesai)</td>
                        </tr>
                        <tr>
                            <td><strong>Biaya Keterlambatan</strong></td>
                            <td>Dikenakan jika tagihan melewati batas waktu pembayaran</td>
                            <td>Tidak</td>
                        </tr>
                        <tr>
                            <td><strong>Biaya Penggantian Perangkat</strong></td>
                            <td>Dikenakan jika perangkat Tim-7 Net rusak akibat kelalaian Pelanggan</td>
                            <td>Tidak</td>
                        </tr>
                    </tbody>
                </table>
                <h3>4.2 Siklus Penagihan</h3>
                <ul>
                    <li>Tagihan diterbitkan setiap bulan, pada tanggal yang sama dengan tanggal aktivasi layanan pertama kali.</li>
                    <li>Pelanggan menerima notifikasi tagihan melalui WhatsApp dan/atau email.</li>
                    <li>Batas waktu pembayaran adalah <strong>7 (tujuh) hari kalender</strong> sejak tanggal tagihan diterbitkan.</li>
                </ul>
                <h3>4.3 Keterlambatan Pembayaran</h3>
                <ul>
                    <li>Tagihan yang belum dibayar setelah melewati batas waktu dapat dikenakan biaya keterlambatan sebesar <strong>Rp 10.000,–</strong> per hari.</li>
                    <li>Layanan dapat ditangguhkan sementara apabila tagihan tidak dibayar dalam <strong>7 hari</strong> setelah jatuh tempo.</li>
                    <li>Layanan dipulihkan dalam <strong>1 × 24 jam</strong> setelah pembayaran dikonfirmasi.</li>
                </ul>
                <h3>4.4 Perubahan Harga</h3>
                <p>Tim-7 Net berhak mengubah harga berlangganan dengan memberikan pemberitahuan minimal <strong>30 (tiga puluh) hari kalender</strong> sebelum perubahan berlaku efektif. Pelanggan yang tidak menyetujui perubahan harga berhak mengakhiri berlangganan tanpa dikenakan penalti.</p>
            </div>
        </div>

        <!-- ═══ PASAL 5 ═══ -->
        <div class="tnc-section" id="pasal-5">
            <div class="tnc-section-header">
                <div class="sec-num">5</div>
                <h2>Sistem Pembayaran Midtrans</h2>
            </div>
            <div class="tnc-body">
                <h3>5.1 Metode Pembayaran yang Tersedia</h3>
                <p>Seluruh transaksi pembayaran Tim-7 Net diproses melalui platform Midtrans dan mendukung metode berikut:</p>
                <div class="highlight">
                    <table>
                        <thead><tr><th>Kategori</th><th>Metode</th></tr></thead>
                        <tbody>
                            <tr><td>Transfer Bank / Virtual Account</td><td>BCA, BNI, BRI, Mandiri, Permata, CIMB, dan bank lainnya</td></tr>
                            <tr><td>Dompet Digital (e-Wallet)</td><td>GoPay, OVO, Dana, LinkAja, ShopeePay</td></tr>
                            <tr><td>Kartu Kredit / Debit</td><td>Visa, Mastercard, JCB (3D Secure)</td></tr>
                            <tr><td>Pembayaran Tunai</td><td>Indomaret, Alfamart, Alfamidi</td></tr>
                            <tr><td>QRIS</td><td>Semua aplikasi yang mendukung QRIS</td></tr>
                            <tr><td>Paylater</td><td>Akulaku Paylater, Kredivo</td></tr>
                        </tbody>
                    </table>
                </div>
                <h3>5.2 Ketentuan Transaksi</h3>
                <ul>
                    <li>Setiap transaksi akan diverifikasi oleh sistem Midtrans secara otomatis.</li>
                    <li>Konfirmasi pembayaran dikirimkan kepada Pelanggan melalui email dan/atau WhatsApp.</li>
                    <li>Bukti pembayaran yang valid hanya berupa konfirmasi resmi dari sistem Midtrans atau Tim-7 Net.</li>
                    <li>Transaksi yang gagal atau dibatalkan tidak mengakibatkan perubahan status berlangganan.</li>
                    <li>Pelanggan bertanggung jawab atas keakuratan data yang dimasukkan saat melakukan transaksi.</li>
                </ul>
                <h3>5.3 Keamanan Transaksi</h3>
                <ul>
                    <li>Seluruh transaksi dienkripsi menggunakan protokol <strong>SSL/TLS</strong>.</li>
                    <li>Sistem pembayaran Midtrans tersertifikasi <strong>PCI-DSS</strong> (<em>Payment Card Industry Data Security Standard</em>).</li>
                    <li>Tim-7 Net <strong>tidak pernah</strong> menyimpan data kartu kredit, nomor rekening, atau PIN/OTP Pelanggan.</li>
                    <li>Midtrans diawasi dan berlisensi dari <strong>Bank Indonesia</strong> sebagai PJSP (Penyelenggara Jasa Sistem Pembayaran).</li>
                </ul>
                <div class="warn">
                    <strong>Peringatan Keamanan:</strong> Tim-7 Net dan Midtrans tidak pernah meminta data kartu kredit, PIN, atau OTP melalui telepon, WhatsApp, atau email. Jangan pernah memberikan informasi tersebut kepada siapapun yang mengaku sebagai perwakilan Tim-7 Net atau Midtrans.
                </div>
            </div>
        </div>

        <!-- ═══ PASAL 6 ═══ -->
        <div class="tnc-section" id="pasal-6">
            <div class="tnc-section-header">
                <div class="sec-num">6</div>
                <h2>Kebijakan Pengembalian Dana (Refund)</h2>
            </div>
            <div class="tnc-body">
                <h3>6.1 Kondisi yang Memenuhi Syarat Refund</h3>
                <p>Tim-7 Net akan memproses pengembalian dana dalam kondisi berikut:</p>
                <ol>
                    <li><strong>Pembayaran Ganda (<em>Double Payment</em>)</strong> — Terjadi pembayaran lebih dari satu kali untuk tagihan yang sama akibat kesalahan sistem atau kesalahan Pelanggan.</li>
                    <li><strong>Pembatalan Sebelum Instalasi</strong> — Pelanggan membatalkan pendaftaran setelah membayar biaya berlangganan pertama namun proses instalasi belum dilaksanakan.</li>
                    <li><strong>Layanan Tidak Dapat Diaktifkan</strong> — Tim-7 Net tidak dapat mengaktifkan layanan dalam 14 hari sejak pembayaran diterima karena keterbatasan teknis internal Tim-7 Net.</li>
                    <li><strong>Garansi 7 Hari</strong> — Pelanggan mengajukan pembatalan dalam 7 hari pertama sejak aktivasi dan terbukti layanan tidak memenuhi standar kecepatan minimum yang dijanjikan.</li>
                </ol>
                <h3>6.2 Kondisi yang Tidak Memenuhi Syarat Refund</h3>
                <ul>
                    <li>Biaya instalasi tidak dapat dikembalikan setelah proses instalasi selesai dilaksanakan.</li>
                    <li>Tagihan bulanan yang telah berjalan tidak dapat dikembalikan meskipun layanan tidak digunakan secara aktif oleh Pelanggan.</li>
                    <li>Pembatalan akibat pelanggaran Syarat &amp; Ketentuan oleh Pelanggan.</li>
                    <li>Gangguan layanan yang disebabkan oleh kondisi Force Majeure.</li>
                    <li>Pengembalian dana atas biaya keterlambatan yang telah dikenakan secara sah.</li>
                </ul>
                <h3>6.3 Prosedur Pengajuan Refund</h3>
                <ol>
                    <li>Ajukan permintaan refund melalui email ke <a href="mailto:admin@tim-7.net">admin@tim-7.net</a> atau WhatsApp ke <a href="https://wa.me/6282279122727" target="_blank">+62 822-7912-2727</a>.</li>
                    <li>Sertakan: nama lengkap, nomor KTP, nomor tagihan, tanggal transaksi, jumlah yang dibayarkan, dan alasan permintaan refund.</li>
                    <li>Tim verifikasi Tim-7 Net akan menghubungi Pelanggan dalam <strong>1–3 hari kerja</strong>.</li>
                    <li>Jika disetujui, dana dikembalikan ke metode pembayaran asal dalam <strong>7–14 hari kerja</strong> melalui sistem Midtrans.</li>
                </ol>
                <div class="warn">
                    <strong>Catatan:</strong> Proses refund melalui kartu kredit dapat memerlukan waktu hingga 30 hari kerja tergantung kebijakan bank penerbit kartu Pelanggan.
                </div>
            </div>
        </div>

        <!-- ═══ PASAL 7 ═══ -->
        <div class="tnc-section" id="pasal-7">
            <div class="tnc-section-header">
                <div class="sec-num">7</div>
                <h2>Kewajiban Pelanggan</h2>
            </div>
            <div class="tnc-body">
                <p>Sebagai Pelanggan Tim-7 Net, Anda berkewajiban untuk:</p>
                <ol>
                    <li>Memberikan data dan informasi yang <strong>benar, lengkap, dan terkini</strong> pada saat pendaftaran maupun selama masa berlangganan. Pembaruan data dilakukan melalui saluran komunikasi resmi Tim-7 Net.</li>
                    <li><strong>Membayar tagihan tepat waktu</strong> sesuai siklus penagihan yang ditetapkan.</li>
                    <li><strong>Menjaga perangkat</strong> milik Tim-7 Net (ONU/ONT, kabel drop fiber, konektor, dll) yang terpasang di lokasi Pelanggan dari kerusakan, kehilangan, atau penyalahgunaan. Biaya penggantian perangkat yang rusak akibat kelalaian Pelanggan menjadi tanggung jawab Pelanggan.</li>
                    <li><strong>Memberikan akses</strong> yang memadai kepada teknisi Tim-7 Net untuk keperluan pemeliharaan, perbaikan, peningkatan, atau penghentian layanan.</li>
                    <li><strong>Melaporkan gangguan</strong> layanan sesegera mungkin kepada tim support Tim-7 Net.</li>
                    <li>Tidak melakukan modifikasi, pemindahan, atau pembongkaran perangkat Tim-7 Net tanpa izin tertulis dari Tim-7 Net.</li>
                    <li>Memberitahu Tim-7 Net apabila terdapat perubahan alamat atau pindah lokasi minimal <strong>14 hari</strong> sebelumnya.</li>
                </ol>
            </div>
        </div>

        <!-- ═══ PASAL 8 ═══ -->
        <div class="tnc-section" id="pasal-8">
            <div class="tnc-section-header">
                <div class="sec-num">8</div>
                <h2>Larangan Penggunaan</h2>
            </div>
            <div class="tnc-body">
                <p>Pelanggan dilarang menggunakan layanan Tim-7 Net untuk kegiatan-kegiatan berikut:</p>
                <ul>
                    <li><strong>Reselling tanpa izin:</strong> Menjual kembali, menyewakan, atau mendistribusikan akses internet Tim-7 Net kepada pihak ketiga tanpa izin tertulis dari Tim-7 Net.</li>
                    <li><strong>Konten ilegal:</strong> Mengakses, menyimpan, atau menyebarluaskan konten pornografi, perjudian online, ujaran kebencian, atau konten lain yang dilarang oleh hukum Indonesia.</li>
                    <li><strong>Aktivitas siber berbahaya:</strong> Melakukan serangan DDoS, <em>hacking</em>, <em>phishing</em>, penyebaran malware/virus, atau aktivitas yang mengancam keamanan sistem pihak lain.</li>
                    <li><strong>Pelanggaran hak cipta:</strong> Mendistribusikan konten berhak cipta tanpa izin dari pemilik hak.</li>
                    <li><strong>Spam:</strong> Mengirimkan pesan massal yang tidak diminta (<em>spam</em>) melalui email atau platform komunikasi lainnya.</li>
                    <li><strong>Penggunaan berlebihan:</strong> Melakukan aktivitas yang secara signifikan mengganggu kenyamanan Pelanggan lain dalam jaringan bersama Tim-7 Net.</li>
                    <li>Segala aktivitas yang melanggar peraturan perundang-undangan yang berlaku di Indonesia.</li>
                </ul>
                <p>Pelanggaran terhadap ketentuan ini memberikan hak kepada Tim-7 Net untuk <strong>memutus layanan secara sepihak tanpa pemberitahuan sebelumnya</strong> dan tanpa kewajiban pengembalian dana, serta Tim-7 Net berhak melaporkan pelanggaran kepada pihak berwajib.</p>
            </div>
        </div>

        <!-- ═══ PASAL 9 ═══ -->
        <div class="tnc-section" id="pasal-9">
            <div class="tnc-section-header">
                <div class="sec-num">9</div>
                <h2>Keamanan Transaksi &amp; Akun</h2>
            </div>
            <div class="tnc-body">
                <h3>9.1 Tanggung Jawab Keamanan Akun</h3>
                <ul>
                    <li>Pelanggan bertanggung jawab penuh atas keamanan akun dan informasi login yang diberikan Tim-7 Net.</li>
                    <li>Pelanggan wajib segera melaporkan kepada Tim-7 Net jika terdapat indikasi penyalahgunaan akun atau akses tidak sah.</li>
                </ul>
                <h3>9.2 Keamanan Pembayaran</h3>
                <ul>
                    <li>Tim-7 Net menggunakan sistem pembayaran Midtrans yang tersertifikasi PCI-DSS Level 1, standar keamanan tertinggi dalam industri pembayaran digital.</li>
                    <li>Seluruh data transaksi dienkripsi dengan teknologi <strong>256-bit SSL/TLS</strong>.</li>
                    <li>Tim-7 Net tidak memiliki akses terhadap data pembayaran sensitif Pelanggan (nomor kartu, CVV, PIN).</li>
                    <li>Verifikasi OTP dan autentikasi 3D Secure diberlakukan untuk transaksi menggunakan kartu kredit/debit.</li>
                </ul>
                <h3>9.3 Tindakan Pencegahan Penipuan</h3>
                <p>Tim-7 Net menerapkan mekanisme deteksi penipuan untuk melindungi Pelanggan. Jika sistem mendeteksi aktivitas mencurigakan, Tim-7 Net berhak menghentikan sementara akses pembayaran dan menghubungi Pelanggan untuk verifikasi.</p>
            </div>
        </div>

        <!-- ═══ PASAL 10 ═══ -->
        <div class="tnc-section" id="pasal-10">
            <div class="tnc-section-header">
                <div class="sec-num">10</div>
                <h2>Kerahasiaan &amp; Perlindungan Data Pribadi</h2>
            </div>
            <div class="tnc-body">
                <p>Tim-7 Net berkomitmen untuk melindungi data pribadi Pelanggan sesuai dengan <strong>Undang-Undang Nomor 27 Tahun 2022 tentang Perlindungan Data Pribadi (UU PDP)</strong> dan regulasi terkait lainnya.</p>
                <h3>10.1 Data yang Dikumpulkan</h3>
                <ul>
                    <li><strong>Data Identitas:</strong> Nama lengkap, nomor KTP, tanggal lahir.</li>
                    <li><strong>Data Kontak:</strong> Alamat domisili, nomor telepon, alamat email.</li>
                    <li><strong>Data Teknis:</strong> Alamat IP, data penggunaan bandwidth, informasi perangkat.</li>
                    <li><strong>Data Transaksi:</strong> Riwayat pembayaran, metode pembayaran (tanpa data sensitif).</li>
                </ul>
                <h3>10.2 Tujuan Penggunaan Data</h3>
                <ul>
                    <li>Pengelolaan akun dan penyediaan layanan internet.</li>
                    <li>Pemrosesan tagihan dan transaksi pembayaran melalui Midtrans.</li>
                    <li>Komunikasi terkait layanan (pemberitahuan gangguan, tagihan, pembaruan).</li>
                    <li>Peningkatan kualitas layanan dan pengembangan produk.</li>
                    <li>Kepatuhan terhadap kewajiban hukum dan regulasi.</li>
                </ul>
                <h3>10.3 Berbagi Data</h3>
                <ul>
                    <li>Tim-7 Net <strong>tidak menjual</strong> data pribadi Pelanggan kepada pihak ketiga.</li>
                    <li>Data dibagikan kepada <strong>Midtrans</strong> semata-mata untuk keperluan pemrosesan pembayaran.</li>
                    <li>Data dapat diungkapkan kepada instansi berwenang atas <strong>permintaan hukum yang sah</strong> (putusan pengadilan, permintaan penyidik, dll).</li>
                </ul>
                <h3>10.4 Hak-Hak Pelanggan atas Data Pribadi</h3>
                <ul>
                    <li><strong>Hak Akses:</strong> Meminta salinan data pribadi yang tersimpan di Tim-7 Net.</li>
                    <li><strong>Hak Koreksi:</strong> Meminta pembaruan atau perbaikan data yang tidak akurat.</li>
                    <li><strong>Hak Penghapusan:</strong> Meminta penghapusan data pribadi (sesuai batasan hukum yang berlaku).</li>
                    <li><strong>Hak Keberatan:</strong> Mengajukan keberatan atas pemrosesan data untuk tujuan tertentu.</li>
                </ul>
                <p>Permintaan terkait data pribadi dapat diajukan ke: <a href="mailto:admin@tim-7.net">admin@tim-7.net</a></p>
                <h3>10.5 Retensi Data</h3>
                <p>Data Pelanggan disimpan selama masa berlangganan aktif dan hingga <strong>5 (lima) tahun</strong> setelah berlangganan berakhir untuk keperluan kepatuhan perpajakan dan hukum. Setelah periode tersebut, data akan dihapus secara aman.</p>
            </div>
        </div>

        <!-- ═══ PASAL 11 ═══ -->
        <div class="tnc-section" id="pasal-11">
            <div class="tnc-section-header">
                <div class="sec-num">11</div>
                <h2>Jaminan Kualitas Layanan (SLA)</h2>
            </div>
            <div class="tnc-body">
                <h3>11.1 Standar Layanan</h3>
                <table>
                    <thead><tr><th>Indikator</th><th>Standar</th></tr></thead>
                    <tbody>
                        <tr><td>Ketersediaan Jaringan (<em>Uptime</em>)</td><td>Minimum 99,5% per bulan</td></tr>
                        <tr><td>Kecepatan Download</td><td>Sesuai paket yang dipilih (±20%)</td></tr>
                        <tr><td>Latensi (Ping)</td><td>≤ 20 ms untuk jaringan lokal</td></tr>
                        <tr><td>Waktu Respons Support</td><td>≤ 2 jam di hari kerja</td></tr>
                        <tr><td>Waktu Perbaikan Gangguan Kritis</td><td>≤ 4 jam setelah laporan diterima</td></tr>
                    </tbody>
                </table>
                <h3>11.2 Kompensasi Gangguan</h3>
                <p>Jika ketersediaan layanan dalam satu bulan di bawah standar SLA akibat kesalahan Tim-7 Net (bukan Force Majeure), Pelanggan berhak mengajukan kompensasi berupa:</p>
                <ul>
                    <li>Perpanjangan masa berlangganan setara durasi gangguan, atau</li>
                    <li>Potongan tagihan bulan berikutnya secara proporsional.</li>
                </ul>
                <p>Kompensasi tidak berlaku untuk gangguan yang disebabkan oleh Force Majeure atau faktor di luar kendali Tim-7 Net.</p>
            </div>
        </div>

        <!-- ═══ PASAL 12 ═══ -->
        <div class="tnc-section" id="pasal-12">
            <div class="tnc-section-header">
                <div class="sec-num">12</div>
                <h2>Penangguhan &amp; Pemutusan Layanan</h2>
            </div>
            <div class="tnc-body">
                <h3>12.1 Penangguhan Sementara oleh Tim-7 Net</h3>
                <p>Tim-7 Net berhak menangguhkan layanan tanpa kompensasi dalam kondisi berikut:</p>
                <ul>
                    <li>Tagihan tidak dibayar dalam <strong>7 hari</strong> setelah jatuh tempo.</li>
                    <li>Terdapat indikasi pelanggaran Syarat &amp; Ketentuan.</li>
                    <li>Pemeliharaan jaringan terjadwal (dengan pemberitahuan minimal 24 jam sebelumnya).</li>
                    <li>Pemeliharaan darurat untuk menjaga stabilitas jaringan secara keseluruhan.</li>
                </ul>
                <h3>12.2 Pemutusan Permanen</h3>
                <p>Tim-7 Net berhak memutus layanan secara permanen jika:</p>
                <ul>
                    <li>Tagihan tidak dilunasi dalam <strong>30 hari</strong> setelah penangguhan pertama.</li>
                    <li>Pelanggan terbukti melanggar ketentuan Pasal 8 (Larangan Penggunaan).</li>
                    <li>Pelanggan memberikan informasi palsu saat pendaftaran.</li>
                    <li>Atas permintaan Pelanggan (pembatalan berlangganan).</li>
                </ul>
                <h3>12.3 Pembatalan oleh Pelanggan</h3>
                <ul>
                    <li>Pelanggan dapat mengakhiri berlangganan dengan menghubungi Tim-7 Net minimal <strong>14 hari</strong> sebelum tanggal penagihan berikutnya.</li>
                    <li>Setelah pemutusan, seluruh perangkat milik Tim-7 Net wajib dikembalikan dalam kondisi baik dalam <strong>14 hari</strong>.</li>
                    <li>Kerusakan perangkat akibat kelalaian dikenakan biaya penggantian sesuai harga pasar.</li>
                </ul>
            </div>
        </div>

        <!-- ═══ PASAL 13 ═══ -->
        <div class="tnc-section" id="pasal-13">
            <div class="tnc-section-header">
                <div class="sec-num">13</div>
                <h2>Batasan Tanggung Jawab</h2>
            </div>
            <div class="tnc-body">
                <p>Tim-7 Net <strong>tidak bertanggung jawab</strong> atas kerugian yang timbul akibat:</p>
                <ul>
                    <li>Gangguan layanan yang disebabkan oleh kondisi <strong>Force Majeure</strong>.</li>
                    <li>Kerugian bisnis, finansial, atau kehilangan data Pelanggan akibat gangguan koneksi internet.</li>
                    <li>Kerusakan perangkat milik Pelanggan yang disebabkan oleh penggunaan layanan yang tidak sesuai petunjuk teknis.</li>
                    <li>Konten atau materi yang diakses, diunggah, atau disebarkan Pelanggan melalui jaringan Tim-7 Net.</li>
                    <li>Tindakan penipuan atau kejahatan siber yang dilakukan oleh pihak ketiga yang tidak berkaitan dengan Tim-7 Net.</li>
                    <li>Gangguan pada layanan pihak ketiga (platform digital, server game, layanan streaming, dll) yang menggunakan koneksi Tim-7 Net.</li>
                </ul>
                <p>Dalam hal apapun, <strong>total kewajiban ganti rugi</strong> Tim-7 Net kepada Pelanggan tidak akan melebihi jumlah tagihan berlangganan <strong>1 (satu) bulan</strong> yang berlaku pada saat kejadian.</p>
            </div>
        </div>

        <!-- ═══ PASAL 14 ═══ -->
        <div class="tnc-section" id="pasal-14">
            <div class="tnc-section-header">
                <div class="sec-num">14</div>
                <h2>Penyelesaian Perselisihan</h2>
            </div>
            <div class="tnc-body">
                <ol>
                    <li>
                        <strong>Musyawarah:</strong> Setiap perselisihan antara Tim-7 Net dan Pelanggan diselesaikan terlebih dahulu melalui musyawarah untuk mufakat paling lambat <strong>30 (tiga puluh) hari kalender</strong> sejak perselisihan dilaporkan secara tertulis.
                    </li>
                    <li>
                        <strong>BPSK:</strong> Jika musyawarah tidak menghasilkan kesepakatan, perselisihan dapat diajukan ke <strong>Badan Penyelesaian Sengketa Konsumen (BPSK)</strong> setempat.
                    </li>
                    <li>
                        <strong>Pengadilan:</strong> Apabila penyelesaian melalui BPSK tidak tercapai, para pihak sepakat untuk menyelesaikan perselisihan melalui <strong>Pengadilan Negeri Lampung</strong> sesuai yurisdiksi yang berlaku.
                    </li>
                    <li>
                        <strong>Perselisihan terkait Midtrans:</strong> Untuk perselisihan yang berkaitan dengan transaksi pembayaran yang diproses oleh Midtrans, Pelanggan dapat menghubungi layanan pelanggan Midtrans di <strong>support@midtrans.com</strong> atau melalui saluran resmi Midtrans lainnya.
                    </li>
                </ol>
            </div>
        </div>

        <!-- ═══ PASAL 15 ═══ -->
        <div class="tnc-section" id="pasal-15">
            <div class="tnc-section-header">
                <div class="sec-num">15</div>
                <h2>Hukum yang Berlaku</h2>
            </div>
            <div class="tnc-body">
                <p>Syarat &amp; Ketentuan ini dibuat dan ditafsirkan berdasarkan hukum yang berlaku di <strong>Negara Kesatuan Republik Indonesia</strong>, termasuk namun tidak terbatas pada:</p>
                <ul>
                    <li>UU No. 36 Tahun 1999 tentang Telekomunikasi dan perubahannya.</li>
                    <li>UU No. 11 Tahun 2008 jo. UU No. 19 Tahun 2016 tentang Informasi dan Transaksi Elektronik (ITE).</li>
                    <li>UU No. 8 Tahun 1999 tentang Perlindungan Konsumen.</li>
                    <li>UU No. 27 Tahun 2022 tentang Perlindungan Data Pribadi (UU PDP).</li>
                    <li>PP No. 71 Tahun 2019 tentang Penyelenggaraan Sistem dan Transaksi Elektronik.</li>
                    <li>Peraturan Bank Indonesia terkait penyelenggaraan pemrosesan transaksi pembayaran.</li>
                    <li>Peraturan Menteri Komunikasi dan Informatika terkait penyelenggaraan jasa telekomunikasi.</li>
                </ul>
            </div>
        </div>

        <!-- ═══ PASAL 16 ═══ -->
        <div class="tnc-section" id="pasal-16">
            <div class="tnc-section-header">
                <div class="sec-num">16</div>
                <h2>Perubahan Syarat &amp; Ketentuan</h2>
            </div>
            <div class="tnc-body">
                <ul>
                    <li>Tim-7 Net berhak mengubah, memperbarui, atau merevisi Syarat &amp; Ketentuan ini sewaktu-waktu sesuai kebutuhan operasional dan/atau perubahan regulasi.</li>
                    <li>Perubahan material akan diumumkan melalui website Tim-7 Net dan/atau notifikasi kepada Pelanggan terdaftar paling lambat <strong>14 (empat belas) hari kalender</strong> sebelum berlaku efektif.</li>
                    <li>Versi terbaru Syarat &amp; Ketentuan selalu tersedia di halaman ini.</li>
                    <li>Penggunaan layanan Tim-7 Net setelah tanggal efektif perubahan dianggap sebagai penerimaan Pelanggan terhadap Syarat &amp; Ketentuan yang telah diperbarui.</li>
                    <li>Jika Pelanggan tidak menyetujui perubahan, Pelanggan berhak mengakhiri berlangganan sesuai prosedur yang diatur dalam Pasal 12.3.</li>
                </ul>
            </div>
        </div>

        <!-- ═══ PASAL 17 ═══ -->
        <div class="tnc-section" id="pasal-17">
            <div class="tnc-section-header">
                <div class="sec-num">17</div>
                <h2>Informasi Perusahaan</h2>
            </div>
            <div class="tnc-body">
                <div class="highlight">
                    <table>
                        <tbody>
                            <tr><td style="width:200px"><strong>Nama Perusahaan</strong></td><td>Tim-7 Net</td></tr>
                            <tr><td><strong>Jenis Usaha</strong></td><td>Penyedia Layanan Internet (ISP) – Fiber Optik</td></tr>
                            <tr><td><strong>Domisili</strong></td><td>Lampung, Indonesia</td></tr>
                            <tr><td><strong>Koordinat Operasional</strong></td><td>-5.37463823669505, 105.07924978783007</td></tr>
                            <tr><td><strong>Email Resmi</strong></td><td><a href="mailto:admin@tim-7.net">admin@tim-7.net</a></td></tr>
                            <tr><td><strong>Customer Service</strong></td><td>+62 822-7912-2727 (Herma, WhatsApp)</td></tr>
                            <tr><td><strong>Jam Operasional CS</strong></td><td>Senin–Sabtu, 08.00–20.00 WIB</td></tr>
                            <tr><td><strong>Payment Gateway</strong></td><td>Midtrans (PT Midtrans) – Izin BI No. 18/196/DKSP/68</td></tr>
                        </tbody>
                    </table>
                </div>
                <p style="margin-top:16px">Untuk pertanyaan, keluhan, atau permintaan terkait Syarat &amp; Ketentuan ini, silakan hubungi kami:</p>
                <ul>
                    <li>Email: <a href="mailto:admin@tim-7.net">admin@tim-7.net</a></li>
                    <li>WhatsApp: <a href="https://wa.me/6282279122727" target="_blank">+62 822-7912-2727</a></li>
                </ul>
            </div>
        </div>

        <!-- Print / Download -->
        <div style="text-align:center;padding:32px 0 8px;border-top:1px solid var(--border)">
            <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:16px">Simpan salinan Syarat &amp; Ketentuan ini untuk referensi Anda.</p>
            <button onclick="window.print()" style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:var(--dark-3);border:1px solid var(--border);border-radius:10px;color:var(--text);font-family:inherit;font-size:.85rem;font-weight:500;cursor:pointer;transition:all .25s ease" onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)'">
                <i class="fas fa-print"></i> Cetak / Simpan sebagai PDF
            </button>
        </div>

    </main>
</div>

<!-- FOOTER -->
<div style="border-top:1px solid var(--border)">
    <div class="tnc-footer">
        <p>&copy; {{ date('Y') }} Tim-7 Net. Hak Cipta Dilindungi. | Lampung, Indonesia</p>
        <div class="midtrans-badge">
            <i class="fas fa-lock"></i>
            Pembayaran aman via <strong style="color:var(--text);margin-left:4px">Midtrans</strong>
        </div>
    </div>
</div>

<script>
    // Highlight active TOC link on scroll
    const sections = document.querySelectorAll('.tnc-section');
    const tocLinks = document.querySelectorAll('.toc ul a');

    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                tocLinks.forEach(l => l.classList.remove('active'));
                const link = document.querySelector(`.toc ul a[href="#${e.target.id}"]`);
                if (link) link.classList.add('active');
            }
        });
    }, { rootMargin: '-20% 0px -60% 0px' });

    sections.forEach(s => obs.observe(s));
</script>

</body>
</html>
