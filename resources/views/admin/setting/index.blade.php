@extends('admin.layout.page-app')
@section('page_title', __('Label.Settings'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('Label.Settings')}}</h1>

            <div class="border-bottom row">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('Label.Setting')}}</li>
                    </ol>
                </div>
            </div>

            <ul class="nav nav-pills custom-tabs inline-tabs" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="app-tab" data-toggle="tab" href="#app" role="tab" aria-controls="app" aria-selected="true">{{__('Label.APP SETTINGS')}}</a>
                </li>
                @if(Check_Admin_Access() == 1)
                <li class="nav-item">
                    <a class="nav-link" id="smtp-tab" data-toggle="tab" href="#smtp" role="tab" aria-controls="smtp" aria-selected="false">SMTP</a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" id="social-links-tab" data-toggle="tab" href="#social-links" role="tab" aria-controls="smtp" aria-selected="true">{{__('Label.social_links')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="onboarding-tab" data-toggle="tab" href="#onboarding" role="tab" aria-controls="onboarding" aria-selected="false">Onboarding Screen</a>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="app" role="tabpanel" aria-labelledby="app-tab">
                    <div class="card custom-border-card">
                        <h5 class="card-header">{{__('Label.App Settings')}}</h5>
                        <div class="card-body">
                            <form id="app_setting" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="form-row">
                                    <div class="col-md-9">
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>{{__('Label.App Name')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="app_name" class="form-control" placeholder="Enter App Name" value="{{$result['app_name']}}" autofocus>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>{{__('Label.Host Email')}}<span class="text-danger">*</span></label>
                                                <input type="email" name="host_email" class="form-control" value="{{$result['host_email']}}" placeholder="Enter Host Email">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>{{__('Label.App Version')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="app_version" class="form-control" value="{{$result['app_version']}}" placeholder="Enter App Version">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>{{__('Label.Author')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="author" class="form-control" value="{{$result['author']}}" placeholder="Enter Author">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>{{__('Label.Email')}}<span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" value="{{$result['email']}}" placeholder="Enter Email">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>{{__('Label.Contact')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="contact" class="form-control" value="{{$result['contact']}}" placeholder="Enter Contact">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>{{__('Label.website')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="website" class="form-control" value="{{$result['website']}}" placeholder="Enter Your Website">
                                            </div>
                                            <div class="form-group col-md-8">
                                                <label>{{__('Label.APP DESCRIPATION')}}<span class="text-danger">*</span></label>
                                                <textarea name="app_desripation" rows="1" class="form-control" placeholder="Enter App Desripation">{{$result['app_desripation']}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group ml-5">
                                            <label class="ml-5">App Icon<span class="text-danger">*</span></label>
                                            <div class="avatar-upload ml-5">
                                                <div class="avatar-edit">
                                                    <input type='file' name="app_logo" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                                    <label for="imageUpload" title="Select File"></label>
                                                </div>
                                                <div class="avatar-preview">
                                                    <img src="{{$result['app_logo']}}" alt="upload_img.png" id="imagePreview">
                                                </div>
                                            </div>
                                            <input type="hidden" name="old_app_logo" value="{{$result['app_logo']}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="app_setting()">{{__('Label.SAVE')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="card custom-border-card">
                                <h5 class="card-header">API Configrations</h5>
                                <div class="card-body">
                                    <div class="input-group">
                                        <div class="col-2">
                                            <label class="pt-3" style="font-size:16px; font-weight:500; color:#1b1b1b">{{__('Label.API Path')}}</label>
                                        </div>
                                        <input type="text" readonly value="{{url('/')}}/api/" name="api_path" class="form-control" id="api_path">
                                        <div class="input-group-text ml-2" onclick="Function_Api_path()" title="Copy">
                                            <i class="fa-solid fa-copy fa-2xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card custom-border-card">
                                <h5 class="card-header">{{__('Label.Currency Settings')}}</h5>
                                <div class="card-body">
                                    <form id="save_currency" enctype="multipart/form-data">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>{{__('Label.Currency Name')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="currency" class="form-control" value="{{$result['currency']}}" placeholder="Enter Currency Name">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>{{__('Label.Currency Code')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="currency_code" class="form-control" value="{{$result['currency_code']}}" placeholder="Enter Currency Code">
                                            </div>
                                        </div>
                                        <div class="border-top pt-3 text-right">
                                            <button type="button" class="btn btn-default mw-120" onclick="save_currency()">{{__('Label.SAVE')}}</button>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="smtp" role="tabpanel" aria-labelledby="smtp-tab">
                    <div class="card custom-border-card">
                        <h5 class="card-header">{{__('Label.Email Setting [SMTP]')}}</h5>
                        <div class="card-body">
                            <form id="smtp_setting">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="id" value="@if($smtp){{$smtp->id}}@endif">
                                <div class="form-row">
                                    <div class="form-group  col-md-3">
                                        <label>{{__('Label.IS SMTP Active')}}<span class="text-danger">*</span></label>
                                        <select name="status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="0" @if($smtp){{ $smtp->status == 0  ? 'selected' : ''}}@endif>{{__('Label.No')}}</option>
                                            <option value="1" @if($smtp){{ $smtp->status == 1  ? 'selected' : ''}}@endif>{{__('Label.Yes')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('Label.Host')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="host" class="form-control" value="@if($smtp){{$smtp->host}}@endif" placeholder="Enter Host">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('Label.Port')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="port" class="form-control" value="@if($smtp){{$smtp->port}}@endif" placeholder="Enter Port">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('Label.Protocol')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="protocol" class="form-control" value="@if($smtp){{$smtp->protocol}}@endif" placeholder="Enter Protocol">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>{{__('Label.User name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="user" class="form-control" value="@if($smtp){{$smtp->user}}@endif" placeholder="Enter User Name">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('Label.Password')}}<span class="text-danger">*</span></label>
                                        <input type="password" name="pass" class="form-control" value="@if($smtp){{$smtp->pass}}@endif" placeholder="Enter Password">
                                        <label class="mt-1 text-gray">Search for better result <a href="https://support.google.com/mail/answer/185833?hl=en" target="_blank" class="btn-link">Click Here</a></label>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('Label.From name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="from_name" class="form-control" value="@if($smtp){{$smtp->from_name}}@endif" placeholder="Enter From Name">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('Label.From Email')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="from_email" class="form-control" value="@if($smtp){{$smtp->from_email}}@endif" placeholder="Enter From Email">
                                    </div>
                                </div>
                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="smtp_setting()">{{__('Label.SAVE')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- social link -->
                <div class="tab-pane fade" id="social-links" role="tabpanel" aria-labelledby="social-links-tab">
                    <form id="edit_social_links" autocomplete="off" method="post" enctype="multipart/form-data">
                        <div class="card custom-border-card mt-3">
                            <h5 class="card-header">{{__('Label.social_links')}}</h5>
                            <div class="card-body">
                                <div class="main_step form-row mt-3 mb-3">
                                    <div class="col-md-3">
                                        <input type="hidden" name="step_id[]" value="">
                                        <div class="form-group">
                                            <label>{{__('Label.Name')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="step_name[]" class="form-control" placeholder="{{__('Label.enter_name')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{__('Label.URL')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="step_url[]" class="form-control" placeholder="{{__('Label.enter_url')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{__('Label.Image')}}<span class="text-danger">*</span></label>
                                            <input type="file" class="form-control step-image import-file" name="step_image[]" accept="image/png, image/jpg, image/jpeg" preview-id="Uploaded-step-Image">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <div class="custom-file ml-2">
                                                <img src="{{asset('assets/imgs/upload_img.png')}}" style="height:90px; width: 120px;" class="img-thumbnail" id="Uploaded-step-Image">
                                                <input type="hidden" name="old_step_image[]" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <div class="flex-grow-1 px-5 d-inline-flex">
                                            <div class="change mr-3 mt-4" id="add_btn" title="Add More">
                                                <a class="add_new_step btn btn-success add-more text-white">+</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @foreach($sociallink as $index => $result)
                                <div class="main_step form-row mt-3 mb-3">
                                    <div class="col-md-3">
                                        <input type="hidden" name="step_id[]" value="{{ $result->id }}">
                                        <div class="form-group">
                                            <label>{{__('Label.Name')}}<span class="text-danger">*</span></label>
                                            <input type="text" value="{{ $result->name }}" name="step_name[]" class="form-control" placeholder="{{__('Label.enter_name')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{__('Label.URL')}}<span class="text-danger">*</span></label>
                                            <input type="text" value="{{ $result->url }}" name="step_url[]" class="form-control" placeholder="{{__('Label.enter_url')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{__('Label.Image')}}<span class="text-danger">*</span></label>
                                            <input type="file" class="form-control step-image import-file" name="step_image[]" accept="image/png, image/jpg, image/jpeg" preview-id="Uploaded-step-Image-{{ $index }}">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-grup">
                                            <div class="custom-file ml-2">
                                                <img src="{{ $result->image }}" style="height: 90px; width: 120px;" class="img-thumbnail" id="Uploaded-step-Image-{{ $index }}">
                                                <input type="hidden" name="old_step_image[]" value="{{ $result->image }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <div class="flex-grow-1 px-5 d-inline-flex">
                                            <div class="change mr-3 mt-4" id="add_btn" title="Remove">
                                                <a class="remove_step btn btn-danger add-more text-white">-</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                <div class="new_step"></div>
                            </div>
                            <div class="border-top pt-3 text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="save_social_links()">{{__('Label.SAVE')}}</button>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </div>
                        </div>
                    </form>
                </div>

                <!-- onboarding screen -->
                <div class="tab-pane fade" id="onboarding" role="tabpanel" aria-labelledby="onboarding-tab">
                    <div class="card custom-border-card">
                        <h5 class="card-header">Onboarding Screen</h5>

                        <div class="card-body">
                            <form id="onboarding_form" enctype="multipart/form-data">
                                <div class="row col-md-12">
                                    <div class="form-group col-md-6">
                                        <label>Title<span class="text-danger">*</span></label>
                                        <input type="text" name="title[]" class="form-control" placeholder="Enter Title">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Image<span class="text-danger">*</span></label>
                                        <input type="file" name="image[]" class="form-control import-file on_boarding_img" id="on_boarding_img" accept=".png, .jpg, .jpeg">
                                        <input type="hidden" name="old_image[]" value="">
                                    </div>
                                    <div class="form-group col-md-1">
                                        <div class="custom-file">
                                            <img src="{{asset('assets/imgs/upload_img.png')}}" style="height: 90px; width: 90px;" class="img-thumbnail" id="link_img_on_boarding_img">
                                        </div>
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <div class="flex-grow-1 px-5 d-inline-flex">
                                            <div class="change mr-3 mt-4" id="add_btn" title="Add More">
                                                <a class="btn btn-success add-more text-white" onclick="add_more_screen()">+</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @for ($i=0; $i < count($onboarding_screen); $i++)
                                    <div class="onboarding_part">
                                        <div class="row col-lg-12">
                                            <div class="form-group col-md-6">
                                                <label>Title<span class="text-danger">*</span></label>
                                                <input type="text" name="title[]" value="{{ $onboarding_screen[$i]['title'] }}" class="form-control" placeholder="Enter Title">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>Image<span class="text-danger">*</span></label>
                                                <input type="file" name="image[]" class="form-control import-file on_boarding_img" id="on_boarding_img{{$i}}" accept=".png, .jpg, .jpeg">
                                                <input type="hidden" name="old_image[]" value="{{ basename($onboarding_screen[$i]['image']) }}">
                                            </div>
                                            <div class="form-group col-md-1">
                                                <div class="custom-file">
                                                    <img src="{{$onboarding_screen[$i]['image']}}" style="height: 90px; width: 90px;" class="img-thumbnail" id="link_img_on_boarding_img{{$i}}">
                                                </div>
                                            </div>
                                            <div class="col-md-1 mt-2">
                                                <div class="flex-grow-1 px-5 d-inline-flex">
                                                    <div class="change mr-3 mt-4" id="add_btn" title="Remove">
                                                        <a class="btn btn-danger text-white remove_on_boarding">-</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor

                                <div class="after-add-more-on-boarding"></div>

                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="onboarding()">{{__('Label.SAVE')}}</button>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
        // Sidebar Scroll Down
		sidebar_down($(document).height());
        
        function Function_Api_path() {
            /* Get the text field */
            var copyText = document.getElementById("api_path");

            /* Select the text field */
            copyText.select();
            copyText.setSelectionRange(0, 99999); /* For mobile devices */

            document.execCommand('copy');

            /* Alert the copied text */
            alert("Copied the API Path: " + copyText.value);
        }
        function app_setting() {

            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){

                var formData = new FormData($("#app_setting")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("setting.app") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'app_setting', '{{ route("setting") }}');
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
        function save_currency() {

            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){

                var formData = new FormData($("#save_currency")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("setting.currency") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        $("html, body").animate({
                            scrollTop: 0
                        }, "swing");
                        get_responce_message(resp);
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
        function smtp_setting() {

            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){

                var formData = new FormData($("#smtp_setting")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("smtp.save") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        $("html, body").animate({scrollTop: 0}, "swing");
                        get_responce_message(resp);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                toastr.error('You have no right to add, edit, and delete.');
            }
        }

        // Multipal Img Show 
        $(document).on('change', '.on_boarding_img', function(){
            readURL(this, this.id);
        });
        function readURL(input, id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                 
                reader.onload = function (e) {
                    $('#link_img_'+id).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // OnBoarding Screen Add-Remove Link Part
        var i = -1;
        function add_more_screen(){

            var data = '<div class="onboarding_part">';
                data += '<div class="row col-md-12">';
                data += '<div class="form-group col-md-6">';
                data += '<label>Title<span class="text-danger">*</span></label>';
                data += '<input type="text" name="title[]" class="form-control" placeholder="Enter Title">';
                data += '</div>';
                data += '<div class="form-group col-lg-3">';
                data += '<label>Image<span class="text-danger">*</span></label>';
                data += '<input type="file" name="image[]" class="form-control import-file on_boarding_img" id="on_boarding_img_'+i+'" accept=".png, .jpg, .jpeg">';
                data += '<input type="hidden" name="old_image[]" value="">';
                data += '</div>';
                data += '<div class="form-group col-md-1">';
                data += '<div class="custom-file">';
                data += '<img src="{{asset("assets/imgs/upload_img.png")}}" style="height: 90px; width: 90px;" class="img-thumbnail" id="link_img_on_boarding_img_'+i+'">';
                data += '</div>';
                data += '</div>';
                data += '<div class="col-md-1 mt-2">';
                data += '<div class="flex-grow-1 px-5 d-inline-flex">';
                data += '<div class="change mr-3 mt-4" id="add_btn" title="Remove">';
                data += '<a class="btn btn-danger add-more text-white remove_on_boarding">-</a>';
                data += '</div>';
                data += '</div>';
                data += '</div>';
                data += '</div>';
                data += '</div>';

            $('.after-add-more-on-boarding').append(data);
            i--;
            $("html, body").animate({ scrollTop: $(document).height() }, "slow");
        }
        $("body").on("click", ".remove_on_boarding", function(e) {
            $(this).parents('.onboarding_part').remove();
        });
        // OnBoarding Screen Save
        function onboarding() {

            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){

                $("#dvloader").show();
                var formData = new FormData($("#onboarding_form")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("setting.obboardingscreen") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'onboarding_form', '{{ route("setting") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                toastr.error('You have no right to Add, Edit and Delete.');
            }
        }

         // social links
        function save_social_links() {
            var isAdmin = <?php echo Check_Admin_Access(); ?>;
            if (isAdmin == 1) {

                $("#dvloader").show();
                var formData = new FormData($("#edit_social_links")[0]);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    enctype: 'multipart/form-data',
                    type: 'POST',
                    url: '{{ route("setting.sociallinksupdate") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        console.log(resp.data);

                        get_responce_message(resp, 'edit_social_links');
                        // Reload the page
                        location.reload();

                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                toastr.error('You have no right to add edit and delete');
            }
        }

        let stepIndex = 0;
        $(document).on('change', '.step-image', function() {
            let reader = new FileReader();
            let previewId = $(this).attr('preview-id');

            reader.readAsDataURL(this.files[0]);
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result);
            };
        });

        $(document).on('click', '.add_new_step', function() {
            let newStep = `
                <div class="step form-row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{__('Label.Name')}}<span class="text-danger">*</span></label>
                            <input type="text" name="step_name[]" class="form-control" placeholder="{{__('Label.enter_name')}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{__('Label.URL')}}<span class="text-danger">*</span></label>
                            <input type="text" name="step_url[]" class="form-control" placeholder="{{__('Label.enter_url')}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{__('Label.Image')}}<span class="text-danger">*</span></label>
                            <input type="file" class="form-control step-image import-file" name="step_image[]" accept="image/png, image/jpg, image/jpeg" preview-id="Uploaded-Image-${stepIndex}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <div class="custom-file ml-2">
                                <img src="{{asset('assets/imgs/upload_img.png')}}" style="height: 90px; width: 120px;" class="img-thumbnail" id="Uploaded-Image-${stepIndex}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 mt-2">
                        <div class="flex-grow-1 px-5 d-inline-flex">
                            <div class="change mr-3 mt-4" id="add_btn" title="Remove">
                                <a class="remove_step btn btn-danger add-more text-white">-</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('.new_step').append(newStep);
            stepIndex++;
        });

        // remove step ot main step
        $(document).on('click', '.remove_step', function() {
            $(this).closest('.step, .main_step').remove();
        });
    </script>
@endsection