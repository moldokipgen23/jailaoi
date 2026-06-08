@extends('user.layout.page-app')
@section('page_title', __('label.earnings'))
@section('tab_title', __('label.earnings'))

@section('content')
    @include('user.layout.sidebar')

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">💰 Earnings Dashboard</h1>

            @if (!$artist)
                <div class="alert alert-warning">
                    {{ __('label.no_artist_profile_linked') }}
                </div>
            @endif

            {{-- ========== HOW YOU EARN INFO BOX ========== --}}
            <div class="card mb-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border: 1px solid #6c63ff33;">
                <div class="card-body">
                    <h5 class="mb-3" style="color:#a78bfa; font-weight:700;">
                        <i class="fa-solid fa-circle-info me-2"></i> How You Earn on JailaOi
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-start">
                                <div style="width:36px;height:36px;border-radius:50%;background:#6c63ff22;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                                    <i class="fa-solid fa-play" style="color:#a78bfa;font-size:14px;"></i>
                                </div>
                                <div>
                                    <div style="color:#fff;font-weight:600;font-size:14px;">Per Stream Earnings</div>
                                    <div style="color:#aaa;font-size:13px;">You earn <strong style="color:#a78bfa;">{{ $stats['currency'] }} {{ number_format($stats['rate'], 4) }}</strong> for every unique play of your tracks.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-start">
                                <div style="width:36px;height:36px;border-radius:50%;background:#06b6d422;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                                    <i class="fa-solid fa-users" style="color:#22d3ee;font-size:14px;"></i>
                                </div>
                                <div>
                                    <div style="color:#fff;font-weight:600;font-size:14px;">Unique Listener Counts</div>
                                    <div style="color:#aaa;font-size:13px;">Each listener counts once per song — grow your fan base to increase earnings.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-start">
                                <div style="width:36px;height:36px;border-radius:50%;background:#10b98122;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                                    <i class="fa-solid fa-wallet" style="color:#34d399;font-size:14px;"></i>
                                </div>
                                <div>
                                    <div style="color:#fff;font-weight:600;font-size:14px;">Minimum Payout</div>
                                    <div style="color:#aaa;font-size:13px;">Request a withdrawal once your balance reaches <strong style="color:#34d399;">{{ $stats['currency'] }} {{ number_format($stats['min_withdrawal'], 2) }}</strong>.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr style="border-color:#ffffff15;margin:4px 0 12px;">
                    <div style="color:#aaa;font-size:12px;">
                        <i class="fa-solid fa-shield-halved me-1" style="color:#a78bfa;"></i>
                        Earnings are credited automatically. Withdrawal requests are reviewed within 3–5 business days.
                        Payouts are sent via your chosen payment method after admin approval.
                    </div>
                </div>
            </div>

            {{-- ========== STAT CARDS ========== --}}
            <div class="row stat-card-row mb-4">
                <div class="col-xl-3 col-md-6 col-12 mb-3">
                    <div class="stat-card" style="border-left:4px solid #6c63ff;">
                        <div class="stat-icon primary"><i class="fa-solid fa-play"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Total Plays</div>
                            <div class="stat-value">{{ number_format($stats['total_plays']) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12 mb-3">
                    <div class="stat-card" style="border-left:4px solid #f59e0b;">
                        <div class="stat-icon" style="background:#f59e0b22;color:#f59e0b;"><i class="fa-solid fa-coins"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Total Earned</div>
                            <div class="stat-value">{{ $stats['currency'] }} {{ number_format($stats['total_earned'], 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12 mb-3">
                    <div class="stat-card" style="border-left:4px solid #ef4444;">
                        <div class="stat-icon" style="background:#ef444422;color:#ef4444;"><i class="fa-solid fa-clock"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Pending Withdrawals</div>
                            <div class="stat-value">{{ $stats['currency'] }} {{ number_format($stats['pending'], 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12 mb-3">
                    <div class="stat-card" style="border-left:4px solid #10b981;">
                        <div class="stat-icon" style="background:#10b98122;color:#10b981;"><i class="fa-solid fa-wallet"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">Available Balance</div>
                            <div class="stat-value">{{ $stats['currency'] }} {{ number_format($stats['available'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== BALANCE + WITHDRAWAL ========== --}}
            <div class="row mb-4">
                {{-- Balance Card --}}
                <div class="col-md-5 mb-3">
                    <div class="card h-100" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border:1px solid #10b98144;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <div style="color:#aaa;font-size:13px;margin-bottom:4px;">Available to Withdraw</div>
                                <div style="color:#34d399;font-size:38px;font-weight:800;line-height:1.1;">
                                    {{ $stats['currency'] }} {{ number_format($stats['available'], 2) }}
                                </div>
                                <div style="color:#6b7280;font-size:12px;margin-top:4px;">
                                    Total paid out: <span style="color:#9ca3af;">{{ $stats['currency'] }} {{ number_format($stats['paid_out'], 2) }}</span>
                                </div>
                            </div>

                            {{-- Progress toward minimum withdrawal --}}
                            @php
                                $min = $stats['min_withdrawal'];
                                $avail = $stats['available'];
                                $pct = $min > 0 ? min(100, round(($avail / $min) * 100)) : 100;
                                $reachedMin = $avail >= $min;
                            @endphp
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span style="color:#9ca3af;font-size:12px;">Progress to minimum payout</span>
                                    <span style="color:{{ $reachedMin ? '#34d399' : '#a78bfa' }};font-size:12px;font-weight:600;">{{ $pct }}%</span>
                                </div>
                                <div style="background:#1f2937;border-radius:999px;height:8px;overflow:hidden;">
                                    <div style="height:100%;border-radius:999px;width:{{ $pct }}%;background:{{ $reachedMin ? 'linear-gradient(90deg,#10b981,#34d399)' : 'linear-gradient(90deg,#6c63ff,#a78bfa)' }};transition:width 0.5s;"></div>
                                </div>
                                <div style="color:#6b7280;font-size:11px;margin-top:4px;">
                                    @if ($reachedMin)
                                        <i class="fa-solid fa-check-circle" style="color:#34d399;"></i> You can request a withdrawal!
                                    @else
                                        {{ $stats['currency'] }} {{ number_format(max(0, $min - $avail), 2) }} more needed
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Withdrawal Form --}}
                <div class="col-md-7 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="mb-3" style="font-weight:700;">Request Withdrawal</h5>
                            @if (!$artist)
                                <div class="alert alert-warning small">No artist profile found.</div>
                            @elseif ($eligibility && !$eligibility['eligible'])
                                {{-- JAILAOI: Eligibility gate --}}
                                <div class="alert alert-warning">
                                    <i class="fa-solid fa-lock me-2"></i>
                                    <strong>Payout Locked</strong>
                                    <p class="mb-2 mt-2">Complete the following requirements to unlock withdrawals:</p>
                                    <ul style="list-style:none;padding-left:0;margin-bottom:0;">
                                        @php
                                            $kycApproved = $eligibility['kyc_approved'];
                                        @endphp
                                        <li style="margin-bottom:6px;">
                                            {!! $kycApproved
                                                ? '<span style="color:#10b981;">✅</span> KYC Verified'
                                                : '<span style="color:#ef4444;">❌</span> KYC not verified — <a href="' . route('user.kyc.index') . '" style="color:#E01E75;font-weight:600;">Verify Now &rarr;</a>'
                                            !!}
                                        </li>
                                        @php
                                            $minPlays = (int) (\App\Models\General_Setting::where('key', 'min_streams_for_payout')->value('value') ?? 50);
                                            $totalPlays = $stats['total_plays'];
                                        @endphp
                                        <li style="margin-bottom:6px;">
                                            {!! $totalPlays >= $minPlays
                                                ? '<span style="color:#10b981;">✅</span> ' . number_format($totalPlays) . ' plays (min ' . number_format($minPlays) . ')'
                                                : '<span style="color:#ef4444;">❌</span> ' . number_format($totalPlays) . ' plays (need ' . number_format($minPlays) . ')'
                                            !!}
                                        </li>
                                        @php
                                            $minEarn = (float) (\App\Models\General_Setting::where('key', 'min_earnings_for_payout')->value('value') ?? 5);
                                            $totalEarned = $stats['total_earned'];
                                        @endphp
                                        <li style="margin-bottom:6px;">
                                            {!! $totalEarned >= $minEarn
                                                ? '<span style="color:#10b981;">✅</span> ' . $stats['currency'] . ' ' . number_format($totalEarned, 2) . ' earned (min ' . $stats['currency'] . ' ' . number_format($minEarn, 2) . ')'
                                                : '<span style="color:#ef4444;">❌</span> ' . $stats['currency'] . ' ' . number_format($totalEarned, 2) . ' earned (need ' . $stats['currency'] . ' ' . number_format($minEarn, 2) . ')'
                                            !!}
                                        </li>
                                        @php
                                            $minDays = (int) (\App\Models\General_Setting::where('key', 'min_account_days_for_payout')->value('value') ?? 30);
                                            $accountAge = $user->created_at ? $user->created_at->diffInDays(now()) : 0;
                                        @endphp
                                        <li style="margin-bottom:6px;">
                                            {!! $accountAge >= $minDays
                                                ? '<span style="color:#10b981;">✅</span> Account ' . $accountAge . ' days old (min ' . $minDays . ')'
                                                : '<span style="color:#ef4444;">❌</span> Account ' . $accountAge . ' days old (need ' . $minDays . ')'
                                            !!}
                                        </li>
                                    </ul>
                                </div>
                            @elseif ($stats['available'] < $stats['min_withdrawal'])
                                <div class="alert alert-info small">
                                    <i class="fa-solid fa-lock me-1"></i>
                                    Minimum withdrawal is <strong>{{ $stats['currency'] }} {{ number_format($stats['min_withdrawal'], 2) }}</strong>.
                                    You need <strong>{{ $stats['currency'] }} {{ number_format(max(0, $stats['min_withdrawal'] - $stats['available']), 2) }}</strong> more in your balance.
                                </div>
                            @else
                                <form id="withdrawalForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small fw-semibold">Amount ({{ $stats['currency'] }})</label>
                                            <input type="number" step="0.01"
                                                   min="{{ $stats['min_withdrawal'] }}"
                                                   max="{{ $stats['available'] }}"
                                                   name="amount" class="form-control"
                                                   placeholder="{{ number_format($stats['available'], 2) }}" required>
                                            <div class="form-text">Max: {{ $stats['currency'] }} {{ number_format($stats['available'], 2) }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small fw-semibold">Payment Method</label>
                                            <select name="payment_method" class="form-control" required>
                                                <option value="paypal">PayPal</option>
                                                <option value="bank">Bank Transfer</option>
                                                <option value="mobile_money">Mobile Money</option>
                                            </select>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label small fw-semibold">Payment Details</label>
                                            <input type="text" name="payment_details" class="form-control"
                                                   placeholder="PayPal email / bank account / mobile number" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fa-solid fa-paper-plane me-2"></i> Submit Withdrawal Request
                                    </button>
                                    <div id="withdrawalMsg" class="mt-2"></div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== MONTHLY TREND ========== --}}
            @if (!empty($monthlyTrend))
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4" style="font-weight:700;">
                        <i class="fa-solid fa-chart-line me-2" style="color:#6c63ff;"></i> Monthly Earnings (Last 6 Months)
                    </h5>
                    @php
                        $maxEarned = max(array_column($monthlyTrend, 'earned')) ?: 1;
                    @endphp
                    <div class="row align-items-end" style="min-height:120px;">
                        @foreach ($monthlyTrend as $month)
                            @php $barH = max(6, round(($month['earned'] / $maxEarned) * 100)); @endphp
                            <div class="col text-center px-1">
                                <div style="font-size:11px;color:#9ca3af;margin-bottom:4px;">
                                    {{ $stats['currency'] }} {{ number_format($month['earned'], 2) }}
                                </div>
                                <div style="background:linear-gradient(180deg,#6c63ff,#a78bfa);border-radius:4px 4px 0 0;height:{{ $barH }}px;width:100%;min-height:6px;transition:height 0.3s;"></div>
                                <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ $month['label'] }}</div>
                                <div style="font-size:10px;color:#4b5563;">{{ number_format($month['plays']) }} plays</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- ========== PER-SONG BREAKDOWN ========== --}}
            @if (!empty($songBreakdown))
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3" style="font-weight:700;">
                        <i class="fa-solid fa-music me-2" style="color:#f59e0b;"></i> Top Earning Tracks
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover" style="font-size:14px;">
                            <thead>
                                <tr style="background:#f9faff;">
                                    <th>#</th>
                                    <th>Track Title</th>
                                    <th class="text-center">Plays</th>
                                    <th class="text-right">Earned ({{ $stats['currency'] }})</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($songBreakdown as $i => $song)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <i class="fa-solid fa-music me-2" style="color:#6c63ff;font-size:12px;"></i>
                                            {{ $song['title'] }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge" style="background:#6c63ff22;color:#6c63ff;padding:4px 10px;border-radius:999px;">
                                                {{ number_format($song['play_count']) }}
                                            </span>
                                        </td>
                                        <td class="text-right" style="font-weight:600;color:#10b981;">
                                            {{ number_format($song['earned'], 4) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- ========== WITHDRAWAL HISTORY ========== --}}
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3" style="font-weight:700;">
                        <i class="fa-solid fa-clock-rotate-left me-2" style="color:#3b82f6;"></i> Withdrawal History
                    </h5>
                    <div class="table-responsive">
                        <table class="table" style="font-size:14px;">
                            <thead>
                                <tr style="background:#f9faff;">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($withdrawals as $w)
                                    <tr>
                                        <td>{{ $w->id }}</td>
                                        <td>{{ $w->created_at?->format('Y-m-d') }}</td>
                                        <td style="font-weight:600;">{{ $stats['currency'] }} {{ number_format($w->amount, 2) }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $w->payment_method)) }}</td>
                                        <td>
                                            @php
                                                $color = match($w->status) {
                                                    'paid'     => '#10b981',
                                                    'approved' => '#3b82f6',
                                                    'rejected' => '#ef4444',
                                                    default    => '#f59e0b',
                                                };
                                            @endphp
                                            <span style="background:{{ $color }}22;color:{{ $color }};padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600;">
                                                {{ ucfirst($w->status) }}
                                            </span>
                                        </td>
                                        <td style="color:#6b7280;font-size:12px;">{{ $w->admin_note ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No withdrawal requests yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>{{-- body-content --}}
    </div>{{-- right-content --}}
@endsection

@section('script')
<script>
document.getElementById('withdrawalForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const form   = e.target;
    const btn    = form.querySelector('[type=submit]');
    const msg    = document.getElementById('withdrawalMsg');
    msg.innerHTML = '';
    btn.disabled  = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Submitting…';
    try {
        const res  = await fetch('{{ route('earnings.withdraw') }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body:   new FormData(form),
        });
        const json = await res.json();
        if (json.status === 200) {
            msg.innerHTML = '<div class="alert alert-success mt-2"><i class="fa-solid fa-check-circle me-2"></i>' + json.success + '</div>';
            setTimeout(() => location.reload(), 1500);
        } else {
            const err = Array.isArray(json.errors) ? json.errors.join('<br>') : json.errors;
            msg.innerHTML = '<div class="alert alert-danger mt-2">' + err + '</div>';
            btn.disabled  = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i> Submit Withdrawal Request';
        }
    } catch (err) {
        msg.innerHTML = '<div class="alert alert-danger mt-2">' + err.message + '</div>';
        btn.disabled  = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i> Submit Withdrawal Request';
    }
});
</script>
@endsection
