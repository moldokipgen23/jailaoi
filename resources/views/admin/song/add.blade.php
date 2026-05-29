@extends('admin.layout.page-app')
@section('page_title', 'Add Radio Station')

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">Add Radio Station</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('song.index') }}">Radio Station</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Radio Station</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('song.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">Radio Station</a>
                </div>
            </div>

            <div class="card custom-border-card">
                <form id="song" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="">
                    <div class="form-row">
                        <div class="col-md-8">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('Label.Name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter Song Name" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('Label.Artist')}}<span class="text-danger">*</span></label>
                                        <select class="form-control" name="artist_id" id="artist_id">
                                            <option value="">{{__('Label.select_artist')}}</option>
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
                                        <label>{{__('Label.Category')}}<span class="text-danger">*</span></label>
                                        <select class="form-control" name="category_id" id="category_id">
                                            <option value="">{{__('Label.select_category')}}</option>
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
                                        <label>{{__('Label.Language')}}<span class="text-danger">*</span></label>
                                        <select class="form-control" name="language_id" id="language_id">
                                            <option value="">{{__('Label.select_language')}}</option>
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
                                        <label>{{__('Label.City')}}<span class="text-danger">*</span></label>
                                        <select class="form-control" name="city_id" id="city_id">
                                            <option value="">{{__('Label.select_city')}}</option>
                                            @foreach ($city as $key => $value)
                                                <option value="{{ $value->id}}">
                                                    {{ $value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('Label.Is Premium')}}<span class="text-danger">*</span></label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_premium" id="is_premium_yes" class="custom-control-input" value="1" checked>
                                                <label class="custom-control-label" for="is_premium_yes">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_premium" id="is_premium_no" class="custom-control-input" value="0">
                                                <label class="custom-control-label" for="is_premium_no">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-lg-6">
                                    <label>{{__('Label.Video_Upload_Type')}}<span class="text-danger">*</span></label>
                                    <select name="song_upload_type" id="song_upload_type" class="form-control">
                                        <option selected="selected" value="server_video">{{__('Label.Server Video')}}</option>
                                        <option value="external_url">External URL</option>
                                        <!-- <option value="youtube">Youtube</option>
                                        <option value="vimeo">Vimeo</option> -->
                                    </select>
                                </div>
                                <div class="form-group col-lg-6 video_box">
                                    <div style="display: block;">
                                        <label>{{__('Label.upload_full_song')}}<span class="text-danger">*</span></label>
                                        <div id="filelist"></div>
                                        <div id="container" style="position: relative;">
                                            <div class="form-group">
                                                <input type="file" id="uploadFile" name="uploadFile" style="position: relative; z-index: 1;" class="form-control import-file">
                                            </div>
                                            <input type="hidden" name="song_url" id="song_url" class="form-control">

                                            <div class="form-group">
                                                <a id="upload" class="btn text-white" style="background-color:#4e45b8;">{{__('Label.Upload_Files')}}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 url_box">
                                    <label>URL<span class="text-danger">*</span></label>
                                    <input name="url" type="url" class="form-control" placeholder="Enter URL">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group ml-5">
                                <label>{{__('Label.Image')}}<span class="text-danger">*</span></label>
                                <div class="avatar-upload">
                                    <div class="avatar-edit">
                                        <input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                        <label for="imageUpload" title="Select File"></label>
                                    </div>
                                    <div class="avatar-preview">
                                        <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_img.png" id="imagePreview">
                                    </div>
                                </div>
                                <label class="mt-3 text-gray">{{__('Label.image_note')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="save_song()">{{__('Label.SAVE')}}</button>
                        <a href="{{route('song.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('Label.CANCEL')}}</a>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
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
                toastr.error('You have no right to add, edit, and delete.');
            }
        }

        $(document).ready(function() {

            $(".url_box").hide();
            $('#song_upload_type').change(function() {
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
    </script>
@endsection