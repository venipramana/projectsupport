<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Project Support</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;800&display=swap" rel="stylesheet">
    @yield('custom-head')
    <style>
        :root {
            --bg-color: #faf7f2;
            --text-main: #2c2721;
            --text-muted: #6e665d;
            --primary: #755f3e;
            --secondary: #54422b;
            --glass-bg: rgba(255, 255, 255, 0.65);
            --glass-border: rgba(117, 95, 62, 0.18);
            --sidebar-width: 280px;
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
        }

        /* Sidebar */
        aside {
            width: var(--sidebar-width);
            background: rgba(244, 240, 234, 0.85);
            border-right: 1px solid var(--glass-border);
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(12px);
            box-shadow: 2px 0 10px rgba(117, 95, 62, 0.04);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 2rem;
            padding-left: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .logo svg {
            flex-shrink: 0;
        }
        .logo-text-container {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }
        .logo-text-primary {
            letter-spacing: 0.5px;
        }
        .logo-text-secondary {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-muted);
            letter-spacing: 1px;
        }

        .nav-section {
            color: var(--text-main);
            font-size: 1.05rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 1.5rem 0 0.5rem 0.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            user-select: none;
            transition: color 0.3s ease;
        }

        .nav-section:hover {
            color: var(--primary);
        }

        .nav-section svg {
            transition: transform 0.3s ease;
            width: 18px;
            height: 18px;
            color: var(--text-muted);
        }

        .nav-section.open svg {
            transform: rotate(180deg);
        }

        .nav-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
            display: flex;
            flex-direction: column;
        }

        .nav-submenu.open {
            max-height: 500px;
        }

        .nav-item {
            padding: 0.8rem 1rem;
            border-radius: 12px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            margin-bottom: 0.2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: 0.5rem;
        }

        .nav-item.active, .nav-item:hover {
            background: rgba(117, 95, 62, 0.12);
            color: var(--text-main);
            border: 1px solid rgba(117, 95, 62, 0.25);
        }

        .spacer {
            flex-grow: 1;
        }

        .user-profile {
            padding: 1rem;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(117, 95, 62, 0.06);
        }
        
        .user-profile:hover {
            background: rgba(117, 95, 62, 0.12);
            border-color: rgba(117, 95, 62, 0.35);
        }

        .avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #ffffff;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name { font-weight: 600; font-size: 0.95rem; }
        .user-email { font-size: 0.8rem; color: var(--text-muted); }

        .btn-logout {
            margin-top: 1rem;
            width: 100%;
            padding: 0.8rem;
            border-radius: 12px;
            background: transparent;
            border: 1px solid rgba(220, 38, 38, 0.4);
            color: #dc2626;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: rgba(220, 38, 38, 0.1);
        }

        /* Password Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(44, 39, 33, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .password-modal {
            background: #ffffff;
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            transform: translateY(20px);
            transition: transform 0.3s ease;
            box-shadow: 0 20px 40px rgba(117, 95, 62, 0.15);
        }
        .modal-overlay.active .password-modal {
            transform: translateY(0);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .modal-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-main);
        }
        .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.5rem;
        }
        .pw-form-group {
            margin-bottom: 1rem;
        }
        .pw-form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .pw-form-group input {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(117, 95, 62, 0.25);
            color: var(--text-main);
            font-size: 0.9rem;
        }
        .pw-form-group input:focus {
            outline: none;
            border-color: var(--primary);
        }
        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
        }
        #pw-alert {
            display: none;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        #pw-alert.error { background: rgba(239, 68, 68, 0.1); color: #fca5a5; border: 1px solid rgba(239,68,68,0.2); }
        #pw-alert.success { background: rgba(16, 185, 129, 0.1); color: #6ee7b7; border: 1px solid rgba(16,185,129,0.2); }

        /* Main Content */
        main {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
        }

        h1 {
            font-size: 2rem;
            font-weight: 600;
        }

        @yield('custom-styles')
    </style>
