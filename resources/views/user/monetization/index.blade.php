@extends('user.layout.page-app')
@section('page_title', 'Monetization')
@section('tab_title', 'Monetization')

@section('content')
    @include('user.layout.sidebar')

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">🚀 Monetization</h1>

            {{-- ========== INFO BOX ========== --}}
            <div class="card mb-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border: 1px solid #6c63ff33;">
                <div class="card-body">
                    <h5 class="mb-3" style="color:#a78bfa; font-weight:700;">
                        <i class="fa-solid fa-circle-info me-2"></i> How Monetization Works
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-start">
                                <div style="width:36px;height:36px;border-radius:50%;background:#6c63ff22;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                                    <i class="fa-solid fa-check-circle" style="color:#a78bfa;font-size:14px;"></i>
                                </div>
                                <div>
                                    <div style="color:#fff;font-weight:600;font-size:14px;">Step 1: Meet Requirements</div>
                                    <div style="color:#aaa;font-size:13px;">Build your audience — reach the minimum plays, followers, and tracks to qualify.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-start">
                                <div style="width:36px;height:36px;border-radius:50%;background:#06b6d422;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                                    <i class="fa-solid fa-file-pen" style="color:#22d3ee;font-size:14px;"></i>
                                </div>
                                <div>
                                    <div style="color:#fff;font-weight:600;font-size:14px;">Step 2: Apply</div>
                                    <div style="color:#aaa;font-size:13px;">Submit your application. Our team will review it within 3–5 business days.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-start">
                                <div style="width:36px;height:36px;border-radius:50%;background:#10b98122;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                                    <i class="fa-solid fa-rocket" style="color:#34d399;font-size:14px;"></i>
                                </div>
                                <div>
                                    <div style="color:#fff;font-weight:600;font-size:14px;">Step 3: Start Earning</div>
                                    <div style="color:#aaa;font-size:13px;">Once approved, complete KYC verification and start withdrawing your earnings.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== STAGE: NOT ELIGIBLE ========== --}}
            @if ($stage === 'not_eligible')
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-4" style="font-weight:700;">
                            <i class="fa-solid fa-list-check me-2" style="color:#f59e0b;"></i> Eligibility Progress
                        </h5>

                        @php
                            $passedCount = 0;
                            $totalChecks = count($eligibility['checks']);
                            foreach ($eligibility['checks'] as $c) {
                                if ($c['passed']) $passedCount++;
                            }
                            $pct = $totalChecks > 0 ? round(($passedCount / $totalChecks) * 100) : 0;
                        @endphp

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="color:#9ca3af;font-size:13px;">Overall Progress</span>
                                <span style="color:#a78bfa;font-size:13px;font-weight:600;">{{ $passedCount }}/{{ $totalChecks }} ({{ $pct }}%)</span>
                            </div>
                            <div style="background:#1f2937;border-radius:999px;height:10px;overflow:hidden;">
                                <div style="height:100%;border-radius:999px;width:{{ $pct }}%;background:linear-gradient(90deg,#6c63ff,#a78bfa);transition:width 0.5s;"></div>
                            </div>
                        </div>

                        <ul style="list-style:none;padding-left:0;">
                            @foreach ($eligibility['checks'] as $check)
                                <li style="margin-bottom:12px;padding:10px 14px;background:#f9faff;border-radius:8px;border-left:4px solid {{ $check['passed'] ? '#10b981' : '#ef4444' }};">
                                    {!! $check['passed']
                                        ? '<span style="color:#10b981;">✅</span>'
                                        : '<span style="color:#ef4444;">❌</span>'
                                    !!}
                                    <strong>{{ $check['label'] }}:</strong>
                                    {{ number_format($check['current']) }} / {{ number_format($check['required']) }}
                                    @if ($check['passed'])
                                        <span style="color:#10b981;">✓</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        <div class="alert alert-info py-2 mb-0">
                            <i class="fa-solid fa-lightbulb me-2"></i>
                            Keep uploading and growing your audience! Once you meet all requirements, you can apply for monetization.
                        </div>
                    </div>
                </div>

            {{-- ========== STAGE: ELIGIBLE ========== --}}
            @elseif ($stage === 'eligible')
                <div class="card mb-4" style="border:2px solid #10b981;">
                    <div class="card-body text-center py-4">
                        <div style="font-size:64px;color:#10b981;margin-bottom:12px;">
                            <i class="fa-solid fa-party-popper"></i>
                        </div>
                        <h4 style="color:#10b981;font-weight:700;">🎉 You're Eligible!</h4>
                        <p class="text-muted">You meet all the requirements for monetization. Apply now to start earning from your content.</p>

                        <ul style="list-style:none;padding-left:0;display:inline-block;text-align:left;margin:12px auto;">
                            @foreach ($eligibility['checks'] as $check)
                                <li style="color:#10b981;margin-bottom:4px;">
                                    ✅ {{ $check['label'] }}: {{ number_format($check['current']) }} / {{ number_format($check['required']) }}
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-3">
                            <button type="button" class="btn btn-success btn-lg" onclick="applyMonetization()">
                                <i class="fa-solid fa-paper-plane me-2"></i> Apply for Monetization
                            </button>
                        </div>
                        <div id="applyMsg" class="mt-3"></div>
                    </div>
                </div>

            {{-- ========== STAGE: APPLIED ========== --}}
            @elseif ($stage === 'applied')
                <div class="card mb-4">
                    <div class="card-body text-center py-5">
                        <div style="font-size:64px;color:#f59e0b;margin-bottom:16px;">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <h4>Application Under Review</h4>
                        <p class="text-muted">
                            Submitted on <strong>{{ $application->applied_at?->format('F j, Y') }}</strong>.
                            We'll notify you by email once reviewed.
                        </p>
                        <span class="badge badge-warning" style="font-size:14px;padding:6px 16px;">
                            <i class="fa-solid fa-spinner fa-spin me-1"></i> Pending Review
                        </span>
                    </div>
                </div>

            {{-- ========== STAGE: APPROVED ========== --}}
            @elseif ($stage === 'approved')
                <div class="card mb-4" style="border:2px solid #10b981;">
                    <div class="card-body text-center py-4">
                        <div style="font-size:64px;color:#10b981;margin-bottom:12px;">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                        <h4 style="color:#10b981;font-weight:700;">🎉 Monetization Approved!</h4>
                        <p class="text-muted">Congratulations! Your account is approved for monetization.</p>

                        <div class="row mt-4" style="max-width:500px;margin:0 auto;">
                            <div class="col-6 mb-3">
                                <div style="background:#f9faff;padding:12px;border-radius:8px;">
                                    <div style="font-size:20px;font-weight:700;color:#6c63ff;">{{ number_format($totalPlays) }}</div>
                                    <div style="font-size:12px;color:#6b7280;">Total Plays</div>
                                </div>
                            </div>
                            @php
                                $currency = \App\Models\General_Setting::where('key', 'payout_currency')->value('value') ?? 'USD';
                            @endphp
                            <div class="col-6 mb-3">
                                <div style="background:#f9faff;padding:12px;border-radius:8px;">
                                    <div style="font-size:20px;font-weight:700;color:#f59e0b;">{{ $currency }} {{ number_format($totalEarned, 2) }}</div>
                                    <div style="font-size:12px;color:#6b7280;">Total Earned ({{ $currency }})</div>
                                </div>
                            </div>
                        </div>

                        @if ($kyc && $kyc->status === 'approved')
                            <a href="{{ route('user.earnings.index') }}" class="btn btn-primary mt-3">
                                <i class="fa-solid fa-wallet me-2"></i> Go to Earnings & Withdraw
                            </a>
                        @else
                            <div class="alert alert-warning mt-3" style="max-width:500px;margin:0 auto;">
                                <i class="fa-solid fa-shield-halved me-2"></i>
                                <strong>Next Step:</strong>
                                Complete KYC verification to unlock withdrawals.
                                <a href="{{ route('user.kyc.index') }}" style="color:#E01E75;font-weight:600;display:inline-block;margin-top:4px;">
                                    Complete KYC &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

            {{-- ========== STAGE: REJECTED ========== --}}
            @elseif ($stage === 'rejected')
                <div class="card mb-4" style="border:2px solid #ef4444;">
                    <div class="card-body text-center py-4">
                        <div style="font-size:64px;color:#ef4444;margin-bottom:12px;">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                        <h4 style="color:#ef4444;font-weight:700;">Application Rejected</h4>
                        @if ($application && $application->admin_note)
                            <div style="background:#fef2f2;border-left:4px solid #ef4444;padding:12px 16px;margin:12px auto;max-width:500px;font-size:14px;color:#991b1b;">
                                <strong>Reason:</strong> {{ $application->admin_note }}
                            </div>
                        @endif
                        <p class="text-muted">You can improve your stats and reapply once you meet all requirements.</p>

                        @if ($eligibility['eligible'])
                            <button type="button" class="btn btn-success" onclick="applyMonetization()">
                                <i class="fa-solid fa-rotate me-2"></i> Reapply
                            </button>
                        @else
                            <div class="alert alert-info mt-3" style="max-width:500px;margin:0 auto;">
                                <i class="fa-solid fa-list-check me-2"></i>
                                Improve your stats to meet all requirements before reapplying.
                            </div>
                        @endif
                        <div id="applyMsg" class="mt-3"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
<script>
function applyMonetization() {
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Submitting...';

    $.ajax({
        type: 'POST',
        url: '{{ route("user.monetization.apply") }}',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(resp) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i> Apply for Monetization';
            if (resp.status == 200) {
                toastr.success(resp.success);
                setTimeout(() => location.reload(), 1500);
            } else {
                const err = Array.isArray(resp.errors) ? resp.errors.join('\n') : resp.errors;
                toastr.error(err);
            }
        },
        error: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i> Apply for Monetization';
            toastr.error('Something went wrong');
        }
    });
}
</script>
@endsection
