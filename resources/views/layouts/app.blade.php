<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Student Purchase Advisor')</title>
    <link rel="stylesheet" href="{{ asset('css/advisor.css') }}">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        /* ── Stats Grid ────────────────────────────── */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        @media (max-width: 640px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        .stat-card { background: white; border-radius: .75rem; padding: 1.25rem 1.5rem; border: 1px solid #e5e7eb; }
        .stat-label { display: block; font-size: .8rem; color: #6b7280; margin-bottom: .35rem; font-weight: 500; }
        .stat-value { display: block; font-size: 2rem; font-weight: 800; color: #111827; line-height: 1; }
        .stat-layak  .stat-value { color: #16a34a; }
        .stat-kurang .stat-value { color: #d97706; }
        .stat-tidak  .stat-value { color: #dc2626; }

        /* ── Priority List ─────────────────────────── */
        .priority-list { display: flex; flex-direction: column; gap: .5rem; }
        .priority-row { display: flex; align-items: center; gap: 1rem; padding: .75rem 1rem; background: #f9fafb; border-radius: .5rem; border: 1px solid #f3f4f6; }
        .priority-rank { font-size: .85rem; font-weight: 800; width: 2rem; text-align: center; flex-shrink: 0; }
        .rank-gold    { color: #b45309; }
        .rank-silver  { color: #6b7280; }
        .rank-bronze  { color: #92400e; }
        .rank-default { color: #9ca3af; }
        .priority-info { flex: 1; display: flex; flex-direction: column; gap: .1rem; }
        .priority-info strong { font-size: .95rem; color: #111827; }
        .priority-info small  { font-size: .75rem; color: #9ca3af; }
        .priority-right { display: flex; flex-direction: column; align-items: flex-end; gap: .25rem; }
        .priority-score { font-size: 1.1rem; font-weight: 800; color: #1d4ed8; }
        .see-all { display: block; text-align: right; font-size: .8rem; color: #6b7280; margin-top: .75rem; text-decoration: none; }
        .see-all:hover { color: #1d4ed8; }

        /* ── Grid Two ──────────────────────────────── */
        .grid.two { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        @media (max-width: 640px) { .grid.two { grid-template-columns: 1fr; } }
        .grid.two article { background: white; border: 1px solid #e5e7eb; border-radius: .75rem; padding: 1.25rem 1.5rem; }
        .grid.two article span { display: block; font-size: .8rem; color: #6b7280; margin-bottom: .35rem; font-weight: 500; }
        .grid.two article strong { display: block; font-size: 2rem; font-weight: 800; color: #111827; line-height: 1; }
        .panel canvas { max-width: 100%; }

        /* ═══════════════════════════════════════════
           SIDEBAR — Icons + Mobile Responsive
        ═══════════════════════════════════════════ */
        .sidebar {
            display: flex;
            flex-direction: column;
            transition: transform .25s ease;
        }

        .side-link {
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .side-link svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            stroke-width: 2.2;
        }

        /* ── Mobile Topbar (hidden on desktop) ──────── */
        .mobile-topbar {
            display: none;
            align-items: center;
            justify-content: space-between;
            background: #0f4da3;
            color: white;
            padding: .85rem 1.25rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .mobile-topbar .brand {
            color: white;
            font-size: 1.05rem;
            font-weight: 800;
        }
        .mobile-topbar .brand span { color: #93c5fd; }
        .hamburger-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: .25rem;
            display: flex;
            align-items: center;
        }
        .hamburger-btn svg { width: 26px; height: 26px; }

        /* ── Overlay for mobile sidebar ─────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 90;
        }
        .sidebar-overlay.active { display: block; }

        @media (max-width: 900px) {
            .mobile-topbar { display: flex; }
    .sidebar .sidebar-nav {
    margin-top: .5rem;
    display: flex;
    flex-direction: column;
    gap: .25rem;
    padding: 0 .75rem;
}

.sidebar {
    position: fixed;
    top: 0;
    right: 0;
    left: auto;
    height: auto;
    max-height: 45vh;
    width: 250px;
    z-index: 100;
    transform: translateX(100%);
    overflow-y: auto;
    border-radius: 1rem 0 0 1rem;
    background: #0f4da3;
    backdrop-filter: blur(8px);
}

.sidebar .side-link {
    width: 100%;
    padding: .65rem .9rem;
    border-radius: .5rem;
    white-space: nowrap;
}

.sidebar-footer {
    padding: 0 .75rem .75rem;
}
            .app-shell { display: block; }
            
            .sidebar-footer button.profile-card {
    font-family: inherit;
    background: rgba(255,255,255,.08);
    }
    .sidebar-footer button.profile-card:hover {
        background: rgba(255,255,255,.14);
    }

   

/* Sembunyikan brand & subtitle di sidebar mobile (sudah ada di topbar) */
.sidebar > .brand,
.sidebar > .sidebar-subtitle {
    display: none;
}

.sidebar .sidebar-nav {
    margin-top: .5rem;
}
            .sidebar.active {
                transform: translateX(0);
                box-shadow: -4px 0 24px rgba(0,0,0,.15);
            }

            .app-main { margin-left: 0 !important; width: 100%; }
        }
        /* Profile edit toggle button (di halaman profile.edit) */
        .profile-edit-toggle {
            margin-top: .75rem;
            background: rgba(255, 255, 255, .18);
            color: white;
            border: 1px solid rgba(255, 255, 255, .25);
            padding: .6rem 1.5rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .9rem;
            cursor: pointer;
            transition: background .15s;
        }
        .profile-edit-toggle:hover {
        background: rgba(255, 255, 255, .28);
    }

        /* Form di dalam modal */
        .modal-box .form-stack {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .modal-box .form-stack label {
            display: flex;
            flex-direction: column;
            gap: .35rem;
            font-size: .85rem;
            font-weight: 600;
            color: #374151;
        }
        .modal-box .form-stack input {
            border: 1px solid #d1d5db;
            border-radius: .5rem;
            padding: .6rem .75rem;
            font-size: .9rem;
            font-weight: 400;
            color: #111827;
        }
        .modal-box .form-stack input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,.1);
        }
        .modal-box .form-divider {
            border-top: 1px solid #e5e7eb;
            margin: .25rem 0;
        }
        .modal-box .compact-text {
            font-size: .8rem;
            margin: -.25rem 0 0;
        }
        .modal-box .error {
            color: #dc2626;
            font-size: .75rem;
            font-weight: 500;
        }

/* ── Priority Legend (di bawah line chart) ─── */
.priority-legend {
    display: flex;
    flex-direction: column;
    gap: .5rem;
    margin-top: 1.25rem;
}

.priority-legend-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .6rem .9rem;
    background: #f9fafb;
    border-radius: .5rem;
    border: 1px solid #f3f4f6;
}

.legend-rank {
    font-size: .8rem;
    font-weight: 800;
    width: 1.75rem;
    text-align: center;
    flex-shrink: 0;
}

.legend-name {
    flex: 1;
    font-size: .9rem;
    font-weight: 600;
    color: #111827;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.legend-score {
    font-size: 1rem;
    font-weight: 800;
    color: #2563eb;
    min-width: 2.5rem;
    text-align: right;
}
        /* ═══════════════════════════════════════════
           PROFILE MODAL
        ═══════════════════════════════════════════ */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .55);
            z-index: 200;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-overlay.active { display: flex; }

        .modal-box {
            background: white;
            border-radius: 1rem;
            width: 100%;
            max-width: 480px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 1.75rem;
            position: relative;
            animation: modalIn .2s ease;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(12px) scale(.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #f3f4f6;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #6b7280;
        }
        .modal-close:hover { background: #e5e7eb; }
        .modal-close svg { width: 18px; height: 18px; }

        .modal-box h2 {
            margin-top: 0;
            margin-bottom: 1.25rem;
            font-size: 1.25rem;
        }

/* ── History Card (horizontal, responsive) ──── */
.history-card {
    display: grid;
    grid-template-columns: 1fr auto auto;
    align-items: center;
    gap: 1.5rem;
    border: 1px solid #e5e7eb;
    border-radius: .75rem;
    padding: 1rem 1.25rem;
    margin-bottom: .75rem;
}

.history-card-main {
    display: flex;
    flex-direction: column;
    gap: .25rem;
    min-width: 0; /* supaya truncate bekerja */
}

.history-card-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.history-card-meta {
    color: #6b7280;
    font-size: .8rem;
}

.history-card-status {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-shrink: 0;
}

.history-card-score {
    font-size: 1.05rem;
    font-weight: 800;
    color: #1d4ed8;
    min-width: 4.5rem;
    text-align: right;
}

.history-card-action {
    flex-shrink: 0;
}

/* ── Responsive: layar kecil jadi bersusun ──── */
@media (max-width: 640px) {
    .history-card {
        grid-template-columns: 1fr;
        gap: .75rem;
    }

    .history-card-title {
        white-space: normal;
    }

    .history-card-status {
        justify-content: space-between;
        width: 100%;
    }

    .history-card-score {
        text-align: left;
    }

    .history-card-action {
        width: 100%;
    }

    .history-card-action .button {
        display: block;
        width: 100%;
        text-align: center;
    }
}

    </style>
</head>
<body>
    @php
        $simpleLayout = request()->routeIs('home', 'login', 'register');
    @endphp

    @if($simpleLayout)
        <header class="topbar">
            <div class="container nav">
                <a class="brand" href="{{ route('home') }}">Smart Student <span>Advisor</span></a>
                
            </div>
        </header>

        <main class="container page simple-page">
            @if(session('status'))
                <div class="alert success">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>

        <footer class="footer">Smart Student Advisor &copy; {{ date('Y') }} Ani Shofiyyah Zazqia</footer>

    @else

    {{-- ── Mobile Topbar ──────────────────────────── --}}
    <div class="mobile-topbar">
        <a class="brand" href="{{ route('home') }}">Smart Student <span>Advisor</span></a>
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Buka menu">
            <i data-lucide="menu"></i>
        </button>
    </div>

    {{-- ── Overlay (mobile) ───────────────────────── --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('home') }}">Smart Student <span>Advisor</span></a>
            <p class="sidebar-subtitle">Bantu rencanakan pembelian tanpa mengorbankan kebutuhan utama.</p>

            <nav class="sidebar-nav">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                            href="{{ route('admin.dashboard') }}">
                            <i data-lucide="layout-dashboard"></i> Dashboard
                        </a>
                        <a class="side-link {{ request()->routeIs('admin.users') ? 'active' : '' }}"
                            href="{{ route('admin.users') }}">
                            <i data-lucide="users"></i> Manajemen User
                        </a>
                        <a class="side-link {{ request()->routeIs('admin.analyses*') ? 'active' : '' }}"
                            href="{{ route('admin.analyses') }}">
                            <i data-lucide="history"></i> Riwayat Analisis
                        </a>
                        <a class="side-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}"
                            href="{{ route('admin.statistics') }}">
                            <i data-lucide="bar-chart-3"></i> Statistik
                        </a>
                    @else
                        <a class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i data-lucide="layout-dashboard"></i> Beranda
                        </a>
                        <a class="side-link {{ request()->routeIs('analyses.create') ? 'active' : '' }}"
                            href="{{ route('analyses.create') }}">
                            <i data-lucide="calculator"></i> Pertimbangan Baru
                        </a>
                        <a class="side-link {{ request()->routeIs('history') ? 'active' : '' }}"
                            href="{{ route('history') }}">
                            <i data-lucide="clock"></i> Riwayat
                        </a>
                    @endif
                @else
                    <a class="side-link {{ request()->routeIs('home') ? 'active' : '' }}"
                        href="{{ route('home') }}">
                        <i data-lucide="home"></i> Beranda
                    </a>
                    <a class="side-link {{ request()->routeIs('login') ? 'active' : '' }}"
                        href="{{ route('login') }}">
                        <i data-lucide="log-in"></i> Masuk
                    </a>
                    <a class="side-link {{ request()->routeIs('register') ? 'active' : '' }}"
                        href="{{ route('register') }}">
                        <i data-lucide="user-plus"></i> Daftar
                    </a>
                @endauth
            </nav>

            <div class="sidebar-footer">
    @auth
        {{-- Tombol ini ke halaman /profile --}}
        <a href="{{ route('profile.edit') }}" class="profile-card {{ request()->routeIs('profile.edit') ? 'active' : '' }}" style="width:100%; text-align:left;">
            <div class="profile-info">
                <strong>{{ auth()->user()->name }}</strong>
                <span>{{ auth()->user()->isAdmin() ? 'Admin' : 'User' }}</span>
            </div>
            <small>Profil</small>
        </a>
    @else
        <p>Ani Shofiyyah Zazqia.</p>
    @endauth
</div>
        </aside>

        <div class="app-main">
            <main class="container page">
                @if(session('status'))
                    <div class="alert success">{{ session('status') }}</div>
                @endif
                @yield('content')
            </main>

            <footer class="footer">Smart Student Advisor &copy; {{ date('Y') }} - Ani Shofiyyah Zazqia.</footer>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         PROFILE MODAL (isi form via @include)
    ═══════════════════════════════════════════ --}}
    @auth
    <div class="modal-overlay" id="profileModalOverlay">
        <div class="modal-box">
            <button class="modal-close" id="closeProfileModal" aria-label="Tutup">
                <i data-lucide="x"></i>
            </button>
            <h2>Edit Profil</h2>
            @include('profile.form')
        </div>
    </div>
    @endauth

    @endif

    @stack('scripts')

    <script>
        lucide.createIcons();

        // ── Mobile sidebar toggle ──────────────────
        const hamburgerBtn   = document.getElementById('hamburgerBtn');
        const sidebar        = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function closeSidebar() {
            sidebar?.classList.remove('active');
            sidebarOverlay?.classList.remove('active');
        }

        hamburgerBtn?.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        });
        sidebarOverlay?.addEventListener('click', closeSidebar);

        // ── Profile modal toggle ───────────────────
        const closeProfileBtn = document.getElementById('closeProfileModal');
        const profileOverlay = document.getElementById('profileModalOverlay');

        @if($errors->has('name') || $errors->has('email') || $errors->has('password'))
            profileOverlay?.classList.add('active');
        @endif

        closeProfileBtn?.addEventListener('click', () => {
            profileOverlay.classList.remove('active');
        });
        profileOverlay?.addEventListener('click', (e) => {
            if (e.target === profileOverlay) profileOverlay.classList.remove('active');
        });
    </script>
</body>
</html>