</head>
<body>

    <aside>
        <div class="logo">
            <svg viewBox="0 0 100 100" width="40" height="40">
                <!-- Top-Left -->
                <path d="M 50 45 Q 15 45 15 25 A 20 20 0 0 1 45 10 Q 50 20 50 45 Z" fill="#c4aa82" />
                <!-- Top-Right -->
                <path d="M 50 45 Q 85 45 85 25 A 20 20 0 0 0 55 10 Q 50 20 50 45 Z" fill="#755f3e" />
                <!-- Bottom-Left -->
                <path d="M 50 55 Q 15 55 15 75 A 20 20 0 0 0 45 90 Q 50 80 50 55 Z" fill="#54422b" />
                <!-- Bottom-Right -->
                <path d="M 50 55 Q 85 55 85 75 A 20 20 0 0 1 55 90 Q 50 80 50 55 Z" fill="#9c815c" />
            </svg>
            <div class="logo-text-container">
                <span class="logo-text-primary">Project</span>
                <span class="logo-text-secondary">Support</span>
            </div>
        </div>
        
        @if(Auth::user() && Auth::user()->levelpengguna == 0)
        <div class="nav-section" onclick="toggleMenu('menu-referensi', this)">
            Referensi
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </div>
        <div class="nav-submenu {{ request()->routeIs('pengguna.*', 'rdirektorat.*', 'rproject.*') ? 'open' : '' }}" id="menu-referensi">
            <a href="{{ route('pengguna.index') ?? '#' }}" class="nav-item {{ request()->routeIs('pengguna.*') ? 'active' : '' }}">Pengguna</a>
            <a href="{{ route('rdirektorat.index') ?? '#' }}" class="nav-item {{ request()->routeIs('rdirektorat.*') ? 'active' : '' }}">Direktorat</a>
            <a href="{{ route('rproject.index') ?? '#' }}" class="nav-item {{ request()->routeIs('rproject.*') ? 'active' : '' }}">Status Projects</a>
        </div>
        
        <div class="nav-section" onclick="toggleMenu('menu-transaksi', this)">
            Transaksi
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </div>
        <div class="nav-submenu {{ request()->routeIs('rrkap.*', 'rcatalog.*', 'project.*') ? 'open' : '' }}" id="menu-transaksi">
            <a href="{{ route('rrkap.index') ?? '#' }}" class="nav-item {{ request()->routeIs('rrkap.*') ? 'active' : '' }}">RKAP</a>
            <a href="{{ route('rcatalog.index') ?? '#' }}" class="nav-item {{ request()->routeIs('rcatalog.*') ? 'active' : '' }}">Katalog</a>
            <a href="{{ route('project.index') ?? '#' }}" class="nav-item {{ request()->routeIs('project.*') ? 'active' : '' }}">Data Project</a>
        </div>
        @endif
        
        <div class="nav-section" onclick="toggleMenu('menu-laporan', this)">
            Laporan
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </div>
        <div class="nav-submenu {{ request()->routeIs('laporan.*') || request()->routeIs('dashboard') ? 'open' : '' }}" id="menu-laporan">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('laporan.progress') }}" class="nav-item {{ request()->routeIs('laporan.progress') ? 'active' : '' }}">Progres Project</a>
        </div>
        
        <div class="spacer"></div>

        <div class="user-profile" onclick="openPasswordModal()">
            <div class="avatar">{{ substr(Auth::user()->nama ?? 'U', 0, 1) }}</div>
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->nama ?? 'User' }}</span>
                <span class="user-email">{{ Auth::user()->idpengguna ?? 'ID' }}</span>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">Sign Out</button>
        </form>
    </aside>

    <main>
        <header>
            <h1>@yield('header-title', 'Overview')</h1>
        </header>

        @yield('content')
    </main>

    <!-- Password Change Modal -->
    <div class="modal-overlay" id="passwordModal">
        <div class="password-modal">
            <div class="modal-header">
                <h3>Ubah Password</h3>
                <button class="modal-close" onclick="closePasswordModal()">&times;</button>
            </div>
            <div id="pw-alert"></div>
            <form id="passwordForm">
                @csrf
                <div class="pw-form-group">
                    <label>Password Lama</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="pw-form-group">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" required minlength="6">
                </div>
                <div class="pw-form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" required minlength="6">
                </div>
                <button type="submit" class="btn-primary" id="btn-submit-pw">Simpan Password</button>
            </form>
        </div>
    </div>

    @yield('custom-scripts')
    
    <script>
        function toggleMenu(menuId, headerElement) {
            const menu = document.getElementById(menuId);
            menu.classList.toggle('open');
            headerElement.classList.toggle('open');
        }
        
        // Auto-open active menus on load
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.nav-submenu.open').forEach(menu => {
                const header = menu.previousElementSibling;
                if(header && header.classList.contains('nav-section')) {
                    header.classList.add('open');
                }
            });
        });

        // Password Modal Logic
        function openPasswordModal() {
            document.getElementById('passwordModal').classList.add('active');
            document.getElementById('passwordForm').reset();
            document.getElementById('pw-alert').style.display = 'none';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.remove('active');
        }

        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btn = document.getElementById('btn-submit-pw');
            const alertBox = document.getElementById('pw-alert');
            
            btn.disabled = true;
            btn.innerText = 'Menyimpan...';
            alertBox.style.display = 'none';
            alertBox.className = '';

            fetch('{{ route("change.password") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json().then(data => ({status: response.status, body: data})))
            .then(res => {
                btn.disabled = false;
                btn.innerText = 'Simpan Password';
                
                alertBox.style.display = 'block';
                if (res.status === 200 && res.body.success) {
                    alertBox.classList.add('success');
                    alertBox.innerText = res.body.message;
                    setTimeout(closePasswordModal, 2000);
                } else {
                    alertBox.classList.add('error');
                    alertBox.innerText = res.body.message || (res.body.errors && res.body.errors.new_password ? res.body.errors.new_password[0] : 'Terjadi kesalahan.');
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerText = 'Simpan Password';
                alertBox.style.display = 'block';
                alertBox.classList.add('error');
                alertBox.innerText = 'Koneksi error.';
            });
        });
    </script>
</body>
</html>
