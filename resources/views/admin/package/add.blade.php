@extends('admin.layout.page-app')
@section('page_title', __('label.add_package'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.add_package')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('package.index') }}">{{__('label.package')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.add_package')}}</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('package.index') }}" class="btn btn-default mw-120 mb-3">{{__('label.package_list')}}</a>
            </div>
        </div>
        <!-- add package  -->
        <div class="card custom-border-card">
            <div class="card-body">
                <form id="package" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" name="id">
                    <div class="form-row">
                        <div class="col-md-9">
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" placeholder="{{__('label.name_here')}}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{__('label.price')}}<span class="text-danger">*</span></label>
                                        <input type="number" name="price" min="0" class="form-control" placeholder="{{__('label.price_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.package_time')}}<span class="text-danger">*</span></label>
                                        <select class="form-control" id="validity_type" name="type">
                                            <option value="">{{__('label.select_type')}}</option>
                                            <option value="Day">{{__('label.day')}}</option>
                                            <option value="Week">{{__('label.week')}}</option>
                                            <option value="Month">{{__('label.month')}}</option>
                                            <option value="Year">{{__('label.year')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <div class="form-group mt-2">
                                        <select class="form-control time" id="time" name="time">
                                            <option value="">{{__('label.select_number')}}</option>
                                            @for($i=1; $i<=31; $i++)
                                                <option value="{{$i}}">{{$i}}</option>
                                                @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.android_package')}}</label>
                                        <input name="android_product_package" type="text" class="form-control" placeholder="{{__('label.android_package_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.ios_package')}}</label>
                                        <input name="ios_product_package" type="text" class="form-control" placeholder="{{__('label.ios_package_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.web_package')}}</label>
                                        <input name="web_product_package" type="text" class="form-control" placeholder="{{__('label.web_package_here')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.color')}}<span class="text-danger">*</span></label>
                                        <div class="input-group colorpicker-component">
                                            <input type="text" id="hexcolor-1" class="form-control hexcolor" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                            <input type="color" id="colorpicker-1" name="color" class="colorpicker" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.device_limit')}}</label>
                                        <input name="device_limit" type="number" class="form-control" placeholder="{{__('label.device_limit_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="is_download">{{__('label.is_download')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="is_download" name="is_download" class="custom-control-input" value="1">
                                                <label class="custom-control-label" for="is_download">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="is_download1" name="is_download" class="custom-control-input" value="0" checked>
                                                <label class="custom-control-label" for="is_download1">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 ml-5">
                            <div class="form-group ml-3">
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
                        <button type="button" class="btn btn-default mw-120" onclick="save_package()">{{__('label.save')}}</button>
                        <a href="{{route('package.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
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
    sidebar_down($(document).height());

    function save_package() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {
            $("#dvloader").show();
            var formData = new FormData($("#package")[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route("package.store") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'package', '{{ route("package.index") }}');
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

    $('.time').hide();
    $('#validity_type').on('click', function() {

        $('.time').show();
        var type = $("#validity_type").val()

        for (let i = 1; i <= 31; i++) {
            $(".time option[value=" + i + "]").show();
            $(".time option[value=" + i + "]").attr("selected", false);
        }

        if (type == "Day") {
            for (let i = 8; i <= 31; i++) {
                $(".time option[value=" + i + "]").hide();
            }
        } else if (type == "Week") {
            for (let i = 5; i <= 31; i++) {
                $(".time option[value=" + i + "]").hide();
            }
        } else if (type == "Month") {
            for (let i = 13; i <= 31; i++) {
                $(".time option[value=" + i + "]").hide();
            }
        } else if (type == "Year") {
            for (let i = 2; i <= 31; i++) {
                $(".time option[value=" + i + "]").hide();
            }
        } else {
            $('.time').hide();
        }
    })

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
</script>
@endsection