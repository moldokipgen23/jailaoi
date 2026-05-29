@extends('admin.layout.page-app')
@section('page_title', 'Songs')

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">Songs</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Songs</li>
                    </ol>
                </div>
            </div>

            <!-- Search -->
            <form action="{{route('song.index')}}" method="GET">
                <div class="page-search mb-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-magnifying-glass fa-xl light-gray"></i>
                            </span>
                        </div>
                        <input type="text" name="input_search" value="@if(isset($_GET['input_search'])){{$_GET['input_search']}}@endif" class="form-control" placeholder="Search Songs" aria-label="Search" aria-describedby="basic-addon1">
                    </div>
                    <div class="sorting" style="width: 500px;">
                        <label>Sort by :</label>
                        <select class="form-control" name="input_artist" id="artist_id">
                            <option value="0" selected>All Artist</option>
                            @for ($i = 0; $i < count($artist); $i++) 
                                <option value="{{ $artist[$i]['id'] }}" @if(isset($_GET['input_artist'])){{ $_GET['input_artist'] == $artist[$i]['id'] ? 'selected' : ''}} @endif>
                                    {{ $artist[$i]['name'] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="mr-3 ml-5">
                        <button class="btn btn-default" type="submit"> {{__('Label.SEARCH')}} </button>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                    <a href="{{ route('song.create') }}" class="add-video-btn">
                        <i class="fa-regular fa-square-plus fa-3x icon" style="color: #818181;"></i>
                        Add New Radio Station
                    </a>
                </div>

                @foreach ($data as $key => $value)
                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                    <div class="card video-card">
                        <div class="position-relative">
                            <img class="card-img-top" src="{{$value->image}}" alt="">
                            @if($value->song_upload_type == "server_video")
                            <button class="btn play-btn-top video" data-toggle="modal" data-target="#videoModal" data-video="{{$value->song_url}}" data-image="{{$value->image}}">
                                <i class="fa-regular fa-circle-play text-white fa-4x mr-2 mt-2"></i>
                            </button>
                            @endif
                            <ul class="list-inline overlap-control" aria-labelledby="dropdownMenuLink">
                                <li class="list-inline-item">
                                    <a class="btn" href="{{route('song.edit', [$value->id])}}" title="Edit">
                                        <i class="fa-solid fa-pen-to-square fa-xl" class="dot-icon" style="color: #4e45b8;"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="btn" href="{{route('song.show', [$value->id])}}" title="Delete" onclick="return confirm('Are you sure !!! You want to Delete this Radio Station ?')">
                                        <i class="fa-solid fa-trash-can fa-xl" class="dot-icon" style="color: #4e45b8;"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{$value->name}}</h5>
                            <div class="d-flex justify-content-between">
                                <span class="d-flex text-align-center mr-3">
                                    <h6>{{$value->artist->name}}</h5>
                                </span>
                                
                                <div class="d-flex text-align-center">
                                    <span class="d-flex text-align-center">
                                        <i class="fa-solid fa-play fa-xl mr-3" style="color:#4e45b8; margin-top:12px"></i>
                                        <h5 class="counting" data-count="{{No_Format($value->total_play ?? 0)}}">{{No_Format($value->total_play)}}</h5>
                                    </span>
                                </div>
                            </div>
                            @if($value->status == 1)
                            <button class="btn btn-sm px-3" id="{{$value->id}}" onclick="change_status('{{ $value->id }}', '{{ $value->status }}')" style="background:#058f00; color:#fff; font-weight:bold; border:none">{{__('Label.show')}}</button>
                            @elseif($value->status == 0)
                            <button class="btn btn-sm px-3" id="{{$value->id}}" onclick="change_status('{{ $value->id }}', '{{ $value->status }}')" style="background:#e3000b; color:#fff; font-weight:bold; border:none">{{__('Label.hide')}}</button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach

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

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center">
                <div> Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of total {{$data->total()}} entries </div>
                <div class="pb-5"> {{ $data->links('pagination::bootstrap-4') }} </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        $("#artist_id").select2();

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
        var isAdmin = <?php echo Check_Admin_Access(); ?>;
        if (isAdmin == 1) {
            $("#dvloader").show();
            var url = "{{route('song.status', '')}}" + "/" + id;
            $.ajax({
                type: "GET",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: id,
                success: function(resp) {
                    $("#dvloader").hide();

                    if (resp.status == 200) {

                        if (resp.Status == 1) {
                            $('#' + id).text("{{__('Label.show')}}");
                            $('#' + id).css({
                                "background": "#058f00",
                                "color": "white",
                                "font-weight": "bold",
                                "border": "none"
                            });
                        } else {
                            $('#' + id).text("{{__('Label.hide')}}");
                            $('#' + id).css({
                                "background": "#e3000b",
                                "color": "white",
                                "font-weight": "bold",
                                "border": "none"
                            });
                        }
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
            toastr.error('{{__("Label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    };
    </script>
@endsection