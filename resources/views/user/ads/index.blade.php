@extends('user.layout.page-app')
@section('page_title', __('label.custom_ads'))
@section('tab_title', __('label.custom_ads'))

@section('content')
    @include('user.layout.sidebar')

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.custom_ads')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.custom_ads')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('user.ads.create') }}" class="btn btn-default mw-120" style="margin-top: -14px;">{{__('label.add_custom_ads')}}</a>
                </div>
            </div>

            <!-- Search -->
            <form action="{{ route('user.ads.index')}}" method="GET">
                <div class="page-search">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-magnifying-glass fa-xl"></i>
                            </span>
                        </div>
                        <input type="text" name="input_search" value="{{ $_GET['input_search'] ?? '' }}" class="form-control" placeholder="{{__('label.search')}}" aria-label="Search" aria-describedby="basic-addon1">
                    </div>
                    <div class="sorting mr-2 w-25">
                        <select class="form-control" name="input_type" id="input_type">
                            <option value="0" {{ request('input_type') == 0 ? 'selected' : ''}}>{{__('label.all_ads')}}</option>
                            <option value="1" {{ request('input_type') == 1 ? 'selected' : ''}}>{{__('label.banner_ads')}}</option>
                            <option value="2" {{ request('input_type') == 2 ? 'selected' : ''}}>{{__('label.interstital_ads')}}</option>
                            <option value="3" {{ request('input_type') == 3 ? 'selected' : ''}}>{{__('label.reward_ads')}}</option>
                        </select>
                    </div>
                    <button class="btn btn-default mx-2" type="submit">{{__('label.search')}}</button>
                </div>
            </form>

            <div class="row">
                @foreach ($data as $key => $value)
                    <div class="col-12 col-xl-4">
                        <div class="card landscape-card">
                            <div class="media">
                                @if($value['status'] == 1)
                                    <div class="ribbon ribbon-top-left"><span>{{__('label.active')}}</span></div>
                                @else
                                    <div class="ribbon ribbon-top-left"><span>{{__('label.inactive')}}</span></div>
                                @endif
                                <img src="{{ $value['image'] }}" class="wallet-image">
                                <div class="card-body px-2 py-0">
                                    <h6 class="landscape-card-title">{{$value['title'] ?? ''}}</h6>
                                    <h6 class="primary-color">{{__('label.budget_:')}}{{$value['budget'] ?? '0'}}</h6>
                                    <p class="landscape-card-name mb-0">{{$value['user']['channel_name'] ?? ''}}</p>
                                    <div class="landscape-card-border"></div>

                                    <ul class="list-inline overlap-control mb-0" aria-labelledby="dropdownMenuLink">
                                        <li class="list-inline-item">
                                            @if($value['is_hide'] == 0)
                                                <button type="button" class="show-btn px-2">{{__('label.show')}}</button>
                                            @elseif($value['is_hide'] == 1)
                                                <button type="button" class="hide-btn px-2">{{__('label.hide')}}</button>
                                            @endif
                                        </li>
                                        @if($value['type'] == 3)
                                            <li class="list-inline-item">
                                                <button class="btn edit-delete-btn play-btn-top video" data-toggle="modal" data-target="#videoModal" data-video="{{ $value['video'] }}" data-image="{{ $value['image'] }}">
                                                    <i class="fa-solid fa-eye fa-xl" class="dot-icon"></i>
                                                </button>
                                            </li>
                                        @endif
                                        <li class="list-inline-item">
                                            <a class="btn edit-delete-btn" href="{{ $value['redirect_uri']}}" target="_blank">
                                                <i class="fa-solid fa-up-right-from-square fa-xl" class="dot-icon"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a class="btn edit-delete-btn" href="{{route('user.ads.edit', [$value->id])}}">
                                                <i class="fa-solid fa-gauge fa-xl" class="dot-icon"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a class="btn edit-delete-btn" href="{{route('user.ads.show', [$value->id])}}" onclick="return confirm('{{__('label.delete_ads')}}')">
                                                <i class="fa-solid fa-trash-can fa-xl" class="dot-icon"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="modal fade" id="videoModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-body p-0 bg-transparent">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="text-dark">&times;</span>
                            </button>
                            <video controls width="800" height="500" preload='none' poster="" id="theVideo" controlsList="nodownload noplaybackrate" disablepictureinpicture>
                                <source src="">
                            </video>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center">
                <div>Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of total {{$data->total()}} entries</div>
                <div class="pb-5"> {{ $data->links() }} </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        $(".video").click(function() {
            var theModal = $(this).data("target"),
                videoSRC = $(this).attr("data-video"),
                videoPoster = $(this).attr("data-image"),
                videoSRCauto = videoSRC + "";

            $(theModal + ' source').attr('src', videoSRCauto);
            $(theModal + ' video').attr('poster', videoPoster);
            $(theModal + ' video').load();
            $(theModal + ' button.close').click(function() {
                $(theModal + ' source').attr('src', videoSRC);
            });
        });
        $("#videoModal .close").click(function() {
            theVideo.pause()
        });
    </script>
@endsection