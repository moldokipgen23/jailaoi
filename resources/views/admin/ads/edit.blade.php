@extends('admin.layout.page-app')
@section('page_title', __('label.custom_ads_analytics'))
@section('tab_title', __('label.custom_ads_analytics'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.custom_ads_analytics')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.ads.index') }}">{{__('label.custom_ads')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.custom_ads_analytics')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.ads.index') }}" class="btn btn-default mw-150" style="margin-top: -14px;">{{__('label.custom_ads_list')}}</a>
                </div>
            </div>

            <div class="card custom-border-card mb-3">
                <div class="card-body">
                    <h4 class="mb-2">{{__('label.title')}}: {{ $data['title'] }}</h4>
                    <h5 class="text-muted mb-1">{{__('label.channel')}}: {{ $data['user']['channel_name'] ?? '' }}</h5>
                    <h5 class="text-muted">{{__('label.user')}}: {{ $data['user']['full_name'] ?? '' }}</h5>
                </div>
            </div>

            <!-- Stats -->
            <div class="row stat-card-row">
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.total_budget')}}</div>
                            <div class="stat-value">{{$data['budget'] ?? 0}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fa-solid fa-eye"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.total_view')}}</div>
                            <div class="stat-value">{{$total_ads_cpv ?? 0}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fa-solid fa-hand-point-up"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.total_click')}}</div>
                            <div class="stat-value">{{$total_ads_cpc ?? 0}}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row stat-card-row">
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fa-solid fa-money-bill-trend-up"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.total_used_budget')}}</div>
                            <div class="stat-value">{{$total_use_budget ?? 0}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fa-solid fa-coins"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.total_view_coin')}}</div>
                            <div class="stat-value">{{$total_ads_cpv_coin ?? 0}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fa-solid fa-coins"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.total_click_coin')}}</div>
                            <div class="stat-value">{{$total_ads_cpc_coin ?? 0}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        // Sidebar Scroll Down
        let sidebarHeight = $('.sidebar')[0].scrollHeight;
        sidebar_down(sidebarHeight);
    </script>
@endsection