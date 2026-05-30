@extends('admin.layout.page-app')
@section('page_title', __('label.reels'))
@section('tab_title', __('label.reels'))

@section('content')
    @include('admin.layout.sidebar')

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.reels')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.reels')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.reels.create') }}" class="btn btn-default mw-120" style="margin-top: -14px;">{{__('label.add_reels')}}</a>
                </div>
            </div>

            <!-- Search -->
            <form action="{{ route('admin.reels.index')}}" method="GET">
                <div class="page-search">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-magnifying-glass fa-xl"></i>
                            </span>
                        </div>
                        <input type="text" name="input_search" value="{{ $_GET['input_search'] ?? '' }}" class="form-control" placeholder="{{__('label.search')}}" aria-label="Search" aria-describedby="basic-addon1">
                    </div>
                    <div class="sorting mr-2 w-75">
                        <label>{{__('label.sort_by')}}</label>
                        <select class="form-control" name="input_channel" id="input_channel">
                            <option value="0" selected>{{__('label.all_channel')}}</option>
                            @for ($i = 0; $i < count($channel); $i++) 
                            <option value="{{ $channel[$i]['channel_id'] }}" {{ request('input_channel') == $channel[$i]['channel_id'] ? 'selected' : ''}}>
                                {{ $channel[$i]['channel_name'] }}
                            </option>
                            @endfor
                        </select>
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
                                    <a class="btn" href="{{route('admin.reels.edit', [$value->id])}}">
                                        <i class="fa-solid fa-pen-to-square fa-xl primary-color" class="dot-icon"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="btn" href="{{route('admin.reels.show', [$value->id])}}" onclick="return confirm('{{__('label.delete_reels')}}')">
                                        <i class="fa-solid fa-trash-can fa-xl primary-color" class="dot-icon"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">{{ $value['title'] }}</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                @if($value['status'] == 1)
                                    <button class="btn show-btn" id="{{$value['id']}}" onclick="change_status({{$value['id']}}, {{$value['status']}})">{{__('label.show')}}</button>
                                @elseif($value['status'] == 0)
                                    <button class="btn hide-btn" id="{{$value['id']}}" onclick="change_status({{$value['id']}}, {{$value['status']}})">{{__('label.hide')}}</button>
                                @endif

                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-flex align-items-center text-muted" style="gap:4px;font-size:13px">
                                        <i class="fa-solid fa-thumbs-up"></i>
                                        {{ No_Format($value['total_like']) }}
                                    </span>
                                    <span class="d-flex align-items-center text-muted" style="gap:4px;font-size:13px">
                                        <i class="fa-regular fa-eye"></i>
                                        {{ No_Format($value['total_view']) }}
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
                            <video controls width="800" height="500" preload='none' poster="" id="theVideo" controlsList="nodownload noplaybackrate" disablepictureinpicture autoplay>
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
        // Sidebar Scroll Down
		sidebar_down(350);

        $("#input_channel").select2();

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

        function change_status(id, status) {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "GET",
                    url: "{{route('admin.reels.status')}}",
                    data: {id: id},
                    success: function(resp) {
                        $("#dvloader").hide();
                        
                        if (resp.status == 200) {
                            if (resp.status_code == 1) {
                                $('#' + id).text('{{__("label.show")}}').removeClass('hide-btn').addClass('show-btn');
                            } else {
                                $('#' + id).text('{{__("label.hide")}}').removeClass('show-btn').addClass('hide-btn');
                            }
                            toastr.success(resp.success);
                        } else {
                            toastr.error(resp.errors);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                showError();
            }
        };
    </script>
@endsection