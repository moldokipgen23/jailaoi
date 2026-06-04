@extends('admin.layout.page-app')
@section('page_title', __('label.facebook_ads_settings'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.facebook_ads_settings')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.facebook_ads_settings')}}</li>
                </ol>
            </div>
        </div>
        <!-- android settings  -->
        <div class="card custom-border-card mt-3">
            <h5 class="card-header">{{__('label.facebook_ads_android_settings')}}</h5>
            <div class="card-body">
                <form id="fbad">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label for="fb_native_status">{{__('label.native_status')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_native_status" name="fb_native_status" class="custom-control-input" {{ ($result['fb_native_status']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="fb_native_status">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_native_status1" name="fb_native_status" class="custom-control-input" {{ ($result['fb_native_status']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="fb_native_status1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label for="fb_banner_status">{{__('label.banner_status')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_banner_status" name="fb_banner_status" class="custom-control-input" {{($result['fb_banner_status']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="fb_banner_status">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_banner_status1" name="fb_banner_status" class="custom-control-input" {{ ($result['fb_banner_status']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="fb_banner_status1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group col-lg-6">
                                <label for="fb_native_full_status">{{__('label.native_full_status')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_native_full_status" name="fb_native_full_status" class="custom-control-input" {{($result['fb_native_full_status']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="fb_native_full_status">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_native_full_status1" name="fb_native_full_status" class="custom-control-input" {{ ($result['fb_native_full_status']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="fb_native_full_status1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.native_key')}}</label>
                                <input type="text" name="fb_native_id" class="form-control" value="{{$result['fb_native_id']}}" placeholder="{{__('label.native_key_here')}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.banner_key')}}</label>
                                <input type="text" name="fb_banner_id" class="form-control" value="{{$result['fb_banner_id']}}" placeholder="{{__('label.banner_key_here')}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.native_full_key')}}</label>
                                <input type="text" name="fb_native_full_id" class="form-control" value="{{$result['fb_native_full_id']}}" placeholder="{{__('label.native_full_key_here')}}">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group col-lg-6">
                                <label for="fb_rewardvideo_status">{{__('label.reward_video_status')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_rewardvideo_status" name="fb_rewardvideo_status" class="custom-control-input" {{($result['fb_rewardvideo_status']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="fb_rewardvideo_status">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_rewardvideo_status1" name="fb_rewardvideo_status" class="custom-control-input" {{ ($result['fb_rewardvideo_status']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="fb_rewardvideo_status1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label for="fb_interstiatial_status">{{__('label.interstiatial_status')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_interstiatial_status" name="fb_interstiatial_status" class="custom-control-input" {{($result['fb_interstiatial_status']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="fb_interstiatial_status">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_interstiatial_status1" name="fb_interstiatial_status" class="custom-control-input" {{ ($result['fb_interstiatial_status']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="fb_interstiatial_status1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.reward_video_status_key')}}</label>
                                <input type="text" name="fb_rewardvideo_id" class="form-control" value="{{$result['fb_rewardvideo_id']}}" placeholder="{{__('label.reward_video_status_key_here')}}">
                            </div>
                            <div class="form-group">
                                <label>{{__('label.reward_video_status_ad_click')}}</label>
                                <input type="text" name="fb_reward_adclick" class="form-control" value="{{$result['fb_reward_adclick']}}" placeholder="{{__('label.reward_video_status_ad_click_here')}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.interstiatial_key')}}</label>
                                <input type="text" name="fb_interstiatial_id" class="form-control" value="{{$result['fb_interstiatial_id']}}" placeholder="{{__('label.interstiatial_key_here')}}">
                            </div>
                            <div class="form-group">
                                <label>{{__('label.interstital_ad_click')}}</label>
                                <input type="text" name="fb_interstital_adclick" class="form-control" value="{{$result['fb_interstital_adclick']}}" placeholder="{{__('label.interstital_ad_click_here')}}">
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="fbad()">{{__('label.save')}}</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- ios settings  -->
        <div class="card custom-border-card mt-3">
            <h5 class="card-header">{{__('label.facebook_ads_ios_settings')}}</h5>
            <div class="card-body">
                <form id="fbad_ios">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label for="fb_ios_native_status">{{__('label.native_status')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_ios_native_status" name="fb_ios_native_status" class="custom-control-input" {{ ($result['fb_ios_native_status']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="fb_ios_native_status">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_ios_native_status1" name="fb_ios_native_status" class="custom-control-input" {{ ($result['fb_ios_native_status']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="fb_ios_native_status1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label for="fb_ios_banner_status">{{__('label.banner_status')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_ios_banner_status" name="fb_ios_banner_status" class="custom-control-input" {{($result['fb_ios_banner_status']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="fb_ios_banner_status">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_ios_banner_status1" name="fb_ios_banner_status" class="custom-control-input" {{ ($result['fb_ios_banner_status']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="fb_ios_banner_status1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group col-lg-6">
                                <label for="fb_ios_native_full_status">{{__('label.native_full_status')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_ios_native_full_status" name="fb_ios_native_full_status" class="custom-control-input" {{($result['fb_ios_native_full_status']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="fb_ios_native_full_status">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_ios_native_full_status1" name="fb_ios_native_full_status" class="custom-control-input" {{ ($result['fb_ios_native_full_status']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="fb_ios_native_full_status1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.native_key')}}</label>
                                <input type="text" name="fb_ios_native_id" class="form-control" value="{{$result['fb_ios_native_id']}}" placeholder="{{__('label.native_key_here')}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.banner_key')}}</label>
                                <input type="text" name="fb_ios_banner_id" class="form-control" value="{{$result['fb_ios_banner_id']}}" placeholder="{{__('label.banner_key_here')}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.native_full_key')}}</label>
                                <input type="text" name="fb_ios_native_full_id" class="form-control" value="{{$result['fb_ios_native_full_id']}}" placeholder="{{__('label.native_full_key_here')}}">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group col-lg-6">
                                <label for="fb_ios_rewardvideo_status">{{__('label.reward_video_status')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_ios_rewardvideo_status" name="fb_ios_rewardvideo_status" class="custom-control-input" {{($result['fb_ios_rewardvideo_status']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="fb_ios_rewardvideo_status">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_ios_rewardvideo_status1" name="fb_ios_rewardvideo_status" class="custom-control-input" {{ ($result['fb_ios_rewardvideo_status']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="fb_ios_rewardvideo_status1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label for="fb_ios_interstiatial_status">{{__('label.interstiatial_status')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_ios_interstiatial_status" name="fb_ios_interstiatial_status" class="custom-control-input" {{($result['fb_ios_interstiatial_status']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="fb_ios_interstiatial_status">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="fb_ios_interstiatial_status1" name="fb_ios_interstiatial_status" class="custom-control-input" {{ ($result['fb_ios_interstiatial_status']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="fb_ios_interstiatial_status1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.reward_video_status_key')}}</label>
                                <input type="text" name="fb_ios_rewardvideo_id" class="form-control" value="{{$result['fb_ios_rewardvideo_id']}}" placeholder="{{__('label.reward_video_status_key_here')}}">
                            </div>
                            <div class="form-group">
                                <label>{{__('label.reward_video_status_ad_click')}}</label>
                                <input type="text" name="fb_ios_reward_adclick" class="form-control" placeholder="{{__('label.reward_video_status_ad_click_here')}}" value="{{$result['fb_ios_reward_adclick']}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.interstiatial_key')}}</label>
                                <input type="text" name="fb_ios_interstiatial_id" class="form-control" value="{{$result['fb_ios_interstiatial_id']}}" placeholder="{{__('label.interstiatial_key_here')}}">
                            </div>
                            <div class="form-group">
                                <label>{{__('label.interstital_ad_click')}}</label>
                                <input type="text" name="fb_ios_interstital_adclick" class="form-control" placeholder="{{__('label.interstital_ad_click_here')}}" value="{{$result['fb_ios_interstital_adclick']}}">
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="fbad_ios()">{{__('label.save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    function fbad() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            var formData = new FormData($("#fbad")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("fbads.android") }}',
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
            toastr.error("{{__('label.you_have_no_right_to_add_edit_and_delete')}}");
        }
    }

    function fbad_ios() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            var formData = new FormData($("#fbad_ios")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("fbads.ios") }}',
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
            toastr.error("{{__('label.you_have_no_right_to_add_edit_and_delete')}}");
        }
    }
</script>
@endsection