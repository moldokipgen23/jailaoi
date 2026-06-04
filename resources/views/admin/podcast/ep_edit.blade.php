@extends('admin.layout.page-app')
@section('page_title',__('label.edit_episode'))

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
                    <li class="breadcrumb-item"><a href="{{ route('podcast.index', $podcasts_id) }}">{{__('label.podcast')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('podcast.episode.index', $podcasts_id) }}">{{__('label.episodes')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Episode</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end mb-3">
                <a href="{{ route('podcast.episode.index', $podcasts_id) }}" class="btn btn-default mw-120">{{__('label.episode_list')}}</a>
            </div>
        </div>
        <!-- edit podcast episode  -->
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
                                    <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="@if($data){{$data->name}}@endif" class="form-control" placeholder="{{__('label.name_here')}}" autofocus>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('label.description')}}<span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="{{__('label.description_here')}}">@if($data){{$data->description}}@endif</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group ml-5">
                            <label>{{__('label.potrait_image')}}<span class="text-danger">*</span></label>
                            <div class="avatar-upload">
                                <div class="avatar-edit">
                                    <input type='file' name="portrait_img" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                    <label for="imageUpload" title="Select File"></label>
                                </div>
                                <div class="avatar-preview">
                                    <img src="{{$data->portrait_img}}" alt="upload_img.png" id="imagePreview">
                                </div>
                            </div>
                            <label class="mt-3 text-gray">{{__('label.image_note')}}</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group ml-5">
                            <label>{{__('label.landscape_image')}}<span class="text-danger">*</span></label>
                            <div class="avatar-upload-landscape">
                                <div class="avatar-edit-landscape">
                                    <input type='file' name="landscape_img" id="imageUploadLandscape" accept=".png, .jpg, .jpeg" />
                                    <label for="imageUploadLandscape" title="Select File"></label>
                                </div>
                                <div class="avatar-preview-landscape">
                                    <img src="{{$data->landscape_img}}" alt="upload_img.png" id="imagePreviewLandscape">
                                </div>
                            </div>
                            <label class="mt-3 text-gray">{{__('label.image_note')}}</label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{__('label.duration')}}</label>
                            <input type="text" id="timePicker" name="duration" placeholder="{{__('label.duration_here')}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{__('label.upload_type')}}<span class="text-danger">*</span></label>
                            <select class="form-control" name="episode_upload_type" id="episode_upload_type">
                                <option value="1" {{ $data->episode_upload_type == 1 ? 'selected' : ''}}>{{__('label.server_audio')}}</option>
                                <option value="2" {{ $data->episode_upload_type == 2 ? 'selected' : ''}}>{{__('label.external_url')}}</option>
                                <!-- <option value="3" {{ $data->episode_upload_type == 3 ? 'selected' : ''}}>{{__('label.youtube')}}</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 video_box">
                        <div class="form-group">
                            <div class="d-block">
                                <label>{{__('label.upload_audio')}}<span class="text-danger">*</span></label>
                                <div id="filelist1"></div>
                                <div id="container1">
                                    <div class="form-group">
                                        <input type="file" id="uploadFile1" name="uploadFile1" class="form-control import-file p-2">
                                    </div>
                                    <input type="hidden" name="episode_audio" value="{{basename($data->episode_audio)}}" id="episode_audio" class="form-control">
                                </div>
                            </div>
                        </div>
                        @if($data->episode_upload_type==1)
                        <a href="{{$data->episode_audio}}" target="_blank" class="btn-link">{{basename($data->episode_audio)}}</a>
                        @endif
                    </div>
                    <div class="col-md-2 mt-4 video_box">
                        <div class="form-group mt-3">
                            <a id="upload1" class="btn text-white bg-primary-color">{{__('label.upload_files')}}</a>
                        </div>
                    </div>
                    <div class="col-md-5 url_box">
                        <div class="form-group">
                            <label>{{__('label.url')}}<span class="text-danger">*</span></label>
                            <input type="text" name="url" value="@if($data->episode_upload_type != 1){{{$data->episode_audio}}}@endif" class="form-control" placeholder="{{__('label.url_here')}}">
                        </div>
                    </div>
                </div>
                <div class="border-top pt-3 text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_episode()">{{__('label.update')}}</button>
                    <a href="{{ route('podcast.episode.index', $podcasts_id) }}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
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
    date.setHours(hours, minutes, seconds);

    $('#timePicker').datetimepicker({

        useCurrent: false,
        format: 'HH:mm:ss',
        defaultDate: date,
        showClose: true,
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
        if (episode_upload_type == 1) {
            $(".url_box").hide();
        } else {
            $(".video_box").hide();
        }
        $('#episode_upload_type').change(function() {
            var optionValue = $(this).val();

            if (optionValue == 1) {
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
            toastr.error("{{__('label.you_have_no_right_to_add_edit_and_delete')}}");
        }
    }
</script>
@endsection