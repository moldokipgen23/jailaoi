@php
    $user = auth()->guard('user')->user();
    $artistName = $user->full_name ?? $user->user_name ?? 'Artist';
    $artistInitial = strtoupper(substr($artistName, 0, 1));
@endphp

<style>
/* === JailaOi Artist Sidebar — overrides style.css === */
.sidebar {
    background: #1a1a2e !important;
    width: 250px !important;
    padding: 0 !important;
    border-right: 1px solid rgba(255,255,255,0.07) !important;
}
.sidebar .side-head {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 20px 18px 16px !important;
    border-bottom: 1px solid rgba(255,255,255,0.07) !important;
    background: #1a1a2e !important;
    margin: 0 !important;
}
.side-logo {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    text-decoration: none !important;
}
.side-logo h3 {
    color: #fff !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    margin: 0 !important;
    line-height: 1.2 !important;
}
.side-logo small {
    font-size: 9px !important;
    letter-spacing: 1.5px !important;
    color: #7c6fcd !important;
    font-weight: 600 !important;
    margin-top: 0 !important;
}
.side-artist-card {
    display: flex;
    align-items: center;
    gap: 11px;
    margin: 14px 12px;
    padding: 11px 13px;
    background: rgba(124,111,205,0.13);
    border: 1px solid rgba(124,111,205,0.28);
    border-radius: 12px;
}
.side-artist-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c6fcd, #a855f7);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; font-weight: 700; color: #fff;
    flex-shrink: 0;
}
.side-artist-name {
    font-size: 13px; font-weight: 600; color: #fff;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    max-width: 150px;
}
.side-artist-role {
    display: flex; align-items: center; gap: 5px;
    font-size: 11px; color: #7c6fcd; font-weight: 500;
    margin-top: 2px;
}
.side-artist-role .dot {
    width: 6px; height: 6px; background: #22c55e; border-radius: 50%;
}
/* Menu */
.sidebar .side-menu {
    padding: 4px 10px 24px !important;
    margin: 0 !important;
    list-style: none !important;
}
.side-section-label {
    font-size: 12px !important;
    font-weight: 800 !important;
    letter-spacing: 1.2px !important;
    text-transform: uppercase !important;
    color: rgba(255,255,255,0.7) !important;
    padding: 16px 8px 5px !important;
    display: block !important;
}
.sidebar .side-menu .side_line a,
.sidebar .side-menu .side-logout a {
    display: flex !important;
    align-items: center !important;
    gap: 11px !important;
    padding: 9px 12px !important;
    border-radius: 10px !important;
    text-decoration: none !important;
    color: #8888aa !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    background: transparent !important;
    margin-bottom: 2px !important;
    transition: all 0.18s ease !important;
    white-space: nowrap !important;
}
.sidebar .side-menu .side_line a:hover {
    background: rgba(255,255,255,0.06) !important;
    color: #fff !important;
}
.sidebar .side-menu .side_line a:hover .menu-icon,
.sidebar .side-menu .side_line a:hover i {
    color: #a78bfa !important;
}
.sidebar .side-menu .side_line.active a {
    background: rgba(124,111,205,0.2) !important;
    color: #c4b8ff !important;
    font-weight: 600 !important;
}
.sidebar .side-menu .side_line.active a .menu-icon,
.sidebar .side-menu .side_line.active a i {
    color: #a78bfa !important;
}
/* Remove active indicator line from old CSS */
.sidebar .side-menu .side_line.active a:after { content: none !important; }
/* Icons */
.sidebar .side-menu a .menu-icon,
.sidebar .side-menu a i {
    font-size: 14px !important;
    width: 18px !important;
    text-align: center !important;
    color: #3d3d5c !important;
    flex-shrink: 0 !important;
    transition: color 0.18s !important;
    margin-right: 0 !important;
}
/* Logout */
.side-logout {
    border-top: 1px solid rgba(255,255,255,0.07);
    padding-top: 8px;
    margin-top: 8px;
}
.sidebar .side-menu .side-logout a {
    color: #e05252 !important;
}
.sidebar .side-menu .side-logout a:hover {
    background: rgba(224,82,82,0.1) !important;
    color: #f87171 !important;
}
.sidebar .side-menu .side-logout a i {
    color: #e05252 !important;
}
/* Scrollbar */
.sidebar::-webkit-scrollbar { width: 4px; }
.sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
</style>

<div class="sidebar">
    {{-- Brand --}}
    <div class="side-head">
        <a href="{{ route('user.earnings.index') }}" class="side-logo">
            <div>
                <h3>{{ App_Name() }}</h3>
                <small>ARTIST PORTAL</small>
            </div>
        </a>
        <button class="btn side-toggle p-0 border-0" style="background:none">
            <span style="display:block;width:18px;height:2px;background:#555;margin:3px 0;border-radius:2px"></span>
            <span style="display:block;width:18px;height:2px;background:#555;margin:3px 0;border-radius:2px"></span>
            <span style="display:block;width:18px;height:2px;background:#555;margin:3px 0;border-radius:2px"></span>
        </button>
    </div>

    {{-- Artist card --}}
    <div class="side-artist-card">
        <div class="side-artist-avatar">{{ $artistInitial }}</div>
        <div style="overflow:hidden">
            <div class="side-artist-name">{{ $artistName }}</div>
            <div class="side-artist-role"><span class="dot"></span>Artist</div>
        </div>
    </div>

    <ul class="side-menu">

        <li><span class="side-section-label">Overview</span></li>
        <li class="side_line {{ request()->routeIs('user.earnings*') ? 'active' : '' }}">
            <a href="{{ route('user.earnings.index') }}">
                <i class="fa-solid fa-wallet menu-icon"></i><span>Earnings & Wallet</span>
            </a>
        </li>

        <li><span class="side-section-label">My Content</span></li>
        <li class="side_line {{ request()->routeIs('user.music*') ? 'active' : '' }}">
            <a href="{{ route('user.music.index') }}">
                <i class="fa-solid fa-music menu-icon"></i><span>Music</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('user.playlist*') ? 'active' : '' }}">
            <a href="{{ route('user.playlist.index') }}">
                <i class="fa-solid fa-list menu-icon"></i><span>Playlists</span>
            </a>
        </li>

        <li><span class="side-section-label">Monetization</span></li>
        <li class="side_line {{ request()->routeIs('user.earnings*') ? 'active' : '' }}">
            <a href="{{ route('user.earnings.index') }}">
                <i class="fa-solid fa-coins menu-icon"></i><span>Earnings & Wallet</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('user.monetization*') ? 'active' : '' }}">
            <a href="{{ route('user.monetization.index') }}">
                <i class="fa-solid fa-rocket menu-icon"></i><span>Apply to Monetize</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('user.kyc*') ? 'active' : '' }}">
            <a href="{{ route('user.kyc.index') }}">
                <i class="fa-solid fa-shield-halved menu-icon"></i><span>KYC Verification</span>
            </a>
        </li>

        <li><span class="side-section-label">Account</span></li>
        <li class="side_line {{ request()->routeIs('user.profile*') ? 'active' : '' }}">
            <a href="{{ route('user.profile.index') }}">
                <i class="fa-solid fa-user menu-icon"></i><span>Profile</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('user.password*') ? 'active' : '' }}">
            <a href="{{ route('user.password.index') }}">
                <i class="fa-solid fa-lock menu-icon"></i><span>Change Password</span>
            </a>
        </li>

        <li class="side-logout">
            <a href="{{ route('user.logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-arrow-right-from-bracket menu-icon"></i><span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('user.logout') }}" method="GET" class="d-none">@csrf</form>
        </li>

    </ul>
</div>
