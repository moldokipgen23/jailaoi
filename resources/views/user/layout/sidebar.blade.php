@php
    $user = auth()->guard('user')->user();
    $artistName = $user->full_name ?? $user->user_name ?? 'Artist';
    $artistInitial = strtoupper(substr($artistName, 0, 1));
@endphp

<div class="sidebar">
    {{-- Brand --}}
    <div class="side-head">
        <a href="{{ route('user.earnings.index') }}" class="side-logo">
            <span class="side-logo-icon">🎵</span>
            <div>
                <h3>{{ App_Name() }}</h3>
                <small>ARTIST PORTAL</small>
            </div>
        </a>
        <button class="btn side-toggle">
            <span></span><span></span><span></span>
        </button>
    </div>

    {{-- Artist mini profile --}}
    <div class="side-artist-card">
        <div class="side-artist-avatar">{{ $artistInitial }}</div>
        <div class="side-artist-info">
            <div class="side-artist-name">{{ $artistName }}</div>
            <div class="side-artist-badge">
                <span class="dot"></span> Artist
            </div>
        </div>
    </div>

    <ul class="side-menu">

        {{-- Overview --}}
        <li class="side-section-label">Overview</li>

        <li class="side_line {{ request()->routeIs('user.dashboard*') || request()->routeIs('user.earnings*') ? 'active' : '' }}">
            <a href="{{ route('user.earnings.index') }}">
                <i class="fa-solid fa-chart-line menu-icon"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- Content --}}
        <li class="side-section-label">My Content</li>

        <li class="side_line {{ request()->routeIs('user.music*') ? 'active' : '' }}">
            <a href="{{ route('user.music.index') }}">
                <i class="fa-solid fa-music menu-icon"></i>
                <span>Music</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('user.playlist*') ? 'active' : '' }}">
            <a href="{{ route('user.playlist.index') }}">
                <i class="fa-solid fa-list menu-icon"></i>
                <span>Playlists</span>
            </a>
        </li>

        {{-- Monetization --}}
        <li class="side-section-label">Monetization</li>

        <li class="side_line {{ request()->routeIs('user.monetization*') ? 'active' : '' }}">
            <a href="{{ route('user.monetization.index') }}">
                <i class="fa-solid fa-rocket menu-icon"></i>
                <span>Apply to Monetize</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('user.kyc*') ? 'active' : '' }}">
            <a href="{{ route('user.kyc.index') }}">
                <i class="fa-solid fa-shield-halved menu-icon"></i>
                <span>KYC Verification</span>
            </a>
        </li>

        {{-- Account --}}
        <li class="side-section-label">Account</li>

        <li class="side_line {{ request()->routeIs('user.profile*') ? 'active' : '' }}">
            <a href="{{ route('user.profile.index') }}">
                <i class="fa-solid fa-user menu-icon"></i>
                <span>Profile</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('user.password*') ? 'active' : '' }}">
            <a href="{{ route('user.password.index') }}">
                <i class="fa-solid fa-lock menu-icon"></i>
                <span>Change Password</span>
            </a>
        </li>

        <li class="side-logout">
            <a href="{{ route('user.logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-arrow-right-from-bracket menu-icon"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('user.logout') }}" method="GET" class="d-none">@csrf</form>
        </li>

    </ul>
</div>

<style>
/* ── Sidebar shell ─────────────────────────────────────────── */
.sidebar {
    width: 240px;
    min-height: 100vh;
    background: #0f0f1a;
    display: flex;
    flex-direction: column;
    padding: 0;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    overflow-y: auto;
    z-index: 100;
    border-right: 1px solid rgba(255,255,255,0.06);
}

/* ── Brand ─────────────────────────────────────────────────── */
.side-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 18px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.side-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none !important;
}
.side-logo-icon { font-size: 22px; }
.side-logo h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
}
.side-logo small {
    font-size: 9px;
    letter-spacing: 1.5px;
    color: #7c6fcd;
    font-weight: 600;
}
.side-toggle {
    display: none;
    flex-direction: column;
    gap: 4px;
    background: none;
    border: none;
    padding: 4px;
}
.side-toggle span {
    display: block;
    width: 20px; height: 2px;
    background: #aaa;
    border-radius: 2px;
}

/* ── Artist card ───────────────────────────────────────────── */
.side-artist-card {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 16px 14px;
    padding: 12px 14px;
    background: rgba(124,111,205,0.12);
    border: 1px solid rgba(124,111,205,0.25);
    border-radius: 12px;
}
.side-artist-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c6fcd, #a855f7);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 700; color: #fff;
    flex-shrink: 0;
}
.side-artist-name {
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 130px;
}
.side-artist-badge {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    color: #7c6fcd;
    font-weight: 500;
    margin-top: 2px;
}
.side-artist-badge .dot {
    width: 6px; height: 6px;
    background: #22c55e;
    border-radius: 50%;
}

/* ── Menu ──────────────────────────────────────────────────── */
.side-menu {
    list-style: none;
    padding: 4px 10px 24px;
    margin: 0;
    flex: 1;
}
.side-section-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: #4a4a6a;
    padding: 18px 8px 6px;
}
.side_line a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 9px 12px;
    border-radius: 10px;
    text-decoration: none !important;
    color: #9191b4;
    font-size: 13.5px;
    font-weight: 500;
    transition: all 0.18s ease;
    margin-bottom: 2px;
}
.side_line a:hover {
    background: rgba(255,255,255,0.06);
    color: #fff;
}
.side_line.active a {
    background: rgba(124,111,205,0.18);
    color: #c4b8ff;
    font-weight: 600;
}
.side_line.active a .menu-icon {
    color: #a78bfa;
}
.menu-icon {
    font-size: 14px;
    width: 18px;
    text-align: center;
    color: #4a4a6a;
    flex-shrink: 0;
    transition: color 0.18s;
}
.side_line a:hover .menu-icon {
    color: #a78bfa;
}

/* ── Logout ────────────────────────────────────────────────── */
.side-logout {
    margin-top: 8px;
    border-top: 1px solid rgba(255,255,255,0.06);
    padding-top: 10px;
}
.side-logout a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 9px 12px;
    border-radius: 10px;
    text-decoration: none !important;
    color: #e05252;
    font-size: 13.5px;
    font-weight: 500;
    transition: all 0.18s;
}
.side-logout a:hover {
    background: rgba(224,82,82,0.1);
    color: #f87171;
}
.side-logout .menu-icon { color: #e05252; }

/* ── Mobile toggle ─────────────────────────────────────────── */
@media (max-width: 768px) {
    .side-toggle { display: flex; }
}
</style>
