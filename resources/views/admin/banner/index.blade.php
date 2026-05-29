@extends('admin.layout.page-app')
@section('page_title', __('Label.Banner'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('Label.Banner')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('Label.Banner')}}</li>
                    </ol>
                </div>
            </div>


            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="app" role="tabpanel" aria-labelledby="app-tab">
                    <div class="card custom-border-card">
                        <h5 class="card-header">{{__('Label.Banner')}}</h5>
                        <div class="card-body">

                            <form id="save_banner" name="banner">
                                @csrf

                                <div class="form-row mb-5">
                                    <div class="col-md-3 option_class_video">
                                        <div class="form-group">
                                            <label>{{__('Label.Type')}}</label>
                                            <select class="form-control" name="content_type" id="contenttype" style="width:100%!important;">
                                                <option value="0">{{__('Label.select_type')}}</option>
                                                <option value="1">{{__('Label.radio_station')}}</option>
                                                <option value="2">{{__('Label.poadcast')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 content_name">
                                        <div class="form-group">
                                            <label>{{__('Label.content')}}</label>
                                            <select class="form-control content_id" name="content_id" id="content_id">
                                                <option value=""> {{__('Label.select_content')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="after-add-more"></div>

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
        $(document).ready(function() {

            $("#content_id").select2();

        });

        // Get Book or Podcast Name
        $('#contenttype').change(function() {
            var contentType = $(this).val();

            $.ajax({
                url: '{{ route("getcontent") }}',
                type: 'GET',
                data: {
                    content_type: contentType
                },
                success: function(response) {
                    $('#content_id').empty();

                    $('#content_id').append('<option value=""> {{__("Label.select_content")}}</option>');
                    response.content.forEach(function(item) {
                        if(contentType == 1){
                            $('#content_id').append(`<option value="${item.id}">${item.name}</option>`);
                        } else if(contentType == 2){
                            $('#content_id').append(`<option value="${item.id}">${item.title}</option>`);
                        }
                    });
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                 toastr.error(errorThrown, textStatus);
                }
            });
        });

        // List Show
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            url: '{{ route("banner.index") }}',
            success: function(resp) {

                for (var i = 0; i < resp.result.length; i++) {
                    var data = '<div class="form-group row">';
                    data += '<div class="col-md-2  mb-0">';
                    data += '<input type="text" class="form-control" name="type" value="' + resp.result[i].type + '" id="type" placeholder="Dropdown" readonly/>';
                    data += '</div>';
                    data += '<div class="col-md-6  mb-0">';
                    data += '<input type="text" class="form-control" name="title" value="' + resp.result[i].title + '" id="title" placeholder="Dropdown" readonly/>';
                    data += '</div>';
                    data += '<div class="col-md-1 ml-5">';
                    data += '<img src="' + resp.result[i].image + '" height=50 Width=50>';
                    data += '</div>';
                    data += '<div class="col-md-1 change ">';
                    data += '<label>&nbsp;</label>';
                    data += '<a onclick="DeleteBanner(' + resp.result[i].id + ')" class="btn btn-danger remove mb-0" id="remove"><i class="fa-solid fa-trash-can fa-xl mb-1"></i></a>';
                    data += '</div>';
                    data += '</div>';
                    $('.after-add-more').append(data);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                 toastr.error(errorThrown, textStatus);
            }
        });

        // Save
        $('#content_id').on('change', function() {

            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){

                var contentId = $('select[name=content_id] option').filter(':selected').val();
                var type = $('#contenttype').val();

                $("#dvloader").show();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{ route("banner.store") }}',
                    data: {
                        content_id: contentId,
                        type: type
                    },
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_banner', '{{ route("banner.index") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                         toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                toastr.error('You have no right to add, edit, and delete.');
            }
        });

        // Delete Banner
        function DeleteBanner(id) {

            if(confirm('Are you sure !!! You want to Delete this Banner ?')) {

                var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
                if(Check_Admin == 1) {

                    $("#dvloader").show();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'GET',
                        url: "{{route('banner.show', '')}}" + "/" + id,
                        success: function(resp) {
                            $("#dvloader").hide();
                            get_responce_message(resp, 'save_banner', '{{route("banner.index")}}');
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
        }
    </script>
@endsection