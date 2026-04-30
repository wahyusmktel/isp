<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelanggan - Tim-7 Net</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; overflow: hidden; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(0,0,0,0.02);
        }
        /* Speedometer Animation */
        .speedometer { position: relative; width: 180px; height: 90px; margin: 0 auto 30px; overflow: hidden; }
        .speed-arc {
            position: absolute; top: 0; left: 0; width: 180px; height: 180px;
            border-radius: 50%;
            border: 12px solid #e2e8f0;
            border-bottom-color: transparent; border-right-color: transparent;
            transform: rotate(-45deg);
        }
        .speed-progress {
            position: absolute; top: 0; left: 0; width: 180px; height: 180px;
            border-radius: 50%;
            border: 12px solid transparent;
            border-top-color: #0ea5e9; border-left-color: #0ea5e9;
            transform: rotate(-45deg);
            animation: speedFill 2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        .speed-needle {
            position: absolute; bottom: -2px; left: 50%; width: 4px; height: 75px;
            background: linear-gradient(to top, #334155, #0f172a); border-radius: 4px;
            transform-origin: bottom center;
            transform: translateX(-50%) rotate(-90deg);
            animation: speedNeedle 2.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        .speed-needle::after {
            content:''; position:absolute; bottom:-4px; left:50%; transform:translateX(-50%);
            width:14px; height:14px; background:#0f172a; border-radius:50%; border:3px solid #fff;
        }
        .speed-text {
            position: absolute; bottom: 10px; width: 100%; text-align: center;
            font-size: 1.25rem; font-weight: 800; color: #0ea5e9;
            animation: fadeIn 2s forwards;
        }
        @keyframes speedFill { 0% { transform: rotate(-45deg); } 100% { transform: rotate(135deg); } }
        @keyframes speedNeedle { 0% { transform: translateX(-50%) rotate(-90deg); } 100% { transform: translateX(-50%) rotate(90deg); } }
        @keyframes fadeIn { 0% { opacity: 0; } 100% { opacity: 1; } }

        /* Floating background shapes */
        .shape { position: absolute; border-radius: 50%; filter: blur(60px); z-index: -1; opacity: 0.6; animation: float 10s infinite alternate ease-in-out; }
        .shape-1 { width: 400px; height: 400px; background: #bae6fd; top: -100px; left: -100px; }
        .shape-2 { width: 350px; height: 350px; background: #e0f2fe; bottom: -50px; right: -50px; animation-delay: -5s; }
        @keyframes float { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(30px, 30px) scale(1.1); } }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative p-4">
    <!-- Background Decor -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    
    <div class="glass-panel w-full max-w-md rounded-3xl p-8 sm:p-10 relative z-10">
        <!-- Back to Home -->
        <a href="{{ url('/') }}" class="absolute top-6 left-6 text-gray-400 hover:text-gray-700 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>

        <!-- Logo -->
        <div class="text-center mb-6">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="h-10 mx-auto">
        </div>

        <!-- Animation -->
        <div class="speedometer mt-8">
            <div class="speed-arc"></div>
            <div class="speed-progress"></div>
            <div class="speed-needle"></div>
            <div class="speed-text">100+ Mbps</div>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Portal Pelanggan</h1>
            <p class="text-sm text-gray-500">Masuk untuk melihat tagihan & layanan Anda.</p>
        </div>

        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-100 text-red-600 text-sm px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('customer.login.post') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">ID / Nomor Pelanggan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input type="text" name="customer_number" required placeholder="Contoh: 12345" 
                           class="w-full bg-white border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 block pl-11 pr-4 py-3.5 transition-all outline-none font-medium placeholder-gray-400 shadow-sm" autofocus>
                </div>
            </div>

            <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-sky-500/30 transition-all hover:shadow-sky-500/50 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                Masuk Dashboard <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-gray-400 font-medium">
            Lupa ID Pelanggan? <a href="https://wa.me/6282279122727" target="_blank" class="text-sky-500 hover:underline">Hubungi CS</a>
        </div>
    </div>
</body>
</html>
