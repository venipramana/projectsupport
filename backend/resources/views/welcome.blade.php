<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="pm-ises - Next generation project management system. Streamline your workflow and collaborate seamlessly.">
    <title>pm-ises | Project Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #8b5cf6;
            --secondary: #3b82f6;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glow: 0 0 40px rgba(139, 92, 246, 0.15);
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
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Background Effects */
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

        /* Navbar */
        nav {
            padding: 2rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: fadeInDown 0.8s ease-out;
            border-bottom: 1px solid var(--glass-border);
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #a78bfa, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1px;
            cursor: pointer;
        }

        .nav-links {
            display: flex;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            margin-left: 2.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: var(--text-main);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        /* Hero Section */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 6rem 20px;
            animation: fadeInUp 1s ease-out;
            position: relative;
            z-index: 10;
        }

        .badge {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #a78bfa;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
            box-shadow: var(--glow);
            letter-spacing: 1px;
            text-transform: uppercase;
            animation: pulse 3s infinite alternate;
        }

        h1 {
            font-size: 5.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -2.5px;
        }

        .gradient-text {
            background: linear-gradient(135deg, #c4b5fd, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        p.subtitle {
            font-size: 1.25rem;
            color: var(--text-muted);
            max-width: 650px;
            margin-bottom: 3.5rem;
            line-height: 1.7;
            font-weight: 300;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 1.5rem;
        }

        .btn {
            padding: 1.1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 15px 35px rgba(139, 92, 246, 0.6);
        }

        .btn-secondary {
            background: var(--glass-bg);
            color: var(--text-main);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-4px);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Features Section (Glassmorphism) */
        .features {
            display: flex;
            gap: 2rem;
            margin-top: 6rem;
            max-width: 1100px;
            width: 100%;
            animation: fadeInUp 1.4s ease-out;
        }

        .feature-card {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            text-align: left;
            backdrop-filter: blur(20px);
            flex: 1;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: rgba(139, 92, 246, 0.4);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: rgba(139, 92, 246, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(139, 92, 246, 0.2);
            font-size: 1.8rem;
        }

        .feature-title {
            font-size: 1.3rem;
            margin-bottom: 0.8rem;
            font-weight: 600;
            color: #f8fafc;
        }

        .feature-desc {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Animations */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            from { box-shadow: 0 0 20px rgba(139, 92, 246, 0.1); }
            to { box-shadow: 0 0 35px rgba(139, 92, 246, 0.3); }
        }

        /* Responsive */
        @media (max-width: 900px) {
            h1 { font-size: 4rem; }
            .features { flex-direction: column; }
        }

        @media (max-width: 600px) {
            h1 { font-size: 3rem; }
            .nav-links { display: none; }
            .btn-group { flex-direction: column; width: 100%; max-width: 300px; }
            .btn { justify-content: center; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="ambient-light light-1"></div>
    <div class="ambient-light light-2"></div>

    <nav>
        <div class="logo">pm-ises</div>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#solutions">Solutions</a>
            <a href="#pricing">Pricing</a>
            <a href="{{ route('login') }}">Login</a>
        </div>
    </nav>

    <main>
        <div class="badge">v1.0 is now live</div>
        <h1>Elevate Your <br><span class="gradient-text">Project Management</span></h1>
        <p class="subtitle">Experience the future of collaboration. pm-ises streamlines your workflow, bringing your entire team together in one beautifully crafted, high-performance workspace.</p>
        
        <div class="btn-group">
            <a href="{{ route('login') }}" class="btn btn-primary">
                Get Started Free
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
            <a href="#" class="btn btn-secondary">
                View Documentation
            </a>
        </div>

        <div class="features" id="features">
            <div class="feature-card">
                <div class="feature-icon-wrapper">🚀</div>
                <div class="feature-title">Lightning Performance</div>
                <div class="feature-desc">Engineered for speed. Enjoy zero-latency interactions and instantaneous updates across all your devices.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon-wrapper">🛡️</div>
                <div class="feature-title">Bank-Grade Security</div>
                <div class="feature-desc">Your data is encrypted at rest and in transit. Granular access controls keep your sensitive projects secure.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon-wrapper">📈</div>
                <div class="feature-title">Actionable Insights</div>
                <div class="feature-desc">Visualize your team's progress with dynamic dashboards and reports that turn data into decisions.</div>
            </div>
        </div>
    </main>
</body>
</html>
