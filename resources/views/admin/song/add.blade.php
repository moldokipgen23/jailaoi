@extends('admin.layout.page-app')
@section('page_title', __('label.add_radio_station'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.add_radio_station')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('song.index') }}">{{__('label.radio_station')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.add_radio_station')}}</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('song.index') }}" class="btn btn-default mw-120 mb-3">{{__('label.radio_station')}}</a>
            </div>
        </div>
        <!-- add song  -->
        <div class="card custom-border-card">
            <form id="song" autocomplete="off" enctype="multipart/form-data">
                <input type="hidden" name="id" value="">
                <div class="form-row">
                    <div class="col-md-8">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="{{__('label.name_here')}}" autofocus>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.artist')}}<span class="text-danger">*</span></label>
                                    <select class="form-control" name="artist_id" id="artist_id">
                                        <option value="">{{__('label.select_artist')}}</option>
                                        @foreach ($artist as $key => $value)
                                        <option value="{{ $value->id}}">
                                            {{ $value->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.category')}}<span class="text-danger">*</span></label>
                                    <select class="form-control" name="category_id" id="category_id">
                                        <option value="">{{__('label.select_category')}}</option>
                                        @foreach ($category as $key => $value)
                                        <option value="{{ $value->id}}">
                                            {{ $value->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.language')}}<span class="text-danger">*</span></label>
                                    <select class="form-control" name="language_id" id="language_id">
                                        <option value="">{{__('label.select_language')}}</option>
                                        @foreach ($language as $key => $value)
                                        <option value="{{ $value->id}}">
                                            {{ $value->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.city')}}<span class="text-danger">*</span></label>
                                    <select class="form-control" name="city_id" id="city_id">
                                        <option value="">{{__('label.select_city')}}</option>
                                        @foreach ($city as $key => $value)
                                        <option value="{{ $value->id}}">
                                            {{ $value->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('label.is_premium')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_premium" id="is_premium_yes" class="custom-control-input" value="1" checked>
                                            <label class="custom-control-label" for="is_premium_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_premium" id="is_premium_no" class="custom-control-input" value="0">
                                            <label class="custom-control-label" for="is_premium_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('label.duration')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="duration" id="timePicker" class="form-control" placeholder="{{__('label.duration_here')}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-lg-3">
                                <label>{{__('label.upload_type')}}<span class="text-danger">*</span></label>
                                <select name="upload_type" id="upload_type" class="form-control">
                                    <option selected="selected" value="1">{{__('label.server_audio')}}</option>
                                    <option value="2">{{__('label.external_url')}}</option>
                                    <option value="3">Curated Station (No Upload)</option>
                                    <!-- <option value="youtube">{{__('label.youtube')}}</option>
                                        <option value="vimeo">{{__('label.vimeo')}}</option> -->
                                </select>
                            </div>
                            <div class="col-md-6 video_box">
                                <div class="form-group">
                                    <div class="d-block">
                                        <label>{{__('label.upload_full_song')}}<span class="text-danger">*</span></label>
                                        <div id="filelist"></div>
                                        <div id="container">
                                            <div class="form-group">
                                                <input type="file" id="uploadFile" name="uploadFile" class="form-control import-file p-2">
                                            </div>
                                            <input type="hidden" name="song_url" id="song_url" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <label class="text-gray edit_basename"></label>
                            </div>
                            <div class="col-md-2 mt-4 video_box">
                                <div class="form-group mt-3">
                                    <a id="upload" class="btn text-white bg-primary-color">{{__('label.upload_files')}}</a>
                                </div>
                            </div>
                            <div class="col-md-6 url_box">
                                <div class="form-group">
                                    <label>{{__('label.audio_url')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="url" class="form-control" placeholder="{{__('label.audio_url_here')}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-row curated_box" style="display:none;">
                            <div class="col-md-12"><p class="text-muted mb-2">Curated station &mdash; pick any mix of categories, artists and languages (all optional). The station plays every song matching your selection, non-stop.</p></div>
                            <div class="col-md-4"><div class="form-group">
                                <label>Categories</label>
                                <select class="form-control" name="station_category_ids[]" id="station_category_ids" multiple>
                                    @foreach ($category as $value)<option value="{{ $value->id }}">{{ $value->name }}</option>@endforeach
                                </select>
                            </div></div>
                            <div class="col-md-4"><div class="form-group">
                                <label>Artists</label>
                                <select class="form-control" name="station_artist_ids[]" id="station_artist_ids" multiple>
                                    @foreach ($artist as $value)<option value="{{ $value->id }}">{{ $value->name }}</option>@endforeach
                                </select>
                            </div></div>
                            <div class="col-md-4"><div class="form-group">
                                <label>Languages</label>
                                <select class="form-control" name="station_language_ids[]" id="station_language_ids" multiple>
                                    @foreach ($language as $value)<option value="{{ $value->id }}">{{ $value->name }}</option>@endforeach
                                </select>
                            </div></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group ml-5">
                            <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                            <div class="avatar-upload">
                                <div class="avatar-edit">
                                    <input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                    <label for="imageUpload" title="Select File"></label>
                                </div>
                                <div class="avatar-preview">
                                    <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_img.png" id="imagePreview">
                                </div>
                            </div>
                            <label class="mt-3 text-gray">{{__('label.image_note')}}</label>
                        </div>
                    </div>
                </div>
                <div class="border-top pt-3 text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_song()">{{__('label.save')}}</button>
                    <a href="{{route('song.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    var d = new Date();
    d.setHours(0, 0, 0);
    $('#timePicker').datetimepicker({
        useCurrent: false,
        format: 'HH:mm:ss',
        defaultDate: d,
        showClose: true,
        showTodayButton: true,
        icons: {
            up: "fa fa-chevron-up",
            down: "fa fa-chevron-down",
            today: "fa fa-clock fa-regular",
            close: "fa fa-times",
        }
    })
    
    $("#category_id").select2();
    $("#artist_id").select2();
    $("#language_id").select2();
    $("#city_id").select2();

    function save_song() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var formData = new FormData($("#song")[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route("song.store") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'song', '{{ route("song.index") }}');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    $(document).ready(function() {

        $(".url_box").hide();
        $(".curated_box").hide();
        $("#station_category_ids").select2({ width: '100%' });
        $("#station_artist_ids").select2({ width: '100%' });
        $("#station_language_ids").select2({ width: '100%' });
        function jailaoiToggleUploadType(v) {
            var f = $('#artist_id,#category_id,#language_id,#city_id,[name="duration"],[name="is_premium"]').closest('.form-group');
            if (v == '1') { $(".video_box").show(); $(".url_box").hide(); $(".curated_box").hide(); f.show(); }
            else if (v == '3') { $(".video_box").hide(); $(".url_box").hide(); $(".curated_box").show(); f.hide(); }
            else { $(".url_box").show(); $(".video_box").hide(); $(".curated_box").hide(); f.show(); }
        }
        $('#upload_type').change(function() { jailaoiToggleUploadType($(this).val()); });
        jailaoiToggleUploadType($('#upload_type').val());
    });
</script>
@endsection