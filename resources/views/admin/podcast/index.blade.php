@extends('admin.layout.page-app')
@section('page_title', __('label.podcast'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.podcast')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.podcast')}}</li>
                </ol>
            </div>
        </div>
        <!-- add podcast -->
        <div class="card custom-border-card mt-3">
            <h5 class="card-header">{{__('label.add_podcast')}}</h5>
            <div class="card-body">
                <form id="podcast" enctype="multipart/form-data">
                    <input type="hidden" name="id">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control" placeholder="{{__('label.title_here')}}" autofocus>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Artist<span class="text-danger">*</span></label>
                                        <select name="artist_id" class="form-control artist_id" style="width:100%!important;">
                                            <option value="">{{__('label.select_artist')}}</option>
                                            @foreach ($artist as $key => $value)
                                            <option value="{{$value->id}}">
                                                {{ $value->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('label.category')}}<span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-control category_id">
                                            <option value="">{{__('label.select_category')}}</option>
                                            @foreach ($category as $key => $value)
                                            <option value="{{$value->id}}">
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
                                        <label>Language<span class="text-danger">*</span></label>
                                        <select name="language_id" class="form-control language_id" style="width:100%!important;">
                                            <option value="">{{__('label.select_language')}}</option>
                                            @foreach ($language as $key => $value)
                                            <option value="{{$value->id}}">
                                                {{ $value->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
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
                            </div>
                            <div class="form-row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>{{__('label.description')}}<span class="text-danger">*</span></label>
                                        <textarea name="description" class="form-control" rows="1" placeholder="{{__('label.description_here')}}"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.duration')}}</label>
                                        <input type="text" id="timePicker" name="duration" placeholder="{{__('label.duration_here')}}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.upload_type')}}</label>
                                        <select class="form-control" name="trailer_upload_type" id="trailer_upload_type">
                                            <option value="1">{{__('label.server_audio')}}</option>
                                            <option value="2">{{__('label.external_url')}}</option>
                                            <!-- <option value="3">{{__('label.youtube')}}</option> -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 video_box">
                                    <div class="form-group">
                                        <div class="d-block">
                                            <label>{{__('label.upload_trailer_audio')}}</label>
                                            <div id="filelist3"></div>
                                            <div id="container3">
                                                <div class="form-group">
                                                    <input type="file" id="uploadFile3" name="uploadFile3" class="form-control import-file p-2">
                                                </div>
                                                <input type="hidden" name="trailer_audio" id="trailer_audio" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4 video_box">
                                    <div class="form-group mt-3">
                                        <a id="upload3" class="btn text-white bg-primary-color">{{__('label.upload_files')}}</a>
                                    </div>
                                </div>
                                <div class="col-md-8 url_box">
                                    <div class="form-group">
                                        <label>{{__('label.trailer_url')}}</label>
                                        <input type="text" name="url" class="form-control" placeholder="{{__('label.url_here')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group ml-5">
                                <label class="ml-5">{{__('label.potrait_image')}}<span class="text-danger">*</span></label>
                                <div class="avatar-upload ml-5">
                                    <div class="avatar-edit">
                                        <input type='file' name="portrait_img" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                        <label for="imageUpload" title="Select File"></label>
                                    </div>
                                    <div class="avatar-preview">
                                        <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_img.png" id="imagePreview">
                                    </div>
                                </div>
                                <label class="mt-3 ml-5 text-gray">{{__('label.image_note')}}</label>
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
                                        <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_img.png" id="imagePreviewLandscape">
                                    </div>
                                </div>
                                <label class="mt-3 text-gray">{{__('label.image_note')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="save_podcast()">{{__('label.save')}}</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>
            </div>
        </div>
        <!-- search && table -->
        <div class="card custom-border-card mt-3">
            <div class="page-search mb-3">
                <div class="input-group" title="Search">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass fa-xl light-gray"></i></span>
                    </div>
                    <input type="text" id="input_search" class="form-control" placeholder="{{__('label.search_podcast')}}" aria-label="Search" aria-describedby="basic-addon1">
                </div>
            </div>
            <div class="table-responsive table">
                <table class="table table-striped text-center table-bordered" id="datatable">
                    <thead>
                        <tr class="bg-table">
                            <th> {{__('label.#')}} </th>
                            <th> {{__('label.image')}} </th>
                            <th>{{__('label.title')}}</th>
                            <th>{{__('label.episode')}}</th>
                            <th>{{__('label.status')}}</th>
                            <th>{{__('label.action')}}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <!-- edit model -->
        <div class="modal fade" id="EditModel" data-backdrop="static" role="dialog" aria-labelledby="exampleModallabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModallabel">{{__('label.edit_podcast')}}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="edit_podcasts" autocomplete="off">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="edit_id">
                            <input type="hidden" name="old_portrait_img" id="edit_old_portrait_img">
                            <input type="hidden" name="old_landscape_img" id="edit_old_landscape_img">
                            <input type="hidden" name="old_trailer_upload_type" id="edit_old_trailer_upload_type">
                            <input type="hidden" name="old_trailer_audio" id="edit_old_trailer_audio">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="edit_title" class="form-control" placeholder="{{__('label.title_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Artist<span class="text-danger">*</span></label>
                                        <select name="artist_id" id="edit_artist_id" class="form-control artist_id" style="width: 100%!important;">
                                            <option value="">{{__('label.select_artist')}}</option>
                                            @foreach ($artist as $key => $value)
                                            <option value="{{$value->id}}">
                                                {{ $value->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Category<span class="text-danger">*</span></label>
                                        <select name="category_id" id="edit_category_id" class="form-control category_id" style="width: 100%!important;">
                                            <option value="">{{__('label.select_category')}}</option>
                                            @foreach ($category as $key => $value)
                                            <option value="{{$value->id}}">
                                                {{ $value->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Language<span class="text-danger">*</span></label>
                                        <select name="language_id" id="edit_language_id" class="form-control language_id" style="width: 100%!important;">
                                            <option value="">{{__('label.select_language')}}</option>
                                            @foreach ($language as $key => $value)
                                            <option value="{{$value->id}}">
                                                {{ $value->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('label.is_premium')}}<span class="text-danger">*</span></label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_premium" id="edit_is_premium_yes" class="custom-control-input" value="1" checked>
                                                <label class="custom-control-label" for="edit_is_premium_yes">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_premium" id="edit_is_premium_no" class="custom-control-input" value="0">
                                                <label class="custom-control-label" for="edit_is_premium_no">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>{{__('label.description')}}<span class="text-danger">*</span></label>
                                        <textarea name="description" id="edit_description" class="form-control" rows="1" placeholder="{{__('label.description_here')}}"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.duration')}}</label>
                                        <input type="text" id="edit_timePicker" name="duration" placeholder="{{__('label.duration_here')}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.upload_type')}}</label>
                                        <select class="form-control" name="trailer_upload_type" id="edit_trailer_upload_type">
                                            <option value="1">{{__('label.server_audio')}}</option>
                                            <option value="2">{{__('label.external_url')}}</option>
                                            <!-- <option value="3">{{__('label.youtube')}}</option> -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 edit_video_box">
                                    <div class="form-group">
                                        <div class="d-block">
                                            <label>{{__('label.upload_trailer_audio')}}</label>
                                            <div id="filelist4"></div>
                                            <div id="container4">
                                                <div class="form-group">
                                                    <input type="file" id="uploadFile4" name="uploadFile4" class="form-control import-file p-2">
                                                </div>
                                                <input type="hidden" name="trailer_audio" id="edit_trailer_audio" class="form-control">
                                            </div>
                                        </div>
                                        <a class="btn-link" href="" target="_blank" id="file_name"></a>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4 edit_video_box">
                                    <div class="form-group mt-3">
                                        <a id="upload4" class="btn text-white bg-primary-color">{{__('label.upload_files')}}</a>
                                    </div>
                                </div>
                                <div class="col-md-9 edit_url_box">
                                    <div class="form-group">
                                        <label>{{__('label.trailer_url')}}</label>
                                        <input type="text" name="url" id="edit_url" class="form-control" placeholder="{{__('label.url_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <div class="form-group ml-3">
                                                <label class="">{{__('label.potrait_image')}}<span class="text-danger">*</span></label>
                                                <div class="avatar-upload">
                                                    <div class="avatar-edit">
                                                        <input type='file' name="portrait_img" id="imageUploadModel" accept=".png, .jpg, .jpeg" />
                                                        <label for="imageUploadModel" title="Select File"></label>
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <img src="" alt="upload_img.png" id="imagePreviewModel">
                                                    </div>
                                                </div>
                                                <label class="mt-3 text-gray">{{__('label.image_note')}}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{__('label.landscape_image')}}<span class="text-danger">*</span></label>
                                                <div class="avatar-upload-landscape">
                                                    <div class="avatar-edit-landscape">
                                                        <input type='file' name="landscape_img" id="imageUploadLandscapeModel" accept=".png, .jpg, .jpeg" />
                                                        <label for="imageUploadLandscapeModel" title="Select File"></label>
                                                    </div>
                                                    <div class="avatar-preview-landscape">
                                                        <img src="" alt="upload_img.png" id="imagePreviewLandscapeModel">
                                                    </div>
                                                </div>
                                                <label class="mt-3 text-gray">{{__('label.image_note')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default mw-120" onclick="update_podcasts()">{{__('label.update')}}</button>
                            <button type="button" class="btn btn-cancel mw-120" data-dismiss="modal">{{__('label.close')}}</button>
                            <input type="hidden" name="_method" value="PATCH">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    $(".artist_id").select2();
    $(".category_id").select2();
    $(".language_id").select2();

    // Time Picker
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

    $(document).ready(function() {

        $(".url_box").hide();
        $('#trailer_upload_type').change(function() {
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


    $(document).ready(function() {

        var table = $('#datatable').DataTable({
            ...datatabledefault,
            ajax: {
                url: "{{ route('podcast.index') }}",
                data: function(d) {
                    d.input_search = $('#input_search').val();
                },
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'portrait_img',
                    name: 'portrait_img',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, full, meta) {
                        return "<a href='" + data + "' target='_blank' title='Watch'><img src='" + data + "' class='img-thumbnail size-55'></a>";
                    },
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    }
                },
                {
                    data: 'episode',
                    name: 'episode',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
        });

        $('#input_search').keyup(function() {
            table.draw();
        });
    });

    function save_podcast() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var formData = new FormData($("#podcast")[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route("podcast.store") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'podcast', '{{ route("podcast.index") }}');
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

    $(document).on("click", ".edit_podcasts", function() {

        $(".modal-body #file_name").attr("href", "").text("");
        $("#edit_url").val("");



        $(".edit_url_box").hide();
        $('#edit_trailer_upload_type').change(function() {
            var optionValue = $(this).val();

            if (optionValue == 1) {
                $(".edit_video_box").show();
                $(".edit_url_box").hide();
            } else {
                $(".edit_url_box").show();
                $(".edit_video_box").hide();
            }
        });

        var id = $(this).data('id');
        var title = $(this).data('title');
        var description = $(this).data('description');
        var portrait_img = $(this).data('portrait_img');
        var landscape_img = $(this).data('landscape_img');
        var category_id = $(this).data('category_id');
        var language_id = $(this).data('language_id');
        var is_premium = $(this).data('is_premium');
        var duration = $(this).data('duration');
        var trailer_upload_type = $(this).data('trailer_upload_type');
        var trailer_audio = $(this).data('trailer_audio');
        var artist_id = $(this).data('artist_id');

        $(".modal-body #edit_id").val(id);
        $(".modal-body #edit_title").val(title);
        $(".modal-body #edit_description").val(description);
        $(".modal-body #edit_category_id").val(category_id).change();
        $(".modal-body #edit_language_id").val(language_id).change();
        $(".modal-body #edit_artist_id").val(artist_id).change();
        $(".modal-body #edit_trailer_upload_type").val(trailer_upload_type).change();

        $(".modal-body #imagePreviewModel").attr("src", portrait_img);
        $(".modal-body #imagePreviewLandscapeModel").attr("src", landscape_img);
        $(".modal-body #edit_old_portrait_img").val(portrait_img);
        $(".modal-body #edit_old_landscape_img").val(landscape_img);
        $(".modal-body #edit_old_trailer_upload_type").val(trailer_upload_type);
        $(".modal-body #edit_old_trailer_audio").val(trailer_audio);

        if (is_premium == 1) {
            $(".modal-body #edit_is_premium_yes").attr('checked', 'checked');
        } else {
            $(".modal-body #edit_is_premium_no").attr('checked', 'checked');
        }

        if (trailer_upload_type == 1) {
            $(".modal-body #file_name").attr('href', trailer_audio);
            $(".modal-body #file_name").text(trailer_audio.split('/').pop());
            $(".edit_video_box").show();
            $(".edit_url_box").hide();
        } else {
            $(".modal-body #edit_url").val(trailer_audio);
            $(".edit_video_box").hide();
            $(".edit_url_box").show();
        }

        let hours = msToHours(duration);
        let minutes = msToMinutes(duration);
        let seconds = msToSeconds(duration);
        var date = new Date();
        date.setHours(hours, minutes, seconds);

        $('.modal-body #edit_timePicker').datetimepicker({
            useCurrent: false,
            format: 'HH:mm:ss',
            showClose: true,
            showTodayButton: true,
            icons: {
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                today: "fa fa-clock fa-regular",
                close: "fa fa-times",
            }
        })

        $('.modal-body #edit_timePicker').data('DateTimePicker').date(moment(date));
    });

    function update_podcasts() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var formData = new FormData($("#edit_podcasts")[0]);

            var Edit_Id = $("#edit_id").val();

            var url = '{{ route("podcast.update", ":id") }}';
            url = url.replace(':id', Edit_Id);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                enctype: 'multipart/form-data',
                type: 'POST',
                url: url,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();

                    if (resp.status == 200) {
                        $('#EditModel').modal('toggle');
                    }
                    get_responce_message(resp, 'edit_podcasts', '{{ route("podcast.index") }}');
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

    function change_status(id) {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var url = "{{route('podcast.show', '')}}" + "/" + id;
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
            toastr.error("{{__('label.you_have_no_right_to_add_edit_and_delete')}}");
        }
    };

    $(document).on('change', '.status-checkbox', function() {
        id = $(this).data('id');
        change_status(id);
    })
</script>
@endsection