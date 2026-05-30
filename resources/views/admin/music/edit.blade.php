@extends('admin.layout.page-app')
@section('page_title', __('label.edit_music'))
@section('tab_title', __('label.edit_music'))

@section('content')
    @include('admin.layout.sidebar')

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    <!-- Date Time Picker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> 

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.edit_music')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.music.index') }}">{{__('label.music')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.edit_music')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.music.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('label.music_list')}}</a>
                </div>
            </div>

            <form id="music" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{{ $data['id'] }}">
                <input type="hidden" name="old_hashtag_id" value="{{ $data['hashtag_id'] }}">
                <input type="hidden" name="old_portrait_img" value="{{ $data['portrait_img'] }}">
                <input type="hidden" name="old_landscape_img" value="{{ $data['landscape_img'] }}">
                <input type="hidden" name="old_content" value="{{ $data['content_file'] }}">
                <input type="hidden" name="old_content_upload_type" value="{{ $data['content_upload_type'] }}">
                <input type="hidden" name="old_portrait_img_storage_type" value="{{ $data['portrait_img_storage_type'] }}">
                <input type="hidden" name="old_landscape_img_storage_type" value="{{ $data['landscape_img_storage_type'] }}">
                <input type="hidden" name="old_content_storage_type" value="{{ $data['content_storage_type'] }}">
                <div class="card custom-border-card mt-3">
                    <div class="card-body">
                    <div class="form-row">
                        <div class="col-md-10">
                            <div class="form-row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="title" value="{{ $data['title'] }}" class="form-control" placeholder="{{__('label.title_here')}}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.channel')}}<span class="text-danger">*</span></label>
                                        <select name="channel_id" id="channel_id" class="form-control" style="width:100%!important;">
                                            <option value="">{{__('label.select_channel')}}</option>
                                            @foreach ($channel as $key => $value)
                                                <option value="{{$value['channel_id']}}" {{ $data['channel_id'] == $value['channel_id'] ? 'selected' : ''}}>
                                                    {{ $value['channel_name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.description')}}</label>
                                        <textarea name="description" class="form-control" rows="6" placeholder="{{__('label.description_here')}}">{{ strip_tags($data['description']) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Lyrics</label>
                                        <textarea name="lyrics" class="form-control" rows="6" placeholder="Add lyrics here...">{{ $data['lyrics'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Album</label>
                                        <select name="album_id" class="form-control">
                                            <option value="">None</option>
                                            @php $albums = \App\Models\Album::where('status', 1)->get(); @endphp
                                            @foreach ($albums as $album)
                                                <option value="{{ $album->id }}" {{ ($data['album_id'] ?? '') == $album->id ? 'selected' : '' }}>{{ $album->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{__('label.category')}}<span class="text-danger">*</span></label>
                                        <select name="category_id" id="category_id" class="form-control" style="width:100%!important;">
                                            <option value="">{{__('label.select_category')}}</option>
                                            @foreach ($category as $key => $value)
                                                <option value="{{$value['id']}}" {{ $data['category_id'] == $value['id'] ? 'selected' : ''}}>
                                                    {{ $value['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{__('label.language')}}<span class="text-danger">*</span></label>
                                        <select name="language_id" id="language_id" class="form-control" style="width:100%!important;">
                                            <option value="">{{__('label.select_language')}}</option>
                                            @foreach ($language as $key => $value)
                                                <option value="{{$value['id']}}" {{ $data['language_id'] == $value['id'] ? 'selected' : ''}}>
                                                    {{ $value['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group ml-4">
                                <label>{{__('label.portrait_image')}}</label>
                                <div class="avatar-upload">
                                    <div class="avatar-edit">
                                        <input type='file' name="portrait_img" id="imageUpload1" accept=".png, .jpg, .jpeg, .webp" />
                                        <label for="imageUpload1" title="{{__('label.upload_file')}}"></label>
                                    </div>
                                    <div class="avatar-preview">
                                        <img src="{{ $data['portrait_img'] }}" id="imagePreview1">
                                    </div>
                                </div>
                                <label class="mt-3 text-gray">{{__('label.max_size_5mb')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-10">
                            <div class="form-row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.upload_type')}}<span class="text-danger">*</span></label>
                                        <select class="form-control" name="content_upload_type" id="content_upload_type">
                                            <option value="server_video" {{ $data['content_upload_type'] == "server_video" ? 'selected' : ''}}>{{__('label.server_audio')}}</option>
                                            <option value="external_url" {{ $data['content_upload_type'] == "external_url" ? 'selected' : ''}}>{{__('label.external_url')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 video_box">
                                    <div class="form-group">
                                        <div style="display: block;">
                                            <label>{{__('label.upload_music')}}<span class="text-danger">*</span></label>
                                            <div id="filelist1"></div>
                                            <div id="container1" style="position: relative;">
                                                <div class="form-group">
                                                    <input type="file" id="uploadFile1" name="uploadFile1" class="form-control import-file p-2">
                                                </div>
                                                <input type="hidden" name="music" id="mp3_file_name1" class="form-control">
                                                @if($data['content_file'])<label class="text-gray">Current: {{ basename($data['content_file']) }}</label><audio controls style="width:100%;height:36px;margin-top:4px;"><source src="{{ $data['content'] }}" type="audio/mpeg"></audio>@endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4 video_box">
                                    <div class="form-group mt-3">
                                        <a id="upload1" class="btn text-white primary-bg">{{__('label.upload_file')}}</a>
                                    </div>
                                </div>
                                <div class="col-md-6 s3_video_box">
                                    <div class="form-group">
                                        <label>{{__('label.upload_music')}}<span class="text-danger">*</span></label>
                                        <input type="file" name="music" class="form-control import-file" accept=".mp3">
                                        @if($data['content_file'])<label class="text-gray">Current: {{ basename($data['content_file']) }}</label><audio controls style="width:100%;height:36px;margin-top:4px;"><source src="{{ $data['content'] }}" type="audio/mpeg"></audio>@endif
                                    </div>
                                </div>
                                <div class="col-md-6 url_box">
                                    <div class="form-group">
                                        <label>{{__('label.url')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="url" value="@if($data['content_upload_type'] != 'server_video'){{{$data['content']}}}@endif" class="form-control" placeholder="{{__('label.url_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.video_duration')}}<span class="text-danger">*</span></label>
                                        <input type="text" id="timePicker" name="content_duration" placeholder="{{__('label.video_duration_here')}}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row mt-4">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.is_comment')}}<span class="text-danger">*</span></label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_comment" id="is_comment_yes" class="custom-control-input" value="1" {{ $data['is_comment'] == 1 ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="is_comment_yes">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_comment" id="is_comment_no" class="custom-control-input" value="0" {{ $data['is_comment'] == 0 ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="is_comment_no">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.is_download')}}<span class="text-danger">*</span></label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_download" id="is_download_yes" class="custom-control-input" value="1" {{ $data['is_download'] == 1 ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="is_download_yes">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_download" id="is_download_no" class="custom-control-input" value="0" {{ $data['is_download'] == 0 ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="is_download_no">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.is_like')}}<span class="text-danger">*</span></label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_like" id="is_like_yes" class="custom-control-input" value="1" {{ $data['is_like'] == 1 ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="is_like_yes">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_like" id="is_like_no" class="custom-control-input" value="0" {{ $data['is_like'] == 0 ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="is_like_no">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{__('label.landscape_image')}}</label>
                                <div class="avatar-upload-landscape">
                                    <div class="avatar-edit-landscape">
                                        <input type='file' name="landscape_img" id="imageUpload2" accept=".png, .jpg, .jpeg, .webp" />
                                        <label for="imageUpload2" title="{{__('label.upload_file')}}"></label>
                                    </div>
                                    <div class="avatar-preview-landscape">
                                        <img src="{{ $data['landscape_img'] }}" id="imagePreview2">
                                    </div>
                                </div>
                                <label class="mt-3 text-gray">{{__('label.max_size_5mb')}}</label>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="save_music()">{{__('label.update')}}</button>
                        <a href="{{ route('admin.music.index') }}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
    					<input type="hidden" name="_method" value="PATCH">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Data Time Picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <!-- Chunk JS -->
    <script src="{{ asset('/assets/js/plupload.full.min.js')}}"></script>
    <script src="{{ asset('/assets/js/common.js')}}"></script>

	<script>
        // Sidebar Scroll Down
		sidebar_down(350);

        $("#channel_id").select2();
        $("#category_id").select2();
        $("#language_id").select2();

        // Time Picker
        var duration = parseInt('<?php echo $data['content_duration'] ?? 0; ?>') || 0;
        let hours = msToHours(duration);
        let minutes = msToMinutes(duration);
        let seconds = msToSeconds(duration);
        var date = new Date();
            date.setHours(hours,minutes,seconds);
        $('#timePicker').datetimepicker({
            useCurrent: false,
            format:'HH:mm:ss',
            defaultDate: date,
            showClose:true,
            showTodayButton: true,
            icons: {
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                today: "fa fa-clock fa-regular",
                close: "fa fa-times",
            }
        })

        $(document).ready(function() {
            var storage_type = "<?php echo Storage_Type(); ?>";
            var content_upload_type = "<?php echo $data['content_upload_type']; ?>";
            if (content_upload_type == "server_video") {
                if(storage_type == 1){
                    $(".s3_video_box").hide();
                } else if(storage_type == 2){
                    $(".video_box").hide();
                }
                $(".url_box").hide();
            } else {
                $(".video_box").hide();
                $(".s3_video_box").hide();
            }
            $('#content_upload_type').change(function() {

                var optionValue = $(this).val();
                if (optionValue == 'server_video') {

                    if (storage_type == 1) {
                        $(".video_box").show();
                        $(".s3_video_box").hide();
                    } else if (storage_type == "2") {
                        $(".video_box").hide();
                        $(".s3_video_box").show();
                    }
                    $(".url_box").hide();
                } else {
                    $(".url_box").show();
                    $(".video_box").hide();
                    $(".s3_video_box").hide();
                }
            });
        });

		function save_music(){

            var Check_Admin = '<?php echo Demo_Mode(); ?>';
            if(Check_Admin == 1){

                $("#dvloader").show();
                var formData = new FormData($("#music")[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type:'POST',
                    url: '{{route("admin.music.update", [$data->id])}}',
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'music', '{{ route("admin.music.index") }}');
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