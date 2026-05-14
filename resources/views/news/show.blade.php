<!DOCTYPE html>
<html lang="id" class="dark" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

    {{-- ── Primary SEO ── --}}
    <title>{{ $news->seo_title }} – Tim-7 Net</title>
    <meta name="title" content="{{ $news->seo_title }} – Tim-7 Net">
    <meta name="description" content="{{ $news->seo_description }}">
    <meta name="keywords" content="{{ $news->category_label }}, tim-7 net, internet lampung, {{ Str::words(strip_tags($news->title), 4, '') }}">
    <meta name="author" content="{{ $news->author }}">
    <link rel="canonical" href="{{ url('/berita/' . $news->slug) }}">

    {{-- ── Open Graph ── --}}
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Tim-7 Net">
    <meta property="og:url" content="{{ url('/berita/' . $news->slug) }}">
    <meta property="og:title" content="{{ $news->seo_title }}">
    <meta property="og:description" content="{{ $news->seo_description }}">
    <meta property="og:locale" content="id_ID">
    @if($news->cover_image_url)
    <meta property="og:image" content="{{ $news->cover_image_url }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $news->title }}">
    @endif
    <meta property="article:published_time" content="{{ $news->published_at?->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $news->updated_at->toIso8601String() }}">
    <meta property="article:author" content="{{ $news->author }}">
    <meta property="article:section" content="{{ $news->category_label }}">

    {{-- ── Twitter Card ── --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $news->seo_title }}">
    <meta name="twitter:description" content="{{ $news->seo_description }}">
    @if($news->cover_image_url)
    <meta name="twitter:image" content="{{ $news->cover_image_url }}">
    @endif

    {{-- ── JSON-LD Structured Data ── --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context'      => 'https://schema.org',
        '@type'         => 'NewsArticle',
        'headline'      => $news->title,
        'description'   => $news->seo_description,
        'url'           => url('/berita/' . $news->slug),
        'datePublished' => $news->published_at?->toIso8601String(),
        'dateModified'  => $news->updated_at->toIso8601String(),
        'author'        => ['@type' => 'Person', 'name' => $news->author],
        'publisher'     => [
            '@type' => 'Organization',
            'name'  => 'Tim-7 Net',
            'logo'  => ['@type' => 'ImageObject', 'url' => asset('logo.png')],
        ],
        'image'         => $news->cover_image_url,
        'articleSection'=> $news->category_label,
        'inLanguage'    => 'id-ID',
        'commentCount'  => $comments->count(),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

    {{-- ── BreadcrumbList ── --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda',         'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Berita',          'item' => url('/berita')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $news->title,      'item' => url('/berita/' . $news->slug)],
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
            --border: rgba(255, 255, 255, 0.1);
            --border-hover: rgba(59, 130, 246, 0.3);
            --card-bg: rgba(17, 24, 39, 0.7);
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
            /* Latar belakang yang lebih bersih untuk menghindari efek smudge teks */
        }

        h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); font-weight: 700; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; height: auto; display: block; border-radius: 0.75rem; }
        
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-color); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }

        .container { max-width: 800px; margin: 0 auto; padding: 0 1.5rem; }
        .container-wide { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }

        /* Navbar */
        #navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 50;
            background: rgba(11, 17, 32, 0.85);
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
        .logo img { height: 36px; border-radius: 6px; padding: 4px; background: white; }
        .nav-links { display: flex; gap: 2rem; list-style: none; }
        .nav-links a {
            font-size: 0.95rem; font-weight: 500; color: var(--text-muted); transition: color 0.2s;
        }
        .nav-links a:hover, .nav-links a.active { color: var(--text-main); }
        .nav-actions { display: flex; gap: 1rem; align-items: center; }
        
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0.6rem 1.25rem; border-radius: 0.5rem; font-weight: 600;
            font-size: 0.9rem; transition: all 0.3s ease; cursor: pointer; border: none; font-family: var(--font-body);
        }
        .btn-outline { background: transparent; border: 1px solid var(--primary); color: var(--primary); }
        .btn-outline:hover { background: rgba(59, 130, 246, 0.1); box-shadow: 0 0 15px var(--primary-glow); }
        .btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 14px var(--primary-glow); }
        .btn-primary:hover { background: var(--primary-hover); box-shadow: 0 6px 20px var(--primary-glow); transform: translateY(-2px); }

        /* Mobile Menu */
        .mobile-menu-btn { display: none; background: none; border: none; color: var(--text-main); font-size: 1.5rem; cursor: pointer; }
        .mobile-menu {
            display: none; position: absolute; top: 100%; left: 0; right: 0;
            background: var(--bg-secondary); border-bottom: 1px solid var(--border); padding: 1rem;
        }
        .mobile-menu.active { display: block; }
        .mobile-menu a {
            display: block; padding: 0.75rem 1rem; color: var(--text-muted);
            border-radius: 0.5rem; margin-bottom: 0.25rem;
        }
        .mobile-menu a:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }

        /* Breadcrumb */
        .breadcrumb {
            display: inline-flex; align-items: center; flex-wrap: wrap; gap: 0.6rem;
            font-size: 0.82rem; color: var(--text-muted);
            margin-top: 7.5rem;
            margin-bottom: 2rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .breadcrumb a {
            display: flex; align-items: center; gap: 0.4rem;
            transition: color 0.2s;
        }
        .breadcrumb a:hover { color: var(--primary); }
        .breadcrumb i.sep { font-size: 0.55rem; opacity: 0.4; margin: 0 0.1rem; }
        .breadcrumb span.current { color: var(--text-main); font-weight: 700; }

        /* Article Header */
        .article-header { margin-bottom: 2rem; text-align: left; }
        .tag {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.4rem 1rem; border-radius: 2rem; font-size: 0.75rem;
            font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
            margin-bottom: 1rem;
        }
        .tag-blue { background: rgba(59, 130, 246, 0.15); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.3); }
        .tag-red { background: rgba(239, 68, 68, 0.15); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .tag-amber { background: rgba(245, 158, 11, 0.15); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .tag-green { background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .tag-gray { background: rgba(156, 163, 175, 0.15); color: #9CA3AF; border: 1px solid rgba(156, 163, 175, 0.3); }

        .article-title {
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            line-height: 1.3;
            margin-bottom: 1.2rem;
            color: var(--text-main);
            /* Menghapus gradient untuk menghindari teks blur atau smudge pada beberapa browser */
        }
        
        .article-meta {
            display: flex; flex-wrap: wrap; align-items: center;
            justify-content: flex-start; gap: 1.5rem;
            font-size: 0.9rem; color: var(--text-muted);
            padding-bottom: 1.5rem; border-bottom: 1px solid var(--border);
        }
        .article-meta span { display: flex; align-items: center; gap: 0.5rem; }
        .article-meta i { color: var(--primary); font-size: 1rem; }

        /* Hero cover */
        .article-cover {
            width: 100%; max-height: 450px; object-fit: cover;
            border-radius: 1rem; margin: 0 0 2.5rem;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .article-cover-placeholder {
            width: 100%; height: 350px;
            background: linear-gradient(135deg, var(--bg-secondary), rgba(11, 17, 32, 0.5));
            border-radius: 1rem; display: flex; align-items: center; justify-content: center;
            margin: 0 0 2.5rem; color: var(--border); border: 1px dashed rgba(255,255,255,0.1);
        }

        /* Body */
        .article-body { font-size: 1.1rem; line-height: 1.8; color: #D1D5DB; }
        .article-body h2, .article-body h3, .article-body h4 {
            color: var(--text-main); margin: 2.5rem 0 1rem; line-height: 1.3;
        }
        .article-body h2 { font-size: 1.6rem; }
        .article-body h3 { font-size: 1.4rem; }
        .article-body p { margin-bottom: 1.5rem; }
        .article-body ul, .article-body ol { padding-left: 1.5rem; margin-bottom: 1.5rem; }
        .article-body li { margin-bottom: 0.5rem; }
        .article-body blockquote {
            border-left: 4px solid var(--primary); padding: 1.2rem 1.5rem; margin: 2rem 0;
            background: rgba(59, 130, 246, 0.05); border-radius: 0 0.75rem 0.75rem 0;
            color: #E5E7EB; font-style: italic; font-size: 1.15rem;
        }
        .article-body a {
            color: var(--primary); text-decoration: underline;
            text-decoration-color: rgba(59, 130, 246, 0.4); text-underline-offset: 4px; transition: all 0.2s;
        }
        .article-body a:hover { color: var(--primary-hover); text-decoration-color: var(--primary-hover); }
        .article-body strong { color: var(--text-main); font-weight: 700; }
        .article-body img { margin: 2rem 0; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }

        /* Share */
        .share-bar {
            display: flex; align-items: center; gap: 1rem;
            padding: 1.5rem 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
            margin: 3rem 0; flex-wrap: wrap;
        }
        .share-btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.6rem 1.25rem; border-radius: 2rem; font-size: 0.9rem; font-weight: 600;
            cursor: pointer; transition: all 0.3s ease; border: none; font-family: inherit;
        }
        .share-wa { background: rgba(37, 211, 102, 0.15); color: #4ADE80; border: 1px solid rgba(37, 211, 102, 0.3); }
        .share-wa:hover { background: #25D366; color: white; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3); }
        .share-copy { background: rgba(255, 255, 255, 0.05); color: var(--text-main); border: 1px solid var(--border); }
        .share-copy:hover { background: rgba(59, 130, 246, 0.1); color: var(--primary); border-color: var(--primary); transform: translateY(-2px); }

        /* Comments */
        .comments-section { margin-top: 2rem; margin-bottom: 2rem; }
        .comments-title {
            font-size: 1.4rem; font-weight: 800; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 0.75rem;
        }
        .comment-count-badge {
            background: rgba(59, 130, 246, 0.15); color: var(--primary);
            border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 2rem;
            font-size: 0.85rem; padding: 0.2rem 0.75rem; font-weight: 700;
        }
        .comment-item {
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: 1rem; padding: 1.5rem; margin-bottom: 1rem;
        }
        .comment-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .comment-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #8B5CF6);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1.1rem; color: white; flex-shrink: 0;
        }
        .comment-meta { flex: 1; }
        .comment-name { font-weight: 700; font-size: 1rem; color: var(--text-main); }
        .comment-time { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem; }
        .comment-text { font-size: 1rem; color: #D1D5DB; line-height: 1.7; white-space: pre-line; }
        
        /* Comment form */
        .comment-form {
            background: rgba(255,255,255,0.02); border: 1px solid var(--border);
            border-radius: 1rem; padding: 1.5rem; margin-top: 2rem;
        }
        .form-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem; }
        .form-input {
            width: 100%; background: rgba(17, 24, 39, 0.8); border: 1px solid var(--border);
            border-radius: 0.5rem; padding: 0.75rem 1rem; font-size: 0.95rem; color: var(--text-main);
            font-family: inherit; outline: none; transition: all 0.3s ease;
        }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }
        .form-input::placeholder { color: #6B7280; }
        textarea.form-input { resize: vertical; min-height: 120px; }
        .form-error {
            background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 0.5rem; padding: 1rem; color: #F87171; font-size: 0.9rem;
            margin-bottom: 1.5rem; display: none;
        }
        
        .btn-submit {
            background: var(--primary); color: white; padding: 0.8rem 1.5rem;
            border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; cursor: pointer; border: none; font-family: inherit;
            display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;
        }
        .btn-submit:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 4px 15px var(--primary-glow); }
        .btn-submit:disabled { opacity: 0.6; pointer-events: none; }

        /* Related Articles */
        .related-section {
            padding: 4rem 0; margin-top: 4rem; border-top: 1px solid var(--border);
            background: rgba(255,255,255,0.01);
        }
        .related-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem; margin-top: 1.5rem;
        }
        .related-card {
            background: var(--card-bg); border: 1px solid var(--border); border-radius: 1rem;
            overflow: hidden; transition: all 0.3s ease; display: flex; flex-direction: column;
        }
        .related-card:hover { border-color: var(--border-hover); transform: translateY(-4px); }
        .related-img { width: 100%; height: 180px; object-fit: cover; border-radius: 0; }
        .related-img-ph {
            width: 100%; height: 180px; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--bg-secondary), rgba(11, 17, 32, 0.5)); color: var(--border); font-size: 2rem;
        }
        .related-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
        .related-title {
            font-size: 1.05rem; font-weight: 700; color: var(--text-main); line-height: 1.4;
            margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .related-date { font-size: 0.85rem; color: var(--text-muted); margin-top: auto; display: flex; align-items: center; gap: 0.5rem; }
        .related-date i { color: var(--primary); }

        /* Footer */
        footer {
            padding: 2rem 0; border-top: 1px solid var(--border); text-align: center; color: var(--text-muted);
            font-size: 0.9rem; background: rgba(11, 17, 32, 0.5);
            margin-top: 5rem; /* Menjaga jarak dari kotak komentar jika tidak ada artikel terkait */
        }
        footer a { color: var(--primary); font-weight: 500; transition: color 0.2s; }
        footer a:hover { color: var(--primary-hover); text-decoration: underline; }

        @media(max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .article-title { font-size: 1.8rem; }
            .nav-links, .nav-actions .btn-outline { display: none; }
            .mobile-menu-btn { display: block; }
            .article-meta { flex-direction: column; gap: 0.75rem; align-items: flex-start; }
            .article-meta span { display: inline-flex; }
        }
    </style>
</head>
<body>

<nav id="navbar">
    <div class="container-wide nav-content">
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('logo.png') }}" alt="Tim-7 Net">
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
<div class="container">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ url('/') }}"><i class="fas fa-home"></i> Beranda</a>
        <i class="fas fa-chevron-right sep"></i>
        <a href="{{ route('news.index') }}">Berita</a>
        <i class="fas fa-chevron-right sep"></i>
        <a href="{{ route('news.index', ['kategori' => $news->category]) }}">{{ $news->category_label }}</a>
        <i class="fas fa-chevron-right sep"></i>
        <span class="current">{{ Str::limit($news->title, 40) }}</span>
    </nav>

    {{-- Article header --}}
    <article itemscope itemtype="https://schema.org/NewsArticle">
        <div class="article-header">
            @php
                $bc = $news->category_color;
                $badgeMap = ['blue'=>'tag-blue','red'=>'tag-red','amber'=>'tag-amber','green'=>'tag-green','gray'=>'tag-gray'];
            @endphp
            <span class="tag {{ $badgeMap[$bc] ?? 'tag-gray' }}">
                <i class="fas fa-circle" style="font-size:0.5rem; margin-right:0.2rem;"></i> {{ $news->category_label }}
            </span>

            <h1 class="article-title" itemprop="headline">{{ $news->title }}</h1>

            <div class="article-meta">
                <span itemprop="author" itemscope itemtype="https://schema.org/Person">
                    <i class="fas fa-user-circle"></i>
                    <span itemprop="name">{{ $news->author }}</span>
                </span>
                <span>
                    <i class="fas fa-calendar-alt"></i>
                    <time itemprop="datePublished" datetime="{{ $news->published_at?->toIso8601String() }}">
                        {{ $news->published_at?->translatedFormat('d F Y') }}
                    </time>
                </span>
                <span><i class="fas fa-clock"></i> {{ $news->reading_time }} menit baca</span>
                <span><i class="fas fa-eye"></i> {{ number_format($news->view_count) }} dilihat</span>
            </div>
        </div>

        {{-- Cover Image --}}
        @if($news->cover_image_url)
        <img class="article-cover" src="{{ $news->cover_image_url }}" alt="{{ $news->title }}"
             itemprop="image" loading="eager">
        @else
        <div class="article-cover-placeholder">
            <i class="fas fa-image" style="font-size:4rem"></i>
        </div>
        @endif

        {{-- Body --}}
        <div class="article-body" itemprop="articleBody">
            {!! nl2br(e($news->body)) !!}
        </div>
    </article>

    {{-- Share --}}
    <div class="share-bar">
        <span style="font-size:0.95rem;font-weight:600;color:var(--text-main);">Bagikan Artikel:</span>
        <button class="share-btn share-wa" onclick="shareWA()">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </button>
        <button class="share-btn share-copy" id="btn-copy" onclick="copyLink()">
            <i class="fas fa-link"></i> Salin Tautan
        </button>
    </div>

    {{-- ═══ Comments ═══ --}}
    <section class="comments-section" id="komentar" aria-label="Komentar">
        <h2 class="comments-title">
            <i class="fas fa-comments" style="color:var(--primary)"></i>
            Diskusi <span class="comment-count-badge" id="comment-count">{{ $comments->count() }}</span>
        </h2>

        {{-- Comment list --}}
        <div id="comment-list">
            @forelse($comments as $c)
            <div class="comment-item">
                <div class="comment-header">
                    <div class="comment-avatar">{{ strtoupper(mb_substr($c->name, 0, 1)) }}</div>
                    <div class="comment-meta">
                        <div class="comment-name">{{ $c->name }}</div>
                        <div class="comment-time">{{ $c->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                <p class="comment-text">{{ $c->comment }}</p>
            </div>
            @empty
            <div id="no-comments-msg" style="text-align:center; padding:3rem 0; color:var(--text-muted);">
                <i class="far fa-comment-dots" style="font-size:3rem; margin-bottom:1rem; opacity:0.5;"></i>
                <p style="font-size:1.05rem;">Belum ada komentar. Jadilah yang pertama berdiskusi!</p>
            </div>
            @endforelse
        </div>

        {{-- Comment form --}}
        <div class="comment-form">
            <h3 class="form-title">
                <i class="fas fa-pencil-alt" style="color:var(--primary);"></i> Tulis Komentar
            </h3>
            <div id="form-error" class="form-error"></div>
            <form id="comment-form" onsubmit="submitComment(event)" novalidate>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="c-name">Nama <span style="color:#f87171">*</span></label>
                        <input type="text" id="c-name" class="form-input" placeholder="Masukkan nama Anda" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="c-email">Email <span style="color:var(--text-muted);font-weight:400">(opsional)</span></label>
                        <input type="email" id="c-email" class="form-input" placeholder="email@contoh.com" maxlength="200">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="c-comment">Komentar <span style="color:#f87171">*</span></label>
                    <textarea id="c-comment" class="form-input" placeholder="Tulis pendapat atau pertanyaan Anda di sini..." required minlength="5" maxlength="1000"></textarea>
                </div>
                <button type="submit" class="btn-submit" id="btn-comment">
                    <i class="fas fa-paper-plane"></i> Kirim Komentar
                </button>
            </form>
            <div id="form-success" style="display:none;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:0.5rem;padding:1rem;color:#4ade80;font-size:0.95rem;margin-top:1.5rem;align-items:center;gap:0.5rem;">
                <i class="fas fa-check-circle"></i> Komentar berhasil dikirim!
            </div>
        </div>
    </section>

</div>{{-- /container --}}

{{-- Related Articles --}}
@if($related->count())
<section class="related-section">
    <div class="container-wide">
        <h2 style="font-size:1.4rem;font-weight:800;display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem;">
            <i class="fas fa-layer-group" style="color:var(--primary)"></i> Baca Juga
        </h2>
        <div class="related-grid">
            @foreach($related as $r)
            <a href="{{ route('news.show', $r->slug) }}" class="related-card">
                @if($r->cover_image_url)
                    <img class="related-img" src="{{ $r->cover_image_url }}" alt="{{ $r->title }}" loading="lazy">
                @else
                    <div class="related-img-ph"><i class="fas fa-image"></i></div>
                @endif
                <div class="related-body">
                    <p class="related-title">{{ $r->title }}</p>
                    <p class="related-date"><i class="fas fa-calendar-alt"></i>{{ $r->published_at?->format('d M Y') }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
</main>

<footer>
    <div class="container-wide">
        <p>© {{ date('Y') }} Tim-7 Net · <a href="{{ url('/') }}">Beranda</a> · <a href="{{ route('news.index') }}">Berita</a> · <a href="{{ url('/#kontak') }}">Kontak</a> · <a href="{{ route('terms') }}">Syarat & Ketentuan</a></p>
    </div>
</footer>

<script>
const CSRF = '{{ csrf_token() }}';
const COMMENT_URL = '{{ route("news.comment.store", $news->id) }}';

// Navbar Scroll Effect
window.addEventListener('scroll', () => {
    const nav = document.getElementById('navbar');
    if (window.scrollY > 20) {
        nav.style.background = 'rgba(11, 17, 32, 0.95)';
        nav.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.3)';
    } else {
        nav.style.background = 'rgba(11, 17, 32, 0.85)';
        nav.style.boxShadow = 'none';
    }
});

// Mobile Menu Toggle
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

// Share
function shareWA() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('{{ addslashes($news->title) }} – Tim-7 Net\n' + window.location.href);
    window.open('https://wa.me/?text=' + text, '_blank');
}
function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        const btn = document.getElementById('btn-copy');
        btn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
        btn.style.color = '#4ade80';
        btn.style.borderColor = '#4ade80';
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-link"></i> Salin Tautan';
            btn.style.color = '';
            btn.style.borderColor = '';
        }, 2000);
    });
}

