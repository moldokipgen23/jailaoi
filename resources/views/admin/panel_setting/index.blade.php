@extends('admin.layout.page-app')
@section('page_title', __('label.panel_settings'))
@section('tab_title', __('label.panel_settings'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">

        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.panel_settings')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.panel_settings')}}</li>
                </ol>
            </div>
        </div>

        <div class="card custom-border-card">
            <h5 class="card-header">{{__('label.panel_login_page')}}</h5>
            <div class="card-body">
                <form id="pannel_setting" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="col-md-3 text_view">
                            <div class="form-group">
                                <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                                <div class="avatar-upload">
                                    <div class="avatar-edit">
                                        <input type='file' name="login_page_image" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                        <label for="imageUpload" title="{{__('label.upload_file')}}"></label>
                                    </div>
                                    <div class="avatar-preview">
                                        <img src="{{ $result['login_page_image'] }}" id="imagePreview">
                                    </div>
                                </div>
                                <input type="hidden" name="old_login_page_image" value="{{ $result['login_page_image'] }}">

                                <label class="mt-3 text-gray">{{__('label.ratio_2_3')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="save_panel_setting()">{{__('label.save')}}</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    // Sidebar Scroll Down
    let sidebarHeight = $('.sidebar')[0].scrollHeight;
    sidebar_down(sidebarHeight);

    function save_panel_setting() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var formData = new FormData($("#pannel_setting")[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route("panel_setting.save") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'pannel_setting', '{{ route("panel_setting.index") }}');
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