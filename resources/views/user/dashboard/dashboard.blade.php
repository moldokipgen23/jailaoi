@extends('user.layout.page-app')
@section('page_title', __('label.dashboard'))
@section('tab_title', __('label.dashboard'))

@section('content')
    @include('user.layout.sidebar')

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.dashboard')}}</h1>

            <!-- Profile Card -->
            <div class="profile-card mb-3">
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
                            <div class="row stat-card-row">
                                <div class="col-xl-4 col-md-6 col-12">
                                    <div class="stat-card">
                                        <div class="stat-icon primary">
                                            <i class="fa-solid fa-users"></i>
                                        </div>
                                        <div class="stat-info">
                                            <div class="stat-label">{{__('label.subscriber')}}</div>
                                            <div class="stat-value">{{$data['total_subscriber'] ?? 0}}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6 col-12">
                                    <div class="stat-card">
                                        <div class="stat-icon success">
                                            <i class="fa-solid fa-wallet"></i>
                                        </div>
                                        <div class="stat-info">
                                            <div class="stat-label">{{__('label.wallet_balance')}}</div>
                                            <div class="stat-value">{{$data['wallet_balance'] ?? 0}}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6 col-12">
                                    <div class="stat-card">
                                        <div class="stat-icon warning">
                                            <i class="fa-solid fa-chart-line"></i>
                                        </div>
                                        <div class="stat-info">
                                            <div class="stat-label">{{__('label.wallet_earning')}}</div>
                                            <div class="stat-value">{{$data['wallet_earning'] ?? 0}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Stats -->
            <div class="row stat-card-row">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fa-solid fa-video"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.my_videos')}}</div>
                            <div class="stat-value">{{ No_Format($VideoCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fa-solid fa-music"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.my_music')}}</div>
                            <div class="stat-value">{{ No_Format($MusicCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fa-solid fa-podcast"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.my_podcasts')}}</div>
                            <div class="stat-value">{{ No_Format($PodcastsCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row stat-card-row">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fa-solid fa-headphones"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.my_playlists')}}</div>
                            <div class="stat-value">{{ No_Format($PlaylistCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fa-solid fa-radio"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.my_radio')}}</div>
                            <div class="stat-value">{{ No_Format($RadioCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fa-solid fa-rectangle-ad"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.my_ads')}}</div>
                            <div class="stat-value">{{ No_Format($AdsCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most View Content -->
            <div class="row mx-0 mb-3">
                <div class="col-12 cart-bg">
                    <div class="box-title">
                        <h2 class="title"><i class="fa-solid fa-chart-bar fa-lg mr-2"></i>{{__('label.most_view_content')}}</h2>
                    </div>

                    <ul class="nav nav-pills custom-tabs" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-video-view-tab" data-toggle="pill" href="#pills-video-view" role="tab" aria-controls="pills-video-view" aria-selected="true">{{__('label.videos')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-music-view-tab" data-toggle="pill" href="#pills-music-view" role="tab" aria-controls="pills-music-view" aria-selected="false">{{__('label.music')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-podcasts-view-tab" data-toggle="pill" href="#pills-podcasts-view" role="tab" aria-controls="pills-podcasts-view" aria-selected="false">{{__('label.podcasts')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-radio-view-tab" data-toggle="pill" href="#pills-radio-view" role="tab" aria-controls="pills-radio-view" aria-selected="false">{{__('label.radio')}}</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-video-view" role="tabpanel" aria-labelledby="pills-video-view-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_video_view); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_video_view[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_video_view[$i]['title'], 330)}}
                                                </span>
                                            </div>
                                            <div class="col-2 d-flex justify-content-start primary-color">
                                                <i class="fa-solid fa-eye mr-2 fa-xl"></i>
                                                <h5 class="m-0">{{No_Format($top_video_view[$i]['total_view'] ?? 00)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-music-view" role="tabpanel" aria-labelledby="pills-music-view-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_music_view); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_music_view[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_music_view[$i]['title'], 330)}}
                                                </span>
                                            </div>
                                            <div class="col-2 d-flex justify-content-start primary-color">
                                                <i class="fa-solid fa-eye mr-2 fa-xl"></i>
                                                <h5 class="m-0">{{No_Format($top_music_view[$i]['total_view'] ?? 00)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-podcasts-view" role="tabpanel" aria-labelledby="pills-podcasts-view-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_podcasts_view); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_podcasts_view[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_podcasts_view[$i]['title'], 330)}}
                                                </span>
                                            </div>
                                            <div class="col-2 d-flex justify-content-start primary-color">
                                                <i class="fa-solid fa-eye mr-2 fa-xl"></i>
                                                <h5 class="m-0">{{No_Format($top_podcasts_view[$i]['total_view'] ?? 00)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-radio-view" role="tabpanel" aria-labelledby="pills-radio-view-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_radio_view); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_radio_view[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_radio_view[$i]['title'], 330)}}
                                                </span>
                                            </div>
                                            <div class="col-2 d-flex justify-content-start primary-color">
                                                <i class="fa-solid fa-eye mr-2 fa-xl"></i>
                                                <h5 class="m-0">{{No_Format($top_radio_view[$i]['total_view'] ?? 00)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most Like Content -->
            <div class="row mx-0 mb-3">
                <div class="col-12 cart-bg">
                    <div class="box-title">
                        <h2 class="title"><i class="fa-solid fa-chart-bar fa-lg mr-2"></i>{{__('label.most_like_content')}}</h2>
                    </div>

                    <ul class="nav nav-pills custom-tabs" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-video-tab" data-toggle="pill" href="#pills-video" role="tab" aria-controls="pills-video" aria-selected="true">{{__('label.videos')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-music-tab" data-toggle="pill" href="#pills-music" role="tab" aria-controls="pills-music" aria-selected="false">{{__('label.music')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-podcasts-tab" data-toggle="pill" href="#pills-podcasts" role="tab" aria-controls="pills-podcasts" aria-selected="false">{{__('label.podcasts')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-radio-tab" data-toggle="pill" href="#pills-radio" role="tab" aria-controls="pills-radio" aria-selected="false">{{__('label.radio')}}</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-video" role="tabpanel" aria-labelledby="pills-video-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_video_like); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_video_like[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_video_like[$i]['title'], 330)}}
                                                </span>
                                            </div>
                                            <div class="col-2 d-flex justify-content-start primary-color">
                                                <i class="fa-solid fa-thumbs-up mr-2 fa-xl"></i>
                                                <h5 class="m-0">{{No_Format($top_video_like[$i]['total_view'] ?? 00)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-music" role="tabpanel" aria-labelledby="pills-music-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_music_like); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_music_like[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_music_like[$i]['title'], 330)}}
                                                </span>
                                            </div>
                                            <div class="col-2 d-flex justify-content-start primary-color">
                                                <i class="fa-solid fa-thumbs-up mr-2 fa-xl"></i>
                                                <h5 class="m-0">{{No_Format($top_music_like[$i]['total_view'] ?? 00)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-podcasts" role="tabpanel" aria-labelledby="pills-podcasts-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_podcasts_like); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_podcasts_like[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_podcasts_like[$i]['title'], 330)}}
                                                </span>
                                            </div>
                                            <div class="col-2 d-flex justify-content-start primary-color">
                                                <i class="fa-solid fa-thumbs-up mr-2 fa-xl"></i>
                                                <h5 class="m-0">{{No_Format($top_podcasts_like[$i]['total_view'] ?? 00)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-radio" role="tabpanel" aria-labelledby="pills-radio-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_radio_like); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_radio_like[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_radio_like[$i]['title'], 330)}}
                                                </span>
                                            </div>
                                            <div class="col-2 d-flex justify-content-start primary-color">
                                                <i class="fa-solid fa-thumbs-up mr-2 fa-xl"></i>
                                                <h5 class="m-0">{{No_Format($top_radio_like[$i]['total_view'] ?? 00)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Badges & Bonus -->
            <div class="row mx-0 mb-3">
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
            <div class="row">
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