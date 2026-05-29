@extends('user.layout.page-app')
@section('page_title', __('label.reels'))
@section('tab_title', __('label.reels'))

@section('content')
    @include('user.layout.sidebar')

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.reels')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.reels')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('user.reels.create') }}" class="btn btn-default mw-120" style="margin-top: -14px;">{{__('label.add_reels')}}</a>
                </div>
            </div>

            <!-- Search -->
            <form action="{{ route('user.reels.index')}}" method="GET">
                <div class="page-search">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-magnifying-glass fa-xl"></i>
                            </span>
                        </div>
                        <input type="text" name="input_search" value="{{ $_GET['input_search'] ?? '' }}" class="form-control" placeholder="{{__('label.search')}}" aria-label="Search" aria-describedby="basic-addon1">
                    </div>
                    <button class="btn btn-default mx-2" type="submit">{{__('label.search')}}</button>
                </div>
            </form>

            <div class="row">
                @foreach ($data as $key => $value)
                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                    <div class="card video-card">
                        <div class="position-relative">

                            <img class="card-img-top" src="{{ $value['portrait_img'] }}">

                            @if($value['content_upload_type'] == "server_video")
                                <button class="btn play-btn-top video" data-toggle="modal" data-target="#videoModal" data-video="{{ $value['content'] }}" data-image="{{ $value['landscape_img'] }}">
                                    <i class="fa-regular fa-circle-play text-white fa-4x mr-2 mt-2"></i>
                                </button>
                            @endif

                            <ul class="list-inline overlap-control" aria-labelledby="dropdownMenuLink">
                                <li class="list-inline-item">
                                    <a class="btn" href="{{route('user.reels.edit', [$value->id])}}">
                                        <i class="fa-solid fa-pen-to-square fa-xl primary-color" class="dot-icon"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="btn" href="{{route('user.reels.show', [$value->id])}}" onclick="return confirm('{{__('label.delete_reels')}}')">
                                        <i class="fa-solid fa-trash-can fa-xl primary-color" class="dot-icon"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">{{ $value['title'] }}</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                @if($value['status'] == 1)
                                    <button class="btn show-btn">{{__('label.show')}}</button>
                                @elseif($value['status'] == 0)
                                    <button class="btn hide-btn">{{__('label.hide')}}</button>
                                @endif

                                <div class="d-flex text-align-center">
                                    <span class="d-flex text-align-center mr-3">
                                        <i class="fa-solid fa-thumbs-up fa-xl mr-3 primary-color" style="margin-top:12px"></i>
                                        <h5>{{ No_Format($value['total_like']) }}</h5>
                                    </span>
                                    <span class="d-flex text-align-center">
                                        <i class="fa-regular fa-eye fa-xl mr-3 primary-color" style="margin-top:12px"></i>
                                        <h5>{{ No_Format($value['total_view']) }}</h5>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center">
                <div> Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of total {{$data->total()}} entries </div>
                <div class="pb-5"> {{ $data->links() }} </div>
            </div>

            <!-- Reels Model -->
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
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(function() {
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
        });
        $("#videoModal .close").click(function() {
            theVideo.pause()
        });
    </script>
@endsection