@extends('user.layout.page-app')
@section('page_title', __('label.earnings'))
@section('tab_title', __('label.earnings'))

@section('content')
    @include('user.layout.sidebar')

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">{{ __('label.earnings') }}</h1>

            @if (!$artist)
                <div class="alert alert-warning">
                    {{ __('label.no_artist_profile_linked') }}
                </div>
            @endif

            <div class="row stat-card-row mb-3">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon primary"><i class="fa-solid fa-play"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">{{ __('label.total_plays') }}</div>
                            <div class="stat-value">{{ number_format($stats['total_plays']) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon primary"><i class="fa-solid fa-coins"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">{{ __('label.total_earned') }}</div>
                            <div class="stat-value">{{ $stats['currency'] }} {{ number_format($stats['total_earned'], 4) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon primary"><i class="fa-solid fa-clock"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">{{ __('label.pending_withdrawals') }}</div>
                            <div class="stat-value">{{ $stats['currency'] }} {{ number_format($stats['pending'], 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon primary"><i class="fa-solid fa-wallet"></i></div>
                        <div class="stat-info">
                            <div class="stat-label">{{ __('label.available_balance') }}</div>
                            <div class="stat-value">{{ $stats['currency'] }} {{ number_format($stats['available'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('label.request_withdrawal') }}</h5>
                    <p class="text-muted small">
                        {{ __('label.payout_rate_info') }}: {{ $stats['currency'] }} {{ $stats['rate'] }} / play.
                        {{ __('label.minimum_withdrawal') }}: {{ $stats['currency'] }} {{ $stats['min_withdrawal'] }}.
                    </p>
                    <form id="withdrawalForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label>{{ __('label.amount') }} ({{ $stats['currency'] }})</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>{{ __('label.payment_method') }}</label>
                                <select name="payment_method" class="form-control" required>
                                    <option value="paypal">PayPal</option>
                                    <option value="bank">Bank Transfer</option>
                                    <option value="mobile_money">Mobile Money</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>{{ __('label.payment_details') }}</label>
                                <input type="text" name="payment_details" class="form-control" placeholder="PayPal email / account info" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2" {{ !$artist || $stats['available'] <= 0 ? 'disabled' : '' }}>
                            {{ __('label.submit_request') }}
                        </button>
                        <div id="withdrawalMsg" class="mt-2"></div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('label.withdrawal_history') }}</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('label.date') }}</th>
                                    <th>{{ __('label.amount') }}</th>
                                    <th>{{ __('label.method') }}</th>
                                    <th>{{ __('label.status') }}</th>
                                    <th>{{ __('label.admin_note') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($withdrawals as $w)
                                    <tr>
                                        <td>{{ $w->id }}</td>
                                        <td>{{ $w->created_at?->format('Y-m-d') }}</td>
                                        <td>{{ $stats['currency'] }} {{ number_format($w->amount, 2) }}</td>
                                        <td>{{ ucfirst($w->payment_method) }}</td>
                                        <td>
                                            @php
                                                $color = match($w->status) {
                                                    'paid' => 'success',
                                                    'approved' => 'info',
                                                    'rejected' => 'danger',
                                                    default => 'warning',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ ucfirst($w->status) }}</span>
                                        </td>
                                        <td>{{ $w->admin_note }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted">{{ __('label.no_records') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.getElementById('withdrawalForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const msg = document.getElementById('withdrawalMsg');
            msg.innerHTML = '';
            try {
                const res = await fetch('{{ route('user.earnings.withdraw') }}', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData,
                });
                const json = await res.json();
                if (json.status === 200) {
                    msg.innerHTML = '<div class="alert alert-success">' + json.success + '</div>';
                    setTimeout(() => location.reload(), 1200);
                } else {
                    const err = Array.isArray(json.errors) ? json.errors.join('<br>') : json.errors;
                    msg.innerHTML = '<div class="alert alert-danger">' + err + '</div>';
                }
            } catch (err) {
                msg.innerHTML = '<div class="alert alert-danger">' + err.message + '</div>';
            }
        });
    </script>
@endsection
