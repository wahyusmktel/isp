<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Seed SEO-focused public news articles.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => '10 Tips Internet Stabil di Rumah agar Video Call, Game, dan Streaming Tetap Lancar',
                'excerpt' => 'Panduan praktis membuat koneksi internet rumah lebih stabil, mulai dari posisi router, pilihan frekuensi Wi-Fi, sampai kebiasaan mengecek perangkat yang menyedot bandwidth.',
                'body' => $this->article([
                    'Koneksi internet yang terasa lambat tidak selalu disebabkan oleh paket internet. Dalam banyak kasus, masalahnya muncul dari posisi router, terlalu banyak perangkat aktif, interferensi sinyal, atau kabel jaringan yang sudah tidak layak. Karena itu, langkah pertama untuk mendapatkan internet stabil di rumah adalah memeriksa kondisi jaringan dari sisi pengguna.',
                    'Letakkan router di area terbuka, lebih tinggi dari lantai, dan sedekat mungkin dengan titik aktivitas utama. Hindari menaruh router di belakang televisi, di dalam lemari, dekat microwave, atau menempel pada tembok tebal. Sinyal Wi-Fi bekerja lebih baik ketika tidak terhalang benda padat dan tidak bertabrakan dengan perangkat elektronik lain.',
                    'Gunakan jaringan 5 GHz untuk aktivitas yang membutuhkan latensi rendah seperti meeting online, gaming, dan streaming resolusi tinggi. Frekuensi 2.4 GHz tetap berguna untuk jarak lebih jauh dan perangkat IoT, tetapi biasanya lebih padat karena dipakai banyak perangkat rumah tangga.',
                    'Periksa perangkat yang tersambung ke Wi-Fi. Satu ponsel yang sedang backup foto, satu laptop yang mengunduh update besar, atau smart TV yang streaming 4K dapat memengaruhi pengalaman perangkat lain. Jika router mendukung fitur quality of service, prioritaskan perangkat kerja, kelas online, atau perangkat kasir bisnis kecil.',
                    'Restart router secara berkala ketika koneksi terasa tidak normal, tetapi jangan menjadikannya satu-satunya solusi. Jika gangguan sering berulang, cek kabel LAN, adaptor daya, suhu router, dan jumlah perangkat yang terhubung. Router lama yang bekerja 24 jam setiap hari dapat kehilangan performa karena panas dan keterbatasan memori.',
                    'Untuk rumah bertingkat atau bangunan memanjang, pertimbangkan access point tambahan atau mesh Wi-Fi. Repeater murah memang mudah dipasang, tetapi sering mengorbankan kecepatan. Access point kabel lebih stabil karena jalur utama tetap lewat LAN, bukan memantulkan sinyal Wi-Fi yang sudah lemah.',
                    'Terakhir, lakukan speed test dengan benar. Uji memakai kabel LAN langsung ke router untuk mengetahui performa paket utama, lalu bandingkan dengan hasil Wi-Fi dari beberapa ruangan. Cara ini membantu membedakan masalah dari sisi provider, router, atau cakupan Wi-Fi di rumah.',
                    'Dengan pengaturan yang rapi, internet rumah bisa terasa jauh lebih konsisten. Target utamanya bukan hanya angka Mbps tinggi, tetapi ping stabil, jitter rendah, dan sinyal merata di area yang benar-benar dipakai.',
                ]),
                'cover_image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1200&q=80',
                'category' => 'tips',
                'meta_title' => '10 Tips Internet Stabil di Rumah untuk Wi-Fi Lancar',
                'meta_description' => 'Pelajari 10 tips internet stabil di rumah: posisi router, 2.4 GHz vs 5 GHz, QoS, kabel LAN, mesh Wi-Fi, dan cara speed test yang benar.',
                'published_at' => now()->subDays(1)->setTime(8, 30),
            ],
            [
                'title' => 'Apa Itu Wi-Fi 7 dan Kapan Rumah Perlu Upgrade Router?',
                'excerpt' => 'Wi-Fi 7 menawarkan kapasitas lebih besar, latensi lebih rendah, dan multi-link operation. Namun tidak semua rumah harus langsung mengganti router.',
                'body' => $this->article([
                    'Wi-Fi 7 adalah generasi Wi-Fi modern yang dirancang untuk lingkungan dengan banyak perangkat, kebutuhan bandwidth besar, dan aplikasi real-time. Teknologi ini membawa fitur seperti kanal lebih lebar, modulasi lebih efisien, dan multi-link operation yang memungkinkan perangkat memanfaatkan beberapa jalur koneksi secara lebih pintar.',
                    'Bagi pengguna rumahan, manfaat paling terasa bukan sekadar angka kecepatan maksimum. Wi-Fi 7 lebih menarik karena membantu menjaga koneksi tetap responsif ketika rumah dipenuhi ponsel, laptop, smart TV, kamera CCTV, konsol game, dan perangkat smart home. Koneksi yang konsisten sering kali lebih penting daripada hasil speed test sesaat.',
                    'Meski begitu, upgrade router tidak selalu wajib. Jika paket internet masih di bawah 100 Mbps, rumah tidak terlalu padat perangkat, dan router Wi-Fi 5 atau Wi-Fi 6 masih stabil, peningkatan ke Wi-Fi 7 mungkin belum mendesak. Pengguna akan lebih dulu merasakan manfaat dari penempatan router yang benar atau pemasangan access point tambahan.',
                    'Upgrade mulai masuk akal ketika rumah memakai paket berkecepatan tinggi, banyak perangkat aktif bersamaan, sering video conference, bermain game online, atau streaming 4K di beberapa layar. Wi-Fi 7 juga lebih relevan untuk rumah yang ingin siap beberapa tahun ke depan, terutama jika perangkat baru sudah mendukung standar tersebut.',
                    'Sebelum membeli, pastikan perangkat klien juga mendukung Wi-Fi 7. Router canggih tetap bisa melayani perangkat lama, tetapi fitur terbaiknya hanya terasa pada ponsel, laptop, atau adaptor yang kompatibel. Cek juga apakah router memiliki port WAN dan LAN gigabit atau multi-gigabit agar tidak menjadi bottleneck.',
                    'Untuk ISP lokal, edukasi Wi-Fi 7 dapat menjadi konten yang bernilai tinggi karena banyak pelanggan menyamakan internet cepat dengan router mahal. Artikel edukatif membantu pelanggan memahami bahwa performa internet adalah gabungan paket, jaringan fiber, router, posisi perangkat, dan kualitas instalasi.',
                    'Kesimpulannya, Wi-Fi 7 adalah langkah maju yang kuat, tetapi keputusan upgrade harus disesuaikan dengan kebutuhan nyata. Rumah padat perangkat dan paket internet cepat akan mendapat manfaat lebih besar dibanding rumah kecil dengan pemakaian ringan.',
                ]),
                'cover_image' => 'https://images.unsplash.com/photo-1600267165477-6d4cc741b379?auto=format&fit=crop&w=1200&q=80',
                'category' => 'umum',
                'meta_title' => 'Apa Itu Wi-Fi 7? Manfaat dan Waktu Upgrade Router',
                'meta_description' => 'Kenali Wi-Fi 7, manfaatnya untuk rumah padat perangkat, fitur multi-link operation, dan kapan waktu terbaik upgrade router internet.',
                'published_at' => now()->subDays(2)->setTime(9, 10),
            ],
            [
                'title' => 'Fiber Optik vs Wireless: Mana yang Lebih Stabil untuk Internet Rumah dan Bisnis?',
                'excerpt' => 'Perbandingan fiber optik dan wireless dari sisi stabilitas, latensi, kapasitas, biaya instalasi, dan kebutuhan pengguna rumah maupun bisnis.',
                'body' => $this->article([
                    'Fiber optik dan wireless sama-sama dipakai untuk menghadirkan internet, tetapi karakter keduanya berbeda. Fiber optik mengirim data melalui cahaya di dalam kabel kaca atau plastik khusus, sedangkan wireless mengandalkan gelombang radio dari titik pemancar ke penerima. Perbedaan media ini memengaruhi stabilitas, latensi, dan kapasitas jaringan.',
                    'Dari sisi stabilitas, fiber optik biasanya lebih unggul karena tidak mudah terganggu cuaca, kepadatan sinyal, atau halangan bangunan. Selama kabel, konektor, dan perangkat optik dalam kondisi baik, kualitas koneksi dapat dipertahankan secara konsisten. Inilah alasan fiber banyak dipilih untuk rumah, kantor, sekolah, dan bisnis yang membutuhkan internet aktif sepanjang hari.',
                    'Wireless tetap punya kelebihan besar: pemasangan lebih cepat dan fleksibel untuk area yang belum terjangkau kabel. Di wilayah dengan medan sulit, wireless dapat menjadi solusi awal yang ekonomis. Namun performanya sangat bergantung pada line of sight, kualitas radio, interferensi, tinggi tower, dan kondisi cuaca ekstrem.',
                    'Untuk latensi, fiber cenderung memberikan ping lebih rendah dan stabil. Ini penting untuk video conference, kasir online, cloud POS, game online, CCTV cloud, dan aplikasi bisnis yang sensitif terhadap delay. Wireless dapat tetap baik jika desain link benar, tetapi butuh perencanaan frekuensi yang disiplin.',
                    'Bagi pelanggan rumah, pilihan terbaik biasanya fiber jika area sudah tercover. Untuk bisnis, fiber lebih ideal karena mendukung kapasitas besar, monitoring lebih mudah, dan upgrade paket lebih fleksibel. Wireless dapat dijadikan backup link agar operasional tetap berjalan ketika jalur utama mengalami gangguan fisik.',
                    'Komponen penting pada jaringan fiber meliputi OLT, ODP, kabel distribusi, drop core, konektor, splitter, dan ONT di sisi pelanggan. Setiap sambungan harus rapi karena redaman kecil sekalipun dapat memengaruhi kualitas sinyal optik. Instalasi yang bersih akan mengurangi gangguan berulang dan mempercepat proses troubleshooting.',
                    'Kesimpulannya, fiber optik unggul untuk stabilitas jangka panjang, sedangkan wireless unggul dalam kecepatan deployment. Kombinasi keduanya bisa menjadi strategi terbaik untuk ISP, terutama ketika ingin memperluas coverage tanpa mengorbankan kualitas pelanggan prioritas.',
                ]),
                'cover_image' => 'https://images.unsplash.com/photo-1516321497487-e288fb19713f?auto=format&fit=crop&w=1200&q=80',
                'category' => 'tips',
                'meta_title' => 'Fiber Optik vs Wireless: Internet Mana Lebih Stabil?',
                'meta_description' => 'Bandingkan fiber optik dan wireless untuk internet rumah serta bisnis dari stabilitas, latensi, kapasitas, instalasi, dan biaya.',
                'published_at' => now()->subDays(3)->setTime(10, 0),
            ],
            [
                'title' => 'Mengenal OLT, ODP, ONT, dan Splitter pada Jaringan Internet Fiber',
                'excerpt' => 'Penjelasan sederhana tentang komponen utama jaringan fiber optik ISP: OLT, ODP, ONT, splitter, kabel drop core, dan konektor optik.',
                'body' => $this->article([
                    'Jaringan fiber optik terlihat sederhana dari sisi pelanggan karena hanya ada kabel kecil dan perangkat ONT di rumah. Di balik itu, ada beberapa komponen penting yang bekerja bersama agar internet dapat berjalan stabil dari pusat jaringan ISP sampai ke perangkat pelanggan.',
                    'OLT atau Optical Line Terminal berada di sisi provider. Perangkat ini menjadi pusat distribusi layanan fiber dan mengatur koneksi ke banyak pelanggan. OLT terhubung ke jaringan utama ISP, lalu menyalurkan layanan melalui port optik menuju jalur distribusi.',
                    'ODP atau Optical Distribution Point adalah titik distribusi yang biasanya berada di tiang atau area dekat pelanggan. Dari ODP, teknisi menarik kabel drop core ke rumah atau tempat usaha. ODP yang tertata rapi memudahkan aktivasi pelanggan baru dan mempercepat perbaikan ketika terjadi gangguan.',
                    'ONT atau Optical Network Terminal adalah perangkat di sisi pelanggan. Fungsinya mengubah sinyal optik menjadi koneksi Ethernet dan Wi-Fi yang bisa dipakai oleh ponsel, laptop, router tambahan, CCTV, atau perangkat bisnis. Banyak orang menyebut ONT sebagai modem fiber, meski secara teknis fungsinya berbeda dari modem kabel lama.',
                    'Splitter membagi satu jalur optik menjadi beberapa jalur pelanggan. Komponen ini efisien, tetapi setiap pembagian menambah redaman. Karena itu ISP harus menghitung power budget agar sinyal yang sampai ke pelanggan tetap dalam batas aman.',
                    'Kabel drop core, patch cord, adaptor, dan konektor juga tidak boleh dianggap sepele. Konektor yang kotor, tekukan kabel terlalu tajam, atau sambungan asal-asalan bisa membuat koneksi putus nyambung. Di jaringan fiber, kebersihan dan kerapian instalasi adalah bagian dari performa.',
                    'Memahami komponen ini membantu pelanggan lebih mudah berkomunikasi dengan teknisi. Saat terjadi gangguan, informasi seperti lampu LOS merah pada ONT, kabel tertarik, atau ODP terkena pekerjaan lapangan dapat mempercepat diagnosis.',
                    'Bagi ISP, artikel edukasi komponen jaringan seperti ini juga membangun kepercayaan. Pelanggan melihat bahwa layanan internet bukan hanya menjual Mbps, tetapi mengelola infrastruktur yang membutuhkan perencanaan, perawatan, dan standar teknis.',
                ]),
                'cover_image' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=1200&q=80',
                'category' => 'umum',
                'meta_title' => 'Fungsi OLT, ODP, ONT, dan Splitter Jaringan Fiber',
                'meta_description' => 'Mengenal OLT, ODP, ONT, splitter, drop core, dan konektor optik pada jaringan internet fiber agar pelanggan paham penyebab gangguan.',
                'published_at' => now()->subDays(4)->setTime(8, 45),
            ],
            [
                'title' => 'Cara Mengamankan Wi-Fi Rumah dari Pembobol dan Perangkat Tidak Dikenal',
                'excerpt' => 'Langkah SEO-friendly dan praktis mengamankan Wi-Fi rumah: password kuat, WPA2/WPA3, guest network, update router, dan cek perangkat asing.',
                'body' => $this->article([
                    'Wi-Fi rumah yang tidak aman dapat membuat koneksi melambat, kuota perangkat tersedot, dan data pribadi lebih rentan. Banyak kasus internet terasa lambat ternyata bukan karena gangguan provider, melainkan karena ada perangkat tidak dikenal yang ikut memakai jaringan.',
                    'Langkah pertama adalah mengganti nama Wi-Fi dan password bawaan. Gunakan kata sandi minimal 12 karakter dengan kombinasi huruf besar, huruf kecil, angka, dan simbol. Hindari password yang mudah ditebak seperti nomor rumah, tanggal lahir, nama anak, atau nama Wi-Fi yang sama dengan password.',
                    'Aktifkan WPA2-PSK atau WPA3 jika tersedia. Hindari mode keamanan lama seperti WEP karena sudah tidak layak dipakai. Jika router menyediakan pilihan mixed mode, gunakan hanya jika masih ada perangkat lama yang benar-benar membutuhkan kompatibilitas.',
                    'Matikan WPS apabila tidak diperlukan. Fitur ini memudahkan perangkat tersambung, tetapi pada beberapa router bisa menjadi celah keamanan. Untuk penggunaan rumah, memasukkan password secara manual jauh lebih aman daripada membiarkan WPS aktif sepanjang waktu.',
                    'Gunakan guest network untuk tamu. Dengan jaringan tamu, perangkat pengunjung tidak perlu masuk ke jaringan utama yang berisi laptop kerja, kamera CCTV, printer, atau perangkat smart home. Beberapa router juga memungkinkan pembatasan kecepatan pada guest network agar aktivitas utama tetap lancar.',
                    'Cek daftar perangkat secara berkala melalui halaman admin router atau aplikasi manajemen router. Jika menemukan nama perangkat asing, ubah password dan restart koneksi. Untuk keamanan tambahan, update firmware router agar mendapatkan perbaikan bug dan peningkatan stabilitas.',
                    'Jangan lupa amankan akun admin router. Banyak pengguna mengganti password Wi-Fi tetapi membiarkan username dan password admin tetap bawaan. Ini berisiko karena siapa pun yang sudah tersambung ke jaringan dapat mencoba masuk ke pengaturan router.',
                    'Keamanan Wi-Fi adalah kebiasaan, bukan tindakan sekali selesai. Dengan password kuat, enkripsi modern, guest network, dan pengecekan berkala, internet rumah menjadi lebih aman, stabil, dan nyaman dipakai keluarga.',
                ]),
                'cover_image' => 'https://images.unsplash.com/photo-1563986768494-4dee2763ff3f?auto=format&fit=crop&w=1200&q=80',
                'category' => 'tips',
                'meta_title' => 'Cara Mengamankan Wi-Fi Rumah dari Pembobol',
                'meta_description' => 'Panduan mengamankan Wi-Fi rumah dengan password kuat, WPA2/WPA3, guest network, update firmware, dan cek perangkat tidak dikenal.',
                'published_at' => now()->subDays(5)->setTime(11, 20),
            ],
            [
                'title' => 'AI di Jaringan Internet: Bagaimana Kecerdasan Buatan Membantu ISP Menjaga Koneksi Stabil',
                'excerpt' => 'AI mulai dipakai untuk monitoring jaringan, prediksi gangguan, analisis trafik, chatbot layanan pelanggan, dan optimasi kapasitas ISP.',
                'body' => $this->article([
                    'Kecerdasan buatan tidak hanya hadir di aplikasi chat dan pembuat gambar. Di industri jaringan internet, AI mulai digunakan untuk membaca pola trafik, mendeteksi anomali, memprediksi gangguan, dan membantu tim teknis mengambil keputusan lebih cepat.',
                    'Salah satu penggunaan paling relevan adalah monitoring jaringan. Sistem dapat mempelajari pola normal dari perangkat seperti router, OLT, server, dan access point. Ketika terjadi perubahan tidak wajar seperti lonjakan packet loss, port flapping, atau pemakaian bandwidth yang tiba-tiba melonjak, sistem bisa memberi peringatan lebih awal.',
                    'AI juga membantu dalam prediksi gangguan. Misalnya, jika redaman optik pelanggan perlahan memburuk dari hari ke hari, sistem dapat menandai kemungkinan kabel tertekuk, konektor kotor, atau sambungan mulai bermasalah sebelum pelanggan benar-benar kehilangan koneksi.',
                    'Di sisi layanan pelanggan, chatbot berbasis AI dapat membantu menjawab pertanyaan dasar seperti cara restart ONT, cek lampu indikator, informasi tagihan, atau status gangguan area. Namun chatbot harus tetap terhubung dengan data operasional yang benar agar jawaban tidak sekadar terdengar pintar, tetapi benar-benar membantu.',
                    'Untuk optimasi kapasitas, AI dapat membaca jam sibuk, area dengan pertumbuhan pelanggan tinggi, dan rute yang mulai padat. Data ini membantu ISP menentukan kapan perlu upgrade backhaul, menambah perangkat distribusi, atau menyeimbangkan trafik antar jalur.',
                    'Meski menjanjikan, AI bukan pengganti desain jaringan yang baik. Kabel tetap harus rapi, power budget fiber tetap harus dihitung, perangkat harus dipilih sesuai kapasitas, dan tim lapangan tetap membutuhkan SOP yang jelas. AI bekerja paling baik ketika data dasar akurat dan proses operasional sudah tertata.',
                    'Bagi pelanggan, manfaat akhirnya adalah gangguan lebih cepat terdeteksi, respon teknis lebih tepat, dan layanan lebih konsisten. Bagi ISP lokal, adopsi AI bertahap dapat dimulai dari dashboard monitoring, klasifikasi tiket, dan laporan performa jaringan harian.',
                ]),
                'cover_image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80',
                'category' => 'umum',
                'meta_title' => 'AI di Jaringan Internet untuk ISP Lebih Stabil',
                'meta_description' => 'Pelajari bagaimana AI membantu ISP menjaga internet stabil lewat monitoring jaringan, prediksi gangguan, chatbot, dan optimasi trafik.',
                'published_at' => now()->subDays(6)->setTime(9, 35),
            ],
            [
                'title' => 'Internet untuk Smart Home: Tips Menyiapkan Wi-Fi untuk CCTV, Smart TV, dan Perangkat IoT',
                'excerpt' => 'Rumah pintar membutuhkan Wi-Fi stabil. Simak cara menyiapkan jaringan untuk CCTV, smart TV, lampu pintar, speaker pintar, dan perangkat IoT lainnya.',
                'body' => $this->article([
                    'Perangkat smart home semakin umum: CCTV Wi-Fi, smart TV, lampu pintar, speaker pintar, door lock, sensor, dan perangkat otomatisasi rumah. Semua perangkat ini bergantung pada jaringan yang stabil. Jika Wi-Fi tidak dirancang dengan baik, rumah pintar justru terasa merepotkan karena perangkat sering offline.',
                    'Mulailah dengan memisahkan perangkat berdasarkan kebutuhan. Smart TV, konsol game, dan laptop kerja membutuhkan bandwidth lebih besar. CCTV membutuhkan koneksi stabil sepanjang hari. Lampu pintar dan sensor biasanya tidak butuh kecepatan tinggi, tetapi perlu sinyal yang konsisten.',
                    'Gunakan frekuensi 2.4 GHz untuk perangkat IoT yang jaraknya jauh atau tidak membutuhkan bandwidth besar. Banyak perangkat smart home masih hanya mendukung 2.4 GHz karena frekuensi ini lebih luas jangkauannya. Untuk smart TV dan perangkat streaming, gunakan 5 GHz atau kabel LAN jika memungkinkan.',
                    'Buat jaringan tamu atau jaringan khusus IoT jika router mendukung. Pemisahan ini membantu keamanan karena perangkat pintar tidak harus berada di jaringan yang sama dengan laptop kerja atau perangkat berisi data penting. Jika salah satu perangkat IoT bermasalah, dampaknya terhadap jaringan utama bisa dibatasi.',
                    'Perhatikan upload speed untuk CCTV cloud. Banyak pelanggan hanya melihat angka download, padahal kamera yang mengirim rekaman ke cloud membutuhkan upload stabil. Semakin banyak kamera dan semakin tinggi resolusinya, semakin besar kebutuhan upload.',
                    'Untuk rumah besar, gunakan access point tambahan. Jangan memaksa satu router melayani semua sudut rumah jika sinyal sudah lemah. Perangkat IoT yang berada di teras, garasi, atau lantai atas sering menjadi sumber masalah karena berada di batas cakupan sinyal.',
                    'Update firmware perangkat pintar dan router secara berkala. Selain memperbaiki bug, update juga menutup celah keamanan. Gunakan password berbeda untuk akun aplikasi smart home dan aktifkan verifikasi dua langkah jika tersedia.',
                    'Internet smart home yang baik tidak harus selalu memakai paket paling mahal. Yang paling penting adalah desain Wi-Fi yang sesuai, pemisahan perangkat, cakupan sinyal merata, dan pemahaman kebutuhan upload serta latency.',
                ]),
                'cover_image' => 'https://images.unsplash.com/photo-1558002038-1055907df827?auto=format&fit=crop&w=1200&q=80',
                'category' => 'tips',
                'meta_title' => 'Internet Smart Home untuk CCTV dan Perangkat IoT',
                'meta_description' => 'Tips menyiapkan Wi-Fi smart home untuk CCTV, smart TV, lampu pintar, dan IoT agar koneksi stabil, aman, dan tidak mudah offline.',
                'published_at' => now()->subDays(7)->setTime(14, 0),
            ],
            [
                'title' => 'Kenapa Ping Rendah Penting untuk Game Online, Meeting, dan Aplikasi Real-Time?',
                'excerpt' => 'Kecepatan internet bukan hanya Mbps. Ping, jitter, dan packet loss menentukan kualitas game online, video call, VoIP, CCTV, dan kerja remote.',
                'body' => $this->article([
                    'Banyak orang menilai kualitas internet hanya dari angka Mbps. Padahal untuk aktivitas real-time seperti game online, video meeting, telepon VoIP, remote desktop, dan live streaming, ping rendah dan stabil sering lebih penting daripada download besar.',
                    'Ping adalah waktu yang dibutuhkan data untuk pergi dari perangkat ke server dan kembali lagi. Semakin rendah ping, semakin cepat respons yang terasa. Dalam game online, ping tinggi menyebabkan delay saat menekan tombol. Dalam meeting, ping tinggi bisa membuat suara terlambat dan percakapan saling tumpang tindih.',
                    'Jitter adalah perubahan ping dari waktu ke waktu. Ping 20 ms yang stabil biasanya lebih nyaman daripada ping yang naik turun dari 20 ms ke 200 ms. Jitter tinggi membuat suara patah-patah, video tersendat, dan game terasa tidak konsisten.',
                    'Packet loss terjadi ketika sebagian paket data hilang di perjalanan. Dampaknya bisa lebih mengganggu daripada koneksi lambat. Pada video call, packet loss membuat suara hilang sebentar. Pada game, karakter bisa berpindah tiba-tiba. Pada aplikasi bisnis, sinkronisasi data bisa gagal.',
                    'Penyebab ping tinggi bisa berasal dari Wi-Fi lemah, router kelebihan beban, kabel LAN rusak, server tujuan jauh, atau rute jaringan sedang padat. Karena itu, diagnosis harus dilakukan bertahap: uji lewat kabel LAN, cek perangkat lain yang memakai bandwidth besar, lalu bandingkan hasil ke beberapa server.',
                    'Untuk pengguna rumah, cara termudah menurunkan ping adalah memakai kabel LAN untuk perangkat penting, memilih posisi router yang tepat, menggunakan 5 GHz ketika jarak dekat, dan membatasi download besar saat meeting atau gaming. Untuk bisnis, router dengan QoS dan manajemen bandwidth lebih penting.',
                    'Provider internet yang baik tidak hanya mengejar bandwidth tinggi, tetapi juga menjaga kualitas routing, kapasitas backhaul, dan monitoring gangguan. Pelanggan akan merasakan kualitas tersebut ketika koneksi tetap responsif pada jam sibuk.',
                    'Kesimpulannya, Mbps menjelaskan kapasitas, sedangkan ping, jitter, dan packet loss menjelaskan kenyamanan. Untuk pengalaman internet modern, ketiganya harus diperhatikan bersama.',
                ]),
                'cover_image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1200&q=80',
                'category' => 'tips',
                'meta_title' => 'Ping Rendah untuk Game Online dan Meeting Stabil',
                'meta_description' => 'Kenali ping, jitter, dan packet loss. Pelajari kenapa latency rendah penting untuk game online, meeting, VoIP, dan kerja remote.',
                'published_at' => now()->subDays(8)->setTime(15, 15),
            ],
            [
                'title' => 'Checklist Memilih Paket Internet Rumah: Jangan Hanya Lihat Mbps',
                'excerpt' => 'Panduan memilih paket internet rumah berdasarkan jumlah pengguna, jenis aktivitas, stabilitas, upload, FUP, router, dan layanan teknis.',
                'body' => $this->article([
                    'Memilih paket internet rumah sering terlihat sederhana: semakin besar Mbps, semakin baik. Kenyataannya, paket terbaik adalah paket yang sesuai dengan jumlah pengguna, jenis aktivitas, perangkat, dan kualitas jaringan di lokasi rumah.',
                    'Hitung dulu jumlah pengguna aktif. Rumah dengan dua orang yang sesekali streaming tentu berbeda dengan rumah berisi keluarga besar, kelas online, kerja remote, CCTV cloud, dan smart TV. Setiap perangkat aktif menambah kebutuhan bandwidth dan membuat kualitas router semakin penting.',
                    'Perhatikan jenis aktivitas. Browsing, chat, dan media sosial tidak membutuhkan bandwidth sebesar streaming 4K, upload video, backup cloud, meeting harian, atau game online. Untuk keluarga yang sering meeting dan upload file, upload speed perlu diperhatikan, bukan hanya download.',
                    'Tanyakan teknologi jaringan yang digunakan. Fiber optik biasanya lebih stabil untuk penggunaan jangka panjang, terutama jika instalasi dan perangkat distribusinya baik. Jika layanan memakai wireless, pastikan provider memiliki desain link yang rapi dan dukungan teknis yang jelas.',
                    'Cek kebijakan FUP atau batas pemakaian wajar jika ada. Paket terlihat murah bisa terasa kurang nyaman jika performa turun setelah pemakaian tertentu. Transparansi kebijakan lebih penting daripada promosi besar yang tidak menjelaskan batas layanan.',
                    'Pastikan router sesuai kebutuhan rumah. Paket cepat tidak akan maksimal jika router hanya mendukung standar lama atau cakupannya terlalu kecil. Untuk rumah besar, biaya access point tambahan bisa lebih berguna daripada memaksa satu router murah bekerja sendirian.',
                    'Lihat juga kualitas layanan teknis. Internet adalah layanan berkelanjutan, bukan produk sekali beli. Kecepatan respon gangguan, kanal komunikasi, jadwal teknisi, dan informasi maintenance sangat memengaruhi kepuasan pelanggan.',
                    'Checklist terbaik sebelum berlangganan adalah: jumlah pengguna, aktivitas utama, kebutuhan upload, cakupan Wi-Fi, jenis jaringan, kebijakan FUP, kualitas router, dan reputasi support. Dengan cara ini, pelanggan memilih paket berdasarkan kebutuhan nyata, bukan sekadar angka promosi.',
                ]),
                'cover_image' => 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1200&q=80',
                'category' => 'tips',
                'meta_title' => 'Checklist Memilih Paket Internet Rumah Terbaik',
                'meta_description' => 'Jangan hanya lihat Mbps. Gunakan checklist paket internet rumah: pengguna, aktivitas, upload, FUP, router, fiber, dan layanan teknis.',
                'published_at' => now()->subDays(9)->setTime(7, 50),
            ],
            [
                'title' => 'Tren Teknologi Internet 2026: Wi-Fi 7, AI Network Monitoring, Fiber, dan Keamanan Digital',
                'excerpt' => 'Ringkasan tren teknologi internet 2026 yang relevan untuk pengguna rumah, bisnis kecil, dan ISP lokal: Wi-Fi 7, fiber, AI, IoT, dan cybersecurity.',
                'body' => $this->article([
                    'Tahun 2026 memperlihatkan arah yang jelas dalam industri internet: koneksi harus lebih stabil, lebih aman, dan lebih siap menghadapi perangkat yang semakin banyak. Pengguna tidak hanya membutuhkan download cepat, tetapi juga latensi rendah, upload konsisten, cakupan Wi-Fi merata, dan perlindungan dari ancaman digital.',
                    'Wi-Fi 7 menjadi salah satu topik utama karena menawarkan kapasitas lebih besar dan pengalaman lebih responsif di rumah padat perangkat. Namun tren berikutnya tidak berhenti pada kecepatan. Industri mulai bergerak ke arah Wi-Fi yang lebih andal, lebih pintar mengelola interferensi, dan lebih baik untuk lingkungan dengan banyak access point.',
                    'Fiber optik tetap menjadi tulang punggung konektivitas. Pertumbuhan aplikasi AI, cloud, streaming, CCTV, dan kerja jarak jauh mendorong kebutuhan bandwidth yang lebih besar. Untuk ISP, investasi pada jaringan fiber yang rapi, monitoring power optik, dan kapasitas backhaul menjadi semakin penting.',
                    'AI network monitoring juga semakin relevan. Sistem yang mampu membaca pola trafik, mendeteksi anomali, mengelompokkan tiket gangguan, dan memprediksi area berisiko dapat membantu tim teknis bekerja lebih cepat. Bukan berarti semua keputusan diserahkan ke AI, tetapi data yang baik membuat prioritas kerja lebih jelas.',
                    'Smart home dan IoT terus bertambah. CCTV, sensor, smart TV, dan perangkat otomasi rumah membuat jaringan rumah lebih kompleks. Pelanggan mulai membutuhkan edukasi tentang guest network, pemisahan perangkat IoT, keamanan password, dan pentingnya upload speed.',
                    'Keamanan digital menjadi kebutuhan dasar. Router dengan firmware usang, password lemah, dan perangkat tidak dikenal dapat menurunkan kualitas koneksi sekaligus membuka risiko privasi. ISP yang aktif mengedukasi pelanggan tentang keamanan Wi-Fi akan memiliki nilai tambah di mata pengguna.',
                    'Bagi bisnis kecil, internet stabil menjadi bagian dari operasional. Kasir online, marketplace, WhatsApp Business, absensi, CCTV, dan aplikasi cloud tidak bisa berjalan baik jika koneksi sering putus. Karena itu, paket bisnis, backup link, dan support responsif semakin penting.',
                    'Tren 2026 menegaskan satu hal: masa depan internet bukan hanya lebih cepat, tetapi lebih cerdas dan lebih dapat diandalkan. Provider yang mampu menggabungkan fiber kuat, Wi-Fi modern, monitoring aktif, dan edukasi pelanggan akan lebih mudah memenangkan kepercayaan pasar.',
                ]),
                'cover_image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80',
                'category' => 'umum',
                'meta_title' => 'Tren Teknologi Internet 2026: Wi-Fi 7, AI, Fiber',
                'meta_description' => 'Simak tren teknologi internet 2026: Wi-Fi 7, AI network monitoring, fiber optik, smart home, IoT, keamanan digital, dan bisnis kecil.',
                'published_at' => now()->subDays(10)->setTime(8, 0),
            ],
        ];

        foreach ($articles as $article) {
            $slug = Str::slug($article['title']);

            News::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $article['title'],
                    'excerpt' => $article['excerpt'],
                    'body' => $article['body'],
                    'cover_image' => $article['cover_image'],
                    'author' => 'Tim-7 Net',
                    'category' => $article['category'],
                    'status' => 'published',
                    'published_at' => $article['published_at'],
                    'meta_title' => $article['meta_title'],
                    'meta_description' => $article['meta_description'],
                    'view_count' => 0,
                ]
            );
        }
    }

    /**
     * Convert paragraph arrays to the plain-text format used by the news view.
     *
     * @param  array<int, string>  $paragraphs
     */
    private function article(array $paragraphs): string
    {
        return implode("\n\n", $paragraphs);
    }
}
