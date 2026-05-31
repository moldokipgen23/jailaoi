@extends('admin.layout.page-app')
@section('page_title', __('label.content_report'))
@section('tab_title', __('label.content_report'))

@section('content')
    @include('admin.layout.sidebar')

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.content_report')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.content_report')}}</li>
                    </ol>
                </div>
            </div>

            <!-- Search -->
            <form action="{{ route('admin.contentreport.index')}}" method="GET">
                <div class="page-search">
                    <div class="sorting mr-2 w-50">
                        <label>{{__('label.sort_by')}}</label>
                        <select class="form-control" name="input_user" id="input_user">
                            <option value="0" selected>{{__('label.all_users')}}</option>
                            @for ($i = 0; $i < count($user); $i++) 
                            <option value="{{ $user[$i]['id'] }}" {{ request('input_user') == $user[$i]['id'] ? 'selected' : ''}}>
                                {{ $user[$i]['channel_name'] }}
                            </option>
                            @endfor
                        </select>
                    </div>
                    <div class="sorting mr-2 w-50">
                        <select class="form-control" name="input_report_user" id="input_report_user">
                            <option value="0" selected>{{__('label.all_report_users')}}</option>
                            @for ($i = 0; $i < count($user); $i++) 
                            <option value="{{ $user[$i]['id'] }}" {{ request('input_report_user') == $user[$i]['id'] ? 'selected' : ''}}>
                                {{ $user[$i]['channel_name'] }}
                            </option>
                            @endfor
                        </select>
                    </div>
                    <div class="sorting mr-2 w-25">
                        <select class="form-control" name="input_type" id="input_type">
                            <option value="0" {{ request('input_type') == 0 ? 'selected' : ''}}>{{__('label.all_content')}}</option>
                            <option value="1" {{ request('input_type') == 1 ? 'selected' : ''}}>{{__('label.video')}}</option>
                            <option value="2" {{ request('input_type') == 2 ? 'selected' : ''}}>{{__('label.music')}}</option>
                            <option value="3" {{ request('input_type') == 3 ? 'selected' : ''}}>{{__('label.reels')}}</option>
                            <option value="4" {{ request('input_type') == 3 ? 'selected' : ''}}>{{__('label.podcasts')}}</option>
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
                                @if($value['content_type'] == 1)
                                    <div class="ribbon ribbon-top-left"><span>{{__('label.video')}}</span></div>
                                @elseif($value['content_type'] == 2)
                                    <div class="ribbon ribbon-top-left"><span>{{__('label.music')}}</span></div>
                                @elseif($value['content_type'] == 3)
                                    <div class="ribbon ribbon-top-left"><span>{{__('label.reels')}}</span></div>
                                @elseif($value['content_type'] == 4)
                                    <div class="ribbon ribbon-top-left"><span>{{__('label.podcasts')}}</span></div>
                                @endif

                                @if($value['content_type'] == 4)
                                    <img src="{{ $value['episode_img'] }}" class="wallet-image">
                                @else
                                    <img src="{{ $value['portrait_img'] }}" class="wallet-image">
                                @endif

                                <div class="card-body px-2 py-0">
                                    <h6 class="landscape-card-title">{{$value['content']['title'] ?? ''}}</h6>
                                    @if($value['content_type'] == 4)
                                        <p class="landscape-card-name mb-0">{{$value['episode']['name'] ?? ''}}</p>
                                    @endif
                                    <h6 class="primary-color">{{$value['report_user']['channel_name'] ?? ''}}</h6>
                                    <div class="landscape-card-border"></div>
                                    <h6 class="primary-color">{{$value['user']['channel_name'] ?? ''}}</h6>
                                    <div class="landscape-card-border"></div>
                                    <h6 class="landscape-card-title">{{$value['message'] ?? ''}}</h6>
                                </div>
                            </div>
                            <ul class="list-inline overlap-control mb-0 mt-2" aria-labelledby="dropdownMenuLink">
                                <li class="list-inline-item">
                                    @if($value['content_type'] == 4)
                                        @if(isset($value['episode']) && $value['episode']['status'] == 1)
                                            <button type="button" class="show-btn" id="{{$value['content']['id']}}" onclick="change_status({{$value['content']['id']}}, {{$value['episode']['id']}})">{{__('label.show')}}</button>
                                        @elseif(isset($value['episode']) && $value['episode']['status'] == 0)
                                            <button type="button" class="hide-btn" id="{{$value['content']['id']}}" onclick="change_status({{$value['content']['id']}}, {{$value['episode']['id']}})">{{__('label.hide')}}</button>
                                        @endif
                                    @else
                                        @if(isset($value['content']) && $value['content']['status'] == 1)
                                            <button type="button" class="show-btn" id="{{$value['content']['id']}}" onclick="change_status({{$value['content']['id']}}, 0)">{{__('label.show')}}</button>
                                        @elseif(isset($value['content']) && $value['content']['status'] == 0)
                                            <button type="button" class="hide-btn" id="{{$value['content']['id']}}" onclick="change_status({{$value['content']['id']}}, 0)">{{__('label.hide')}}</button>
                                        @endif
                                    @endif
                                </li>
                                @if($value['content_type'] == 4 && isset($value['episode']) && $value['episode']['episode_upload_type'] == 'server_audio')
                                    <li class="list-inline-item">
                                        <button class="btn edit-delete-btn play-btn-top video" data-toggle="modal" data-target="#videoModal" data-video="{{ $value['episode_video'] }}" data-image="{{ $value['episode_img'] }}">
                                            <i class="fa-solid fa-eye fa-xl" class="dot-icon"></i>
                                        </button>
                                    </li>
                                @elseif(isset($value['content']) && $value['content']['content_upload_type'] == 'server_video')
                                    <li class="list-inline-item">
                                        <button class="btn edit-delete-btn play-btn-top video" data-toggle="modal" data-target="#videoModal" data-video="{{ $value['video'] }}" data-image="{{ $value['portrait_img'] }}">
                                            <i class="fa-solid fa-eye fa-xl" class="dot-icon"></i>
                                        </button>
                                    </li>
                                @endif
                                <li class="list-inline-item">
                                    <a class="btn edit-delete-btn" href="{{route('admin.contentreport.show', [$value->id])}}" onclick="event.preventDefault(); confirmLink(this.href, '{{__('label.delete')}}', '{{__('label.delete_content_report')}}')">
                                        <i class="fa-solid fa-trash-can fa-xl" class="dot-icon"></i>
                                    </a>
                                </li>
                            </ul>
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
                <div> Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of total {{$data->total()}} entries </div>
                <div class="pb-5"> {{ $data->links() }} </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        // Sidebar Scroll Down
		sidebar_down(850);

        $("#input_user").select2();
        $("#input_report_user").select2();

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

        function change_status(content_id, episode_id) {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{route('admin.contentreport.status')}}",
                    data: {content_id: content_id, episode_id: episode_id},
                    success: function(resp) {
                        $("#dvloader").hide();
                        if (resp.status == 200) {
                            if (resp.status_code == 1) {
                                $('#' + content_id).text('{{__("label.show")}}').removeClass('hide-btn').addClass('show-btn');
                            } else {
                                $('#' + content_id).text('{{__("label.hide")}}').removeClass('show-btn').addClass('hide-btn');
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