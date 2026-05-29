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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('label.login_page_view')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="panel_login_page_view" id="panel_login_page_view1" class="custom-control-input" {{ $result['panel_login_page_view'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="panel_login_page_view1">{{__('label.text_view')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="panel_login_page_view" id="panel_login_page_view2" class="custom-control-input" {{ $result['panel_login_page_view'] == 2 ? "checked" : "" }} value="2">
                                            <label class="custom-control-label" for="panel_login_page_view2">{{__('label.image_view')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text_view">
                                <div class="form-group">
                                    <label>{{__('label.background_image')}}<span class="text-danger">*</span></label>
                                    <div class="avatar-upload">
                                        <div class="avatar-edit">
                                            <input type='file' name="panel_login_page_bg_image" id="imageUpload1" accept=".png, .jpg, .jpeg" />
                                            <label for="imageUpload1" title="{{__('label.upload_file')}}"></label>
                                        </div>
                                        <div class="avatar-preview">
                                            <img src="{{ $result['panel_login_page_bg_image'] }}" id="imagePreview1">
                                        </div>
                                    </div>
                                    <input type="hidden" name="old_panel_login_page_bg_image" value="{{ $result['panel_login_page_bg_image'] }}">
                                    <input type="hidden" name="old_panel_login_page_bg_image_storage_type" value="{{ $result['panel_login_page_bg_image_storage_type'] }}">
                                    <label class="mt-3 text-gray">{{__('label.ratio_2_3')}}</label>
                                </div>
                            </div>
                            <div class="col-md-3 image_view">
                                <div class="form-group">
                                    <label>{{__('label.background_color')}}<span class="text-danger">*</span></label>
                                    <div class="input-group colorpicker-component">
                                        <input type="text" id="hexcolor-1" class="form-control hexcolor" value="{{ isset($result['panel_login_page_bg_color']) ? $result['panel_login_page_bg_color'] : ''}}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                        <input type="color" id="colorpicker-1" name="panel_login_page_bg_color" value="{{ isset($result['panel_login_page_bg_color']) ? $result['panel_login_page_bg_color'] : ''}}" class="colorpicker" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 image_view">
                                <div class="form-group">
                                    <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                                    <div class="avatar-upload ">
                                        <div class="avatar-edit">
                                            <input type='file' name="panel_login_page_image" id="imageUpload2" accept=".png, .jpg, .jpeg" />
                                            <label for="imageUpload2" title="{{__('label.upload_file')}}"></label>
                                        </div>
                                        <div class="avatar-preview">
                                            <img src="{{ $result['panel_login_page_image'] }}" id="imagePreview2">
                                        </div>
                                    </div>
                                    <input type="hidden" name="old_panel_login_page_image" value="{{ $result['panel_login_page_image'] }}">
                                    <input type="hidden" name="old_panel_login_page_image_storage_type" value="{{ $result['panel_login_page_image_storage_type'] }}">
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

        $(document).ready(function () {
			var page_view = <?php echo $result['panel_login_page_view'] ?>;
            if (page_view == 1) {
                $('.text_view').show();
                $('.image_view').hide();
            } else if(page_view == 2) {
                $('.text_view').hide();
                $('.image_view').show();
            } else {
                $('.text_view').hide();
                $('.image_view').hide();
            }

            $("input[name='panel_login_page_view']").change(function () {
                if ($(this).val() == "1") {
                    $('.text_view').show();
                    $('.image_view').hide();
                } else {
                    $('.text_view').hide();
                    $('.image_view').show();
                }
            });
		});

        // Color Picker
        $(document).ready(function() {
            // Event handler for color picker input change
            $('.colorpicker').on('input', function() {
                var target = $(this).attr('id').split('-')[1];
                $('#hexcolor-' + target).val(this.value.toUpperCase());
            });

            // Event handler for hex color input change
            $('.hexcolor').on('input', function() {
                var target = $(this).attr('id').split('-')[1];
                const hexPattern = /^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/;
                if (hexPattern.test(this.value)) {
                    $('#colorpicker-' + target).val(this.value);
                }
            });
        });

        function save_panel_setting(){

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#pannel_setting")[0]);
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.panelsetting.save") }}',
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'pannel_setting', '{{ route("admin.panelsetting.index") }}');
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