@extends('user.layout.page-app')
@section('page_title', __('label.custom_ads_analytics'))
@section('tab_title', __('label.custom_ads_analytics'))

@section('content')
    @include('user.layout.sidebar')

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.custom_ads_analytics')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.ads.index') }}">{{__('label.custom_ads')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.custom_ads_analytics')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('user.ads.index') }}" class="btn btn-default mw-150" style="margin-top: -14px;">{{__('label.custom_ads_list')}}</a>
                </div>
            </div>

            <div class="row counter-row">
                <div class="col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div>
                                <h4 class="text-dark">{{__('label.title')}}: {{ $data['title'] }}</h4>
                                <h5>{{__('label.channel')}}: {{ $data['user']['channel_name'] ?? '' }}</h5>
                                <h5>{{__('label.user')}}: {{ $data['user']['full_name'] ?? '' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card -->
            <div class="row counter-row">
                <div class="col-xl-4 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-wallet fa-2x"></i>
                            </div>
                            <div class="text-right">
                                <h3>{{$data['budget'] ?? 0}}</h3>
                                <span>{{__('label.total_budget')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-eye fa-2x"></i>
                            </div>
                            <div class="text-right">
                                <h3>{{$total_ads_cpv ?? 0}}</h3>
                                <span>{{__('label.total_view')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-hand-point-up fa-2x"></i>
                            </div>
                            <div class="text-right">
                                <h3>{{$total_ads_cpc ?? 0}}</h3>
                                <span>{{__('label.total_click')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row counter-row">
                <div class="col-xl-4 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-money-bill-trend-up fa-2x"></i>
                            </div>
                            <div class="text-right">
                                <h3>{{$total_use_budget ?? 0}}</h3>
                                <span>{{__('label.total_used_budget')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-coins fa-2x"></i>
                            </div>
                            <div class="text-right">
                                <h3>{{$total_ads_cpv_coin ?? 0}}</h3>
                                <span>{{__('label.total_view_coin')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-coins fa-2x"></i>
                            </div>
                            <div class="text-right">
                                <h3>{{$total_ads_cpc_coin ?? 0}}</h3>
                                <span>{{__('label.total_click_coin')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
    </script>
@endsection