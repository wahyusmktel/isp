<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Berita dan informasi terbaru seputar layanan internet, pengumuman, gangguan, dan promo dari Tim-7 Net.">
    <meta name="robots" content="index, follow">
    <title>Berita & Informasi – Tim-7 Net</title>
    <link rel="canonical" href="{{ url('/berita') }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Berita & Informasi – Tim-7 Net">
    <meta property="og:description" content="Berita dan informasi terbaru seputar layanan internet dari Tim-7 Net.">
    <meta property="og:url" content="{{ url('/berita') }}">
    <style>
        :root {
            --bg-color: #0B1120;
            --bg-secondary: #111827;
            --text-main: #F9FAFB;
            --text-muted: #9CA3AF;
            --primary: #3B82F6;
            --primary-hover: #2563EB;
            --primary-glow: rgba(59, 130, 246, 0.4);
            --accent: #10B981;
            --border: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(59, 130, 246, 0.3);
            --card-bg: rgba(17, 24, 39, 0.6);
            --font-heading: 'Plus Jakarta Sans', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: var(--font-body);
            background-color: var(--bg-color);
            color: var(--text-main);
            line-height: 1.6;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(59, 130, 246, 0.08), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(16, 185, 129, 0.05), transparent 25%);
            background-attachment: fixed;
        }

        h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); font-weight: 700; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; height: auto; display: block; }
        
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-color); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Navbar */
        #navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 50;
            background: rgba(11, 17, 32, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            transition: all 0.3s ease;
        }
        .nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 4.5rem;
        }
        .logo img { height: 36px; }
        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }
        .nav-links a {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-muted);
            transition: color 0.2s;
        }
        .nav-links a:hover, .nav-links a.active {
            color: var(--text-main);
        }
        .nav-actions { display: flex; gap: 1rem; align-items: center; }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-family: var(--font-body);
        }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        .btn-outline:hover {
            background: rgba(59, 130, 246, 0.1);
            box-shadow: 0 0 15px var(--primary-glow);
        }
        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 14px var(--primary-glow);
        }
        .btn-primary:hover {
            background: var(--primary-hover);
            box-shadow: 0 6px 20px var(--primary-glow);
            transform: translateY(-2px);
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--text-main);
            font-size: 1.5rem;
            cursor: pointer;
        }
        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            padding: 1rem;
        }
        .mobile-menu.active { display: block; }
        .mobile-menu a {
            display: block;
            padding: 0.75rem 1rem;
            color: var(--text-muted);
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
        }
        .mobile-menu a:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-main);
        }

        /* Hero */
        .hero {
            padding: 9rem 0 5rem;
            text-align: center;
            position: relative;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #60A5FA;
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #ffffff, #9CA3AF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero h1 span {
            background: linear-gradient(135deg, #3B82F6, #8B5CF6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 3rem;
        }

        /* Categories */
        .categories {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 4rem;
        }
        .category-pill {
            padding: 0.6rem 1.5rem;
            border-radius: 2rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .category-pill:hover, .category-pill.active {
            background: rgba(59, 130, 246, 0.1);
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.2);
        }

        /* Featured Article */
        .featured {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 0;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            overflow: hidden;
            margin-bottom: 4rem;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .featured:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border-color: var(--border-hover);
        }
        .featured-img-wrap {
            position: relative;
            overflow: hidden;
        }
        .featured-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            min-height: 420px;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .featured:hover .featured-img {
            transform: scale(1.05);
        }
        .featured-content {
            padding: 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .tag {
            display: inline-block;
            padding: 0.35rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1.25rem;
            width: fit-content;
        }
        .tag-blue { background: rgba(59, 130, 246, 0.15); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.3); }
        .tag-red { background: rgba(239, 68, 68, 0.15); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .tag-amber { background: rgba(245, 158, 11, 0.15); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .tag-green { background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .tag-gray { background: rgba(156, 163, 175, 0.15); color: #9CA3AF; border: 1px solid rgba(156, 163, 175, 0.3); }

        .featured h2 {
            font-size: 2.25rem;
            margin-bottom: 1.25rem;
            line-height: 1.3;
            color: var(--text-main);
        }
        .featured p {
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            font-size: 1.05rem;
            line-height: 1.7;
        }
        .meta {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-top: auto;
            font-weight: 500;
        }
        .meta i { color: var(--primary); font-size: 1rem; }

        /* Grid */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 2rem;
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            overflow: hidden;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.4);
            border-color: var(--border-hover);
        }
        .card-img-wrap {
            height: 240px;
            overflow: hidden;
            position: relative;
        }
        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card:hover .card-img {
            transform: scale(1.08);
        }
        .card-img-overlay {
            position: absolute;
            top: 1.25rem;
            left: 1.25rem;
            z-index: 2;
        }
        .card-content {
            padding: 1.75rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .card h3 {
            font-size: 1.35rem;
            margin-bottom: 1rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            color: var(--text-main);
        }
        .card p {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 1.75rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }
        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border);
        }
        .read-more {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            transition: gap 0.2s ease;
        }
        .card:hover .read-more {
            gap: 0.75rem;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 4rem;
        }
        .page-item {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-weight: 600;
            transition: all 0.2s ease;
            background: rgba(255,255,255,0.02);
        }
        .page-item:hover, .page-item.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 15px var(--primary-glow);
            transform: translateY(-2px);
        }
        .page-item.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 6rem 2rem;
            background: rgba(255,255,255,0.02);
            border-radius: 1.5rem;
            border: 1px dashed var(--border);
        }

        /* Footer */
        footer {
            margin-top: 8rem;
            padding: 2.5rem 0;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
            background: rgba(11, 17, 32, 0.5);
        }
        footer a { color: var(--primary); font-weight: 500; transition: color 0.2s; }
        footer a:hover { color: var(--primary-hover); text-decoration: underline; }

        /* Responsive */
        @media (max-width: 992px) {
            .featured { grid-template-columns: 1fr; }
            .featured-img { min-height: 320px; }
            .featured-content { padding: 2.5rem; }
            .nav-links, .nav-actions .btn-outline { display: none; }
            .mobile-menu-btn { display: block; }
        }
        @media (max-width: 768px) {
            .hero { padding: 7rem 0 3rem; }
            .hero h1 { font-size: 2.25rem; }
            .news-grid { grid-template-columns: 1fr; }
            .featured-content { padding: 2rem; }
            .featured h2 { font-size: 1.75rem; }
        }

        /* Placeholder */
        .img-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--bg-secondary), var(--bg-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--border);
            font-size: 3.5rem;
        }
    </style>