// Comment submit
async function submitComment(e) {
    e.preventDefault();
    const errEl     = document.getElementById('form-error');
    const successEl = document.getElementById('form-success');
    const btn       = document.getElementById('btn-comment');

    errEl.style.display     = 'none';
    successEl.style.display = 'none';
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

    try {
        const res = await fetch(COMMENT_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                name:    document.getElementById('c-name').value,
                email:   document.getElementById('c-email').value,
                comment: document.getElementById('c-comment').value,
            }),
        });
        const data = await res.json();
        if (!data.success) {
            const msg = typeof data.errors === 'object'
                ? Object.values(data.errors).flat().join(' ')
                : (data.message || 'Terjadi kesalahan.');
            errEl.textContent   = msg;
            errEl.style.display = 'block';
            return;
        }

        // Append comment
        const c = data.comment;
        const initial = c.name.charAt(0).toUpperCase();
        const html = `<div class="comment-item">
            <div class="comment-header">
                <div class="comment-avatar">${initial}</div>
                <div class="comment-meta">
                    <div class="comment-name">${escHtml(c.name)}</div>
                    <div class="comment-time">${c.created_at}</div>
                </div>
            </div>
            <p class="comment-text">${escHtml(c.comment)}</p>
        </div>`;

        const list = document.getElementById('comment-list');
        const noMsg = document.getElementById('no-comments-msg');
        if (noMsg) noMsg.remove();
        list.insertAdjacentHTML('afterbegin', html);

        // Update count
        const countEl = document.getElementById('comment-count');
        countEl.textContent = parseInt(countEl.textContent) + 1;

        successEl.style.display = 'flex';
        document.getElementById('comment-form').reset();
        setTimeout(() => successEl.style.display = 'none', 4000);
    } catch (err) {
        errEl.textContent   = 'Gagal mengirim komentar: ' + err.message;
        errEl.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Komentar';
    }
}

function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
</body>
</html>
