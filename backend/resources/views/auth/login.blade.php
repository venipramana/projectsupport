<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Project Support</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #8b5cf6;
            --secondary: #3b82f6;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --input-bg: rgba(15, 23, 42, 0.6);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .ambient-light {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            z-index: -1;
            opacity: 0.5;
        }
        .light-1 {
            top: -10%; left: -10%;
            width: 50vw; height: 50vw;
            background: radial-gradient(circle, var(--primary), transparent 60%);
        }
        .light-2 {
            bottom: -20%; right: -10%;
            width: 60vw; height: 60vw;
            background: radial-gradient(circle, var(--secondary), transparent 60%);
        }

        .login-container {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.01) 100%);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            animation: fadeInUp 0.8s ease-out;
        }

        .logo {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--text-main);
            text-align: center;
            margin-bottom: 1.5rem;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        .logo-text-primary {
            letter-spacing: 1px;
        }
        .logo-text-secondary {
            font-size: 1.2rem;
            font-weight: 500;
            color: var(--text-muted);
            letter-spacing: 2px;
        }

        h2 {
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        p.subtitle {
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            background: var(--input-bg);
            border: 1px solid var(--glass-border);
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.4);
        }

        .error-message {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: block;
        }

        .error-alert {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="ambient-light light-1"></div>
    <div class="ambient-light light-2"></div>

    <div class="login-container">
        <a href="/" class="logo">
            <svg viewBox="0 0 100 100" width="70" height="70" style="margin-bottom: 10px;">
                <!-- Top-Left (Pink) -->
                <path d="M 50 45 Q 15 45 15 25 A 20 20 0 0 1 45 10 Q 50 20 50 45 Z" fill="#e879f9" />
                <!-- Top-Right (Purple) -->
                <path d="M 50 45 Q 85 45 85 25 A 20 20 0 0 0 55 10 Q 50 20 50 45 Z" fill="#a855f7" />
                <!-- Bottom-Left (Blue) -->
                <path d="M 50 55 Q 15 55 15 75 A 20 20 0 0 0 45 90 Q 50 80 50 55 Z" fill="#3b82f6" />
                <!-- Bottom-Right (Cyan) -->
                <path d="M 50 55 Q 85 55 85 75 A 20 20 0 0 1 55 90 Q 50 80 50 55 Z" fill="#0ea5e9" />
            </svg>
            <span class="logo-text-primary">Project</span>
            <span class="logo-text-secondary">Support</span>
        </a>
        <h2>Welcome Back</h2>
        <p class="subtitle">Sign in to your account to continue</p>

        @if ($errors->any())
            <div class="error-alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="idpengguna">ID Pengguna (Username)</label>
                <input type="text" id="idpengguna" name="idpengguna" value="{{ old('idpengguna') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-submit">Sign In</button>
        </form>
    </div>
</body>
</html>
