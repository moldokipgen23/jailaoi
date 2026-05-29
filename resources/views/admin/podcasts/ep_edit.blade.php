@extends('admin.layout.page-app')
@section('page_title', __('label.edit_episode'))
@section('tab_title', __('label.edit_episode'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.edit_episode')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.podcasts.index') }}">{{__('label.podcasts')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.podcast.episode.index', $podcasts_id) }}">{{__('label.episodes')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.edit_episode')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.podcast.episode.index', $podcasts_id) }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('label.episode_list')}}</a>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <form id="episode" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="{{ $data['id'] }}">
                    <input type="hidden" name="podcasts_id" value="{{ $data['podcasts_id'] }}">
                    <input type="hidden" name="old_portrait_img" value="{{ $data['portrait_img'] }}">
                    <input type="hidden" name="old_landscape_img" value="{{ $data['landscape_img'] }}">
                    <input type="hidden" name="old_episode_upload_type" value="{{ $data['episode_upload_type'] }}">
                    <input type="hidden" name="old_episode_audio" value="{{ $data['episode_audio'] }}">
                    <input type="hidden" name="old_portrait_img_storage_type" value="{{ $data['portrait_img_storage_type'] }}">
                    <input type="hidden" name="old_landscape_img_storage_type" value="{{ $data['landscape_img_storage_type'] }}">
                    <input type="hidden" name="old_episode_storage_type" value="{{ $data['episode_storage_type'] }}">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{ $data['name'] }}" class="form-control" placeholder="{{__('label.name_here')}}" autofocus>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{__('label.description')}}</label>
                                        <textarea name="description" class="form-control" rows="2" placeholder="{{__('label.description_here')}}">{{ $data['description'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group ml-5">
                                <label>{{__('label.portrait_image')}}</label>
                                <div class="avatar-upload">
                                    <div class="avatar-edit">
                                        <input type='file' name="portrait_img" id="imageUpload1" accept=".png, .jpg, .jpeg, .webp"/>
                                        <label for="imageUpload1" title="{{__('label.upload_file')}}"></label>
                                    </div>
                                    <div class="avatar-preview">
                                        <img src="{{ $data['portrait_img'] }}" id="imagePreview1">
                                    </div>
                                </div>
                                <label class="mt-3 text-gray">{{__('label.max_size_5mb')}}</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group ml-5">
                                <label>{{__('label.landscape_image')}}</label>
                                <div class="avatar-upload-landscape">
                                    <div class="avatar-edit-landscape">
                                        <input type='file' name="landscape_img" id="imageUpload2" accept=".png, .jpg, .jpeg, .webp"/>
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
                    <div class="form-row">
                        <div class="col-md-9">
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.upload_type')}}<span class="text-danger">*</span></label>
                                        <select class="form-control" name="episode_upload_type" id="episode_upload_type">
                                            <option value="server_audio" {{ $data->episode_upload_type == "server_audio" ? 'selected' : ''}}>{{__('label.server_audio')}}</option>
                                            <option value="external_url" {{ $data->episode_upload_type == "external_url" ? 'selected' : ''}}>{{__('label.external_url')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 video_box">
                                    <div class="form-group">
                                        <div style="display: block;">
                                            <label>{{__('label.upload_audio')}}<span class="text-danger">*</span></label>
                                            <div id="filelist1"></div>
                                            <div id="container1" style="position: relative;">
                                                <div class="form-group">
                                                    <input type="file" id="uploadFile1" name="uploadFile1" class="form-control import-file p-2">
                                                </div>
                                                <input type="hidden" name="music" id="mp3_file_name1" class="form-control">
                                                <label class="text-gray">{{ basename($data['episode_audio']) }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4 video_box">
                                    <div class="form-group mt-3">
                                        <a id="upload1" class="btn primary-bg text-white">{{__('label.upload_file')}}</a>
                                    </div>
                                </div>
                                <div class="col-md-6 s3_video_box">
                                    <div class="form-group">
                                        <label>{{__('label.upload_audio')}}<span class="text-danger">*</span></label>
                                        <input type="file" name="music" class="form-control import-file" accept=".mp3">
                                        <label class="text-gray">@if($data->episode_upload_type == 'server_audio'){{ basename($data['episode_audio']) }}@endif</label>
                                    </div>
                                </div>
                                <div class="col-md-6 url_box">
                                    <div class="form-group">
                                        <label>{{__('label.url')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="url" value="@if($data->episode_upload_type != 'server_audio'){{{$data->episode_audio}}}@endif" class="form-control" placeholder="{{__('label.url_here')}}">
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
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="save_episode()">{{__('label.save')}}</button>
                        <a href="{{ route('admin.podcast.episode.index', $podcasts_id) }}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Chunk JS -->
    <script src="{{ asset('/assets/js/plupload.full.min.js')}}"></script>
    <script src="{{ asset('/assets/js/common.js')}}"></script>

	<script>
        // Sidebar Scroll Down
		sidebar_down(350);

        $(document).ready(function() {

            var storage_type = "<?php echo Storage_Type(); ?>";
            var episode_upload_type = "<?php echo $data['episode_upload_type']; ?>";
            if (episode_upload_type == "server_audio") {
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

            $('#episode_upload_type').change(function() {

                var optionValue = $(this).val();
                if (optionValue == 'server_audio') {

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

		function save_episode(){

            var Check_Admin = '<?php echo Demo_Mode(); ?>';
            if(Check_Admin == 1){

                $("#dvloader").show();
                var formData = new FormData($("#episode")[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    enctype: 'multipart/form-data',
                    type: 'POST',
                    url: '{{route("admin.podcast.episode.update", [$podcasts_id, $data->id])}}',
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'episode', '{{ route("admin.podcast.episode.index", $podcasts_id) }}');
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
