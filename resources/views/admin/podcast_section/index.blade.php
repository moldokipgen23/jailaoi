@extends('admin.layout.page-app')
@section('page_title', __('Label.section'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm"> {{__('Label.section')}} </h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-11">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('Label.section')}}</li>
                    </ol>
                </div>
                <div class="col-sm-1 d-flex justify-content-start mb-3" title="{{__('Label.sortable')}}">
                    <button type="button" data-toggle="modal" data-target="#sortableModal" onclick="sortableBTN()" class="btn btn-default" style="border-radius: 10px;">
                        <i class="fa-solid fa-sort fa-1x"></i>
                    </button>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('Label.add_section')}}</h5>
                <div class="card-body">
                    <form id="podcast_section" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="">
                        <div class="form-row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('Label.Title')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="{{__('Label.enter_section_title')}}" autofocus>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('Label.sub_title')}}</label>
                                    <input type="text" name="sub_title" class="form-control" placeholder="{{__('Label.enter_section_sub_title')}}">
                                </div>
                            </div>
                            <div class="col-md-3 screen_layout">
                                <div class="form-group">
                                    <label>{{__('Label.screen_layout')}}<span class="text-danger">*</span></label>
                                    <select name="screen_layout" class="form-control" id="screen_layout">
                                        <option value="">{{__('Label.select_screen_layout')}}</option>
                                        <option value="landscape">{{__('Label.landscape')}}</option>
                                        <option value="portrait">{{__('Label.portrait')}}</option>
                                        <option value="sqaure">{{__('Label.sqaure')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 category_drop">
                                <div class="form-group ">
                                    <label>{{__('Label.Category')}}<span class="text-danger">*</span></label>
                                    <select class="form-control" style="width:100%!important;" name="category_id" id="category_id">
                                        <option value="0">{{__('Label.all_category')}}</option>
                                        @foreach ($category as $key => $value)
                                        <option value="{{ $value->id}}">
                                            {{ $value->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 language_drop">
                                <div class="form-group ">
                                    <label>{{__('Label.Language')}}<span class="text-danger">*</span></label>
                                    <select class="form-control" style="width:100%!important;" name="language_id" id="language_id">
                                        <option value="0">{{__('Label.all_language')}}</option>
                                        @foreach ($language as $key => $value)
                                        <option value="{{ $value->id}}">
                                            {{ $value->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 no_of_content">
                                <div class="form-group">
                                    <label>{{__('Label.no_of_content')}}<span class="text-danger">*</span></label>
                                    <input type="number" name="no_of_content" class="form-control" placeholder="{{__('Label.enter_no_of_content')}}">
                                </div>
                            </div>
                            <div class="col-md-2 view_all">
                                <div class="form-group ml-1">
                                    <label>{{__('Label.view_all')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="view_all" id="view_all_yes" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="view_all_yes">{{__('Label.Yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="view_all" id="view_all_no" class="custom-control-input" value="0" checked>
                                            <label class="custom-control-label" for="view_all_no">{{__('Label.No')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 is_premium">
                                <div class="form-group  ml-1">
                                    <label>{{__('Label.is_premium')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_premium" id="is_premium_yes" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="is_premium_yes">{{__('Label.Yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_premium" id="is_premium_no" class="custom-control-input" value="0" checked>
                                            <label class="custom-control-label" for="is_premium_no">{{__('Label.No')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 order_by_upload">
                                <div class="form-group  ml-1">
                                    <label>{{__('Label.order_by_upload')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="order_by_upload" id="order_by_upload_asc" class="custom-control-input" value="0">
                                            <label class="custom-control-label" for="order_by_upload_asc">{{__('Label.asc')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="order_by_upload" id="order_by_upload_desc" class="custom-control-input" value="1" checked>
                                            <label class="custom-control-label" for="order_by_upload_desc">{{__('Label.desc')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 order_by_play">
                                <div class="form-group  ml-3">
                                    <label>{{__('Label.order_by_play')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="order_by_play" id="order_by_play_asc" class="custom-control-input" value="0">
                                            <label class="custom-control-label" for="order_by_play_asc">{{__('Label.asc')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="order_by_play" id="order_by_play_desc" class="custom-control-input" value="1" checked>
                                            <label class="custom-control-label" for="order_by_play_desc">{{__('Label.desc')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="save_podcast_section()">{{__('Label.SAVE')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>
            <div class="after-add-more"></div>

            <!-- edit section -->
            <div class="modal fade" id="editpodcastsectioneModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="editsectioneModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editsectioneModalLabel">{{__('Label.edit_section')}}</h5>
                            <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="edit_content_section" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="id" id="edit_id" value="">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('Label.Title')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="title" id="edit_title" class="form-control" placeholder="{{__('Label.enter_section_title')}}" autofocus>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('Label.sub_title')}}</label>
                                            <input type="text" name="sub_title" id="edit_sub_title" class="form-control" placeholder="{{__('Label.enter_section_sub_title')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_screen_layout">
                                        <div class="form-group">
                                            <label>{{__('Label.screen_layout')}}<span class="text-danger">*</span></label>
                                            <select name="screen_layout" class="form-control" id="edit_screen_layout">
                                                <option value="">{{__('Label.select_screen_layout')}}</option>
                                                <option value="landscape">{{__('Label.landscape')}}</option>
                                                <option value="portrait">{{__('Label.portrait')}}</option>
                                                <option value="sqaure">{{__('Label.sqaure')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_category_drop">
                                        <div class="form-group ">
                                            <label>{{__('Label.Category')}}<span class="text-danger">*</span></label>
                                            <select class="form-control" style="width:100%!important;" name="category_id" id="edit_category_id">
                                                <option value="0">{{__('Label.all_category')}}</option>
                                                @foreach ($category as $key => $value)
                                                <option value="{{ $value->id}}">
                                                    {{ $value->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_language_drop">
                                        <div class="form-group ">
                                            <label>{{__('Label.Language')}}<span class="text-danger">*</span></label>
                                            <select class="form-control" style="width:100%!important;" name="language_id" id="edit_language_id">
                                                <option value="0">{{__('Label.all_language')}}</option>
                                                @foreach ($language as $key => $value)
                                                <option value="{{ $value->id}}">
                                                    {{ $value->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 edit_no_of_content">
                                        <div class="form-group">
                                            <label>{{__('Label.no_of_content')}}<span class="text-danger">*</span></label>
                                            <input type="number" name="no_of_content" id="edit_no_of_content" class="form-control" placeholder="{{__('Label.enter_no_of_content')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 edit_view_all">
                                        <div class="form-group ml-1">
                                            <label>{{__('Label.view_all')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="view_all" id="edit_view_all_yes" class="custom-control-input" value="1">
                                                    <label class="custom-control-label" for="edit_view_all_yes">{{__('Label.Yes')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="view_all" id="edit_view_all_no" class="custom-control-input" value="0">
                                                    <label class="custom-control-label" for="edit_view_all_no">{{__('Label.No')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 edit_is_premium">
                                        <div class="form-group  ml-1">
                                            <label>{{__('Label.is_premium')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="is_premium" id="edit_is_premium_yes" class="custom-control-input" value="1">
                                                    <label class="custom-control-label" for="edit_is_premium_yes">{{__('Label.Yes')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="is_premium" id="edit_is_premium_no" class="custom-control-input" value="0">
                                                    <label class="custom-control-label" for="edit_is_premium_no">{{__('Label.No')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 edit_order_by_upload">
                                        <div class="form-group  ml-1">
                                            <label>{{__('Label.order_by_upload')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_upload" id="edit_order_by_upload_asc" class="custom-control-input" value="0">
                                                    <label class="custom-control-label" for="edit_order_by_upload_asc">{{__('Label.asc')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_upload" id="edit_order_by_upload_desc" class="custom-control-input" value="1">
                                                    <label class="custom-control-label" for="edit_order_by_upload_desc">{{__('Label.desc')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 edit_order_by_play">
                                        <div class="form-group  ml-1">
                                            <label>{{__('Label.order_by_play')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_play" id="edit_order_by_play_asc" class="custom-control-input" value="0">
                                                    <label class="custom-control-label" for="edit_order_by_play_asc">{{__('Label.asc')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_play" id="edit_order_by_play_desc" class="custom-control-input" value="1">
                                                    <label class="custom-control-label" for="edit_order_by_play_desc">{{__('Label.desc')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default mw-120" onclick="update_section()">{{__('Label.UPDATE')}}</button>
                                <button type="button" class="btn btn-cancel mw-120" data-dismiss="modal">{{__('Label.CLOSE')}}</button>
                                <input type="hidden" name="_method" value="PATCH">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- sortableModal -->
            <div class="modal fade" id="sortableModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="sortableModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title w-100 text-center" id="sortableModalLabel">{{__('Label.section_sortable_list')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close">
                                <span aria-hidden="true" class="text-dark">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="imageListId">

                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <form enctype="multipart/form-data" id="save_section_sortable">
                                @csrf
                                <input id="outputvalues" type="hidden" name="ids" value="" />
                                <div class="w-100 text-center">
                                    <button type="button" class="btn btn-default mw-120" onclick="save_section_sortable()">{{__('Label.SAVE')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pagescript')
    <script>
        $('#category_id').select2();
        $('#language_id').select2();
        $('#edit_category_id').select2({
            dropdownParent: $('#editpodcastsectioneModal') 
        });
        $('#edit_language_id').select2({
            dropdownParent: $('#editpodcastsectioneModal') 
        });
        // section save
        function save_podcast_section() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if (Check_Admin == 1) {

                $("#dvloader").show();
                var formData = new FormData($("#podcast_section")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("podcastsection.store") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'podcast_section', '{{ route("podcastsection.index") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                toastr.error('{{__("Label.you_have_no_right_to_add_edit_and_delete")}}');
            }
        }

          // sections list 
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{ route("podcastsection.data") }}',
            data: {

            },
            success: function(resp) {
                $('.after-add-more').html('');
                for (var i = 0; i < resp.result.length; i++) {


                    if (resp.result[i].screen_layout == "landscape") {
                        var screen_layout = "{{__('Label.landscape')}}";
                    } else if (resp.result[i].screen_layout == "portrait") {
                        var screen_layout = "{{__('Label.portrait')}}";
                    } else if (resp.result[i].screen_layout == "sqaure") {
                        var screen_layout = "{{__('Label.sqaure')}}";
                    }  else {
                        var screen_layout = "-";
                    }

                    var data = '<div class="card custom-border-card mt-3">' +
                        '<div class="card-header d-flex justify-content-between">' +
                        '<h5>{{__("Label.edit_section")}}</h5>';
                    if (resp.result[i].status == 1) {
                        data += '<button class="btn" id="' + resp.result[i].id + '" onclick="change_status(' + resp.result[i].id + ')" style="background:#058f00; font-weight:bold; border: none; color: white;">{{__("Label.show")}}</button>';
                    } else {
                        data += '<button class="btn" id="' + resp.result[i].id + '" onclick="change_status(' + resp.result[i].id + ')" style="background:#e3000b; font-weight:bold; border: none; color: white;">{{__("Label.hide")}}</button>';
                    }
                    data += '</div>' +
                        '<div class="card-body">' +
                        '<div class="form-row">' +
                        '<div class="col-md-4">' +
                        '<div class="form-group">' +
                        '<label>{{__("Label.Title")}}</label>' +
                        '<input type="text" value="' + resp.result[i].title + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-4">' +
                        '<div class="form-group">' +
                        '<label>{{__("Label.sub_title")}}</label>' +
                        '<input type="text" value="' + resp.result[i].sub_title + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-4">' +
                        '<div class="form-group">' +
                        '<label>{{__("Label.screen_layout")}}</label>' +
                        '<input type="text" value="' + screen_layout + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="border-top pt-3 text-right">' +
                        '<button type="button" data-toggle="modal" data-target="#editpodcastsectioneModal" class="btn btn-default mw-120" onclick="edit_section(' + resp.result[i].id + ')">{{__("Label.UPDATE")}}</button>' +
                        '<button type="button" class="btn btn-cancel mw-120 ml-2" onclick="delete_section(' + resp.result[i].id + ')">{{__("Label.Delete")}}</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    $('.after-add-more').append(data);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("#dvloader").hide();
                toastr.error(errorThrown, textStatus);
            }
        });

        // Update Section
        function edit_section(id) {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route("podcastsection.edit") }}',
                data: {
                    id: id,
                },
                success: function(resp) {
                    if (resp.result != null) {

                        $("#edit_id").val(resp.result.id);
                        $("#edit_title").val(resp.result.title);
                        $("#edit_sub_title").val(resp.result.sub_title);
                        $("#edit_type").val(resp.result.type).attr("selected", "selected");
                        $("#edit_screen_layout").val(resp.result.screen_layout).attr("selected", "selected");
                        $('#edit_category_id').val(resp.result.category_id).trigger('change');
                        $('#edit_language_id').val(resp.result.language_id).trigger('change');
                        $("#edit_no_of_content").val(resp.result.no_of_content);

                        $("#edit_order_by_upload_asc").prop('checked', false);
                        $("#edit_order_by_upload_desc").prop('checked', false);

                        if (resp.result.order_by_upload == 0) {
                            $("#edit_order_by_upload_asc").prop('checked', true);
                        } else {
                            $("#edit_order_by_upload_desc").prop('checked', true);
                        }

                        $("#edit_order_by_play_asc").prop('checked', false);
                        $("#edit_order_by_play_desc").prop('checked', false);
                        if (resp.result.order_by_play == 0) {
                            $("#edit_order_by_play_asc").prop('checked', true);
                        } else {
                            $("#edit_order_by_play_desc").prop('checked', true);
                        }

                        $("#edit_is_premium_no").prop('checked', false);
                        $("#edit_is_premium_yes").prop('checked', false);
                        if (resp.result.is_premium == 0) {
                            $("#edit_is_premium_no").prop('checked', true);
                        } else {
                            $("#edit_is_premium_yes").prop('checked', true);
                        }

                        $("#edit_view_all_yes").prop('checked', false);
                        $("#edit_view_all_no").prop('checked', false);
                        if (resp.result.view_all == 1) {
                            $("#edit_view_all_yes").prop('checked', true);
                        } else {
                            $("#edit_view_all_no").prop('checked', true);
                        }
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        }
        
        function update_section() {

            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if (Check_Admin == 1) {

                $("#dvloader").show();
                var id = $('#edit_id').val();
                var formData = new FormData($("#edit_content_section")[0]);

                var url = '{{ route("podcastsection.update", ":id") }}';
                url = url.replace(':id', id);

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
                            $('#editsectioneModal').modal('toggle');
                        }
                        get_responce_message(resp, 'edit_content_section', '{{ route("podcastsection.index") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                toastr.error('{{__("Label.you_have_no_right_to_add_edit_and_delete")}}');
            }
        }

         // Delete Section
         function delete_section(id) {

            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if (Check_Admin == 1) {

                var result = confirm('{{__("Label.are_you_sure_you_want_to_delete_this_podcast_section")}}');
                if (result) {

                    $("#dvloader").show();

                    var url = '{{ route("podcastsection.show", ":id") }}';
                    url = url.replace(':id', id);

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'GET',
                        url: url,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(resp) {
                            $("#dvloader").hide();
                            get_responce_message(resp, '', '{{ route("podcastsection.index") }}');
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            $("#dvloader").hide();
                            toastr.error(errorThrown, textStatus);
                        }
                    });
                }
            } else {
                toastr.error('{{__("Label.you_have_no_right_to_add_edit_and_delete")}}');
            }
        }

        // Change Status
        function change_status(id) {

            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if (Check_Admin == 1) {

                $("#dvloader").show();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{route('podcastsection.status')}}",
                    data: {
                        id: id
                    },
                    success: function(resp) {
                        $("#dvloader").hide();
                        if (resp.status == 200) {

                            if (resp.Status == 1) {
                                $('#' + id).text('Show');
                                $('#' + id).css({
                                    "background": "#058f00",
                                    "color": "white",
                                    "font-weight": "bold",
                                    "border": "none"
                                });
                            } else {
                                $('#' + id).text('Hide');
                                $('#' + id).css({
                                    "background": "#e3000b",
                                    "color": "white",
                                    "font-weight": "bold",
                                    "border": "none"
                                });
                            }
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
                toastr.error('{{__("Label.you_have_no_right_to_add_edit_and_delete")}}');
            }
        };
     
        // Sortable Section
        $("#imageListId").sortable({
            update: function(event, ui) {
                getIdsOfImages();
            } //end update
        });

        function getIdsOfImages() {
            var values = [];
            $('.listitemClass').each(function(index) {
                values.push($(this).attr("id")
                    .replace("imageNo", ""));
            });
            $('#outputvalues').val(values);
        }

        // Sortable section get data 
        function sortableBTN() {
            $("#dvloader").show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route("podcastsection.sortable") }}',
                success: function(resp) {
                    $("#dvloader").hide();
                    $('#imageListId').html('');
                    for (var i = 0; i < resp.result.length; i++) {

                        var data = '<div id="' + resp.result[i].id + '" class="listitemClass mb-2" style="background-color: #e9ecef;border: 1px solid black; cursor: s-resize;">' +
                            '<p class="m-2">' + resp.result[i].title + '</p>' +
                            '</div>';

                        $('#imageListId').append(data);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        }

        // Sortable save
        function save_section_sortable() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if (Check_Admin == 1) {
                $("#dvloader").show();
                var formData = new FormData($("#save_section_sortable")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("podcastsection.sortable.save") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_section_sortable', '{{ route("podcastsection.index") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                toastr.error('{{__("Label.you_have_no_right_to_add_edit_and_delete")}}');
            }
        }
    </script>
@endsection