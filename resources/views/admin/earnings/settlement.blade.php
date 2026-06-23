@extends('admin.layout.page-app')
@section('page_title', 'Revenue Pool Settlement')
@section('tab_title', 'Settlement')

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">Revenue Pool Settlement</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Revenue Pool Settlement</li>
                    </ol>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Current Model Status --}}
            <div class="card custom-border-card mb-4">
                <h5 class="card-header">Earnings Model</h5>
                <div class="card-body">
                    @if($model === 'pool')
                        <span class="badge badge-success" style="font-size:14px;padding:6px 14px;">Revenue Pool (55% to Artists)</span>
                        <p class="mt-2 mb-0 text-muted">
                            Artists earn 55% of monthly subscription revenue, distributed proportionally by stream share.
                            Settlements run automatically on the 5th of each month.
                        </p>
                    @else
                        <span class="badge badge-warning" style="font-size:14px;padding:6px 14px;">Per Stream (Fixed Rate)</span>
                        <p class="mt-2 mb-0 text-muted">
                            Artists earn a fixed rate per stream. Change to Pool mode in
                            <a href="{{ route('setting') }}#payout-settings">Settings → Artist Payout Settings</a>.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Current Month (unsettled) --}}
            <div class="card custom-border-card mb-4">
                <h5 class="card-header">Current Month ({{ now()->format('F Y') }})</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <h3 class="text-info">{{ number_format($unsettledPlays) }}</h3>
                                <p class="text-muted mb-0">Unsettled Plays</p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <p class="text-muted mb-0 mt-2">
                                <i class="fa-solid fa-info-circle"></i>
                                Current month plays are recorded in real-time.
                                Earnings for this month will be calculated and distributed on the
                                5th of next month via automated settlement.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Previous Month Settlement Action --}}
            <div class="card custom-border-card mb-4">
                <h5 class="card-header">Settle Previous Month ({{ $prevMonth }})</h5>
                <div class="card-body">
                    @if($alreadySettled)
                        <div class="alert alert-success py-2">
                            <i class="fa-solid fa-check-circle"></i>
                            {{ $prevMonth }} has already been settled.
                            <a href="{{ route('admin.earnings.settlement') }}" class="ml-2">Refresh</a>
                        </div>
                    @else
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <h3 class="text-primary">{{ number_format($prevMonthPlays) }}</h3>
                                    <p class="text-muted mb-0">Eligible Streams</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <h3 class="text-success">₹{{ number_format($prevMonthRevenue, 2) }}</h3>
                                    <p class="text-muted mb-0">Subscription Revenue</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <h3 class="text-warning">₹{{ number_format($prevMonthRevenue * 0.55, 2) }}</h3>
                                    <p class="text-muted mb-0">Est. Artist Pool (55%)</p>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-success" onclick="runSettlement('{{ $prevMonth }}')">
                            <i class="fa-solid fa-calculator"></i> Run Settlement for {{ $prevMonth }}
                        </button>
                        <small class="text-muted d-block mt-2">
                            <i class="fa-solid fa-info-circle"></i>
                            This distributes 55% of {{ $prevMonth }} subscription revenue among approved artists
                            based on their share of total streams.
                        </small>
                    @endif
                </div>
            </div>

            {{-- Past Settlements --}}
            <div class="card custom-border-card">
                <h5 class="card-header">Settlement History</h5>
                <div class="card-body">
                    @if($settlements->isEmpty())
                        <p class="text-muted">No settlements have been run yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Revenue</th>
                                        <th>Platform Cut (45%)</th>
                                        <th>Artist Pool (55%)</th>
                                        <th>Total Streams</th>
                                        <th>Rate/Stream</th>
                                        <th>Settled At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settlements as $s)
                                        <tr>
                                            <td><strong>{{ $s->month }}</strong></td>
                                            <td>₹{{ number_format($s->total_revenue, 2) }}</td>
                                            <td>₹{{ number_format($s->platform_cut, 2) }}</td>
                                            <td>₹{{ number_format($s->pool_amount, 2) }}</td>
                                            <td>{{ number_format($s->total_streams) }}</td>
                                            <td>₹{{ number_format($s->rate_per_stream, 4) }}</td>
                                            <td>{{ $s->settled_at ? date('d M Y', strtotime($s->settled_at)) : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
    function runSettlement(month) {
        if (!confirm('Run settlement for ' + month + '? This will distribute artist earnings and cannot be undone.')) {
            return;
        }

        $('#dvloader').show();
        $.ajax({
            type: 'POST',
            url: '{{ route("admin.earnings.run-settlement") }}',
            data: {
                _token: '{{ csrf_token() }}',
                month: month
            },
            success: function(resp) {
                $('#dvloader').hide();
                if (resp.status === 200) {
                    toastr.success('Settlement completed successfully!');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    toastr.error('Settlement failed: ' + (resp.errors || 'Unknown error'));
                    if (resp.output) console.log(resp.output);
                }
            },
            error: function(xhr) {
                $('#dvloader').hide();
                toastr.error('Request failed: ' + xhr.statusText);
            }
        });
    }
    </script>
@endsection