</head>
<body>

<nav id="navbar">
    <div class="container nav-content">
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('logo.png') }}" alt="Tim-7 Net" style="background: white; padding: 4px; border-radius: 6px;">
        </a>
        <ul class="nav-links">
            <li><a href="{{ url('/#paket') }}">Paket</a></li>
            <li><a href="{{ url('/#fitur') }}">Keunggulan</a></li>
            <li><a href="{{ route('news.index') }}" class="active">Berita</a></li>
            <li><a href="{{ url('/#kontak') }}">Kontak</a></li>
            <li><a href="{{ route('terms') }}">Syarat & Ketentuan</a></li>
        </ul>
        <div class="nav-actions">
            <a href="{{ route('customer.login') }}" class="btn btn-outline" style="display: none;">Login</a>
            <a href="https://wa.me/6282279122727" target="_blank" class="btn btn-primary"><i class="fab fa-whatsapp" style="margin-right:0.4rem; font-size:1.1rem;"></i> Daftar</a>
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
    <div class="mobile-menu" id="mobileMenu">
        <a href="{{ url('/#paket') }}">Paket</a>
        <a href="{{ url('/#fitur') }}">Keunggulan</a>
        <a href="{{ route('news.index') }}">Berita</a>
        <a href="{{ url('/#kontak') }}">Kontak</a>
        <a href="{{ route('terms') }}">Syarat & Ketentuan</a>
        <hr style="border-color: var(--border); margin: 0.5rem 0;">
        <a href="{{ route('customer.login') }}" style="color: var(--primary);">Login Pelanggan</a>
    </div>
</nav>

