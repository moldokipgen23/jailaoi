@extends('admin.layout.page-app')
@section('page_title', 'Edit Episode')

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">Edit Episode</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('podcast.index', $podcasts_id) }}">Podcasts</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('podcast.episode.index', $podcasts_id) }}">Episodes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Episode</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('podcast.episode.index', $podcasts_id) }}" class="btn btn-default mw-120" style="margin-top:-14px">Episodes List</a>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <form id="episode" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="@if($data){{$data->id}}@endif">
                    <input type="hidden" name="podcasts_id" value="@if($data){{$data->podcasts_id}}@endif">
                    <input type="hidden" name="old_portrait_img" value="@if($data){{$data->portrait_img}}@endif">
                    <input type="hidden" name="old_landscape_img" value="@if($data){{$data->landscape_img}}@endif">
                    <input type="hidden" name="old_episode_upload_type" value="@if($data){{$data->episode_upload_type}}@endif">
                    <input type="hidden" name="old_episode_audio" value="@if($data){{$data->episode_audio}}@endif">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{__('Label.Name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="@if($data){{$data->name}}@endif" class="form-control" placeholder="Enter Name" autofocus>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{__('Label.Description')}}<span class="text-danger">*</span></label>
                                        <textarea name="description" class="form-control" rows="2" placeholder="Describe Here,">@if($data){{$data->description}}@endif</textarea>
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
                                        <img src="{{$data->portrait_img}}" alt="upload_img.png" id="imagePreview">
                                    </div>
                                </div>
                                <label class="mt-3 text-gray">Maximum size 2MB.</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group ml-5">
                                <label>Landscape Image<span class="text-danger">*</span></label>
                                <div class="avatar-upload-landscape">
                                    <div class="avatar-edit-landscape">
                                        <input type='file' name="landscape_img" id="imageUploadLandscape" accept=".png, .jpg, .jpeg" />
                                        <label for="imageUploadLandscape" title="Select File"></label>
                                    </div>
                                    <div class="avatar-preview-landscape">
                                        <img src="{{$data->landscape_img}}" alt="upload_img.png" id="imagePreviewLandscape">
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
                                    <option value="server_video" {{ $data->episode_upload_type == "server_video" ? 'selected' : ''}}>Server Audio</option>
                                    <option value="external_url" {{ $data->episode_upload_type == "external_url" ? 'selected' : ''}}>External URL</option>
                                    <!-- <option value="youtube" {{ $data->episode_upload_type == "youtube" ? 'selected' : ''}}>Youtube</option> -->
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
                            <label class="text-gray">@if($data->episode_upload_type == 'server_video'){{basename($data->episode_audio)}}@endif</label>
                        </div>
                        <div class="col-md-2 mt-4 video_box">
                            <div class="form-group mt-3">
                                <a id="upload1" class="btn text-white" style="background-color:#4e45b8;">{{__('Label.Upload_Files')}}</a>
                            </div>
                        </div>
                        <div class="col-md-5 url_box">
                            <div class="form-group">
                                <label>URL<span class="text-danger">*</span></label>
                                <input type="text" name="url" value="@if($data->episode_upload_type != 'server_video'){{{$data->episode_audio}}}@endif" class="form-control" placeholder="Enter URL">
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="save_episode()">{{__('Label.UPDATE')}}</button>
                        <a href="{{ route('podcast.episode.index', $podcasts_id) }}" class="btn btn-cancel mw-120 ml-2">{{__('Label.CANCEL')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>

        var duration = '<?php echo $data->duration; ?>';
        function msToHours(duration) {
            var hours = Math.floor((duration / (1000 * 60 * 60)) % 24);
                hours = (hours < 10) ? "0" + hours : hours;
                return hours;
        }
        function msToMinutes(duration) {
            var minutes = Math.floor((duration / (1000 * 60)) % 60),
                minutes = (minutes < 10) ? "0" + minutes : minutes;
                return minutes;
        }
        function msToSeconds(duration) {
            var seconds = Math.floor((duration / 1000) % 60),
                seconds = (seconds < 10) ? "0" + seconds : seconds;
                return seconds;
        }
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

            var episode_upload_type = "<?php echo $data->episode_upload_type; ?>";
            if (episode_upload_type == "server_video") {
                $(".url_box").hide();
            } else {
                $(".video_box").hide();
            }
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
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    enctype: 'multipart/form-data',
                    type: 'POST',
                    url: '{{route("podcast.episode.update", [$podcasts_id, $data->id])}}',
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