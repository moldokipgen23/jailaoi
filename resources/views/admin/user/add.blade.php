@extends('admin.layout.page-app')
@section('page_title', __('label.add_user'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.add_user')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.index') }}">{{__('label.user')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.add_user')}}</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('user.index') }}" class="btn btn-default mw-120 mb-3">{{__('label.user_list')}}</a>
            </div>
        </div>
        <!-- add user  -->
        <div class="card custom-border-card mt-3">
            <form id="user" autocomplete="off" enctype="multipart/form-data">
                <input type="hidden" name="id" value="">
                <div class="form-row">
                    <div class="col-md-8">
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.full_name')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" placeholder="{{__('label.full_name_here')}}" autofocus>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.email')}}<span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="{{__('label.email_here')}}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label> {{__('label.password')}}<span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" placeholder="{{__('label.password_here')}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{__('label.country_code')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="country_code" class="form-control" placeholder="{{__('label.+91')}}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label> {{__('label.mobile_number')}}<span class="text-danger">*</span></label>
                                    <input type="number" name="mobile_number" min="0" class="form-control" placeholder="{{__('label.mobile_number_here')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('label.country_name')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="country_name" class="form-control" placeholder="{{__('label.country_name_here')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="gender">{{__('label.gender')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="gender" name="gender" class="custom-control-input" value="1" checked>
                                            <label class="custom-control-label" for="gender">{{__('label.male')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="gender1" name="gender" class="custom-control-input" value="2">
                                            <label class="custom-control-label" for="gender1">{{__('label.female')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group ml-5">
                            <label class="ml-5">{{__('label.image')}}<span class="text-danger">*</span></label>
                            <div class="avatar-upload ml-5">
                                <div class="avatar-edit">
                                    <input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                    <label for="imageUpload" title="Select File"></label>
                                </div>
                                <div class="avatar-preview">
                                    <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_img.png" id="imagePreview">
                                </div>
                            </div>
                            <label class="mt-3 ml-5 text-gray">{{__('label.image_note')}}</label>
                        </div>
                    </div>
                </div>
                <div class="border-top pt-3 text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_user()">{{__('label.save')}}</button>
                    <a href="{{route('user.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    function save_user() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var formData = new FormData($("#user")[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route("user.store") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'user', '{{ route("user.index") }}');
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
</script>
@endsection