@extends('admin.layout.page-app')
@section('page_title', __('label.admob_ads'))

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
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.admob_settings')}}</li>
                </ol>
            </div>
        </div>
        <!-- android settings  -->
        <div class="card custom-border-card mt-3">
            <h5 class="card-header">{{__('label.android_settings')}}</h5>
            <div class="card-body">
                <form id="admob_android">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label for="banner_ad">{{__('label.banner_ad')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="banner_ad" name="banner_ad" class="custom-control-input" {{ ($result['banner_ad']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="banner_ad">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="banner_ad1" name="banner_ad" class="custom-control-input" {{ ($result['banner_ad']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="banner_ad1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label for="interstital_ad">{{__('label.interstital_ad')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="interstital_ad" name="interstital_ad" class="custom-control-input" {{ ($result['interstital_ad']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="interstital_ad">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="interstital_ad1" name="interstital_ad" class="custom-control-input" {{ ($result['interstital_ad']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="interstital_ad1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label for="reward_ad">{{__('label.reward_ad')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="reward_ad" name="reward_ad" class="custom-control-input" {{ ($result['reward_ad']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="reward_ad">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="reward_ad1" name="reward_ad" class="custom-control-input" {{ ($result['reward_ad']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="reward_ad1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.banner_ad_id')}}</label>
                                <input type="text" name="banner_adid" class="form-control" placeholder="{{__('label.banner_ad_id_here')}}" value="{{$result['banner_adid']}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.interstital_ad_id')}}</label>
                                <input type="text" name="interstital_adid" class="form-control" placeholder="{{__('label.interstital_ad_id_here')}}" value="{{$result['interstital_adid']}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.reward_ad_id')}}</label>
                                <input type="text" name="reward_adid" class="form-control" placeholder="{{__('label.reward_ad_id_here')}}" value="{{$result['reward_adid']}}">
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
                                <label>{{__('label.interstital_ad_click')}}</label>
                                <input type="text" name="interstital_adclick" class="form-control" placeholder="{{__('label.interstital_ad_click_here')}}" value="{{$result['interstital_adclick']}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.reward_ad_click')}}</label>
                                <input type="text" name="reward_adclick" class="form-control" placeholder="{{__('label.reward_ad_click_here')}}" value="{{$result['reward_adclick']}}">
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="admob_android()">{{__('label.save')}}</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- ios settings  -->
        <div class="card custom-border-card mt-3">
            <h5 class="card-header">{{__('label.ios_settings')}}</h5>
            <div class="card-body">
                <form id="admob_ios">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label for="ios_banner_ad">{{__('label.banner_ad')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="ios_banner_ad" name="ios_banner_ad" class="custom-control-input" {{ ($result['ios_banner_ad']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="ios_banner_ad">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="ios_banner_ad1" name="ios_banner_ad" class="custom-control-input" {{ ($result['ios_banner_ad']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="ios_banner_ad1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label for="ios_interstital_ad">{{__('label.interstital_ad')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="ios_interstital_ad" name="ios_interstital_ad" class="custom-control-input" {{ ($result['ios_interstital_ad']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="ios_interstital_ad">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="ios_interstital_ad1" name="ios_interstital_ad" class="custom-control-input" {{ ($result['ios_interstital_ad']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="ios_interstital_ad1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label for="ios_reward_ad">{{__('label.reward_ad')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="ios_reward_ad" name="ios_reward_ad" class="custom-control-input" {{ ($result['ios_reward_ad']=='1')? "checked" : "" }} value="1">
                                        <label class="custom-control-label" for="ios_reward_ad">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="ios_reward_ad1" name="ios_reward_ad" class="custom-control-input" {{ ($result['ios_reward_ad']=='0')? "checked" : "" }} value="0">
                                        <label class="custom-control-label" for="ios_reward_ad1">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.banner_ad_id')}}</label>
                                <input type="text" name="ios_banner_adid" class="form-control" placeholder="{{__('label.banner_ad_id_here')}}" value="{{$result['ios_banner_adid']}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.interstital_ad_id')}}</label>
                                <input type="text" name="ios_interstital_adid" class="form-control" placeholder="{{__('label.interstital_ad_id_here')}}" value="{{$result['ios_interstital_adid']}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.reward_ad_id')}}</label>
                                <input type="text" name="ios_reward_adid" class="form-control" placeholder="{{__('label.reward_ad_id_here')}}" value="{{$result['ios_reward_adid']}}">
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
                                <label>{{__('interstital_ad_click')}}</label>
                                <input type="text" name="ios_interstital_adclick" class="form-control" placeholder="{{__('label.interstital_ad_click_here')}}" value="{{$result['ios_interstital_adclick']}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>{{__('label.reward_ad_click')}}</label>
                                <input type="text" name="ios_reward_adclick" class="form-control" placeholder="{{__('label.reward_ad_click_here')}}" value="{{$result['ios_reward_adclick']}}">
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="admob_ios()">{{__('label.save')}}</button>
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

    function admob_android() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            var formData = new FormData($("#admob_android")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("admob.android") }}',
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

    function admob_ios() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            var formData = new FormData($("#admob_ios")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("admob.ios") }}',
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
</script>
@endsection