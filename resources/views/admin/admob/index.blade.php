@extends('admin.layout.page-app')
@section('page_title', __('label.admob_ads'))
@section('tab_title', __('label.admob_ads'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.admob_ads')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.admob_ads')}}</li>
                    </ol>
                </div>
            </div>

            <!-- Status -->
            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('label.admob_ads_status')}}</h5>
                <div class="card-body">
                    <form id="save_admob_status" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.status')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="admob_status" id="admob_status_on" class="custom-control-input" {{ $result['admob_status'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="admob_status_on">{{__('label.on')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="admob_status" id="admob_status_off" class="custom-control-input" {{ $result['admob_status'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="admob_status_off">{{__('label.off')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="save_admob_status()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>
            <!-- Android -->
            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('label.admob_android')}}</h5>
                <div class="card-body">
                    <form id="admob_android" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.banner_ads')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="banner_ad" id="banner_ad_yes" class="custom-control-input" {{ $result['banner_ad'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="banner_ad_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="banner_ad" id="banner_ad_no" class="custom-control-input" {{ $result['banner_ad'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="banner_ad_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.interstital_ads')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="interstital_ad" id="interstital_ad_yes" class="custom-control-input" {{ $result['interstital_ad'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="interstital_ad_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="interstital_ad" id="interstital_ad_no" class="custom-control-input" {{ $result['interstital_ad'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="interstital_ad_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.reward_ads')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="reward_ad" id="reward_ad_yes" class="custom-control-input" {{ $result['reward_ad'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="reward_ad_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="reward_ad" id="reward_ad_no" class="custom-control-input" {{ $result['reward_ad'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="reward_ad_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.banner_ads_id')}}</label>
                                    <input type="text" name="banner_adid" value="{{ $result['banner_adid'] }}" class="form-control" placeholder="{{__('label.id_here')}}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.interstital_ads_id')}}</label>
                                    <input type="text" name="interstital_adid" value="{{ $result['interstital_adid'] }}" class="form-control" placeholder="{{__('label.id_here')}}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.reward_ads_id')}}</label>
                                    <input type="text" name="reward_adid" value="{{ $result['reward_adid'] }}" class="form-control" placeholder="{{__('label.id_here')}}">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label></label>
                                    &nbsp;
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.interstital_ads_click')}}</label>
                                    <input type="text" name="interstital_adclick" value="{{ $result['interstital_adclick'] }}" class="form-control" placeholder="{{__('label.click_here')}}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.reward_ads_click')}}</label>
                                    <input type="text" name="reward_adclick" value="{{ $result['reward_adclick'] }}" class="form-control" placeholder="{{__('label.click_here')}}">
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="admob_android()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>
            <!-- IOS -->
            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('label.admob_ios')}}</h5>
                <div class="card-body">
                    <form id="admob_ios" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.banner_ads')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="ios_banner_ad" id="ios_banner_ad_yes" class="custom-control-input" {{ $result['ios_banner_ad'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="ios_banner_ad_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="ios_banner_ad" id="ios_banner_ad_no" class="custom-control-input" {{ $result['ios_banner_ad'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="ios_banner_ad_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.interstital_ads')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="ios_interstital_ad" id="ios_interstital_ad_yes" class="custom-control-input" {{ $result['ios_interstital_ad'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="ios_interstital_ad_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="ios_interstital_ad" id="ios_interstital_ad_no" class="custom-control-input" {{ $result['ios_interstital_ad'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="ios_interstital_ad_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.reward_ads')}}</label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="ios_reward_ad" id="ios_reward_ad_yes" class="custom-control-input" {{ $result['ios_reward_ad'] == 1 ? "checked" : "" }} value="1">
                                            <label class="custom-control-label" for="ios_reward_ad_yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="ios_reward_ad" id="ios_reward_ad_no" class="custom-control-input" {{ $result['ios_reward_ad'] == 0 ? "checked" : "" }} value="0">
                                            <label class="custom-control-label" for="ios_reward_ad_no">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.banner_ads_id')}}</label>
                                    <input type="text" name="ios_banner_adid" value="{{ $result['ios_banner_adid'] }}" class="form-control" placeholder="{{__('label.id_here')}}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.interstital_ads_id')}}</label>
                                    <input type="text" name="ios_interstital_adid" value="{{ $result['ios_interstital_adid'] }}" class="form-control" placeholder="{{__('label.id_here')}}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.reward_ads_id')}}</label>
                                    <input type="text" name="ios_reward_adid" value="{{ $result['ios_reward_adid'] }}" class="form-control" placeholder="{{__('label.id_here')}}">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label></label>
                                    &nbsp;
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.interstital_ads_click')}}</label>
                                    <input type="text" name="ios_interstital_adclick" value="{{ $result['ios_interstital_adclick'] }}" class="form-control" placeholder="{{__('label.click_here')}}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.reward_ads_click')}}</label>
                                    <input type="text" name="ios_reward_adclick" value="{{ $result['ios_reward_adclick'] }}" class="form-control" placeholder="{{__('label.click_here')}}">
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="admob_ios()">{{__('label.save')}}</button>
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

        function save_admob_status() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                var formData = new FormData($("#save_admob_status")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.admob.status") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_admob_status', '{{ route("admin.admob.index") }}');
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
        function admob_android() {
			var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                var formData = new FormData($("#admob_android")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.admob.android") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'admob_android', '{{ route("admin.admob.index") }}');
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
        function admob_ios() {
			var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                var formData = new FormData($("#admob_ios")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.admob.ios") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'admob_ios', '{{ route("admin.admob.index") }}');
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