@extends('admin.layout.page-app')
@section('page_title', 'Artist Analytics')
@section('tab_title', 'Artist Analytics')

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <h1 class="page-title-sm">Artist Analytics</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-8">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('label.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">Artist Analytics</li>
                </ol>
            </div>
            <div class="col-sm-4 d-flex align-items-center justify-content-end" style="margin-top:-14px">
                {{-- Period filter buttons --}}
                @foreach(['7d'=>'7 Days','30d'=>'30 Days','90d'=>'90 Days','1yr'=>'1 Year','all'=>'All Time'] as $key=>$label)
                    <a href="{{ route('admin.artist-analytics.index', ['period'=>$key]) }}"
                       class="btn btn-sm {{ $period==$key ? 'btn-default' : 'btn-outline-secondary' }} ml-1">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Summary cards --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card custom-border-card text-center">
                    <div class="card-body py-3">
                        <h3 class="mb-0 text-primary">{{ number_format($totalPlays) }}</h3>
                        <small class="text-muted">Total Plays</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card custom-border-card text-center">
                    <div class="card-body py-3">
                        <h3 class="mb-0 text-success">{{ $currency }} {{ number_format($totalEstimate, 2) }}</h3>
                        <small class="text-muted">Est. Earnings (All Artists)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card custom-border-card text-center">
                    <div class="card-body py-3">
                        <h3 class="mb-0" style="color:#8b5cf6">{{ $artists->count() }}</h3>
                        <small class="text-muted">Active Artists</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card custom-border-card text-center">
                    <div class="card-body py-3">
                        <h3 class="mb-0 text-warning">{{ $monetizedCount }}</h3>
                        <small class="text-muted">Monetized Artists</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rate info --}}
        <div class="alert alert-info d-flex align-items-center mb-4" style="font-size:13px">
            <i class="fa-solid fa-circle-info mr-2"></i>
            Current payout rate: <strong class="ml-1">{{ $currency }} {{ $rate }} per stream</strong>.
            Estimated earnings shown for <em>all artists</em> — actual credits only go to approved monetized artists.
        </div>

        {{-- Artist table --}}
        <div class="card custom-border-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Artist Performance — {{ match($period) { '7d'=>'Last 7 Days','30d'=>'Last 30 Days','90d'=>'Last 90 Days','1yr'=>'Last Year',default=>'All Time' } }}</h5>
                <small class="text-muted">Top {{ $artists->count() }} artists by plays</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:14px">
                        <thead style="background:#f8f9fa">
                            <tr>
                                <th class="pl-3" style="width:50px">#</th>
                                <th>Artist</th>
                                <th class="text-center">Plays</th>
                                <th class="text-center">Est. Earnings</th>
                                <th class="text-center">Monetization</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($artists as $i => $artist)
                                <tr>
                                    <td class="pl-3 text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $artist['image'] }}"
                                                 style="width:36px;height:36px;border-radius:50%;object-fit:cover;margin-right:10px;border:2px solid #e9ecef"
                                                 onerror="this.src='{{ asset('assets/imgs/no_img.png') }}'">
                                            <span class="font-weight-500">{{ $artist['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary" style="font-size:13px;padding:5px 10px">
                                            {{ number_format($artist['play_count']) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($rate > 0)
                                            <span class="{{ $artist['mon_status']==='approved' ? 'text-success font-weight-bold' : 'text-muted' }}">
                                                {{ $currency }} {{ number_format($artist['est_earnings'], 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $badge = match($artist['mon_status']) {
                                                'approved' => ['success',  '💰 Monetized'],
                                                'pending'  => ['warning',  '⏳ Pending'],
                                                'rejected' => ['danger',   '✗ Rejected'],
                                                default    => ['secondary','Not Applied'],
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $badge[0] }}">{{ $badge[1] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('artist.detail', $artist['id']) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-chart-bar"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        No artist plays recorded for this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
