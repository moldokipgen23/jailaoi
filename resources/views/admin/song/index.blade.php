@extends('admin.layout.page-app')
@section('page_title',__('label.radio_station'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.radio_station')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.radio_station')}}</li>
                </ol>
            </div>
        </div>
        <!-- search -->
        <form action="{{route('song.index')}}" method="GET">
            <div class="page-search mb-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fa-solid fa-magnifying-glass fa-xl light-gray"></i>
                        </span>
                    </div>
                    <input type="text" name="input_search" value="@if(isset($_GET['input_search'])){{$_GET['input_search']}}@endif" class="form-control" placeholder="{{__('label.search_radio_station')}}" aria-label="Search" aria-describedby="basic-addon1">
                </div>
                <div class="mr-3 ml-3">
                    <button class="btn btn-default" type="submit"> {{__('label.search')}} </button>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap mb-3" style="gap: 12px;">
                <div class="sorting mb-0" style="min-width: 220px; flex: 1;">
                    <label>{{__('label.sort_by')}}</label>
                    <select class="form-control" name="input_artist" id="artist_id">
                        <option value="0" selected>{{__('label.all_artist')}}</option>
                        @foreach($artist as $key=>$value){
                        <option value="{{$value['id']}}" @if(isset($_GET['input_artist'])){{$_GET['input_artist']==$value['id'] ? "selected" : ""}}@endif>{{$value['name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sorting mb-0" style="min-width: 200px; flex: 1;">
                    <label>{{__('label.category')}}</label>
                    <select class="form-control" name="input_category" id="category_id">
                        <option value="0">{{__('label.all_category')}}</option>
                        @foreach($category as $key=>$value){
                        <option value="{{$value['id']}}" @if(isset($_GET['input_category'])){{$_GET['input_category']==$value['id'] ? "selected" : ""}}@endif>{{$value['name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sorting mb-0" style="min-width: 180px; flex: 1;">
                    <label>{{__('label.language')}}</label>
                    <select class="form-control" name="input_language" id="language_id">
                        <option value="0">{{__('label.all_language')}}</option>
                        @foreach($language as $key=>$value){
                        <option value="{{$value['id']}}" @if(isset($_GET['input_language'])){{$_GET['input_language']==$value['id'] ? "selected" : ""}}@endif>{{$value['name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <a href="{{ route('song.create') }}" class="add-video-btn">
                    <i class="fa-regular fa-square-plus fa-3x icon text-gray"></i>
                    {{__('label.add_new_radio_station')}}
                </a>
            </div>
            <!-- song list  -->
            @foreach ($data as $key => $value)
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card video-card">
                    <div class="position-relative">
                        <img class="card-img-top" src="{{$value->image}}" alt="">
                        @if($value->upload_type == 1)
                        <button class="btn play-btn-top video" data-toggle="modal" data-target="#videoModal" data-video="{{$value->song_url}}" data-image="{{$value->image}}">
                            <i class="fa-regular fa-circle-play text-white fa-4x mr-2 mt-2"></i>
                        </button>
                        @endif
                        <ul class="list-inline overlap-control" aria-labelledby="dropdownMenuLink">
                            <li class="list-inline-item">
                                <a class="btn" href="{{route('song.edit', [$value->id])}}" title="{{__('label.edit')}}">
                                    <i class="fa-solid fa-pen-to-square fa-xl dot-icon primary-color"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a class="btn" href="{{route('song.show', [$value->id])}}" title="{{__('label.delete')}}" onclick='return confirm("{{ __('label.delete_radio_station') }}")'>
                                    <i class="fa-solid fa-trash-can fa-xl dot-icon primary-color"></i>
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
                                <span class="d-flex align-items-center">
                                    <i class="fa-solid fa-play fa-xl mr-3 primary-color mb-1"></i>
                                    <h5 class="counting" data-count="{{No_Format($value->total_play ?? 0)}}">{{No_Format($value->total_play)}}</h5>
                                </span>
                            </div>
                        </div>
                        <div class="switch justify-content-start">
                            <input class="status-checkbox" id="checkbox{{$value->id}}" data-id="{{$value->id}}" type="checkbox" {{$value->status==1?"checked" : ""}}>
                            <label for="checkbox{{$value->id}}"></label>
                            <span class="toggle-text"
                                data-on="{{__('label.show')}}"
                                data-off="{{ __('label.hide')}}"></span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            <!-- video modal  -->
            <div class="modal fade" id="videoModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModallabel" aria-hidden="true">
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
    $("#artist_id").select2({ width: '100%' });
    $("#category_id").select2({ width: '100%' });
    $("#language_id").select2({ width: '100%' });

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
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    };

    $(document).on('change', '.status-checkbox', function() {
        id = $(this).data('id');
        change_status(id);
    })
</script>
@endsection