<main>
    <section class="hero">
        <div class="container">
            <div class="hero-badge">
                <i class="fas fa-newspaper"></i> Pusat Informasi
            </div>
            <h1>Berita & <span>Pengumuman</span></h1>
            <p>Pantau informasi terbaru seputar layanan, gangguan jaringan, promo menarik, dan update sistem dari Tim-7 Net.</p>
            
            <div class="categories">
                <a href="{{ route('news.index') }}" class="category-pill {{ !$category ? 'active' : '' }}">Semua Berita</a>
                @foreach($categories as $key => $label)
                <a href="{{ route('news.index', ['kategori' => $key]) }}" class="category-pill {{ $category === $key ? 'active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container">
            @if($news->isEmpty() && !$featured)
            <div class="empty-state">
                <i class="fas fa-folder-open" style="font-size: 4rem; color: var(--border); margin-bottom: 1.5rem; display: block;"></i>
                <h3 style="color: var(--text-main); font-size: 1.5rem; margin-bottom: 0.5rem;">Belum ada berita</h3>
                <p style="color: var(--text-muted);">Informasi terbaru akan segera kami perbarui di sini.</p>
            </div>
            @else

            @if($featured)
            <a href="{{ route('news.show', $featured->slug) }}" class="featured">
                <div class="featured-img-wrap">
                    @if($featured->cover_image_url)
                        <img src="{{ $featured->cover_image_url }}" alt="{{ $featured->title }}" class="featured-img" loading="lazy">
                    @else
                        <div class="img-placeholder"><i class="fas fa-image"></i></div>
                    @endif
                </div>
                <div class="featured-content">
                    @php
                        $fc = $featured->category_color;
                        $fBadgeColors = ['blue'=>'tag-blue','red'=>'tag-red','amber'=>'tag-amber','green'=>'tag-green','gray'=>'tag-gray'];
                    @endphp
                    <span class="tag {{ $fBadgeColors[$fc] ?? 'tag-gray' }}">
                        <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.3rem; vertical-align: middle;"></i>
                        {{ $featured->category_label }}
                    </span>
                    <h2>{{ $featured->title }}</h2>
                    @if($featured->excerpt)
                    <p>{{ Str::limit($featured->excerpt, 160) }}</p>
                    @endif
                    <div class="meta">
                        <span><i class="fas fa-user-circle"></i> {{ $featured->author }}</span>
                        <span><i class="fas fa-calendar-alt"></i> {{ $featured->published_at?->translatedFormat('d M Y') }}</span>
                        <span><i class="fas fa-clock"></i> {{ $featured->reading_time }} mnt baca</span>
                    </div>
                </div>
            </a>
            @endif

            @if($news->count())
            <div class="news-grid">
                @foreach($news as $item)
                @php $color = $item->category_color; @endphp
                <a href="{{ route('news.show', $item->slug) }}" class="card">
                    <div class="card-img-wrap">
                        <div class="card-img-overlay">
                            <span class="tag {{ ['blue'=>'tag-blue','red'=>'tag-red','amber'=>'tag-amber','green'=>'tag-green','gray'=>'tag-gray'][$color] ?? 'tag-gray' }}" style="margin-bottom:0; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); background-color: rgba(17,24,39,0.85);">
                                {{ $item->category_label }}
                            </span>
                        </div>
                        @if($item->cover_image_url)
                            <img src="{{ $item->cover_image_url }}" alt="{{ $item->title }}" class="card-img" loading="lazy">
                        @else
                            <div class="img-placeholder"><i class="fas fa-image"></i></div>
                        @endif
                    </div>
                    <div class="card-content">
                        <h3>{{ $item->title }}</h3>
                        @if($item->excerpt)
                        <p>{{ Str::limit($item->excerpt, 120) }}</p>
                        @endif
                        <div class="card-footer">
                            <div class="meta" style="gap: 1rem; margin-top:0;">
                                <span><i class="fas fa-calendar"></i> {{ $item->published_at?->format('d M Y') }}</span>
                                <span><i class="fas fa-eye"></i> {{ number_format($item->view_count) }}</span>
                            </div>
                            <span class="read-more">Baca <i class="fas fa-arrow-right" style="font-size:0.8rem;"></i></span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif

            @if($news->hasPages())
            <div class="pagination">
                @if($news->onFirstPage())
                    <span class="page-item disabled"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a href="{{ $news->previousPageUrl() }}" class="page-item"><i class="fas fa-chevron-left"></i></a>
                @endif

                @foreach($news->getUrlRange(max(1, $news->currentPage()-2), min($news->lastPage(), $news->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}" class="page-item {{ $page == $news->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach

                @if($news->hasMorePages())
                    <a href="{{ $news->nextPageUrl() }}" class="page-item"><i class="fas fa-chevron-right"></i></a>
                @else
                    <span class="page-item disabled"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
            @endif

            @endif
        </div>
    </section>
</main>

<footer>
    <div class="container">
        <p>© {{ date('Y') }} Tim-7 Net · <a href="{{ url('/') }}">Beranda</a> · <a href="{{ url('/#kontak') }}">Kontak</a> · <a href="{{ route('terms') }}">Syarat & Ketentuan</a> · <a href="{{ route('customer.login') }}">Portal Pelanggan</a></p>
    </div>
</footer>

<script>
    // Add scroll effect to navbar
    window.addEventListener('scroll', () => {
        const nav = document.getElementById('navbar');
        if (window.scrollY > 20) {
            nav.style.background = 'rgba(11, 17, 32, 0.9)';
            nav.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.3)';
            nav.style.borderBottom = '1px solid rgba(255, 255, 255, 0.1)';
        } else {
            nav.style.background = 'rgba(11, 17, 32, 0.7)';
            nav.style.boxShadow = 'none';
            nav.style.borderBottom = '1px solid rgba(255, 255, 255, 0.08)';
        }
    });

    // Mobile menu toggle
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    
    mobileBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('active');
        const icon = mobileBtn.querySelector('i');
        if (mobileMenu.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
</script>
</body>
</html>
