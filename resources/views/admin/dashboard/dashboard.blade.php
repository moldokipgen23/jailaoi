@extends('admin.layout.page-app')
@section('page_title', __('label.dashboard'))
@section('tab_title', __('label.dashboard'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.dashboard')}}</h1>

            <!-- Summary Cards -->
            <div class="row stat-card-row">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.users')}}</div>
                            <div class="stat-value">{{ No_Format($UserCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fa-solid fa-shapes"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.category')}}</div>
                            <div class="stat-value">{{ No_Format($CategoryCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fa-solid fa-globe"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.language')}}</div>
                            <div class="stat-value">{{ No_Format($LanguageCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fa-solid fa-hashtag"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.hashtag')}}</div>
                            <div class="stat-value">{{ No_Format($HashtagCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row stat-card-row">
                @if($video_enabled ?? true)
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fa-solid fa-video"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.videos')}}</div>
                            <div class="stat-value">{{ No_Format($VideoCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fa-solid fa-music"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.music')}}</div>
                            <div class="stat-value">{{ No_Format($MusicCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                @if($reels_enabled ?? true)
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fa-solid fa-film"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.reels')}}</div>
                            <div class="stat-value">{{ No_Format($ReelsCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fa-solid fa-podcast"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.podcasts')}}</div>
                            <div class="stat-value">{{ No_Format($PodcastsCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row stat-card-row">
                @if($feed_enabled ?? true)
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fa-solid fa-camera-retro"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.feeds')}}</div>
                            <div class="stat-value">{{ No_Format($FeedCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fa-solid fa-headphones"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.playlists')}}</div>
                            <div class="stat-value">{{ No_Format($PlaylistCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fa-solid fa-radio"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.radio')}}</div>
                            <div class="stat-value">{{ No_Format($RadioCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fa-solid fa-gift"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">{{__('label.gift')}}</div>
                            <div class="stat-value">{{ No_Format($GiftCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Statistice && Most Subscribed Channel -->
            <div class="row pl-3">
                <div class="col-12 col-xl-8 cart-bg">
                    <div class="box-title">
                        <h2 class="title"><i class="fa-solid fa-chart-column fa-lg mr-2"></i>{{__('label.join_users_statistice')}}</h2>
                        <a href="{{ route('admin.user.index')}}" class="btn btn-link">{{__('label.view_all')}}</a>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 col-sm-12">
                            <Button id="year" class="btn btn-default">{{__('label.this_year')}}</Button>
                            <Button id="month" class="btn btn-default">{{__('label.this_month')}}</Button>
                        </div>
                    </div>
                    <div class="summary-table-card mt-2">
                        <div id="User_Chart"></div>
                    </div>
                </div>
                <div class="col-12 col-xl-4">
                    <div class="video-box pt-3">
                        <div class="box-title pt-0 mt-0">
                            <h2 class="title"><i class="fa-solid fa-chart-bar fa-lg mr-2"></i>{{__('label.most_subscribed_channel')}}</h2>
                            <a href="{{ route('admin.user.index')}}" class="btn btn-link">{{__('label.view_all')}}</a>
                        </div>
                        <div class="summary-table-card mt-2">
                            @for ($i = 0; $i < count($top_subscriber); $i++) 
                                @if(isset($top_subscriber[$i]['to_user']) && $top_subscriber[$i]['to_user'] !=null)
                                    <div class="border-card bg-white mt-2">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <img src="{{ $top_subscriber[$i]['to_user']['image'] }}" class="avatar-img"/>
                                                    {{ String_Cut($top_subscriber[$i]['to_user']['channel_name'],60) }}
                                                </span>
                                            </div>
                                            <div class="col-2">
                                                <h5 class="pt-2 primary-color">{{ No_Format($top_subscriber[$i]['total_subscriber'] ?? 0)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most View Content && Best Category -->
            <div class="row mt-3 pl-3">
                <div class="col-12 col-xl-8 cart-bg">
                    <div class="box-title">
                        <h2 class="title"><i class="fa-solid fa-chart-bar fa-lg mr-2"></i>{{__('label.most_view_content')}}</h2>
                    </div>

                    <ul class="nav nav-pills custom-tabs" id="pills-tab" role="tablist">
                        @if($video_enabled ?? true)
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-video-view-tab" data-toggle="pill" href="#pills-video-view" role="tab" aria-controls="pills-video-view" aria-selected="true">{{__('label.videos')}}</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link @if(!($video_enabled ?? true)) active @endif" id="pills-music-view-tab" data-toggle="pill" href="#pills-music-view" role="tab" aria-controls="pills-music-view" aria-selected="{{($video_enabled ?? true) ? 'false' : 'true'}}">{{__('label.music')}}</a>
                        </li>
                        @if($reels_enabled ?? true)
                        <li class="nav-item">
                            <a class="nav-link" id="pills-reels-view-tab" data-toggle="pill" href="#pills-reels-view" role="tab" aria-controls="pills-reels-view" aria-selected="false">{{__('label.reels')}}</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" id="pills-podcasts-view-tab" data-toggle="pill" href="#pills-podcasts-view" role="tab" aria-controls="pills-podcasts-view" aria-selected="false">{{__('label.podcasts')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-radio-view-tab" data-toggle="pill" href="#pills-radio-view" role="tab" aria-controls="pills-radio-view" aria-selected="false">{{__('label.radio')}}</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        @if($video_enabled ?? true)
                        <div class="tab-pane fade show active" id="pills-video-view" role="tabpanel" aria-labelledby="pills-video-view-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_video_view); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_video_view[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_video_view[$i]['title'], 170)}}
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
                        @endif
                        <div class="tab-pane fade @if(!($video_enabled ?? true)) show active @endif" id="pills-music-view" role="tabpanel" aria-labelledby="pills-music-view-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_music_view); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_music_view[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_music_view[$i]['title'], 170)}}
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
                        @if($reels_enabled ?? true)
                        <div class="tab-pane fade" id="pills-reels-view" role="tabpanel" aria-labelledby="pills-reels-view-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_reels_view); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_reels_view[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_reels_view[$i]['title'], 170)}}
                                                </span>
                                            </div>
                                            <div class="col-2 d-flex justify-content-start primary-color">
                                                <i class="fa-solid fa-eye mr-2 fa-xl"></i>
                                                <h5 class="m-0">{{No_Format($top_reels_view[$i]['total_view'] ?? 00)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        @endif
                        <div class="tab-pane fade" id="pills-podcasts-view" role="tabpanel" aria-labelledby="pills-podcasts-view-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_podcasts_view); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_podcasts_view[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_podcasts_view[$i]['title'], 170)}}
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
                                                    {{String_Cut($top_radio_view[$i]['title'], 170)}}
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
                    
                <div class="col-12 col-xl-4">
                    <div class="category-box">
                        <div class="box-title mt-0">
                            <h2 class="title"><i class="fa-solid fa-table-cells-large fa-lg mr-2"></i>{{__('label.best_category')}}</h2>
                            <a href="{{ route('admin.category.index')}}" class="btn btn-link">{{__('label.view_all')}}</a>
                        </div>
                        <div class="pt-3">
                            <div class="row pr-3">
                                @for ($i = 0; $i < count($best_category); $i++)
                                    @if($i> 0 && (($i % 4) == 1 || ($i % 4) == 2))
                                        <div class="col-5 mb-3 pr-0">
                                            <img src="{{ $best_category[$i]['image'] }}" class="category-image">
                                            <div class="centered">{{ $best_category[$i]['name'] }}</div>
                                        </div>
                                    @else
                                        <div class="col-7 mb-3 pr-0">
                                            <img src="{{ $best_category[$i]['image'] }}" class="category-image">
                                            <div class="centered">{{ $best_category[$i]['name'] }}</div>
                                        </div>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
      
            <!-- Most Like Content && Most Used Hashtag -->
            <div class="row mt-3 pl-3">
                <div class="col-12 col-xl-8 cart-bg">
                    <div class="box-title">
                        <h2 class="title"><i class="fa-solid fa-chart-bar fa-lg mr-2"></i>{{__('label.most_like_content')}}</h2>
                    </div>

                    <ul class="nav nav-pills custom-tabs" id="pills-tab" role="tablist">
                        @if($video_enabled ?? true)
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-video-tab" data-toggle="pill" href="#pills-video" role="tab" aria-controls="pills-video" aria-selected="true">{{__('label.videos')}}</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link @if(!($video_enabled ?? true)) active @endif" id="pills-music-tab" data-toggle="pill" href="#pills-music" role="tab" aria-controls="pills-music" aria-selected="{{($video_enabled ?? true) ? 'false' : 'true'}}">{{__('label.music')}}</a>
                        </li>
                        @if($reels_enabled ?? true)
                        <li class="nav-item">
                            <a class="nav-link" id="pills-reels-tab" data-toggle="pill" href="#pills-reels" role="tab" aria-controls="pills-reels" aria-selected="false">{{__('label.reels')}}</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" id="pills-podcasts-tab" data-toggle="pill" href="#pills-podcasts" role="tab" aria-controls="pills-podcasts" aria-selected="false">{{__('label.podcasts')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-radio-tab" data-toggle="pill" href="#pills-radio" role="tab" aria-controls="pills-radio" aria-selected="false">{{__('label.radio')}}</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        @if($video_enabled ?? true)
                        <div class="tab-pane fade show active" id="pills-video" role="tabpanel" aria-labelledby="pills-video-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_video_like); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_video_like[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_video_like[$i]['title'], 170)}}
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
                        @endif
                        <div class="tab-pane fade @if(!($video_enabled ?? true)) show active @endif" id="pills-music" role="tabpanel" aria-labelledby="pills-music-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_music_like); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_music_like[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_music_like[$i]['title'], 170)}}
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
                        @if($reels_enabled ?? true)
                        <div class="tab-pane fade" id="pills-reels" role="tabpanel" aria-labelledby="pills-reels-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_reels_like); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_reels_like[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_reels_like[$i]['title'], 170)}}
                                                </span>
                                            </div>
                                            <div class="col-2 d-flex justify-content-start primary-color">
                                                <i class="fa-solid fa-thumbs-up mr-2 fa-xl"></i>
                                                <h5 class="m-0">{{No_Format($top_reels_like[$i]['total_view'] ?? 00)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        @endif
                        <div class="tab-pane fade" id="pills-podcasts" role="tabpanel" aria-labelledby="pills-podcasts-tab">
                            <div class="summary-table-card">
                                @for ($i = 0; $i < count($top_podcasts_like); $i++)
                                    <div class="border-card bg-white">
                                        <div class="row">
                                            <div class="col-10 pl-0">
                                                <span class="avatar-control">
                                                    <div class="mr-3">{{$i + 1 .'.'}}</div>
                                                    <img src="{{ $top_podcasts_like[$i]['portrait_img'] }}" class="avatar-img"/>
                                                    {{String_Cut($top_podcasts_like[$i]['title'], 170)}}
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
                                                    {{String_Cut($top_radio_like[$i]['title'], 170)}}
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

                <div class="col-12 col-xl-4">
                    <div class="video-box pb-2">
                        <div class="box-title mt-0">
                            <h2 class="title"><i class="fa-solid fa-hashtag fa-lg mr-2"></i>{{__('label.most_used_hashtag')}}</h2>
                            <a href="{{ route('admin.hashtag.index') }}" class="btn btn-link">{{__('label.view_all')}}</a>
                        </div>
                        <div class="summary-table-card mt-3">
                            @for ($i = 0; $i < count($most_used_hashtag); $i++)
                                <div class="hashtag-card mb-3">
                                    <div class="row">
                                        <div class="col-10 pl-2">
                                            <h5 class="m-0 primary-color">#{{ String_Cut($most_used_hashtag[$i]['name'],25) }}</h5>
                                        </div>
                                        <div class="col-2 pl-0">
                                            <h5 class="m-0 primary-color">{{ No_Format($most_used_hashtag[$i]['total_used'] ?? 0) }}</h5>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

        </div>          
    </div>
@endsection

@section('pagescript')
    <!-- Chart -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // User Chart
        let userYear = JSON.parse(`<?php echo $user_year ?>`);
        let userMonth = JSON.parse(`<?php echo $user_month ?>`);
        let chartOptions = {
            chart: {
                type: 'bar',
                height: 400,
                toolbar: { show: false }
            },
            dataLabels: {
                enabled: false
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '70%',
                    endingShape: 'rounded'
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#4e45b8'],
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 0.8,
                    stops: [0, 100]
                }
            },
            colors: ['#6a11cb', '#f7971e'],
            grid: {
                borderColor: '#9a9a9a',
                strokeDashArray: 4
            },
            tooltip: {
                theme: 'dark',
                style: {
                    fontSize: '14px'
                }
            },
            series: [],
            xaxis: {
                categories: []
            },
            legend: {
                position: 'bottom',
                fontSize: '16px',
                fontWeight: 'bold',
                labels: {
                    colors: ['#333'],
                    useSeriesColors: false
                }
            },
        };

        let chart = new ApexCharts(document.querySelector("#User_Chart"), chartOptions);
        chart.render();

        function loadChartData(type) {
            if (type === 'year') {
                chart.updateOptions({
                    series: [
                        { name: "{{ __('label.users') }}", data: userYear.sum },
                    ],
                    xaxis: {
                        categories: [
                            '{{__("label.jan")}}', '{{__("label.feb")}}', '{{__("label.mar")}}', '{{__("label.apr")}}',
                            '{{__("label.may")}}', '{{__("label.jun")}}', '{{__("label.jul")}}', '{{__("label.aug")}}',
                            '{{__("label.sep")}}', '{{__("label.oct")}}', '{{__("label.nov")}}', '{{__("label.dec")}}'
                        ],
                        labels: {
                            style: {
                                fontSize: '14px',
                                fontWeight: 'bold'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontSize: '14px',
                                fontWeight: 'bold'
                            }
                        }
                    },
                });
            } else {
                let daysInMonth = userMonth.sum.length;
                chart.updateOptions({
                    series: [
                        { name: "{{ __('label.users') }}", data: userMonth.sum },
                    ],
                    xaxis: {
                        categories: Array.from({ length: daysInMonth }, (_, i) => (i + 1).toString())
                    },
                });
            }
        }

        loadChartData('year');
        document.getElementById('year').addEventListener('click', function () {
            loadChartData('year');
        });
        document.getElementById('month').addEventListener('click', function () {
            loadChartData('month');
        });
    </script>
@endsection