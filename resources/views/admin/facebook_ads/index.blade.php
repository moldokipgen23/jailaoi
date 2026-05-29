@extends('admin.layout.page-app')
@section('page_title', __('label.facebook_ads'))
@section('tab_title', __('label.facebook_ads'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.facebook_ads')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.facebook_ads')}}</li>
                    </ol>
                </div>
            </div>

            <!-- Status -->
            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('label.facebook_ads_status')}}</h5>
                <div class="card-body">
                    <form id="save_facebook_ads_status" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.status')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="facebook_ads_status" id="facebook_ads_on" class="custom-control-input" {{ $result['facebook_ads_status'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="facebook_ads_on">{{__('label.on')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="facebook_ads_status" id="facebook_ads_off" class="custom-control-input" {{ $result['facebook_ads_status'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="facebook_ads_off">{{__('label.off')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="save_facebook_ads_status()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>
            <!-- Android -->
            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('label.facebook_ads_android')}}</h5>
                <div class="card-body">
                    <form id="fbad_android" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.native_status')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_native_status" id="fb_native_status_yes" class="custom-control-input" {{ $result['fb_native_status'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="fb_native_status_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_native_status" id="fb_native_status_no" class="custom-control-input" {{ $result['fb_native_status'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="fb_native_status_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.banner_status')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_banner_status" id="fb_banner_status_yes" class="custom-control-input" {{ $result['fb_banner_status'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="fb_banner_status_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_banner_status" id="fb_banner_status_no" class="custom-control-input" {{ $result['fb_banner_status'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="fb_banner_status_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.interstiatial_status')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_interstiatial_status" id="fb_interstiatial_status_yes" class="custom-control-input" {{ $result['fb_interstiatial_status'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="fb_interstiatial_status_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_interstiatial_status" id="fb_interstiatial_status_no" class="custom-control-input" {{ $result['fb_interstiatial_status'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="fb_interstiatial_status_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.native_key')}}</label>
                                    <input type="text" name="fb_native_id" value="{{ $result['fb_native_id'] }}" class="form-control" placeholder="{{__('label.key_here')}}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.banner_key')}}</label>
                                    <input type="text" name="fb_banner_id" value="{{ $result['fb_banner_id'] }}" class="form-control" placeholder="{{__('label.key_here')}}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.interstiatial_key')}}</label>
                                    <input type="text" name="fb_interstiatial_id" value="{{ $result['fb_interstiatial_id'] }}" class="form-control" placeholder="{{__('label.key_here')}}">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group col-lg-6">
                                    <label>{{__('label.rewardvideo_status')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_rewardvideo_status" id="fb_rewardvideo_status_yes" class="custom-control-input" {{ $result['fb_rewardvideo_status'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="fb_rewardvideo_status_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_rewardvideo_status" id="fb_rewardvideo_status_no" class="custom-control-input" {{ $result['fb_rewardvideo_status'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="fb_rewardvideo_status_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group col-lg-6">
                                    <label>{{__('label.native_full_status')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_native_full_status" id="fb_native_full_status_yes" class="custom-control-input" {{ $result['fb_native_full_status'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="fb_native_full_status_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_native_full_status" id="fb_native_full_status_no" class="custom-control-input" {{ $result['fb_native_full_status'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="fb_native_full_status_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.rewardvideo_status_key')}}</label>
                                    <input type="text" name="fb_rewardvideo_id" value="{{ $result['fb_rewardvideo_id'] }}" class="form-control" placeholder="{{__('label.key_here')}}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.native_full_key')}}</label>
                                    <input type="text" name="fb_native_full_id" value="{{ $result['fb_native_full_id'] }}" class="form-control" placeholder="{{__('label.key_here')}}">
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="fbad_android()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>
            <!-- iOS -->
            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('label.facebook_ads_ios')}}</h5>
                <div class="card-body">
                    <form id="fbad_ios" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.native_status')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_ios_native_status" id="fb_ios_native_status_yes" class="custom-control-input" {{ $result['fb_ios_native_status'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="fb_ios_native_status_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_ios_native_status" id="fb_ios_native_status_no" class="custom-control-input" {{ $result['fb_ios_native_status'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="fb_ios_native_status_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.banner_status')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_ios_banner_status" id="fb_ios_banner_status_yes" class="custom-control-input" {{ $result['fb_ios_banner_status'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="fb_ios_banner_status_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_ios_banner_status" id="fb_ios_banner_status_no" class="custom-control-input" {{ $result['fb_ios_banner_status'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="fb_ios_banner_status_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.interstiatial_status')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_ios_interstiatial_status" id="fb_ios_interstiatial_status_yes" class="custom-control-input" {{ $result['fb_ios_interstiatial_status'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="fb_ios_interstiatial_status_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_ios_interstiatial_status" id="fb_ios_interstiatial_status_no" class="custom-control-input" {{ $result['fb_ios_interstiatial_status'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="fb_ios_interstiatial_status_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.native_key')}}</label>
                                    <input type="text" name="fb_ios_native_id" value="{{ $result['fb_ios_native_id'] }}" class="form-control" placeholder="{{__('label.key_here')}}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.banner_key')}}</label>
                                    <input type="text" name="fb_ios_banner_id" value="{{ $result['fb_ios_banner_id'] }}" class="form-control" placeholder="{{__('label.key_here')}}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.interstiatial_key')}}</label>
                                    <input type="text" name="fb_ios_interstiatial_id" value="{{ $result['fb_ios_interstiatial_id'] }}" class="form-control" placeholder="{{__('label.key_here')}}">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group col-lg-6">
                                    <label>{{__('label.rewardvideo_status')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_ios_rewardvideo_status" id="fb_ios_rewardvideo_status_yes" class="custom-control-input" {{ $result['fb_ios_rewardvideo_status'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="fb_ios_rewardvideo_status_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_ios_rewardvideo_status" id="fb_ios_rewardvideo_status_no" class="custom-control-input" {{ $result['fb_ios_rewardvideo_status'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="fb_ios_rewardvideo_status_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group col-lg-6">
                                    <label>{{__('label.native_full_status')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_ios_native_full_status" id="fb_ios_native_full_status_yes" class="custom-control-input" {{ $result['fb_ios_native_full_status'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="fb_ios_native_full_status_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fb_ios_native_full_status" id="fb_ios_native_full_status_no" class="custom-control-input" {{ $result['fb_ios_native_full_status'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="fb_ios_native_full_status_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.rewardvideo_status_key')}}</label>
                                    <input type="text" name="fb_ios_rewardvideo_id" value="{{ $result['fb_ios_rewardvideo_id'] }}" class="form-control" placeholder="{{__('label.key_here')}}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.native_full_key')}}</label>
                                    <input type="text" name="fb_ios_native_full_id" value="{{ $result['fb_ios_native_full_id'] }}" class="form-control" placeholder="{{__('label.key_here')}}">
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="fbad_ios()">{{__('label.save')}}</button>
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

        function save_facebook_ads_status() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                var formData = new FormData($("#save_facebook_ads_status")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.fbads.status") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_facebook_ads_status', '{{ route("admin.fbads.index") }}');
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
        function fbad_android() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                var formData = new FormData($("#fbad_android")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.fbads.android") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'fbad_android', '{{ route("admin.fbads.index") }}');
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
        function fbad_ios() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                var formData = new FormData($("#fbad_ios")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.fbads.ios") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'fbad_ios', '{{ route("admin.fbads.index") }}');
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