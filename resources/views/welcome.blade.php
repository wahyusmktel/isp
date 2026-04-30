<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tim-7 Net – Penyedia layanan internet fiber optik berkecepatan tinggi, stabil, dan terjangkau di Lampung. Nikmati internet tanpa batas untuk rumah dan bisnis Anda.">
    <meta name="keywords" content="internet lampung, ISP lampung, fiber optik, tim-7 net, internet cepat lampung, wifi rumah lampung">
    <title>Tim-7 Net – Internet Fiber Optik Cepat & Stabil di Lampung</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:     #0ea5e9;
            --primary-dk:  #0284c7;
            --primary-lt:  #38bdf8;
            --accent:      #f97316;
            --dark:        #0a0f1e;
            --dark-2:      #0f172a;
            --dark-3:      #1e293b;
            --dark-4:      #334155;
            --text:        #f1f5f9;
            --text-muted:  #94a3b8;
            --border:      #1e293b;
            --success:     #22c55e;
            --white:       #ffffff;
            --radius:      12px;
            --radius-lg:   20px;
            --shadow:      0 4px 24px rgba(0,0,0,.35);
            --transition:  .25s ease;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; }

        /* ─── SCROLLBAR ─── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--dark-2); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 99px; }

        /* ─── UTILITIES ─── */
        .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        .section { padding: 96px 0; }
        .section-sm { padding: 64px 0; }
        .badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(14,165,233,.15); border: 1px solid rgba(14,165,233,.3);
            color: var(--primary-lt); border-radius: 99px;
            font-size: .75rem; font-weight: 600; letter-spacing: .06em;
            padding: 5px 14px; text-transform: uppercase; margin-bottom: 16px;
        }
        .section-title { font-size: clamp(1.75rem,4vw,2.75rem); font-weight: 800; line-height: 1.2; }
        .section-sub { color: var(--text-muted); font-size: 1.05rem; margin-top: 12px; max-width: 560px; }
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-lt), var(--primary), var(--primary-dk));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .text-accent { color: var(--accent); }

        /* ─── BUTTON ─── */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 13px 28px; border-radius: var(--radius); font-weight: 600;
            font-size: .9rem; cursor: pointer; transition: all var(--transition);
            border: none; font-family: inherit;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dk));
            color: var(--white); box-shadow: 0 4px 20px rgba(14,165,233,.4);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(14,165,233,.55); }
        .btn-outline {
            background: transparent; color: var(--text);
            border: 1.5px solid var(--dark-4);
        }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); background: rgba(14,165,233,.06); }
        .btn-accent {
            background: linear-gradient(135deg, var(--accent), #ea580c);
            color: var(--white); box-shadow: 0 4px 20px rgba(249,115,22,.4);
        }
        .btn-accent:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(249,115,22,.55); }
        .btn-lg { padding: 16px 36px; font-size: 1rem; }
        .btn-sm { padding: 9px 20px; font-size: .82rem; }

        /* ─── NAVBAR ─── */
        #navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            padding: 16px 0;
            transition: all var(--transition);
        }
        #navbar.scrolled {
            background: rgba(10,15,30,.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 12px 0;
            box-shadow: 0 4px 32px rgba(0,0,0,.4);
        }
        .nav-inner {
            display: flex; align-items: center; justify-content: space-between; gap: 24px;
        }
        .nav-logo { display: flex; align-items: center; flex-shrink: 0; }
        .nav-logo img {
            height: 48px; width: auto;
            background: #fff; border-radius: 10px;
            padding: 5px 10px;
            transition: opacity var(--transition);
        }
        .nav-logo:hover img { opacity: .88; }
        .footer-brand .nav-logo img { height: 56px; }
        .nav-links {
            display: flex; align-items: center; gap: 6px; list-style: none;
        }
        .nav-links a {
            padding: 8px 14px; border-radius: 8px; font-size: .88rem; font-weight: 500;
            color: var(--text-muted); transition: all var(--transition);
        }
        .nav-links a:hover, .nav-links a.active { color: var(--text); background: var(--dark-3); }
        .nav-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .nav-toggle {
            display: none; flex-direction: column; gap: 5px;
            background: none; border: none; cursor: pointer; padding: 6px;
        }
        .nav-toggle span {
            display: block; width: 24px; height: 2px;
            background: var(--text); border-radius: 99px; transition: all var(--transition);
        }

        /* Mobile dropdown – tersembunyi secara default di SEMUA ukuran layar */
        .nav-mobile {
            display: none;
            position: absolute; top: 100%; left: 0; right: 0;
            background: var(--dark-2); border-bottom: 1px solid var(--border);
            padding: 16px 24px; z-index: 999;
        }
        .nav-mobile.open { display: block; }
        .nav-mobile ul { list-style: none; display: flex; flex-direction: column; gap: 4px; }
        .nav-mobile ul a {
            display: block; padding: 10px 14px; border-radius: 8px;
            font-size: .9rem; font-weight: 500; color: var(--text-muted);
            transition: all var(--transition);
        }
        .nav-mobile ul a:hover { color: var(--text); background: var(--dark-3); }
        .nav-mobile .mob-actions { margin-top: 12px; display: flex; flex-direction: column; gap: 8px; }

        /* Collapse nav links pada layar < 1100px */
        @media (max-width: 1099px) {
            .nav-links { display: none; }
            .nav-toggle { display: flex; }
        }
        @media (max-width: 480px) {
            .nav-actions .btn { display: none; }
        }

        /* ─── SCROLL MARGIN – supaya navbar tidak menutupi section heading ─── */
        section[id] { scroll-margin-top: 80px; }

        /* ─── HERO ─── */
        #hero {
            min-height: 100vh;
            display: flex; align-items: center;
            position: relative; overflow: hidden;
            padding-top: 76px; /* clearance tepat setinggi navbar */
            scroll-margin-top: 0;
        }
        .hero-bg {
            position: absolute; inset: 0; z-index: 0;
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(14,165,233,.18) 0%, transparent 65%),
                        radial-gradient(ellipse 40% 40% at 85% 70%, rgba(249,115,22,.12) 0%, transparent 60%),
                        var(--dark);
        }
        .hero-grid {
            position: absolute; inset: 0; z-index: 0; opacity: .04;
            background-image: linear-gradient(var(--primary) 1px, transparent 1px),
                              linear-gradient(90deg, var(--primary) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .hero-content { position: relative; z-index: 1; padding: 40px 0 64px; }
        .hero-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center; }
        .hero-eyebrow { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; }
        .hero-eyebrow .dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--success); box-shadow: 0 0 0 3px rgba(34,197,94,.25);
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { box-shadow: 0 0 0 3px rgba(34,197,94,.25); }
            50%       { box-shadow: 0 0 0 7px rgba(34,197,94,.1); }
        }
        .hero-eyebrow span { font-size: .82rem; color: var(--success); font-weight: 600; letter-spacing: .05em; text-transform: uppercase; }
        .hero-title { font-size: clamp(2.4rem, 5.5vw, 3.8rem); font-weight: 900; line-height: 1.1; margin-bottom: 20px; }
        .hero-desc { font-size: 1.05rem; color: var(--text-muted); margin-bottom: 36px; max-width: 480px; line-height: 1.75; }
        .hero-cta { display: flex; gap: 14px; flex-wrap: wrap; }
        .hero-stats {
            display: flex; gap: 32px; margin-top: 52px; padding-top: 32px;
            border-top: 1px solid var(--border); flex-wrap: wrap;
        }
        .stat-item {}
        .stat-number { font-size: 2rem; font-weight: 900; color: var(--primary-lt); line-height: 1; }
        .stat-label { font-size: .78rem; color: var(--text-muted); margin-top: 4px; text-transform: uppercase; letter-spacing: .06em; }

        /* Hero visual */
        .hero-visual { position: relative; display: flex; justify-content: center; }
        .speed-card {
            background: var(--dark-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 36px; width: 100%; max-width: 380px;
            box-shadow: 0 24px 80px rgba(0,0,0,.5), 0 0 0 1px rgba(14,165,233,.1);
            position: relative; overflow: hidden;
        }
        .speed-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }
        .speed-card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 28px; }
        .speed-card-header .icon {
            width: 44px; height: 44px; border-radius: 10px;
            background: rgba(14,165,233,.15); display: flex; align-items: center; justify-content: center;
            color: var(--primary); font-size: 1.2rem;
        }
        .speed-card-header h3 { font-size: 1rem; font-weight: 700; }
        .speed-card-header p { font-size: .78rem; color: var(--text-muted); }
        .speed-gauge { text-align: center; margin: 16px 0 28px; }
        .speed-number { font-size: 4rem; font-weight: 900; color: var(--primary-lt); line-height: 1; }
        .speed-unit { font-size: 1rem; color: var(--text-muted); font-weight: 500; }
        .speed-bar-wrap { background: var(--dark-3); border-radius: 99px; height: 8px; margin: 16px 0 8px; overflow: hidden; }
        .speed-bar { height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--primary-dk), var(--primary-lt)); width: 82%; animation: grow-bar 1.5s ease forwards; }
        @keyframes grow-bar { from { width: 0; } to { width: 82%; } }
        .speed-meta { display: flex; justify-content: space-between; font-size: .78rem; color: var(--text-muted); }
        .speed-features { display: flex; flex-direction: column; gap: 10px; margin-top: 20px; }
        .speed-feat {
            display: flex; align-items: center; gap: 10px;
            background: var(--dark-3); border-radius: 8px; padding: 10px 14px;
            font-size: .82rem; font-weight: 500;
        }
        .speed-feat i { color: var(--primary); width: 16px; text-align: center; }
        .float-badge {
            position: absolute; bottom: -20px; right: -20px;
            background: var(--dark-3); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 12px 16px;
            display: flex; align-items: center; gap: 10px;
            box-shadow: var(--shadow); font-size: .82rem;
        }
        .float-badge .f-icon { width: 32px; height: 32px; border-radius: 8px; background: rgba(34,197,94,.15); display: flex; align-items: center; justify-content: center; color: var(--success); }
        .float-badge strong { display: block; font-size: .88rem; }
        .float-badge span { color: var(--text-muted); font-size: .75rem; }

        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; text-align: center; gap: 40px; }
            .hero-desc { margin: 0 auto 36px; }
            .hero-cta { justify-content: center; }
            .hero-stats { justify-content: center; }
            .hero-visual { display: none; }
        }

        /* ─── MARQUEE / TRUST BAR ─── */
        .trust-bar {
            background: var(--dark-2); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
            padding: 18px 0; overflow: hidden;
        }
        .trust-track {
            display: flex; gap: 48px; align-items: center;
            animation: marquee 28s linear infinite; width: max-content;
        }
        @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .trust-item { display: flex; align-items: center; gap: 10px; white-space: nowrap; font-size: .82rem; color: var(--text-muted); font-weight: 500; }
        .trust-item i { color: var(--primary); }

        /* ─── PACKAGES ─── */
        #paket { background: var(--dark); }
        .packages-header { text-align: center; margin-bottom: 56px; }
        .packages-header .section-sub { margin: 12px auto 0; }
        .packages-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .pkg-card {
            background: var(--dark-2); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 32px;
            transition: all var(--transition); position: relative; overflow: hidden;
        }
        .pkg-card:hover { transform: translateY(-6px); border-color: rgba(14,165,233,.35); box-shadow: 0 20px 60px rgba(0,0,0,.4); }
        .pkg-card.featured {
            border-color: var(--primary); background: var(--dark-2);
            box-shadow: 0 0 0 1px var(--primary), 0 24px 64px rgba(14,165,233,.2);
        }
        .pkg-card.featured::before {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background: radial-gradient(ellipse 60% 40% at 50% 0%, rgba(14,165,233,.12), transparent 65%);
        }
        .pkg-ribbon {
            position: absolute; top: 18px; right: -28px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dk));
            color: #fff; font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
            padding: 5px 40px; transform: rotate(45deg);
            box-shadow: 0 4px 14px rgba(14,165,233,.4);
        }
        .pkg-tag {
            display: inline-block; font-size: .72rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--text-muted); margin-bottom: 20px;
        }
        .pkg-tag.featured-tag { color: var(--primary); }
        .pkg-speed { font-size: 3.2rem; font-weight: 900; line-height: 1; color: var(--text); }
        .pkg-speed span { font-size: 1rem; font-weight: 500; color: var(--text-muted); }
        .pkg-price {
            margin: 16px 0; padding: 14px 0;
            border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
        }
        .pkg-price .amount { font-size: 1.7rem; font-weight: 800; color: var(--primary-lt); }
        .pkg-price .period { font-size: .82rem; color: var(--text-muted); }
        .pkg-features { list-style: none; display: flex; flex-direction: column; gap: 10px; margin: 20px 0 28px; }
        .pkg-features li { display: flex; align-items: center; gap: 10px; font-size: .88rem; }
        .pkg-features li i { color: var(--success); font-size: .9rem; width: 16px; }
        .pkg-features li.no i { color: var(--dark-4); }
        .pkg-features li.no { color: var(--text-muted); }
        .pkg-cta { width: 100%; text-align: center; justify-content: center; }

        @media (max-width: 900px) { .packages-grid { grid-template-columns: 1fr; max-width: 420px; margin: 0 auto; } }
        @media (min-width: 768px) and (max-width: 900px) { .packages-grid { grid-template-columns: repeat(2, 1fr); max-width: 100%; } }

        /* ─── FEATURES ─── */
        #fitur { background: var(--dark-2); }
        .features-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
        .features-visual {
            display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
        }
        .feat-tile {
            background: var(--dark-3); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 24px 20px;
            transition: all var(--transition);
        }
        .feat-tile:hover { border-color: rgba(14,165,233,.35); transform: translateY(-3px); }
        .feat-tile .icon {
            width: 46px; height: 46px; border-radius: 10px; margin-bottom: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }
        .feat-tile h4 { font-size: .92rem; font-weight: 700; margin-bottom: 6px; }
        .feat-tile p { font-size: .8rem; color: var(--text-muted); line-height: 1.55; }
        .icon-blue  { background: rgba(14,165,233,.15); color: var(--primary); }
        .icon-green { background: rgba(34,197,94,.15);  color: var(--success); }
        .icon-orange{ background: rgba(249,115,22,.15); color: var(--accent); }
        .icon-purple{ background: rgba(139,92,246,.15); color: #a78bfa; }
        .feat-tile.span-2 { grid-column: span 2; }

        .features-list { display: flex; flex-direction: column; gap: 24px; }
        .feat-row { display: flex; gap: 16px; }
        .feat-row .icon { width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        .feat-row-text h4 { font-weight: 700; margin-bottom: 4px; }
        .feat-row-text p { font-size: .88rem; color: var(--text-muted); }

        @media (max-width: 900px) {
            .features-inner { grid-template-columns: 1fr; }
            .features-visual { display: none; }
        }

        /* ─── HOW IT WORKS ─── */
        #cara-kerja { background: var(--dark); }
        .steps-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-top: 56px; }
        .step-card {
            text-align: center; padding: 36px 24px;
            background: var(--dark-2); border: 1px solid var(--border);
            border-radius: var(--radius-lg); position: relative;
            transition: all var(--transition);
        }
        .step-card:hover { border-color: rgba(14,165,233,.35); transform: translateY(-4px); }
        .step-card:not(:last-child)::after {
            content: ''; position: absolute; top: 50%; right: -12px; transform: translateY(-50%);
            width: 24px; height: 2px; background: var(--border); z-index: 1;
        }
        .step-num {
            width: 52px; height: 52px; border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dk));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; font-weight: 900; color: #fff;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(14,165,233,.35);
        }
        .step-card h4 { font-weight: 700; font-size: 1rem; margin-bottom: 10px; }
        .step-card p { font-size: .83rem; color: var(--text-muted); line-height: 1.6; }

        @media (max-width: 768px) {
            .steps-grid { grid-template-columns: repeat(2, 1fr); }
            .step-card::after { display: none; }
        }
        @media (max-width: 480px) { .steps-grid { grid-template-columns: 1fr; } }

        /* ─── COVERAGE / MAP ─── */
        #coverage { background: var(--dark-2); }
        .coverage-inner { display: grid; grid-template-columns: 1fr 1.3fr; gap: 64px; align-items: start; }
        .coverage-info {}
        .coverage-info .section-sub { max-width: 100%; }
        .coverage-points { display: flex; flex-direction: column; gap: 14px; margin-top: 32px; }
        .cov-point {
            display: flex; align-items: flex-start; gap: 14px;
            background: var(--dark-3); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 16px;
        }
        .cov-point .icon { width: 38px; height: 38px; border-radius: 9px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
        .cov-point-text h5 { font-weight: 600; font-size: .9rem; margin-bottom: 2px; }
        .cov-point-text p  { font-size: .8rem; color: var(--text-muted); }
        .coverage-map {}
        .map-wrap {
            border-radius: var(--radius-lg); overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 20px 60px rgba(0,0,0,.4);
            height: 420px;
        }
        .map-wrap iframe { width: 100%; height: 100%; border: 0; display: block; }

        @media (max-width: 900px) {
            .coverage-inner { grid-template-columns: 1fr; }
        }

        /* ─── KONTAK ─── */
        #kontak { background: var(--dark); }
        .contact-inner { display: grid; grid-template-columns: 1.2fr 1fr; gap: 64px; align-items: start; }
        .contact-cards { display: flex; flex-direction: column; gap: 16px; margin-top: 36px; }
        .contact-card {
            display: flex; align-items: center; gap: 16px;
            background: var(--dark-2); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 20px;
            transition: all var(--transition);
        }
        .contact-card:hover { border-color: rgba(14,165,233,.35); }
        .contact-card .icon {
            width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
        }
        .contact-card-text h4 { font-weight: 700; font-size: .9rem; margin-bottom: 4px; }
        .contact-card-text a, .contact-card-text p { color: var(--text-muted); font-size: .88rem; }
        .contact-card-text a:hover { color: var(--primary); }
        .contact-form-wrap {
            background: var(--dark-2); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 36px;
        }
        .contact-form-wrap h3 { font-weight: 700; font-size: 1.15rem; margin-bottom: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: .82rem; font-weight: 500; color: var(--text-muted); margin-bottom: 7px; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; background: var(--dark-3); border: 1px solid var(--border);
            border-radius: 9px; padding: 11px 14px; color: var(--text); font-family: inherit;
            font-size: .88rem; transition: border-color var(--transition); outline: none;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: var(--primary); }
        .form-group textarea { resize: vertical; min-height: 110px; }
        .form-group select option { background: var(--dark-3); }

        @media (max-width: 900px) {
            .contact-inner { grid-template-columns: 1fr; }
        }

        /* ─── CTA STRIP ─── */
        .cta-strip {
            background: linear-gradient(135deg, var(--primary-dk) 0%, var(--primary) 50%, #0369a1 100%);
            padding: 72px 0; text-align: center;
            position: relative; overflow: hidden;
        }
        .cta-strip::before {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background: radial-gradient(ellipse 60% 80% at 50% 50%, rgba(255,255,255,.07), transparent);
        }
        .cta-strip h2 { font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 900; color: #fff; margin-bottom: 14px; }
        .cta-strip p { color: rgba(255,255,255,.8); font-size: 1.05rem; margin-bottom: 32px; }
        .cta-strip .btn { background: #fff; color: var(--primary-dk); font-weight: 700; box-shadow: 0 8px 24px rgba(0,0,0,.2); }
        .cta-strip .btn:hover { transform: translateY(-2px); box-shadow: 0 14px 36px rgba(0,0,0,.3); }

        /* ─── TERMS BANNER ─── */
        #syarat { background: var(--dark-2); }
        .terms-banner {
            display: grid; grid-template-columns: 1fr auto; gap: 32px; align-items: center;
            background: var(--dark-3); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 36px 40px;
        }
        .terms-banner h3 { font-size: 1.2rem; font-weight: 700; margin-bottom: 10px; }
        .terms-banner p { font-size: .9rem; color: var(--text-muted); line-height: 1.7; }
        .terms-items {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-top: 28px;
        }
        .terms-item {
            display: flex; align-items: center; gap: 10px;
            background: var(--dark-2); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 14px 16px;
            font-size: .84rem; font-weight: 500; transition: all var(--transition);
        }
        .terms-item:hover { border-color: rgba(14,165,233,.35); color: var(--primary); }
        .terms-item i { color: var(--primary); width: 16px; flex-shrink: 0; }
        @media (max-width: 768px) {
            .terms-banner { grid-template-columns: 1fr; padding: 28px 24px; }
            .terms-items { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 480px) {
            .terms-items { grid-template-columns: 1fr; }
        }

        /* ─── FOOTER ─── */
        footer {
            background: var(--dark-2); border-top: 1px solid var(--border);
            padding: 64px 0 0;
        }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand .nav-logo { margin-bottom: 16px; }
        .footer-brand p { font-size: .85rem; color: var(--text-muted); line-height: 1.75; max-width: 300px; }
        .footer-brand .social-links { display: flex; gap: 10px; margin-top: 20px; }
        .social-link {
            width: 38px; height: 38px; border-radius: 9px;
            background: var(--dark-3); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted); font-size: .9rem; transition: all var(--transition);
        }
        .social-link:hover { border-color: var(--primary); color: var(--primary); background: rgba(14,165,233,.1); }
        .footer-col h5 { font-weight: 700; font-size: .9rem; margin-bottom: 16px; color: var(--text); }
        .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-col ul a { font-size: .84rem; color: var(--text-muted); transition: color var(--transition); }
        .footer-col ul a:hover { color: var(--primary); }
        .footer-bottom {
            border-top: 1px solid var(--border); padding: 20px 0;
            display: flex; align-items: center; justify-content: space-between;
            font-size: .8rem; color: var(--text-muted); flex-wrap: wrap; gap: 10px;
        }
        .footer-bottom a { color: var(--text-muted); transition: color var(--transition); }
        .footer-bottom a:hover { color: var(--primary); }
        .midtrans-badge {
            display: flex; align-items: center; gap: 8px;
            background: var(--dark-3); border: 1px solid var(--border);
            border-radius: 8px; padding: 6px 12px; font-size: .75rem; color: var(--text-muted);
        }
        .midtrans-badge i { color: var(--success); }

        @media (max-width: 900px) {
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .footer-brand { grid-column: span 2; }
        }
        @media (max-width: 480px) {
            .footer-grid { grid-template-columns: 1fr; }
            .footer-brand { grid-column: span 1; }
        }

        /* ─── WHATSAPP BUTTON ─── */
        .wa-float {
            position: fixed; bottom: 28px; right: 28px; z-index: 999;
            width: 56px; height: 56px; border-radius: 50%;
            background: #25d366; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; box-shadow: 0 6px 24px rgba(37,211,102,.55);
            transition: all var(--transition);
        }
        .wa-float:hover { transform: scale(1.1); box-shadow: 0 10px 36px rgba(37,211,102,.7); }

        /* ─── BACK TO TOP ─── */
        #back-top {
            position: fixed; bottom: 96px; right: 28px; z-index: 999;
            width: 42px; height: 42px; border-radius: 10px;
            background: var(--dark-3); border: 1px solid var(--border);
            color: var(--text-muted); display: flex; align-items: center; justify-content: center;
            font-size: .9rem; cursor: pointer; transition: all var(--transition);
            opacity: 0; pointer-events: none;
        }
        #back-top.visible { opacity: 1; pointer-events: all; }
        #back-top:hover { border-color: var(--primary); color: var(--primary); }

        /* ─── MISC ─── */
        .divider { border: none; border-top: 1px solid var(--border); margin: 0; }
        @media (max-width: 600px) {
            .section { padding: 64px 0; }
            .btn-lg { padding: 13px 26px; font-size: .9rem; }
        }
    </style>
</head>
<body>

<!-- ══════════════ NAVBAR ══════════════ -->
<nav id="navbar">
    <div class="container">
        <div class="nav-inner">
            <a href="{{ url('/') }}" class="nav-logo">
                <img src="{{ asset('logo.png') }}" alt="Tim-7 Net">
            </a>

            <ul class="nav-links">
                <li><a href="#paket">Paket</a></li>
                <li><a href="#fitur">Keunggulan</a></li>
                <li><a href="#cara-kerja">Cara Berlangganan</a></li>
                <li><a href="#coverage">Coverage</a></li>
                <li><a href="#kontak">Kontak</a></li>
                <li><a href="{{ route('terms') }}">Syarat & Ketentuan</a></li>
            </ul>

            <div class="nav-actions">
                <a href="https://wa.me/6282279122727" target="_blank" class="btn btn-primary btn-sm">
                    <i class="fab fa-whatsapp"></i> Daftar Sekarang
                </a>
                <button class="nav-toggle" id="navToggle" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="nav-mobile" id="navMobile">
            <ul>
                <li><a href="#paket" class="mob-link">Paket Internet</a></li>
                <li><a href="#fitur" class="mob-link">Keunggulan</a></li>
                <li><a href="#cara-kerja" class="mob-link">Cara Berlangganan</a></li>
                <li><a href="#coverage" class="mob-link">Coverage Area</a></li>
                <li><a href="#kontak" class="mob-link">Kontak</a></li>
                <li><a href="{{ route('terms') }}" class="mob-link">Syarat & Ketentuan</a></li>
            </ul>
            <div class="mob-actions">
                <a href="https://wa.me/6282279122727" target="_blank" class="btn btn-primary" style="justify-content:center;">
                    <i class="fab fa-whatsapp"></i> Daftar Sekarang
                </a>
                @if(Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline" style="justify-content:center;">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline" style="justify-content:center;">Login Admin</a>
                    @endauth
                @endif
            </div>
        </div>
    </div>
</nav>

<!-- ══════════════ HERO ══════════════ -->
<section id="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-inner">
                <!-- LEFT -->
                <div>
                    <div class="hero-eyebrow">
                        <div class="dot"></div>
                        <span>Layanan Aktif di Lampung</span>
                    </div>
                    <h1 class="hero-title">
                        Internet <span class="text-gradient">Fiber Optik</span><br>
                        Cepat, Stabil &<br>Terjangkau
                    </h1>
                    <p class="hero-desc">
                        Tim-7 Net hadir dengan teknologi fiber optik terkini untuk rumah dan bisnis Anda.
                        Nikmati koneksi tanpa gangguan, latensi rendah, dan dukungan 24/7.
                    </p>
                    <div class="hero-cta">
                        <a href="#paket" class="btn btn-primary btn-lg">
                            <i class="fas fa-rocket"></i> Lihat Paket
                        </a>
                        <a href="https://wa.me/6282279122727" target="_blank" class="btn btn-outline btn-lg">
                            <i class="fab fa-whatsapp"></i> Hubungi CS
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number">100+</div>
                            <div class="stat-label">Pelanggan Aktif</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">99.5%</div>
                            <div class="stat-label">Uptime</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">24/7</div>
                            <div class="stat-label">Support</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">&lt;5ms</div>
                            <div class="stat-label">Latensi</div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Speed Card -->
                <div class="hero-visual">
                    <div class="speed-card">
                        <div class="speed-card-header">
                            <div class="icon"><i class="fas fa-wifi"></i></div>
                            <div>
                                <h3>Speed Test</h3>
                                <p>Tim-7 Net Fiber</p>
                            </div>
                        </div>
                        <div class="speed-gauge">
                            <div class="speed-number">100</div>
                            <div class="speed-unit">Mbps Download</div>
                        </div>
                        <div class="speed-bar-wrap"><div class="speed-bar"></div></div>
                        <div class="speed-meta">
                            <span>0 Mbps</span>
                            <span>100 Mbps</span>
                        </div>
                        <div class="speed-features">
                            <div class="speed-feat"><i class="fas fa-check-circle" style="color:var(--success)"></i> Upload simetris</div>
                            <div class="speed-feat"><i class="fas fa-check-circle" style="color:var(--success)"></i> Tanpa batas (FUP bebas)</div>
                            <div class="speed-feat"><i class="fas fa-check-circle" style="color:var(--success)"></i> IP publik tersedia</div>
                        </div>
                    </div>
                    <div class="float-badge">
                        <div class="f-icon"><i class="fas fa-shield-alt"></i></div>
                        <div>
                            <strong>Jaringan Aman</strong>
                            <span>SSL & Anti-DDoS aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust bar -->
<div class="trust-bar">
    <div class="trust-track">
        <div class="trust-item"><i class="fas fa-bolt"></i> Teknologi Fiber Optik GPON</div>
        <div class="trust-item"><i class="fas fa-clock"></i> Instalasi dalam 3 hari kerja</div>
        <div class="trust-item"><i class="fas fa-headset"></i> Customer Service Responsif</div>
        <div class="trust-item"><i class="fas fa-lock"></i> Pembayaran Aman via Midtrans</div>
        <div class="trust-item"><i class="fas fa-star"></i> Rating kepuasan 4.9/5</div>
        <div class="trust-item"><i class="fas fa-map-marker-alt"></i> Melayani Wilayah Lampung</div>
        <!-- Duplikat untuk efek infinite scroll -->
        <div class="trust-item"><i class="fas fa-bolt"></i> Teknologi Fiber Optik GPON</div>
        <div class="trust-item"><i class="fas fa-clock"></i> Instalasi dalam 3 hari kerja</div>
        <div class="trust-item"><i class="fas fa-headset"></i> Customer Service Responsif</div>
        <div class="trust-item"><i class="fas fa-lock"></i> Pembayaran Aman via Midtrans</div>
        <div class="trust-item"><i class="fas fa-star"></i> Rating kepuasan 4.9/5</div>
        <div class="trust-item"><i class="fas fa-map-marker-alt"></i> Melayani Wilayah Lampung</div>
    </div>
</div>

<!-- ══════════════ PAKET ══════════════ -->
<section class="section" id="paket">
    <div class="container">
        <div class="packages-header">
            <div class="badge"><i class="fas fa-tags"></i> Pilihan Paket</div>
            <h2 class="section-title">Paket Internet <span class="text-gradient">Tim-7 Net</span></h2>
            <p class="section-sub">Pilih paket yang sesuai dengan kebutuhan Anda. Semua paket menggunakan teknologi fiber optik tanpa batas kuota.</p>
        </div>

        <div class="packages-grid">

            <!-- Paket Starter -->
            <div class="pkg-card">
                <div class="pkg-tag">STARTER</div>
                <div class="pkg-speed">10 <span>Mbps</span></div>
                <div class="pkg-price">
                    <div class="amount">Rp 150.000</div>
                    <div class="period">/ bulan</div>
                </div>
                <ul class="pkg-features">
                    <li><i class="fas fa-check-circle"></i> Speed 10 Mbps</li>
                    <li><i class="fas fa-check-circle"></i> Tanpa batas kuota</li>
                    <li><i class="fas fa-check-circle"></i> Instalasi gratis</li>
                    <li><i class="fas fa-check-circle"></i> Support via WhatsApp</li>
                    <li><i class="fas fa-check-circle"></i> Router WiFi disediakan</li>
                    <li class="no"><i class="fas fa-times-circle"></i> IP Publik</li>
                </ul>
                <a href="https://wa.me/6282279122727?text=Halo%20Tim-7%20Net%2C%20saya%20tertarik%20paket%20Starter%2010%20Mbps" target="_blank" class="btn btn-outline pkg-cta">
                    Pilih Paket Ini
                </a>
            </div>

            <!-- Paket Home (Featured) -->
            <div class="pkg-card featured">
                <div class="pkg-ribbon">Terpopuler</div>
                <div class="pkg-tag featured-tag">HOME</div>
                <div class="pkg-speed" style="color:var(--primary-lt)">30 <span>Mbps</span></div>
                <div class="pkg-price">
                    <div class="amount">Rp 250.000</div>
                    <div class="period">/ bulan</div>
                </div>
                <ul class="pkg-features">
                    <li><i class="fas fa-check-circle"></i> Speed 30 Mbps</li>
                    <li><i class="fas fa-check-circle"></i> Tanpa batas kuota</li>
                    <li><i class="fas fa-check-circle"></i> Instalasi gratis</li>
                    <li><i class="fas fa-check-circle"></i> Support 24/7</li>
                    <li><i class="fas fa-check-circle"></i> Router dual-band disediakan</li>
                    <li><i class="fas fa-check-circle"></i> IP Publik (opsional)</li>
                </ul>
                <a href="https://wa.me/6282279122727?text=Halo%20Tim-7%20Net%2C%20saya%20tertarik%20paket%20Home%2030%20Mbps" target="_blank" class="btn btn-primary pkg-cta">
                    <i class="fas fa-rocket"></i> Pilih Paket Ini
                </a>
            </div>

            <!-- Paket Business -->
            <div class="pkg-card">
                <div class="pkg-tag">BUSINESS</div>
                <div class="pkg-speed">100 <span>Mbps</span></div>
                <div class="pkg-price">
                    <div class="amount">Rp 500.000</div>
                    <div class="period">/ bulan</div>
                </div>
                <ul class="pkg-features">
                    <li><i class="fas fa-check-circle"></i> Speed 100 Mbps</li>
                    <li><i class="fas fa-check-circle"></i> Tanpa batas kuota</li>
                    <li><i class="fas fa-check-circle"></i> Instalasi & konfigurasi gratis</li>
                    <li><i class="fas fa-check-circle"></i> Support prioritas 24/7</li>
                    <li><i class="fas fa-check-circle"></i> SLA 99.5% uptime</li>
                    <li><i class="fas fa-check-circle"></i> IP Publik dedicated</li>
                </ul>
                <a href="https://wa.me/6282279122727?text=Halo%20Tim-7%20Net%2C%20saya%20tertarik%20paket%20Business%20100%20Mbps" target="_blank" class="btn btn-accent pkg-cta">
                    Pilih Paket Ini
                </a>
            </div>

        </div>

        <p style="text-align:center;margin-top:28px;font-size:.84rem;color:var(--text-muted)">
            <i class="fas fa-info-circle" style="color:var(--primary)"></i>
            Butuh paket custom? <a href="https://wa.me/6282279122727" target="_blank" style="color:var(--primary)">Hubungi kami</a> untuk penawaran khusus.
        </p>
    </div>
</section>

<!-- ══════════════ FITUR ══════════════ -->
<section class="section" id="fitur">
    <div class="container">
        <div class="features-inner">
            <!-- Visual Tiles -->
            <div class="features-visual">
                <div class="feat-tile">
                    <div class="icon icon-blue"><i class="fas fa-tachometer-alt"></i></div>
                    <h4>Kecepatan Tinggi</h4>
                    <p>Fiber optik murni dari ODP hingga rumah Anda untuk kecepatan maksimal.</p>
                </div>
                <div class="feat-tile">
                    <div class="icon icon-green"><i class="fas fa-shield-alt"></i></div>
                    <h4>Jaringan Aman</h4>
                    <p>Proteksi dari ancaman siber dan enkripsi data pelanggan.</p>
                </div>
                <div class="feat-tile">
                    <div class="icon icon-orange"><i class="fas fa-headset"></i></div>
                    <h4>Support 24/7</h4>
                    <p>Tim teknis siap membantu setiap saat melalui WhatsApp dan telepon.</p>
                </div>
                <div class="feat-tile">
                    <div class="icon icon-purple"><i class="fas fa-credit-card"></i></div>
                    <h4>Bayar Mudah</h4>
                    <p>Berbagai metode pembayaran tersedia melalui Midtrans payment gateway.</p>
                </div>
                <div class="feat-tile span-2" style="background:linear-gradient(135deg,rgba(14,165,233,.12),rgba(14,165,233,.05));border-color:rgba(14,165,233,.2)">
                    <div style="display:flex;align-items:center;gap:16px">
                        <div class="icon icon-blue" style="flex-shrink:0"><i class="fas fa-certificate"></i></div>
                        <div>
                            <h4>Berlisensi & Terpercaya</h4>
                            <p style="font-size:.8rem;color:var(--text-muted)">Tim-7 Net beroperasi sesuai regulasi Kominfo dan menggunakan payment gateway resmi Midtrans yang berlisensi Bank Indonesia.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Text Content -->
            <div>
                <div class="badge"><i class="fas fa-star"></i> Keunggulan Kami</div>
                <h2 class="section-title">Mengapa Memilih <span class="text-gradient">Tim-7 Net</span>?</h2>
                <p class="section-sub">Kami berkomitmen memberikan layanan internet terbaik dengan infrastruktur modern dan layanan pelanggan yang responsif.</p>

                <div class="features-list" style="margin-top:36px">
                    <div class="feat-row">
                        <div class="icon icon-blue"><i class="fas fa-network-wired"></i></div>
                        <div class="feat-row-text">
                            <h4>Infrastruktur Fiber Optik GPON</h4>
                            <p>Teknologi Gigabit Passive Optical Network memastikan bandwidth yang stabil dan kecepatan tinggi bahkan di jam sibuk.</p>
                        </div>
                    </div>
                    <div class="feat-row">
                        <div class="icon icon-green"><i class="fas fa-clock"></i></div>
                        <div class="feat-row-text">
                            <h4>Instalasi Cepat 3 Hari Kerja</h4>
                            <p>Tim teknisi berpengalaman kami akan melakukan survey, pemasangan, dan konfigurasi dengan cepat dan profesional.</p>
                        </div>
                    </div>
                    <div class="feat-row">
                        <div class="icon icon-orange"><i class="fas fa-infinity"></i></div>
                        <div class="feat-row-text">
                            <h4>Tanpa Batas Kuota (No FUP)</h4>
                            <p>Streaming, gaming, dan bekerja dari rumah tanpa khawatir kuota habis atau kecepatan diperlambat.</p>
                        </div>
                    </div>
                    <div class="feat-row">
                        <div class="icon icon-purple"><i class="fas fa-undo-alt"></i></div>
                        <div class="feat-row-text">
                            <h4>Garansi Kepuasan</h4>
                            <p>Jika layanan bermasalah dalam 7 hari pertama, kami jamin pengembalian biaya instalasi sepenuhnya.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ CARA BERLANGGANAN ══════════════ -->
<section class="section" id="cara-kerja">
    <div class="container">
        <div style="text-align:center;margin-bottom:16px">
            <div class="badge"><i class="fas fa-list-ol"></i> Proses Mudah</div>
            <h2 class="section-title">Cara <span class="text-gradient">Berlangganan</span></h2>
            <p class="section-sub" style="margin:12px auto 0">Bergabung dengan Tim-7 Net dalam 4 langkah mudah. Tanpa birokrasi rumit.</p>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">1</div>
                <h4>Hubungi CS Kami</h4>
                <p>Chat WhatsApp ke nomor CS kami atau isi form pendaftaran online. Tim kami siap merespons dalam hitungan menit.</p>
            </div>
            <div class="step-card">
                <div class="step-num">2</div>
                <h4>Survey Lokasi</h4>
                <p>Teknisi kami akan mengunjungi lokasi Anda untuk mengecek ketersediaan jaringan dan menentukan titik instalasi terbaik.</p>
            </div>
            <div class="step-card">
                <div class="step-num">3</div>
                <h4>Pembayaran & Kontrak</h4>
                <p>Bayar biaya berlangganan pertama melalui sistem pembayaran online Midtrans yang aman. Tersedia transfer bank, e-wallet, dll.</p>
            </div>
            <div class="step-card">
                <div class="step-num">4</div>
                <h4>Instalasi & Aktif</h4>
                <p>Teknisi kami memasang perangkat dan melakukan konfigurasi. Internet Anda langsung aktif dalam hari yang sama!</p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ COVERAGE ══════════════ -->
<section class="section" id="coverage">
    <div class="container">
        <div class="coverage-inner">
            <div class="coverage-info">
                <div class="badge"><i class="fas fa-map-marker-alt"></i> Area Layanan</div>
                <h2 class="section-title">Coverage <span class="text-gradient">Area</span></h2>
                <p class="section-sub">Tim-7 Net melayani wilayah Lampung dan sekitarnya. Kami terus memperluas jaringan untuk menjangkau lebih banyak pelanggan.</p>

                <div class="coverage-points">
                    <div class="cov-point">
                        <div class="icon icon-blue"><i class="fas fa-map-pin"></i></div>
                        <div class="cov-point-text">
                            <h5>Kantor Operasional</h5>
                            <p>Lampung – Koordinat: -5.374638, 105.079250</p>
                        </div>
                    </div>
                    <div class="cov-point">
                        <div class="icon icon-green"><i class="fas fa-network-wired"></i></div>
                        <div class="cov-point-text">
                            <h5>Jangkauan Jaringan</h5>
                            <p>Radius 5 km dari titik ODP terdekat. Tersedia di berbagai kecamatan di Lampung.</p>
                        </div>
                    </div>
                    <div class="cov-point">
                        <div class="icon icon-orange"><i class="fas fa-expand-arrows-alt"></i></div>
                        <div class="cov-point-text">
                            <h5>Ekspansi Terus Berlanjut</h5>
                            <p>Belum terjangkau? Daftar waiting list dan kami akan menghubungi Anda saat jaringan tersedia.</p>
                        </div>
                    </div>
                    <a href="https://wa.me/6282279122727?text=Halo%2C%20saya%20ingin%20cek%20coverage%20area%20Tim-7%20Net%20di%20lokasi%20saya" target="_blank" class="btn btn-primary" style="margin-top:8px;align-self:flex-start">
                        <i class="fas fa-search-location"></i> Cek Coverage Area Saya
                    </a>
                </div>
            </div>

            <div class="coverage-map">
                <div class="map-wrap">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d308.0712931568074!2d105.07917713506674!3d-5.374654515202038!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e1!3m2!1sid!2sid!4v1777513810003!5m2!1sid!2sid"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Lokasi Tim-7 Net">
                    </iframe>
                </div>
                <p style="font-size:.78rem;color:var(--text-muted);margin-top:10px;text-align:center">
                    <i class="fas fa-map-marker-alt" style="color:var(--accent)"></i>
                    Koordinat: -5.37463823669505, 105.07924978783007
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ KONTAK ══════════════ -->
<section class="section" id="kontak">
    <div class="container">
        <div class="contact-inner">
            <div>
                <div class="badge"><i class="fas fa-envelope"></i> Hubungi Kami</div>
                <h2 class="section-title">Ada Pertanyaan? <span class="text-gradient">Kami Siap Membantu</span></h2>
                <p class="section-sub">Tim customer service Tim-7 Net siap melayani Anda setiap hari.</p>

                <div class="contact-cards">
                    <div class="contact-card">
                        <div class="icon icon-green"><i class="fab fa-whatsapp"></i></div>
                        <div class="contact-card-text">
                            <h4>WhatsApp CS – Herma</h4>
                            <a href="https://wa.me/6282279122727" target="_blank">+62 822-7912-2727</a>
                        </div>
                    </div>
                    <div class="contact-card">
                        <div class="icon icon-blue"><i class="fas fa-envelope"></i></div>
                        <div class="contact-card-text">
                            <h4>Email</h4>
                            <a href="mailto:admin@tim-7.net">admin@tim-7.net</a>
                        </div>
                    </div>
                    <div class="contact-card">
                        <div class="icon icon-orange"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="contact-card-text">
                            <h4>Alamat Kantor</h4>
                            <p>Lampung, Indonesia</p>
                        </div>
                    </div>
                    <div class="contact-card">
                        <div class="icon icon-purple"><i class="fas fa-clock"></i></div>
                        <div class="contact-card-text">
                            <h4>Jam Operasional</h4>
                            <p>Senin – Sabtu: 08.00 – 20.00 WIB</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-form-wrap">
                <h3><i class="fas fa-paper-plane" style="color:var(--primary);margin-right:8px"></i> Kirim Pesan</h3>
                <form id="contactForm" onsubmit="handleContactForm(event)">
                    @csrf
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="name" placeholder="Masukkan nama Anda" required>
                    </div>
                    <div class="form-group">
                        <label>Nomor WhatsApp *</label>
                        <input type="tel" name="phone" placeholder="Contoh: 081234567890" required>
                    </div>
                    <div class="form-group">
                        <label>Pilih Paket</label>
                        <select name="package">
                            <option value="">-- Pilih paket yang diminati --</option>
                            <option value="starter">Starter – 10 Mbps (Rp 150.000/bln)</option>
                            <option value="home">Home – 30 Mbps (Rp 250.000/bln)</option>
                            <option value="business">Business – 100 Mbps (Rp 500.000/bln)</option>
                            <option value="custom">Paket Custom</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pesan / Pertanyaan</label>
                        <textarea name="message" placeholder="Tuliskan pertanyaan atau informasi alamat Anda..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
                        <i class="fas fa-paper-plane"></i> Kirim via WhatsApp
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ CTA STRIP ══════════════ -->
<div class="cta-strip">
    <div class="container" style="position:relative;z-index:1">
        <h2>Siap Menikmati Internet <br>Tanpa Batas?</h2>
        <p>Bergabunglah dengan ratusan pelanggan Tim-7 Net yang sudah merasakan perbedaannya.</p>
        <a href="https://wa.me/6282279122727?text=Halo%20Tim-7%20Net%2C%20saya%20ingin%20berlangganan%20internet%20fiber%20optik" target="_blank" class="btn btn-lg">
            <i class="fab fa-whatsapp"></i> Daftar Sekarang – GRATIS Instalasi!
        </a>
    </div>
</div>

<!-- ══════════════ SYARAT & KETENTUAN ══════════════ -->
<section class="section-sm" id="syarat">
    <div class="container">
        <div class="terms-banner">
            <div>
                <div class="badge" style="margin-bottom:12px"><i class="fas fa-file-contract"></i> Dokumen Legal</div>
                <h3>Syarat &amp; Ketentuan Tim-7 Net</h3>
                <p>
                    Dengan menggunakan layanan Tim-7 Net, Anda menyetujui seluruh ketentuan yang berlaku,
                    termasuk kebijakan pembayaran melalui <strong style="color:var(--text)">Midtrans</strong> (izin Bank Indonesia No. 18/196/DKSP/68),
                    kebijakan refund, dan perlindungan data pribadi sesuai UU PDP.
                </p>
                <div class="terms-items">
                    <a href="{{ route('terms') }}#pasal-5" class="terms-item">
                        <i class="fas fa-lock"></i> Sistem Pembayaran
                    </a>
                    <a href="{{ route('terms') }}#pasal-6" class="terms-item">
                        <i class="fas fa-undo-alt"></i> Kebijakan Refund
                    </a>
                    <a href="{{ route('terms') }}#pasal-10" class="terms-item">
                        <i class="fas fa-shield-alt"></i> Privasi Data
                    </a>
                    <a href="{{ route('terms') }}#pasal-4" class="terms-item">
                        <i class="fas fa-file-invoice-dollar"></i> Biaya & Tagihan
                    </a>
                    <a href="{{ route('terms') }}#pasal-11" class="terms-item">
                        <i class="fas fa-chart-line"></i> Jaminan Layanan (SLA)
                    </a>
                    <a href="{{ route('terms') }}#pasal-14" class="terms-item">
                        <i class="fas fa-gavel"></i> Penyelesaian Sengketa
                    </a>
                </div>
            </div>
            <div style="flex-shrink:0">
                <a href="{{ route('terms') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-file-alt"></i> Baca Selengkapnya
                </a>
            </div>
        </div>

        <div style="display:none" id="tncAccordion">
            <div class="acc-item open">
                <div class="acc-header" onclick="toggleAcc(this)">
                    <div class="acc-header-left">
                        <div class="num">1</div>
                        <h4>Definisi</h4>
                    </div>
                    <i class="fas fa-chevron-down acc-arrow"></i>
                </div>
                <div class="acc-body">
                    <p>Dalam Syarat &amp; Ketentuan ini, yang dimaksud dengan:</p>
                    <ul>
                        <li><strong>"Tim-7 Net" / "Perusahaan"</strong>: Penyedia layanan internet berbasis fiber optik yang beroperasi di wilayah Lampung, Indonesia.</li>
                        <li><strong>"Pelanggan"</strong>: Perseorangan atau badan hukum yang mendaftar dan menggunakan layanan internet Tim-7 Net.</li>
                        <li><strong>"Layanan"</strong>: Akses internet melalui jaringan fiber optik beserta perangkat yang disediakan Tim-7 Net.</li>
                        <li><strong>"Midtrans"</strong>: PT Midtrans selaku penyedia jasa pemrosesan pembayaran yang berlisensi dan diawasi oleh Bank Indonesia.</li>
                        <li><strong>"Transaksi"</strong>: Setiap pembayaran biaya berlangganan, biaya instalasi, atau biaya lain yang dilakukan Pelanggan kepada Tim-7 Net melalui platform Midtrans.</li>
                        <li><strong>"Tagihan"</strong>: Dokumen atau notifikasi elektronik yang menyatakan jumlah yang harus dibayar Pelanggan untuk periode tertentu.</li>
                    </ul>
                </div>
            </div>

            <!-- 2 -->
            <div class="acc-item">
                <div class="acc-header" onclick="toggleAcc(this)">
                    <div class="acc-header-left">
                        <div class="num">2</div>
                        <h4>Ruang Lingkup Layanan</h4>
                    </div>
                    <i class="fas fa-chevron-down acc-arrow"></i>
                </div>
                <div class="acc-body">
                    <p>Tim-7 Net menyediakan layanan berikut kepada Pelanggan:</p>
                    <ul>
                        <li>Akses internet dedicated melalui teknologi fiber optik GPON.</li>
                        <li>Instalasi perangkat CPE (Customer Premises Equipment) di lokasi Pelanggan.</li>
                        <li>Pemeliharaan dan perbaikan jaringan selama masa berlangganan aktif.</li>
                        <li>Layanan pelanggan melalui WhatsApp, telepon, dan email.</li>
                    </ul>
                    <p>Tim-7 Net berhak mengubah, menambah, atau menghentikan fitur layanan dengan pemberitahuan minimal 14 (empat belas) hari kalender kepada Pelanggan.</p>
                </div>
            </div>

            <!-- 3 -->
            <div class="acc-item">
                <div class="acc-header" onclick="toggleAcc(this)">
                    <div class="acc-header-left">
                        <div class="num">3</div>
                        <h4>Pendaftaran &amp; Aktivasi Layanan</h4>
                    </div>
                    <i class="fas fa-chevron-down acc-arrow"></i>
                </div>
                <div class="acc-body">
                    <ul>
                        <li>Pelanggan wajib memberikan informasi yang benar, lengkap, dan terkini pada saat pendaftaran, termasuk nama, nomor identitas (KTP), alamat, dan nomor telepon aktif.</li>
                        <li>Pendaftaran dinyatakan sah setelah Pelanggan menyelesaikan pembayaran biaya berlangganan pertama dan/atau biaya instalasi melalui sistem Midtrans.</li>
                        <li>Aktivasi layanan dilakukan dalam 3 (tiga) hari kerja setelah konfirmasi pembayaran diterima, tergantung ketersediaan slot teknisi.</li>
                        <li>Tim-7 Net berhak menolak pendaftaran jika lokasi Pelanggan berada di luar coverage area atau jika terdapat hambatan teknis.</li>
                    </ul>
                </div>
            </div>

            <!-- 4 -->
            <div class="acc-item">
                <div class="acc-header" onclick="toggleAcc(this)">
                    <div class="acc-header-left">
                        <div class="num">4</div>
                        <h4>Pembayaran &amp; Tagihan</h4>
                    </div>
                    <i class="fas fa-chevron-down acc-arrow"></i>
                </div>
                <div class="acc-body">
                    <p><strong>4.1 Metode Pembayaran</strong></p>
                    <p>Pembayaran tagihan Tim-7 Net diproses melalui Midtrans Payment Gateway dan mendukung metode berikut:</p>
                    <ul>
                        <li>Transfer Bank (BCA, BNI, BRI, Mandiri, dan lainnya)</li>
                        <li>Virtual Account (semua bank nasional)</li>
                        <li>Dompet Digital (GoPay, OVO, Dana, LinkAja, ShopeePay)</li>
                        <li>Kartu Kredit / Debit Visa &amp; Mastercard</li>
                        <li>Indomaret / Alfamart</li>
                        <li>QRIS</li>
                    </ul>
                    <p><strong>4.2 Siklus Tagihan</strong></p>
                    <ul>
                        <li>Tagihan diterbitkan setiap bulan pada tanggal yang sama dengan tanggal aktivasi layanan.</li>
                        <li>Pelanggan wajib melunasi tagihan paling lambat 7 (tujuh) hari kalender setelah tagihan diterbitkan.</li>
                        <li>Pembayaran yang terlambat lebih dari 7 hari dapat mengakibatkan penangguhan layanan sementara.</li>
                    </ul>
                    <p><strong>4.3 Keamanan Transaksi</strong></p>
                    <p>Semua transaksi yang diproses melalui Midtrans dilindungi dengan enkripsi SSL/TLS. Tim-7 Net tidak menyimpan data kartu kredit atau informasi rekening bank Pelanggan. Keamanan data keuangan Pelanggan dijamin oleh sistem Midtrans yang tersertifikasi PCI-DSS.</p>
                </div>
            </div>

            <!-- 5 -->
            <div class="acc-item">
                <div class="acc-header" onclick="toggleAcc(this)">
                    <div class="acc-header-left">
                        <div class="num">5</div>
                        <h4>Kebijakan Pengembalian Dana (Refund)</h4>
                    </div>
                    <i class="fas fa-chevron-down acc-arrow"></i>
                </div>
                <div class="acc-body">
                    <p><strong>5.1 Kondisi Pengembalian Dana</strong></p>
                    <p>Tim-7 Net akan memproses pengembalian dana dalam kondisi berikut:</p>
                    <ul>
                        <li>Pembayaran ganda (double payment) yang terjadi karena kesalahan sistem.</li>
                        <li>Layanan tidak dapat diaktifkan karena keterbatasan teknis Tim-7 Net dalam 14 hari sejak pendaftaran.</li>
                        <li>Pelanggan membatalkan berlangganan dalam 7 hari pertama aktivasi dan belum menggunakan layanan lebih dari 3 hari.</li>
                    </ul>
                    <p><strong>5.2 Kondisi Tidak Dapat Dikembalikan</strong></p>
                    <ul>
                        <li>Biaya instalasi tidak dapat dikembalikan setelah proses instalasi selesai dilakukan.</li>
                        <li>Tagihan bulanan yang sudah berjalan tidak dapat dikembalikan meskipun layanan tidak digunakan.</li>
                        <li>Pengembalian dana tidak berlaku untuk pembatalan yang disebabkan oleh pelanggaran Syarat &amp; Ketentuan oleh Pelanggan.</li>
                    </ul>
                    <p><strong>5.3 Proses Pengembalian Dana</strong></p>
                    <ul>
                        <li>Permintaan refund diajukan melalui email ke <a href="mailto:admin@tim-7.net" style="color:var(--primary)">admin@tim-7.net</a> atau WhatsApp CS.</li>
                        <li>Proses verifikasi berlangsung 1–3 hari kerja.</li>
                        <li>Dana dikembalikan ke metode pembayaran asal dalam 7–14 hari kerja melalui Midtrans.</li>
                    </ul>
                </div>
            </div>

            <!-- 6 -->
            <div class="acc-item">
                <div class="acc-header" onclick="toggleAcc(this)">
                    <div class="acc-header-left">
                        <div class="num">6</div>
                        <h4>Kewajiban &amp; Larangan Pelanggan</h4>
                    </div>
                    <i class="fas fa-chevron-down acc-arrow"></i>
                </div>
                <div class="acc-body">
                    <p><strong>Kewajiban Pelanggan:</strong></p>
                    <ul>
                        <li>Membayar tagihan tepat waktu sesuai jadwal yang telah disepakati.</li>
                        <li>Menjaga perangkat milik Tim-7 Net (ONU/ONT, kabel) yang terpasang di lokasi Pelanggan.</li>
                        <li>Melaporkan gangguan layanan sesegera mungkin kepada tim support.</li>
                        <li>Memberikan akses kepada teknisi Tim-7 Net untuk keperluan pemeliharaan atau perbaikan.</li>
                    </ul>
                    <p><strong>Larangan Pelanggan:</strong></p>
                    <ul>
                        <li>Menjual kembali atau menyewakan akses internet Tim-7 Net kepada pihak ketiga tanpa izin tertulis.</li>
                        <li>Menggunakan layanan untuk aktivitas ilegal, termasuk pornografi, perjudian online, penyebaran malware, atau serangan siber.</li>
                        <li>Memodifikasi, merusak, atau memindahkan perangkat yang dipasang oleh Tim-7 Net.</li>
                        <li>Menggunakan layanan untuk aktivitas yang melanggar hukum Indonesia.</li>
                    </ul>
                    <p>Pelanggaran terhadap ketentuan ini dapat mengakibatkan pemutusan layanan tanpa pengembalian dana dan/atau tindakan hukum lebih lanjut.</p>
                </div>
            </div>

            <!-- 7 -->
            <div class="acc-item">
                <div class="acc-header" onclick="toggleAcc(this)">
                    <div class="acc-header-left">
                        <div class="num">7</div>
                        <h4>Kerahasiaan &amp; Perlindungan Data Pribadi</h4>
                    </div>
                    <i class="fas fa-chevron-down acc-arrow"></i>
                </div>
                <div class="acc-body">
                    <p>Tim-7 Net berkomitmen melindungi privasi Pelanggan sesuai dengan ketentuan Undang-Undang No. 27 Tahun 2022 tentang Perlindungan Data Pribadi (UU PDP).</p>
                    <ul>
                        <li><strong>Data yang dikumpulkan:</strong> Nama, alamat, nomor identitas, nomor telepon, alamat email, dan data penggunaan layanan.</li>
                        <li><strong>Penggunaan data:</strong> Pemrosesan tagihan, peningkatan layanan, komunikasi teknis, dan kepatuhan hukum.</li>
                        <li><strong>Berbagi data:</strong> Data Pelanggan tidak dijual kepada pihak ketiga. Data hanya dibagikan kepada Midtrans untuk keperluan pemrosesan pembayaran, atau kepada instansi berwenang atas permintaan hukum yang sah.</li>
                        <li><strong>Keamanan data:</strong> Data disimpan di server yang terenkripsi dan dilindungi dari akses tidak sah.</li>
                        <li><strong>Hak Pelanggan:</strong> Pelanggan berhak mengakses, memperbaiki, atau menghapus data pribadinya dengan menghubungi <a href="mailto:admin@tim-7.net" style="color:var(--primary)">admin@tim-7.net</a>.</li>
                    </ul>
                </div>
            </div>

            <!-- 8 -->
            <div class="acc-item">
                <div class="acc-header" onclick="toggleAcc(this)">
                    <div class="acc-header-left">
                        <div class="num">8</div>
                        <h4>Penangguhan &amp; Pemutusan Layanan</h4>
                    </div>
                    <i class="fas fa-chevron-down acc-arrow"></i>
                </div>
                <div class="acc-body">
                    <p><strong>Tim-7 Net berhak menangguhkan layanan jika:</strong></p>
                    <ul>
                        <li>Tagihan tidak dibayar dalam 7 hari setelah jatuh tempo.</li>
                        <li>Terdapat dugaan penyalahgunaan layanan.</li>
                        <li>Diperlukan pemeliharaan jaringan (dengan pemberitahuan sebelumnya).</li>
                    </ul>
                    <p><strong>Pemutusan permanen dilakukan jika:</strong></p>
                    <ul>
                        <li>Tagihan belum dibayar dalam 30 hari setelah penangguhan pertama.</li>
                        <li>Pelanggan terbukti melanggar larangan yang tercantum dalam Pasal 6.</li>
                        <li>Pelanggan mengajukan pembatalan berlangganan secara resmi.</li>
                    </ul>
                    <p>Setelah pemutusan, Pelanggan wajib mengembalikan seluruh perangkat milik Tim-7 Net dalam kondisi baik dalam waktu 14 hari. Kerusakan perangkat akan dikenakan biaya sesuai harga pasar.</p>
                </div>
            </div>

            <!-- 9 -->
            <div class="acc-item">
                <div class="acc-header" onclick="toggleAcc(this)">
                    <div class="acc-header-left">
                        <div class="num">9</div>
                        <h4>Batasan Tanggung Jawab</h4>
                    </div>
                    <i class="fas fa-chevron-down acc-arrow"></i>
                </div>
                <div class="acc-body">
                    <p>Tim-7 Net tidak bertanggung jawab atas:</p>
                    <ul>
                        <li>Gangguan layanan akibat force majeure (bencana alam, pemadaman listrik massal, kerusuhan, atau kejadian di luar kendali Tim-7 Net).</li>
                        <li>Kerugian bisnis atau finansial Pelanggan yang disebabkan oleh gangguan koneksi internet.</li>
                        <li>Kerusakan perangkat milik Pelanggan yang disebabkan oleh penggunaan layanan yang tidak sesuai petunjuk.</li>
                        <li>Konten yang diakses atau disebarkan oleh Pelanggan melalui jaringan Tim-7 Net.</li>
                    </ul>
                    <p>Kewajiban ganti rugi maksimum Tim-7 Net kepada Pelanggan tidak melebihi jumlah tagihan 1 (satu) bulan berlangganan.</p>
                </div>
            </div>

            <!-- 10 -->
            <div class="acc-item">
                <div class="acc-header" onclick="toggleAcc(this)">
                    <div class="acc-header-left">
                        <div class="num">10</div>
                        <h4>Penyelesaian Perselisihan</h4>
                    </div>
                    <i class="fas fa-chevron-down acc-arrow"></i>
                </div>
                <div class="acc-body">
                    <ul>
                        <li>Perselisihan antara Tim-7 Net dan Pelanggan diselesaikan terlebih dahulu melalui musyawarah untuk mufakat dalam waktu 30 hari.</li>
                        <li>Jika tidak tercapai kesepakatan, perselisihan akan diselesaikan melalui Badan Penyelesaian Sengketa Konsumen (BPSK) setempat atau Pengadilan Negeri yang berwenang di wilayah Lampung.</li>
                        <li>Untuk perselisihan terkait transaksi pembayaran Midtrans, Pelanggan dapat menghubungi layanan pelanggan Midtrans atau mengajukan komplain melalui saluran resmi Midtrans.</li>
                    </ul>
                </div>
            </div>

            <!-- 11 -->
            <div class="acc-item">
                <div class="acc-header" onclick="toggleAcc(this)">
                    <div class="acc-header-left">
                        <div class="num">11</div>
                        <h4>Hukum yang Berlaku &amp; Perubahan Ketentuan</h4>
                    </div>
                    <i class="fas fa-chevron-down acc-arrow"></i>
                </div>
                <div class="acc-body">
                    <p>Syarat &amp; Ketentuan ini tunduk pada dan ditafsirkan berdasarkan hukum yang berlaku di Negara Kesatuan Republik Indonesia, termasuk namun tidak terbatas pada:</p>
                    <ul>
                        <li>UU No. 36 Tahun 1999 tentang Telekomunikasi</li>
                        <li>UU No. 11 Tahun 2008 jo. UU No. 19 Tahun 2016 tentang Informasi dan Transaksi Elektronik</li>
                        <li>UU No. 8 Tahun 1999 tentang Perlindungan Konsumen</li>
                        <li>UU No. 27 Tahun 2022 tentang Perlindungan Data Pribadi</li>
                        <li>Peraturan Bank Indonesia terkait penyelenggaraan pemrosesan transaksi pembayaran</li>
                    </ul>
                    <p>Tim-7 Net berhak mengubah Syarat &amp; Ketentuan ini sewaktu-waktu. Perubahan akan diumumkan melalui website ini dan/atau notifikasi kepada Pelanggan minimal 14 hari sebelum berlaku efektif. Penggunaan layanan setelah perubahan berlaku dianggap sebagai persetujuan terhadap ketentuan baru.</p>
                </div>
            </div>

            <!-- 12 -->
            <div class="acc-item">
                <div class="acc-header" onclick="toggleAcc(this)">
                    <div class="acc-header-left">
                        <div class="num">12</div>
                        <h4>Informasi Perusahaan &amp; Kontak Legal</h4>
                    </div>
                    <i class="fas fa-chevron-down acc-arrow"></i>
                </div>
                <div class="acc-body">
                    <ul>
                        <li><strong>Nama Perusahaan:</strong> Tim-7 Net</li>
                        <li><strong>Domisili:</strong> Lampung, Indonesia</li>
                        <li><strong>Koordinat Operasional:</strong> -5.37463823669505, 105.07924978783007</li>
                        <li><strong>Email:</strong> <a href="mailto:admin@tim-7.net" style="color:var(--primary)">admin@tim-7.net</a></li>
                        <li><strong>Customer Service:</strong> +62 822-7912-2727 (Herma)</li>
                        <li><strong>Payment Gateway:</strong> Midtrans (PT Midtrans) – Izin Bank Indonesia No. 18/196/DKSP/68</li>
                    </ul>
                    <p style="margin-top:14px">Untuk pertanyaan terkait Syarat &amp; Ketentuan ini, hubungi kami melalui email <a href="mailto:admin@tim-7.net" style="color:var(--primary)">admin@tim-7.net</a>.</p>
                </div>
            </div>

        </div><!-- end accordion -->
    </div>
</section>

<!-- ══════════════ FOOTER ══════════════ -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="{{ url('/') }}" class="nav-logo">
                    <img src="{{ asset('logo.png') }}" alt="Tim-7 Net">
                </a>
                <p>Penyedia layanan internet fiber optik berkecepatan tinggi untuk rumah dan bisnis di wilayah Lampung, Indonesia. Koneksi stabil, harga terjangkau, support terpercaya.</p>
                <div class="social-links">
                    <a href="https://wa.me/6282279122727" target="_blank" class="social-link" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="mailto:admin@tim-7.net" class="social-link" title="Email"><i class="fas fa-envelope"></i></a>
                    <a href="#" class="social-link" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h5>Layanan</h5>
                <ul>
                    <li><a href="#paket">Paket Starter</a></li>
                    <li><a href="#paket">Paket Home</a></li>
                    <li><a href="#paket">Paket Business</a></li>
                    <li><a href="#kontak">Paket Custom</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>Perusahaan</h5>
                <ul>
                    <li><a href="#fitur">Tentang Kami</a></li>
                    <li><a href="#coverage">Coverage Area</a></li>
                    <li><a href="#cara-kerja">Cara Berlangganan</a></li>
                    <li><a href="#kontak">Karir</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>Bantuan</h5>
                <ul>
                    <li><a href="#kontak">Hubungi CS</a></li>
                    <li><a href="{{ route('terms') }}">Syarat &amp; Ketentuan</a></li>
                    <li><a href="{{ route('terms') }}#pasal-10">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('terms') }}#pasal-6">Kebijakan Refund</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Tim-7 Net. Hak Cipta Dilindungi. | Lampung, Indonesia</p>
            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
                <div class="midtrans-badge">
                    <i class="fas fa-lock"></i>
                    Pembayaran aman via <strong style="color:var(--text);margin-left:4px">Midtrans</strong>
                </div>
                <a href="{{ route('terms') }}">Syarat &amp; Ketentuan</a>
                <a href="{{ route('terms') }}#pasal-10">Privasi</a>
            </div>
        </div>
    </div>
</footer>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/6282279122727?text=Halo%20Tim-7%20Net%2C%20saya%20ingin%20informasi%20lebih%20lanjut" target="_blank" class="wa-float" title="Chat WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>

<!-- Back to Top -->
<button id="back-top" onclick="window.scrollTo({top:0,behavior:'smooth'})" title="Kembali ke atas">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- ══════════════ SCRIPTS ══════════════ -->
<script>
    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 40);
        document.getElementById('back-top').classList.toggle('visible', window.scrollY > 400);
    });

    // Mobile nav toggle
    document.getElementById('navToggle').addEventListener('click', () => {
        document.getElementById('navMobile').classList.toggle('open');
    });

    // Close mobile nav on link click
    document.querySelectorAll('.mob-link').forEach(link => {
        link.addEventListener('click', () => {
            document.getElementById('navMobile').classList.remove('open');
        });
    });

    // Accordion
    function toggleAcc(header) {
        const item = header.parentElement;
        const isOpen = item.classList.contains('open');
        // Close all
        document.querySelectorAll('.acc-item').forEach(i => i.classList.remove('open'));
        // Open clicked if was closed
        if (!isOpen) item.classList.add('open');
    }

    // Contact form → redirect to WhatsApp
    function handleContactForm(e) {
        e.preventDefault();
        const form = e.target;
        const name    = form.name.value.trim();
        const phone   = form.phone.value.trim();
        const pkg     = form.package.value;
        const message = form.message.value.trim();

        const pkgLabels = {
            starter:  'Starter 10 Mbps (Rp 150.000/bln)',
            home:     'Home 30 Mbps (Rp 250.000/bln)',
            business: 'Business 100 Mbps (Rp 500.000/bln)',
            custom:   'Paket Custom',
            '':       'Belum dipilih'
        };

        let text = `Halo Tim-7 Net! 👋\n\nSaya ingin informasi lebih lanjut:\n\n`;
        text += `*Nama:* ${name}\n`;
        text += `*No. WA:* ${phone}\n`;
        text += `*Paket:* ${pkgLabels[pkg] || pkg}\n`;
        if (message) text += `*Pesan:* ${message}\n`;

        const encoded = encodeURIComponent(text);
        window.open(`https://wa.me/6282279122727?text=${encoded}`, '_blank');
    }

    // Active nav link on scroll
    const sections = document.querySelectorAll('section[id], div.cta-strip');
    const navLinks = document.querySelectorAll('.nav-links a');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                navLinks.forEach(l => l.classList.remove('active'));
                const active = document.querySelector(`.nav-links a[href="#${entry.target.id}"]`);
                if (active) active.classList.add('active');
            }
        });
    }, { rootMargin: '-40% 0px -40% 0px' });
    document.querySelectorAll('section[id]').forEach(s => observer.observe(s));
</script>

</body>
</html>
