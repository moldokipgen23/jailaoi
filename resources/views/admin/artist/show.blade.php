@extends('admin.layout.page-app')
@section('page_title', 'Artist Detail - ' . $artist->name)
@section('tab_title', 'Artist Detail')

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <div class="border-bottom row mb-3">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('label.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('artist.index') }}">{{ __('label.artist') }}</a></li>
                    <li class="breadcrumb-item active">{{ $artist->name }}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card custom-border-card text-center">
                    <div class="card-body">
                        <img src="{{ $artistImageUrl }}" alt="{{ $artist->name }}" style="width:150px;height:150px;border-radius:50%;object-fit:cover;border:3px solid #e9ecef;">
                        <h4 class="mt-3">{{ $artist->name }}</h4>
                        <span class="badge badge-{{ $artist->status == 1 ? 'success' : 'danger' }}">{{ $artist->status == 1 ? 'Active' : 'Inactive' }}</span>
                        <hr>
                        <p class="text-muted">{{ $artist->bio ?? 'No bio' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card custom-border-card">
                    <div class="card-header">
                        <h5>Artist Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-primary">{{ $totalPlays }}</h3>
                                    <small class="text-muted">Total Plays</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-success">{{ number_format($totalEarned, 2) }}</h3>
                                    <small class="text-muted">Total Earned</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-warning">{{ number_format($available, 2) }}</h3>
                                    <small class="text-muted">Available</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded">
                                    <h3>{{ $totalTracks }}</h3>
                                    <small class="text-muted">Total Tracks</small>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-info">{{ number_format($paidOut, 2) }}</h3>
                                    <small class="text-muted">Paid Out</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-danger">{{ number_format($pending, 2) }}</h3>
                                    <small class="text-muted">Pending Withdrawals</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded">
                                    <h3>{{ number_format($followers) }}</h3>
                                    <small class="text-muted">Followers</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card custom-border-card mt-3">
                    <div class="card-header">
                        <h5>Account Info</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr><th style="width:200px;">User</th><td>{{ $artist->user?->full_name ?? $artist->user?->name ?? '-' }} ({{ $artist->user?->email ?? '-' }})</td></tr>
                            <tr><th>Phone</th><td>{{ $artist->user?->mobile_number ?? '-' }}</td></tr>
                            <tr><th>Type</th><td>{{ $artist->type == 1 ? 'RJ' : ($artist->type == 2 ? 'Podcaster' : ($artist->type == 3 ? 'Singer' : 'Unset')) }}</td></tr>
                            <tr><th>Created</th><td>{{ $artist->created_at->format('Y-m-d H:i') }}</td></tr>
                            <tr><th>Artist Request</th><td>{{ $artist->artistRequest ? 'Submitted on ' . $artist->artistRequest->created_at->format('Y-m-d') : 'N/A' }}</td></tr>
                        </table>
                    </div>
                </div>

                @if($kyc)
                <div class="card custom-border-card mt-3">
                    <div class="card-header">
                        <h5>KYC / Payment Info</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr><th style="width:200px;">Legal Name</th><td>{{ $kyc->legal_first_name }} {{ $kyc->legal_last_name }}</td></tr>
                            <tr><th>ID Type</th><td>{{ $kyc->id_type }}</td></tr>
                            <tr><th>Payment Method</th><td>{{ $kyc->payment_method }}</td></tr>
                            <tr><th>Payment Details</th><td><pre>{{ json_encode($kyc->payment_details, JSON_PRETTY_PRINT) }}</pre></td></tr>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
