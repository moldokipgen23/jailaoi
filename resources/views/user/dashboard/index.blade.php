@extends('user.layout.page-app')
@section('page_title', 'Dashboard')
@section('tab_title', 'Dashboard')

@section('content')
    @include('user.layout.sidebar')

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">Dashboard</h1>

            {{-- ── Top stat cards ── --}}
            <div class="row stat-card-row mb-3">
                <div class="col-xl-3 col-md-6 col-12 mb-3">
                    <div class="stat-card" style="border-left:4px solid #6c63ff;">
                        <div class="stat-icon" style="background:#6c63ff22;color:#6c63ff;"><i class="fa-solid fa-play"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Total Plays</div>
                            <div class="stat-value">{{ number_format($totalPlays) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12 mb-3">
                    <div class="stat-card" style="border-left:4px solid #10b981;">
                        <div class="stat-icon" style="background:#10b98122;color:#10b981;"><i class="fa-solid fa-users"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Followers</div>
                            <div class="stat-value">{{ number_format($totalFollowers) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12 mb-3">
                    <div class="stat-card" style="border-left:4px solid #f59e0b;">
                        <div class="stat-icon" style="background:#f59e0b22;color:#f59e0b;"><i class="fa-solid fa-headphones"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Monthly Listeners</div>
                            <div class="stat-value">{{ number_format($monthlyListeners) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12 mb-3">
                    <div class="stat-card" style="border-left:4px solid #3b82f6;">
                        <div class="stat-icon" style="background:#3b82f622;color:#3b82f6;"><i class="fa-solid fa-music"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Total Tracks</div>
                            <div class="stat-value">{{ number_format($totalContent) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Earnings + Status row ── --}}
            <div class="row mb-3">

                {{-- Wallet card --}}
                <div class="col-md-4 mb-3">
                    <div class="card h-100" style="background:linear-gradient(135deg,#0f172a,#1e293b);border:1px solid #10b98144;">
                        <div class="card-body">
                            <div style="color:#9ca3af;font-size:12px;margin-bottom:4px;">Wallet Balance</div>
                            <div style="color:#34d399;font-size:32px;font-weight:800;line-height:1.1;">INR {{ number_format($walletBalance, 2) }}</div>
                            <div style="color:#6b7280;font-size:12px;margin-top:6px;">
                                Total earned: <span style="color:#9ca3af;">INR {{ number_format($totalEarned, 2) }}</span>
                            </div>
                            <div style="color:#6b7280;font-size:12px;">
                                Paid out: <span style="color:#9ca3af;">INR {{ number_format($paidOut, 2) }}</span>
                            </div>
                            @if ($pendingPlays > 0)
                            <div style="color:#f59e0b;font-size:12px;margin-top:6px;">
                                <i class="fa-solid fa-clock me-1"></i> {{ number_format($pendingPlays) }} plays pending settlement
                            </div>
                            @endif
                            <a href="{{ route('user.earnings.index') }}" style="display:inline-block;margin-top:14px;padding:7px 16px;background:#10b981;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                                View Wallet →
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Content breakdown --}}
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 style="font-weight:700;margin-bottom:16px;"><i class="fa-solid fa-layer-group me-2" style="color:#6c63ff;"></i>Content Breakdown</h6>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="color:#6b7280;font-size:13px;"><i class="fa-solid fa-music me-2" style="color:#6c63ff;"></i>Songs</span>
                                <span style="font-weight:700;">{{ $songCount }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="color:#6b7280;font-size:13px;"><i class="fa-solid fa-compact-disc me-2" style="color:#10b981;"></i>Albums / Music</span>
                                <span style="font-weight:700;">{{ $musicCount }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="color:#6b7280;font-size:13px;"><i class="fa-solid fa-podcast me-2" style="color:#f59e0b;"></i>Podcasts</span>
                                <span style="font-weight:700;">{{ $podcastCount }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status card --}}
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 style="font-weight:700;margin-bottom:16px;"><i class="fa-solid fa-shield-halved me-2" style="color:#3b82f6;"></i>Account Status</h6>
                            @php
                                $kycColor = match($kycStatus) { 'approved' => '#10b981', 'submitted','under_review' => '#f59e0b', 'rejected' => '#ef4444', default => '#6b7280' };
                                $kycLabel = match($kycStatus) { 'approved' => 'Verified', 'submitted' => 'Submitted', 'under_review' => 'Under Review', 'rejected' => 'Rejected', default => 'Not Started' };
                                $monColor = match($monetizationStatus) { 'approved' => '#10b981', 'pending' => '#f59e0b', 'rejected' => '#ef4444', default => '#6b7280' };
                                $monLabel = match($monetizationStatus) { 'approved' => 'Active', 'pending' => 'Under Review', 'rejected' => 'Rejected', default => 'Not Applied' };
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="color:#6b7280;font-size:13px;"><i class="fa-solid fa-id-card me-2"></i>KYC</span>
                                <span style="background:{{ $kycColor }}22;color:{{ $kycColor }};padding:3px 12px;border-radius:999px;font-size:12px;font-weight:600;">{{ $kycLabel }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="color:#6b7280;font-size:13px;"><i class="fa-solid fa-monetization-on me-2"></i>Monetization</span>
                                <span style="background:{{ $monColor }}22;color:{{ $monColor }};padding:3px 12px;border-radius:999px;font-size:12px;font-weight:600;">{{ $monLabel }}</span>
                            </div>
                            @if ($pendingWithdrawal > 0)
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="color:#6b7280;font-size:13px;"><i class="fa-solid fa-clock me-2"></i>Pending Payout</span>
                                <span style="font-weight:700;color:#f59e0b;">INR {{ number_format($pendingWithdrawal, 2) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Monthly plays chart ── --}}
            @if (!empty($monthlyTrend))
            <div class="card mb-3">
                <div class="card-body">
                    <h6 style="font-weight:700;margin-bottom:16px;"><i class="fa-solid fa-chart-bar me-2" style="color:#6c63ff;"></i>Monthly Plays (Last 6 Months)</h6>
                    @php $maxPlays = max(array_column($monthlyTrend, 'plays')) ?: 1; @endphp
                    <div class="row align-items-end" style="min-height:100px;">
                        @foreach ($monthlyTrend as $m)
                            @php $h = max(6, round(($m['plays'] / $maxPlays) * 100)); @endphp
                            <div class="col text-center px-1">
                                <div style="font-size:11px;color:#9ca3af;margin-bottom:4px;">{{ number_format($m['plays']) }}</div>
                                <div style="background:linear-gradient(180deg,#6c63ff,#a78bfa);border-radius:4px 4px 0 0;height:{{ $h }}px;width:100%;min-height:6px;"></div>
                                <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ $m['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- ── Top songs ── --}}
            @if ($topSongs->count())
            <div class="card mb-3">
                <div class="card-body">
                    <h6 style="font-weight:700;margin-bottom:16px;"><i class="fa-solid fa-trophy me-2" style="color:#f59e0b;"></i>Top Songs by Plays</h6>
                    @foreach ($topSongs as $i => $song)
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:24px;color:#6b7280;font-size:13px;font-weight:700;">{{ $i + 1 }}</div>
                        <div style="width:40px;height:40px;border-radius:8px;background:linear-gradient(135deg,#6c63ff,#a78bfa);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                            <i class="fa-solid fa-music" style="color:#fff;font-size:14px;"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:600;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $song->title }}</div>
                        </div>
                        <div style="display:flex;align-items:center;gap:4px;color:#6b7280;font-size:13px;flex-shrink:0;">
                            <i class="fa-solid fa-play" style="font-size:11px;"></i>
                            <span style="font-weight:700;">{{ number_format($song->total_play) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
@endsection
