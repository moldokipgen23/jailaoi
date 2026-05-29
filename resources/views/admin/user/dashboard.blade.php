@extends('admin.layout.page-app')
@section('page_title', __('label.user_dashboard'))
@section('tab_title', __('label.user_dashboard'))

@section('content')
	@include('admin.layout.sidebar')

	<div class="right-content">
		@include('admin.layout.header')

		<div class="body-content">
			<!-- mobile title -->
			<h1 class="page-title-sm">{{__('label.user_dashboard')}}</h1>

			<div class="border-bottom row mb-3">
				<div class="col-sm-10">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
						<li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}">{{__('label.users')}}</a></li>
						<li class="breadcrumb-item active" aria-current="page">{{__('label.user_dashboard')}}</li>
					</ol>
				</div>
				<div class="col-sm-2 d-flex align-items-center justify-content-end">
					<a href="{{ route('admin.user.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('label.users_list')}}</a>
				</div>
			</div>

            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-header"></div>
                <div class="row pt-4 mx-3">
                    <div class="col-md-2 text-center">
                        <img src="{{ $data['image'] }}" class="profile-picture mb-2">
                        <h5 class="primary-color">{{ $data['full_name'] }}</h5>
                    </div>
                    <div class="col-md-10">
                        <div class="profile-details">
                            <div class="mb-3">
                                <h5 class="primary-color">{{ $data['channel_name'] ?? '' }}</h5>
                            </div>
                            <div class="row counter-row">
                                <div class="col-xl-4 col-sm-6 col-12">
                                    <div class="card custom-card card-color-primary bg-white">
                                        <div class="card-body">
                                            <div class="card-icon-primary card-color-primary">
                                                <i class="fa-solid fa-users fa-2x"></i>
                                            </div>
                                            <div class="text-right">
                                                <h3>{{$data['total_subscriber'] ?? 0}}</h3>
                                                <span>{{__('label.subscriber')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-sm-6 col-12">
                                    <div class="card custom-card card-color-primary bg-white">
                                        <div class="card-body">
                                            <div class="card-icon-primary card-color-primary">
                                                <i class="fa-solid fa-wallet fa-2x"></i>
                                            </div>
                                            <div class="text-right">
                                                <h3>{{$data['wallet_balance'] ?? 0}}</h3>
                                                <span>{{__('label.wallet_balance')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-sm-6 col-12">
                                    <div class="card custom-card card-color-primary bg-white">
                                        <div class="card-body">
                                            <div class="card-icon-primary card-color-primary">
                                                <i class="fa-solid fa-chart-line fa-2x"></i>
                                            </div>
                                            <div class="text-right">
                                                <h3>{{$data['wallet_earning'] ?? 0}}</h3>
                                                <span>{{__('label.wallet_earning')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Badges & Bonus -->
            <div class="row pt-4 mx-0">
                <div class="col-12 cart-bg">
                    <div class="box-title mt-2">
                        <h2 class="title primary-color"><i class="fa-solid fa-medal fa-lg mr-2"></i>{{__('label.badges')}}</h2>
                    </div>
                    <div class="row p-2">
                        @for ($i = 0; $i < count($badges); $i++)
                            <div class="col-6 col-md-1 bg-white pt-2 pb-2 mr-2" style="border-radius: 10px;">
                                <div class="avatar-control">
                                    @if(isset($badges[$i]['badges_bonus']) != null)
                                        <img src="{{ $badges[$i]['badges_bonus']['image'] }}" class="artist-image" />
                                    @else
                                        <img src="{{ asset('assets/imgs/no_img.png') }}" class="artist-image" />
                                    @endif
                                </div>
                                <h6 class="mt-1 mb-0 artist-name">{{ $badges[$i]['badges_bonus']['name'] ?? "" }}</h6>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Referrer && Referred User -->
            <div class="row pt-4">
                <div class="col-12 col-xl-6">
                    <div class="video-box pt-3">
                        <div class="box-title pt-0 mt-0">
                            <h2 class="title"><i class="fa-solid fa-user-tie fa-lg mr-2"></i>{{__('label.referrer_user')}}</h2>
                        </div>
                        <div class="summary-table-card mt-2">
                            @for ($i = 0; $i < count($parent_user); $i++) 
                                @if(isset($parent_user[$i]['parent_user']) && $parent_user[$i]['parent_user'] !=null)
                                    <div class="border-card bg-white mt-2">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <img src="{{ $parent_user[$i]['parent_user']['image'] }}" class="avatar-img"/>
                                                    {{ String_Cut($parent_user[$i]['parent_user']['channel_name'],120) }}
                                                </span>
                                            </div>
                                            <div class="col-2">
                                                <h5 class="pt-2 primary-color">{{ No_Format($parent_user[$i]['child_earn'] ?? 0)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="video-box pt-3">
                        <div class="box-title pt-0 mt-0">
                            <h2 class="title"><i class="fa-solid fa-users fa-lg mr-2"></i>{{__('label.referred_users')}}</h2>
                        </div>
                        <div class="summary-table-card mt-2">
                            @for ($i = 0; $i < count($child_user); $i++) 
                                @if(isset($child_user[$i]['child_user']) && $child_user[$i]['child_user'] !=null)
                                    <div class="border-card bg-white mt-2">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <img src="{{ $child_user[$i]['child_user']['image'] }}" class="avatar-img"/>
                                                    {{ String_Cut($child_user[$i]['child_user']['channel_name'],120) }}
                                                </span>
                                            </div>
                                            <div class="col-2">
                                                <h5 class="pt-2 primary-color">{{ No_Format($child_user[$i]['parent_earn'] ?? 0)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endfor
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