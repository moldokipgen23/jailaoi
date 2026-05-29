@extends('admin.layout.page-app')
@section('page_title', __('label.ads_settings'))
@section('tab_title', __('label.ads_settings'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.ads_settings')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.ads_settings')}}</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-4">
                    <div class="card custom-border-card">
                        <h5 class="card-header">{{__('label.banner_ads')}}</h5>
                        <div class="card-body pb-0">
                            <form id="banner_ads">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{__('label.status')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="banner_ads_status" id="banner_ads_status_off" class="custom-control-input" {{ $data['banner_ads_status'] == 0 ? "checked" : "" }} value="0">
                                                    <label class="custom-control-label" for="banner_ads_status_off">{{__('label.off')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="banner_ads_status" id="banner_ads_status_on" name="banner_ads_status" class="custom-control-input" {{ $data['banner_ads_status'] == 1 ? "checked" : "" }} value="1">
                                                    <label class="custom-control-label" for="banner_ads_status_on">{{__('label.on')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{__('label.cost_per_view')}}<span class="text-danger">*</span></label>
                                            <input type="number" name="banner_ads_cpv" value="{{ $data['banner_ads_cpv'] }}" min="0" class="form-control" placeholder="{{__('label.coin_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{__('label.cost_per_click')}}<span class="text-danger">*</span></label>
                                            <input type="number" name="banner_ads_cpc" value="{{ $data['banner_ads_cpc'] }}" min="0" class="form-control" placeholder="{{__('label.coin_here')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="banner_ads()">{{__('label.save')}}</button>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card custom-border-card">
                        <h5 class="card-header">{{__('label.interstital_ads')}}</h5>
                        <div class="card-body pb-0">
                            <form id="interstital_ads">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{__('label.status')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="interstital_ads_status" id="interstital_ads_status_off" class="custom-control-input" {{ $data['interstital_ads_status'] == 0 ? "checked" : "" }} value="0">
                                                    <label class="custom-control-label" for="interstital_ads_status_off">{{__('label.off')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="interstital_ads_status" id="interstital_ads_status_on" class="custom-control-input" {{ $data['interstital_ads_status'] == 1 ? "checked" : "" }} value="1">
                                                    <label class="custom-control-label" for="interstital_ads_status_on">{{__('label.on')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{__('label.cost_per_view')}}<span class="text-danger">*</span></label>
                                            <input type="number" name="interstital_ads_cpv" value="{{ $data['interstital_ads_cpv'] }}" min="0" class="form-control" placeholder="{{__('label.coin_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{__('label.cost_per_click')}}<span class="text-danger">*</span></label>
                                            <input type="number" name="interstital_ads_cpc" value="{{ $data['interstital_ads_cpc'] }}" min="0" class="form-control" placeholder="{{__('label.coin_here')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="interstital_ads()">{{__('label.save')}}</button>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card custom-border-card">
                        <h5 class="card-header">{{__('label.reward_ads')}}</h5>
                        <div class="card-body pb-0">
                            <form id="reward_ads">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{__('label.status')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="reward_ads_status" id="reward_ads_status_off" class="custom-control-input" {{ $data['reward_ads_status'] == 0 ? "checked" : "" }} value="0">
                                                    <label class="custom-control-label" for="reward_ads_status_off">{{__('label.off')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="reward_ads_status" id="reward_ads_status_on" class="custom-control-input" {{ $data['reward_ads_status'] == 1 ? "checked" : "" }} value="1">
                                                    <label class="custom-control-label" for="reward_ads_status_on">{{__('label.on')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{__('label.cost_per_view')}}<span class="text-danger">*</span></label>
                                            <input type="number" name="reward_ads_cpv" value="{{ $data['reward_ads_cpv'] }}" min="0" class="form-control" placeholder="{{__('label.coin_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{__('label.cost_per_click')}}<span class="text-danger">*</span></label>
                                            <input type="number" name="reward_ads_cpc" value="{{ $data['reward_ads_cpc'] }}" min="0" class="form-control" placeholder="{{__('label.coin_here')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="reward_ads()">{{__('label.save')}}</button>
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
        let sidebarHeight = $('.sidebar')[0].scrollHeight;
        sidebar_down(sidebarHeight);

        function banner_ads() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                var formData = new FormData($("#banner_ads")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.adssetting.bannerads") }}',
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
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                showError();
            }
        }
        function interstital_ads() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                var formData = new FormData($("#interstital_ads")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.adssetting.interstitalads") }}',
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
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                showError();
            }
        }
        function reward_ads() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                var formData = new FormData($("#reward_ads")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.adssetting.rewardads") }}',
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