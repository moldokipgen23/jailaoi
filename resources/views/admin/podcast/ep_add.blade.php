@extends('admin.layout.page-app')
@section('page_title', 'Add Episode')

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">Add Episode</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('podcast.index', $podcasts_id) }}">Podcasts</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('podcast.episode.index', $podcasts_id) }}">Episodes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Episode</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('podcast.episode.index', $podcasts_id) }}" class="btn btn-default mw-120" style="margin-top:-14px">Episodes List</a>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <form id="episode" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="podcasts_id" value="@if($podcasts_id){{$podcasts_id}}@endif">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{__('Label.Name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter Name" autofocus>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{__('Label.Description')}}<span class="text-danger">*</span></label>
                                        <textarea name="description" class="form-control" rows="2" placeholder="Describe Here,"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group ml-5">
                                <label>Portrait Image<span class="text-danger">*</span></label>
                                <div class="avatar-upload">
                                    <div class="avatar-edit">
                                        <input type='file' name="portrait_img" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                        <label for="imageUpload" title="Select File"></label>
                                    </div>
                                    <div class="avatar-preview">
                                        <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_img.png" id="imagePreview">
                                    </div>
                                </div>
                                <label class="mt-3 text-gray">Maximum size 2MB.</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Landscape Image<span class="text-danger">*</span></label>
                                <div class="avatar-upload-landscape">
                                    <div class="avatar-edit-landscape">
                                        <input type='file' name="landscape_img" id="imageUploadLandscape" accept=".png, .jpg, .jpeg" />
                                        <label for="imageUploadLandscape" title="Select File"></label>
                                    </div>
                                    <div class="avatar-preview-landscape">
                                        <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_img.png" id="imagePreviewLandscape">
                                    </div>
                                </div>
                                <label class="mt-3 text-gray">Maximum size 2MB.</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Duration</label>
                                <input type="text" id="timePicker" name="duration" placeholder="Duration" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Upload Type<span class="text-danger">*</span></label>
                                <select class="form-control" name="episode_upload_type" id="episode_upload_type">
                                    <option value="server_video">Server Audio</option>
                                    <option value="external_url">External URL</option>
                                    <!-- <option value="youtube">Youtube</option> -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 video_box">
                            <div class="form-group">
                                <div style="display: block;">
                                    <label>Upload Audio<span class="text-danger">*</span></label>
                                    <div id="filelist1"></div>
                                    <div id="container1" style="position: relative;">
                                        <div class="form-group">
                                            <input type="file" id="uploadFile1" name="uploadFile1" class="form-control import-file p-2">
                                        </div>
                                        <input type="hidden" name="episode_audio" id="episode_audio" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mt-4 video_box">
                            <div class="form-group mt-3">
                                <a id="upload1" class="btn text-white" style="background-color:#4e45b8;">{{__('Label.Upload_Files')}}</a>
                            </div>
                        </div>
                        <div class="col-md-5 url_box">
                            <div class="form-group">
                                <label>URL<span class="text-danger">*</span></label>
                                <input type="text" name="url" class="form-control" placeholder="Enter URL">
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="save_episode()">{{__('Label.SAVE')}}</button>
                        <a href="{{route('podcast.episode.index', $podcasts_id)}}" class="btn btn-cancel mw-120 ml-2">{{__('Label.CANCEL')}}</a>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        // Time Picker
        var d = new Date();
        d.setHours(0,0,0);
        $('#timePicker').datetimepicker({
            useCurrent: false,
            format:'HH:mm:ss',
            defaultDate: d,
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
            $(".url_box").hide();
            $('#episode_upload_type').change(function() {
                var optionValue = $(this).val();

                if (optionValue == 'server_video') {
                    $(".video_box").show();
                    $(".url_box").hide();
                } else {
                    $(".url_box").show();
                    $(".video_box").hide();
                }
            });
        });

        function save_episode() {
            
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if (Check_Admin == 1) {

                $("#dvloader").show();
                var formData = new FormData($("#episode")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("podcast.episode.save") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'episode', '{{ route("podcast.episode.index", $podcasts_id) }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                toastr.error('You have no right to add, edit, and delete.');
            }
        }
    </script>
@endsection