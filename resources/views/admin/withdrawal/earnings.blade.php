@extends('admin.layout.page-app')
@section('page_title', 'Monetization Overview')
@section('tab_title', 'Monetization Overview')

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">💰 Monetization Overview</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Monetization</li>
                    </ol>
                </div>
            </div>

            {{-- ========== CURRENT RULES BANNER ========== --}}
            <div class="card mb-4" style="background:linear-gradient(135deg,#1a1a2e,#16213e);border:1px solid #6c63ff44;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div style="width:56px;height:56px;border-radius:50%;background:#6c63ff22;display:flex;align-items:center;justify-content:center;">
                                <i class="fa-solid fa-gear" style="color:#a78bfa;font-size:22px;"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div style="color:#a78bfa;font-weight:700;font-size:16px;margin-bottom:6px;">Active Monetization Rules</div>
                            <div class="row">
                                <div class="col-auto me-4">
                                    <span style="color:#6b7280;font-size:13px;">Per-stream rate:</span>
                                    <strong style="color:#fff;font-size:15px;margin-left:6px;">{{ $currency }} {{ number_format($rate, 4) }}</strong>
                                </div>
                                <div class="col-auto me-4">
                                    <span style="color:#6b7280;font-size:13px;">Currency:</span>
                                    <strong style="color:#fff;font-size:15px;margin-left:6px;">{{ $currency }}</strong>
                                </div>
                                <div class="col-auto">
                                    <span style="color:#6b7280;font-size:13px;">Min withdrawal:</span>
                                    <strong style="color:#fff;font-size:15px;margin-left:6px;">{{ $currency }} {{ number_format($minWithdrawal, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('setting.index') }}#payout-settings"
                               class="btn btn-sm"
                               style="background:#6c63ff;color:#fff;border:none;padding:8px 18px;border-radius:8px;">
                                <i class="fa-solid fa-pen me-2"></i> Edit Rules
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== PLATFORM STAT CARDS ========== --}}
            <div class="row mb-4">
                <div class="col-xl-2 col-md-4 col-6 mb-3">
                    <div class="stat-card" style="border-left:4px solid #6c63ff;">
                        <div class="stat-icon primary"><i class="fa-solid fa-play"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Total Plays</div>
                            <div class="stat-value">{{ number_format($totalPlays) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6 mb-3">
                    <div class="stat-card" style="border-left:4px solid #f59e0b;">
                        <div class="stat-icon" style="background:#f59e0b22;color:#f59e0b;"><i class="fa-solid fa-coins"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Total Earned</div>
                            <div class="stat-value" style="font-size:15px;">{{ $currency }} {{ number_format($totalEarned, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6 mb-3">
                    <div class="stat-card" style="border-left:4px solid #ef4444;">
                        <div class="stat-icon" style="background:#ef444422;color:#ef4444;"><i class="fa-solid fa-money-bill-wave"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Total Owed</div>
                            <div class="stat-value" style="font-size:15px;">{{ $currency }} {{ number_format($totalOwed, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6 mb-3">
                    <div class="stat-card" style="border-left:4px solid #f59e0b;">
                        <div class="stat-icon" style="background:#f59e0b22;color:#f59e0b;"><i class="fa-solid fa-clock"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Pending Requests</div>
                            <div class="stat-value">{{ number_format($pendingCount) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6 mb-3">
                    <div class="stat-card" style="border-left:4px solid #10b981;">
                        <div class="stat-icon" style="background:#10b98122;color:#10b981;"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Total Paid Out</div>
                            <div class="stat-value" style="font-size:15px;">{{ $currency }} {{ number_format($totalPaid, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6 mb-3">
                    <div class="stat-card" style="border-left:4px solid #3b82f6;">
                        <div class="stat-icon" style="background:#3b82f622;color:#3b82f6;"><i class="fa-solid fa-user-tie"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Earning Artists</div>
                            <div class="stat-value">{{ number_format($activeArtists) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== MONTHLY TREND ========== --}}
            @if (!empty($monthlyTrend))
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 style="font-weight:700;margin:0;">
                            <i class="fa-solid fa-chart-line me-2" style="color:#6c63ff;"></i> Monthly Platform Earnings
                        </h5>
                        <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-sm btn-outline-primary">
                            Manage Withdrawals <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    @php $maxE = max(array_column($monthlyTrend, 'earned')) ?: 1; @endphp
                    <div class="row align-items-end" style="min-height:130px;">
                        @foreach ($monthlyTrend as $m)
                            @php $h = max(6, round(($m['earned'] / $maxE) * 110)); @endphp
                            <div class="col text-center px-1">
                                <div style="font-size:11px;color:#9ca3af;margin-bottom:4px;">
                                    {{ $currency }} {{ number_format($m['earned'], 2) }}
                                </div>
                                <div style="background:linear-gradient(180deg,#6c63ff,#a78bfa);border-radius:4px 4px 0 0;height:{{ $h }}px;width:100%;min-height:6px;"></div>
                                <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ $m['label'] }}</div>
                                <div style="font-size:10px;color:#4b5563;">{{ number_format($m['plays']) }} plays</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- ========== PER-ARTIST EARNINGS TABLE ========== --}}
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3" style="font-weight:700;">
                        <i class="fa-solid fa-user-tie me-2" style="color:#f59e0b;"></i> Artist Earnings Breakdown
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" style="font-size:14px;">
                            <thead>
                                <tr style="background:#f9faff;">
                                    <th>#</th>
                                    <th>Artist</th>
                                    <th class="text-center">Plays</th>
                                    <th class="text-right">Total Earned</th>
                                    <th class="text-right">Paid Out</th>
                                    <th class="text-right">Pending</th>
                                    <th class="text-right">Available</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($artistStats as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td style="font-weight:600;">
                                            <i class="fa-solid fa-user-tie me-2" style="color:#6c63ff;font-size:12px;"></i>
                                            {{ $row['artist_name'] }}
                                        </td>
                                        <td class="text-center">
                                            <span style="background:#6c63ff22;color:#6c63ff;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600;">
                                                {{ number_format($row['play_count']) }}
                                            </span>
                                        </td>
                                        <td class="text-right" style="font-weight:600;">
                                            {{ $currency }} {{ number_format($row['total_earned'], 2) }}
                                        </td>
                                        <td class="text-right" style="color:#10b981;">
                                            {{ $currency }} {{ number_format($row['paid_out'], 2) }}
                                        </td>
                                        <td class="text-right" style="color:#f59e0b;">
                                            {{ $currency }} {{ number_format($row['pending'], 2) }}
                                        </td>
                                        <td class="text-right">
                                            @if ($row['available'] > 0)
                                                <strong style="color:#ef4444;">{{ $currency }} {{ number_format($row['available'], 2) }}</strong>
                                            @else
                                                <span style="color:#9ca3af;">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No artist earnings recorded yet. Earnings will appear here once artists' tracks are played in the app.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($artistStats->count() > 0)
                            <tfoot>
                                <tr style="background:#f9faff;font-weight:700;">
                                    <td colspan="2">Totals</td>
                                    <td class="text-center">{{ number_format($totalPlays) }}</td>
                                    <td class="text-right">{{ $currency }} {{ number_format($totalEarned, 2) }}</td>
                                    <td class="text-right" style="color:#10b981;">{{ $currency }} {{ number_format($totalPaid, 2) }}</td>
                                    <td class="text-right" style="color:#f59e0b;">{{ $currency }} {{ number_format($totalPending, 2) }}</td>
                                    <td class="text-right" style="color:#ef4444;">{{ $currency }} {{ number_format($totalOwed, 2) }}</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

        </div>{{-- body-content --}}
    </div>{{-- right-content --}}
@endsection
