<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ISP Management</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap');
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#0a0a0a;color:#00ff41;min-height:100vh;overflow:hidden;position:relative}

        /* Matrix rain canvas */
        #matrix{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;opacity:.15}

        /* Grid overlay */
        .grid-overlay{position:fixed;inset:0;z-index:1;
            background-image:linear-gradient(rgba(0,255,65,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,255,65,.03) 1px,transparent 1px);
            background-size:50px 50px;pointer-events:none}

        /* Scanline */
        .scanline{position:fixed;top:0;left:0;width:100%;height:4px;background:rgba(0,255,65,.08);z-index:2;pointer-events:none;
            animation:scanline 6s linear infinite}
        @keyframes scanline{0%{top:-10px}100%{top:100vh}}

        /* Container */
        .login-container{position:relative;z-index:10;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:1rem}

        /* Card */
        .login-card{width:100%;max-width:420px;background:rgba(10,10,10,.85);border:1px solid rgba(0,255,65,.2);border-radius:16px;
            backdrop-filter:blur(20px);padding:2.5rem;position:relative;overflow:hidden;
            box-shadow:0 0 40px rgba(0,255,65,.08),inset 0 1px 0 rgba(0,255,65,.1)}
        .login-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;
            background:linear-gradient(90deg,transparent,#00ff41,transparent);animation:glow-line 3s ease-in-out infinite}
        @keyframes glow-line{0%,100%{opacity:.3}50%{opacity:1}}

        /* Terminal Header */
        .terminal-header{display:flex;align-items:center;gap:8px;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid rgba(0,255,65,.15)}
        .terminal-dots{display:flex;gap:6px}
        .terminal-dots span{width:10px;height:10px;border-radius:50%}
        .terminal-dots span:nth-child(1){background:#ff5f56;box-shadow:0 0 6px #ff5f56}
        .terminal-dots span:nth-child(2){background:#ffbd2e;box-shadow:0 0 6px #ffbd2e}
        .terminal-dots span:nth-child(3){background:#27c93f;box-shadow:0 0 6px #27c93f}
        .terminal-title{font-family:'JetBrains Mono',monospace;font-size:.7rem;color:#555;margin-left:auto}

        /* Logo */
        .logo-area{text-align:center;margin-bottom:1.5rem}
        .logo-icon{width:56px;height:56px;margin:0 auto .75rem;border-radius:14px;background:rgba(0,255,65,.1);border:1px solid rgba(0,255,65,.3);
            display:flex;align-items:center;justify-content:center;animation:pulse-glow 2s ease-in-out infinite}
        @keyframes pulse-glow{0%,100%{box-shadow:0 0 15px rgba(0,255,65,.2)}50%{box-shadow:0 0 30px rgba(0,255,65,.4)}}
        .logo-icon svg{width:28px;height:28px;color:#00ff41}
        .logo-title{font-family:'JetBrains Mono',monospace;font-size:1.1rem;font-weight:700;color:#00ff41;letter-spacing:2px}
        .logo-sub{font-size:.65rem;color:#444;font-family:'JetBrains Mono',monospace;margin-top:4px}

        /* Typing effect */
        .typing{font-family:'JetBrains Mono',monospace;font-size:.7rem;color:#00ff41;margin-bottom:1.5rem;opacity:.7}
        .typing .cursor{animation:blink .8s infinite}
        @keyframes blink{0%,100%{opacity:1}50%{opacity:0}}

        /* Form */
        .field{margin-bottom:1.25rem}
        .field label{display:block;font-family:'JetBrains Mono',monospace;font-size:.65rem;color:#00ff41;margin-bottom:.5rem;text-transform:uppercase;letter-spacing:2px}
        .input-wrap{position:relative}
        .input-wrap svg{position:absolute;left:14px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#00ff41;opacity:.5}
        .field input{width:100%;padding:.75rem .875rem .75rem 42px;font-size:.875rem;font-family:'JetBrains Mono',monospace;
            background:rgba(0,255,65,.05);border:1px solid rgba(0,255,65,.2);border-radius:10px;color:#00ff41;outline:none;transition:all .2s}
        .field input::placeholder{color:rgba(0,255,65,.25)}
        .field input:focus{border-color:#00ff41;box-shadow:0 0 15px rgba(0,255,65,.15);background:rgba(0,255,65,.08)}

        /* Error */
        .error-msg{font-family:'JetBrains Mono',monospace;font-size:.7rem;color:#ff4444;margin-top:.5rem;padding:.5rem .75rem;
            background:rgba(255,68,68,.1);border:1px solid rgba(255,68,68,.2);border-radius:8px}

        /* Remember */
        .remember{display:flex;align-items:center;gap:8px;margin-bottom:1.5rem}
        .remember input[type="checkbox"]{width:14px;height:14px;accent-color:#00ff41;cursor:pointer}
        .remember label{font-family:'JetBrains Mono',monospace;font-size:.7rem;color:#555;cursor:pointer}

        /* Submit */
        .submit-btn{width:100%;padding:.875rem;font-family:'JetBrains Mono',monospace;font-size:.85rem;font-weight:600;
            background:linear-gradient(135deg,rgba(0,255,65,.2),rgba(0,255,65,.1));border:1px solid rgba(0,255,65,.4);
            border-radius:10px;color:#00ff41;cursor:pointer;letter-spacing:1px;text-transform:uppercase;transition:all .2s;position:relative;overflow:hidden}
        .submit-btn:hover{background:linear-gradient(135deg,rgba(0,255,65,.3),rgba(0,255,65,.15));box-shadow:0 0 25px rgba(0,255,65,.2);transform:translateY(-1px)}
        .submit-btn:active{transform:translateY(0)}
        .submit-btn:disabled{opacity:.5;cursor:not-allowed;transform:none}
        .submit-btn::after{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;
            background:linear-gradient(transparent,rgba(0,255,65,.05),transparent);transform:rotate(45deg);transition:.5s}
        .submit-btn:hover::after{left:100%}

        /* Footer */
        .login-footer{text-align:center;margin-top:1.5rem;font-family:'JetBrains Mono',monospace;font-size:.6rem;color:#333}

        /* Credentials hint */
        .cred-hint{margin-top:1.25rem;padding:.75rem;background:rgba(0,255,65,.03);border:1px solid rgba(0,255,65,.1);border-radius:10px}
        .cred-hint p{font-family:'JetBrains Mono',monospace;font-size:.6rem;color:#444;line-height:1.6}
        .cred-hint span{color:#00ff41}

        /* Status bar */
        .status-bar{position:fixed;bottom:0;left:0;right:0;z-index:10;padding:6px 16px;
            background:rgba(0,255,65,.08);border-top:1px solid rgba(0,255,65,.15);
            display:flex;align-items:center;justify-content:space-between;
            font-family:'JetBrains Mono',monospace;font-size:.6rem;color:#444}
        .status-bar .live{color:#00ff41;display:flex;align-items:center;gap:4px}
        .status-bar .live::before{content:'';width:6px;height:6px;border-radius:50%;background:#00ff41;animation:blink 1.5s infinite}
    </style>
</head>
<body>
    <canvas id="matrix"></canvas>
    <div class="grid-overlay"></div>
    <div class="scanline"></div>

    <div class="login-container">
        <div class="login-card">
            {{-- Terminal Header --}}
            <div class="terminal-header">
                <div class="terminal-dots"><span></span><span></span><span></span></div>
                <span class="terminal-title">root@isp-panel:~</span>
            </div>

            {{-- Logo --}}
            <div class="logo-area">
                <div class="logo-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-7-4h.01M12 16h.01"/>
                    </svg>
                </div>
                <div class="logo-title">ISP PANEL</div>
                <div class="logo-sub">[ NETWORK MANAGEMENT SYSTEM ]</div>
            </div>

            {{-- Typing --}}
            <div class="typing">
                <span id="typed-text"></span><span class="cursor">█</span>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('login.post') }}" id="login-form">
                @csrf
                <div class="field">
                    <label>// Email</label>
                    <div class="input-wrap">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="user@isp.local" required autofocus autocomplete="email">
                    </div>
                </div>

                <div class="field">
                    <label>// Password</label>
                    <div class="input-wrap">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <input type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                    </div>
                </div>

                @if ($errors->any())
                <div class="error-msg">
                    ⚠ {{ $errors->first() }}
                </div>
                @endif

                <div class="remember">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">remember_session = true</label>
                </div>

                <button type="submit" class="submit-btn" id="submit-btn">
                    → Authenticate
                </button>
            </form>

            {{-- Hint --}}
            <div class="cred-hint">
                <p><span>admin</span>: admin@isp.local / admin123</p>
                <p><span>operator</span>: operator@isp.local / operator123</p>
                <p><span>pelanggan</span>: pelanggan@isp.local / pelanggan123</p>
            </div>

            <div class="login-footer">
                &copy; {{ date('Y') }} ISP Management System v2.0
            </div>
        </div>
    </div>

    {{-- Status Bar --}}
    <div class="status-bar">
        <div class="live">SYSTEM ONLINE</div>
        <span id="clock"></span>
    </div>

    <script>
    // Matrix rain
    const c = document.getElementById('matrix');
    const ctx = c.getContext('2d');
    c.width = window.innerWidth;
    c.height = window.innerHeight;
    const chars = 'アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン0123456789ABCDEF<>{}[];:=/\\';
    const fontSize = 14;
    const columns = Math.floor(c.width / fontSize);
    const drops = Array(columns).fill(1);

    function drawMatrix() {
        ctx.fillStyle = 'rgba(10,10,10,0.05)';
        ctx.fillRect(0, 0, c.width, c.height);
        ctx.fillStyle = '#00ff41';
        ctx.font = fontSize + 'px JetBrains Mono, monospace';
        for (let i = 0; i < drops.length; i++) {
            const text = chars[Math.floor(Math.random() * chars.length)];
            ctx.fillText(text, i * fontSize, drops[i] * fontSize);
            if (drops[i] * fontSize > c.height && Math.random() > 0.975) drops[i] = 0;
            drops[i]++;
        }
    }
    setInterval(drawMatrix, 35);
    window.addEventListener('resize', () => { c.width = window.innerWidth; c.height = window.innerHeight; });

    // Typing effect
    const lines = ['Initializing secure connection...', 'Verifying SSL certificate... OK', 'Loading authentication module...', 'Enter your credentials below_'];
    let lineIdx = 0, charIdx = 0;
    const typedEl = document.getElementById('typed-text');
    function typeChar() {
        if (lineIdx >= lines.length) return;
        if (charIdx < lines[lineIdx].length) {
            typedEl.textContent = '> ' + lines[lineIdx].substring(0, charIdx + 1);
            charIdx++;
            setTimeout(typeChar, 30 + Math.random() * 40);
        } else {
            setTimeout(() => { charIdx = 0; lineIdx++; typeChar(); }, 600);
        }
    }
    typeChar();

    // Clock
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleString('id-ID', {hour:'2-digit',minute:'2-digit',second:'2-digit'}) + ' WIB';
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Form submit animation
    document.getElementById('login-form').addEventListener('submit', function() {
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.textContent = '⟳ Authenticating...';
    });
    </script>
</body>
</html>
