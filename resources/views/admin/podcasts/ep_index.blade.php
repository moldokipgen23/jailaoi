@extends('admin.layout.page-app')
@section('page_title', __('label.episodes'))
@section('tab_title', __('label.episodes'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.episodes')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.podcasts.index') }}">{{__('label.podcasts')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.episodes')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.podcast.episode.add', $podcasts_id) }}" class="btn btn-default mw-120" style="margin-top: -14px;">{{__('label.add_episode')}}</a>
                </div>
            </div>

            <!-- Search -->
            <form action="{{ route('admin.podcast.episode.index', $podcasts_id)}}" method="GET">
                <div class="page-search">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-magnifying-glass fa-xl"></i>
                            </span>
                        </div>
                        <input type="text" name="input_search" value="{{ $_GET['input_search'] ?? '' }}" class="form-control" placeholder="{{__('label.search')}}" aria-label="Search" aria-describedby="basic-addon1">
                    </div>
                    <div class="mr-3 ml-5">
                        <button class="btn btn-default" type="submit">{{__('label.search')}}</button>
                    </div>
                    <div>
                        <button type="button" data-toggle="modal" data-target="#sortOrderModal" class="btn btn-default" style="border-radius: 10px;">
                            <i class="fa-solid fa-arrow-up-wide-short fa-1x"></i>
                        </button>
                    </div>
                </div>
            </form>

            <div class="row">
                @foreach ($data as $key => $value)
                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                    <div class="card video-card">
                        <div class="position-relative">
                            <img class="card-img-top" src="{{$value['portrait_img']}}">
                            @if($value->episode_upload_type == "server_audio")
                                <button class="btn play-btn-top video" data-toggle="modal" data-target="#videoModal" data-video="{{$value['episode_audio']}}" data-image="{{$value['landscape_img']}}">
                                    <i class="fa-regular fa-circle-play text-white fa-4x mr-2 mt-2"></i>
                                </button>
                            @endif

                            <ul class="list-inline overlap-control" aria-labelledby="dropdownMenuLink">
                                <li class="list-inline-item">
                                    <a class="btn" href="{{route('admin.podcast.episode.edit', [$podcasts_id, $value->id])}}">
                                        <i class="fa-solid fa-pen-to-square fa-xl primary-color" class="dot-icon"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="btn" href="{{route('admin.podcast.episode.delete', [$podcasts_id, $value->id])}}" onclick="return confirm('{{__('label.delete_episode')}}')">
                                        <i class="fa-solid fa-trash-can fa-xl primary-color" class="dot-icon"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">{{$value['name']}}</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                @if($value['status'] == 1)
                                    <button class="btn show-btn" id="{{$value['id']}}" onclick="change_status({{$value['id']}}, {{$value['status']}})">{{__('label.show')}}</button>
                                @elseif($value['status'] == 0)
                                    <button class="btn hide-btn" id="{{$value['id']}}" onclick="change_status({{$value['id']}}, {{$value['status']}})">{{__('label.hide')}}</button>
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

            <!-- Audio Model  -->
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

            <!-- Sortable -->
            <div class="modal fade" id="sortOrderModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="sortOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title w-100 text-center" id="sortOrderModalLabel">{{__('label.episode_sortorder_list')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close">
                                <span aria-hidden="true" class="text-dark">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="contentListId">
                                @foreach ($data as $key => $value)
                                <div id="{{ $value['id'] }}" class="listitemClass mb-2" style="background-color: #e9ecef;border: 1px solid black;cursor: s-resize;">
                                    <p class="m-3">{{ $value['name'] }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <form enctype="multipart/form-data" id="save_episode_sortable">
                                @csrf
                                <input id="outputvalues" type="hidden" name="ids" value="" />
                                <div class="w-100 text-center">
                                    <button type="button" class="btn btn-default mw-120" onclick="save_episode_sortable()">{{__('label.save')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center">
                <div> Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of total {{$data->total()}} entries </div>
                <div class="pb-5"> {{ $data->links() }} </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Sortorder -->
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

    <script>
        // Sidebar Scroll Down
		sidebar_down(350);

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

        function change_status(id) {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var url = `{{ route('admin.podcast.episode.status', '') }}/${id}`;

                $.ajax({
                    type: "GET",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
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

        $("#contentListId").sortable({
            update: function(event, ui) {
                getIdsOfContent();
            }
        });
        function getIdsOfContent() {
            var values = [];
            $('.listitemClass').each(function(index) {
                values.push($(this).attr("id")
                    .replace("imageNo", ""));
            });
            $('#outputvalues').val(values);
        }
        function save_episode_sortable() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#save_episode_sortable")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.podcast.episode.sort_order") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_episode_sortable', '{{ route("admin.podcast.episode.index", $podcasts_id)}}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                showError();
            }
        }
    </script>
@endsection