@extends('admin.layout.page-app')
@section('page_title', __('label.playlist_content'))
@section('tab_title', __('label.playlist_content'))

@section('content')
    @include('admin.layout.sidebar')

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.playlist_content')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.playlist.index') }}">{{__('label.playlists')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.playlist_content')}}</li>
                    </ol>
                </div>
            </div>
                
            <!-- Add Content -->
            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('label.add_content')}}</h5>
                <div class="card-body">
                    <form id="content" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="playlist_id" value="{{ $playlist_id }}">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.playlist')}}</label>
                                    <input type="text" value="{{ $playlist_name ?? '' }}" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{__('label.type')}}<span class="text-danger">*</span></label>
                                    <select name="content_type" class="form-control" id="content_type">
                                        <option value="">{{__('label.select_content_type')}}</option>
                                        <option value="1">{{__('label.videos')}}</option>
                                        <option value="2">{{__('label.music')}}</option>
                                        <option value="4">{{__('label.podcasts')}}</option>
                                        <option value="6">{{__('label.radio')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label>{{__('label.content')}}<span class="text-danger">*</span></label>
                                    <select name="content[]" class="form-control" id="content_id" style="width:100%!important;" multiple></select>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="save_content()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>

            <!-- List-->
            <div class="card custom-border-card mt-3">
                <h5 class="card-header mb-3">{{__('label.content_list')}}</h5>
                @if(count($data) > 0 && $data != null)
                    <div id="ListId">
                        @foreach ($data as $key => $value)
                            @if($value->content != null && isset($value->content))
                                <div id="{{ $value['id'] }}" class="row listitemClass mb-2" style="background-color: #e9ecef;border: 1px solid black;cursor: s-resize;">
                                    <div class="col-md-10 mt-2">
                                        <h6>
                                            <i class="fa-solid fa-sort fa-xl mr-4"></i>
                                            <img src="{{$value->content->portrait_img}}" width="50px" height="50px" class="mr-3" style="border-radius: 10%;">
                                            {{ String_Cut($value->content->title, 130) }}
                                        </h6>
                                    </div>
                                    <div class="col-md-2 mt-2 text-right d-flex align-items-center justify-content-end">
                                        <h6 class="primary-color">
                                            @if($value->content_type == 1)
                                                {{__('label.videos')}}
                                            @elseif ($value->content_type == 2)
                                                {{__('label.music')}}
                                            @elseif ($value->content_type == 4)
                                                {{__('label.podcasts')}}
                                            @elseif ($value->content_type == 6)
                                                {{__('label.radio')}}
                                            @else
                                                -
                                            @endif
                                            <i class="fa-solid fa-trash-can fa-2xl ml-2 text-dark" style="cursor: pointer;" onclick="delete_content('{{$value->id}}')"></i>
                                        </h6>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center p-3" style="background-color: #e9ecef;">
                        <h2>{{__('label.data_not_available')}}</h2>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Sortorder -->
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

    <script>
        // Sidebar Scroll Down
		sidebar_down(350);

        $("#content_id").select2({placeholder: "{{__('label.select_content')}}"});

        // get data
        $("#content_type").change(function() {

            $("#content_id").empty();

            var content_type = $(this).children("option:selected").val();
            if(content_type == 1 || content_type == 2 || content_type == 4 || content_type == 6){

                playlist_id = '<?php echo $playlist_id; ?>';

                $.ajax({
                    headers: {
					    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				    },
				    enctype: 'multipart/form-data',
                    type: 'post',
                    url: '{{ route("admin.playlist.get.content") }}',
                    data: {content_type:content_type, playlist_id:playlist_id},
                    success: function(resp) {

                        for (var i = 0; i < resp.data.length; i++) {
                            $('#content_id').append(
                                `<option value="${resp.data[i].id}">${resp.data[i].title}</option>`
                            );
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown, textStatus);
                    }
                });
            }
        });
        // save data
        function save_content(){

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#content")[0]);
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.playlist.content.save") }}',
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'content', '{{ route("admin.playlist.content.index", $playlist_id) }}');
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
        // delete data
        function delete_content(id){

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                var playlist_id = '<?php echo $playlist_id; ?>';

                $.ajax({
                    headers: {
					    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				    },
				    enctype: 'multipart/form-data',
                    type: 'POST',
                    url: '{{ route("admin.playlist.content.delete") }}',
                    data: {id:id, playlist_id:playlist_id},
                    success: function(resp) {

                        toastr.success(resp.success);
                        $('#' + resp.id).remove();
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                showError();
            }
        }

        // sortable
        $("#ListId").sortable({
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

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $.ajax({
                    headers: {
					    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				    },
				    enctype: 'multipart/form-data',
                    type: 'POST',
                    url: '{{ route("admin.playlist.content.sort_order") }}',
                    data: {ids:values},
                    success: function(resp) {
                        toastr.success(resp.success);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                showError();
            }
        }
    </script>
@endsection