<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - ISP Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f172a;
            --panel-bg: #1e293b;
            --primary: #3b82f6;
            --primary-glow: rgba(59, 130, 246, 0.5);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            --danger: #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Matrix Rain Background */
        canvas {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            opacity: 0.15;
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 2rem;
        }

        .login-panel {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .sys-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .sys-logo {
            font-family: 'Fira Code', monospace;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            text-shadow: 0 0 10px var(--primary-glow);
            letter-spacing: -1px;
            margin-bottom: 0.5rem;
        }

        .sys-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-family: 'Fira Code', monospace;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap input {
            width: 100%;
            background: #0f172a;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 0.75rem 1rem;
            color: var(--text-main);
            font-family: 'Fira Code', monospace;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .input-wrap input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--danger);
            padding: 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            font-family: 'Fira Code', monospace;
        }

        .submit-btn {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 0.875rem;
            font-family: 'Fira Code', monospace;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            background: #2563eb;
            box-shadow: 0 0 15px var(--primary-glow);
        }

        .login-footer {
            margin-top: 2rem;
            text-align: center;
            font-family: 'Fira Code', monospace;
            font-size: 0.7rem;
            color: var(--text-muted);
        }

    </style>
</head>
<body>

    <canvas id="matrix"></canvas>

    <div class="login-wrapper">
        <div class="login-panel">
            <div class="sys-header">
                <div class="sys-logo">SYS.REGISTER</div>
                <div class="sys-subtitle">Authorized Personnel Only</div>
            </div>

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label>FullName</label>
                    <div class="input-wrap">
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email_Address</label>
                    <div class="input-wrap">
                        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <input type="password" name="password" required autocomplete="new-password">
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm_Password</label>
                    <div class="input-wrap">
                        <input type="password" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>

                @if ($errors->any())
                <div class="error-msg">
                    ⚠ {{ $errors->first() }}
                </div>
                @endif

                <button type="submit" class="submit-btn">
                    → Register Admin
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center;">
                <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 0.5rem;">Sudah punya akun?</p>
                <a href="{{ route('login') }}" class="submit-btn" style="background: transparent; border: 1px solid var(--primary); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                    ← Kembali ke Login
                </a>
            </div>

            <div class="login-footer">
                &copy; {{ date('Y') }} ISP Management System v2.0
            </div>
        </div>
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
    const drops = [];
    for(let x = 0; x < columns; x++) drops[x] = 1;

    function draw() {
        ctx.fillStyle = 'rgba(15, 23, 42, 0.05)';
        ctx.fillRect(0, 0, c.width, c.height);
        
        ctx.fillStyle = '#3b82f6';
        ctx.font = fontSize + 'px monospace';
        
        for(let i = 0; i < drops.length; i++) {
            const text = chars.charAt(Math.floor(Math.random() * chars.length));
            ctx.fillText(text, i * fontSize, drops[i] * fontSize);
            if(drops[i] * fontSize > c.height && Math.random() > 0.975) drops[i] = 0;
            drops[i]++;
        }
    }
    setInterval(draw, 33);
    </script>
</body>
</html>
