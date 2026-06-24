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

        <!-- Tabs -->
        <ul class="nav nav-pills custom-tabs inline-tabs mb-3" id="ads-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="admob-tab" data-toggle="tab" href="#admob" role="tab" aria-controls="admob" aria-selected="true">AdMob</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="startio-tab" data-toggle="tab" href="#startio" role="tab" aria-controls="startio" aria-selected="false">Start.io</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="custom-tab" data-toggle="tab" href="#custom" role="tab" aria-controls="custom" aria-selected="false">Custom Ads</a>
            </li>
        </ul>

        <div class="tab-content" id="ads-tabContent">

            {{-- Tab 1: AdMob --}}
            <div class="tab-pane fade show active" id="admob" role="tabpanel" aria-labelledby="admob-tab">
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
                                        <label>AdMob Interstitial Cooldown (sec)</label>
                                        <input type="number" name="interstital_cooldown" class="form-control" min="0" placeholder="60" value="{{$result['interstital_cooldown'] ?? '60'}}">
                                        <small class="text-muted">Min seconds between AdMob interstitial ads (0 = no limit)</small>
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
                                        <label>AdMob Interstitial Cooldown (sec)</label>
                                        <input type="number" name="ios_interstital_cooldown" class="form-control" min="0" placeholder="60" value="{{$result['ios_interstital_cooldown'] ?? '60'}}">
                                        <small class="text-muted">Min seconds between AdMob interstitial ads (0 = no limit)</small>
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

            {{-- Tab 2: Start.io --}}
            <div class="tab-pane fade" id="startio" role="tabpanel" aria-labelledby="startio-tab">

                {{-- Android --}}
                <div class="card custom-border-card mt-3">
                    <h5 class="card-header">{{__('label.android_settings')}}</h5>
                    <div class="card-body">
                        <p class="text-muted mb-3" style="font-size:13px;">
                            Get your App ID from <a href="https://portal.start.io" target="_blank">portal.start.io</a>.
                            The App ID is baked into <code>AndroidManifest.xml</code> — update it there and rebuild if you change it.
                        </p>
                        <form id="startio_android_form">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Start.io Enabled</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="startio_enabled_1" name="startio_enabled" class="custom-control-input" value="1" {{ ($result['startio_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="startio_enabled_1">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="startio_enabled_0" name="startio_enabled" class="custom-control-input" value="0" {{ ($result['startio_enabled'] ?? '0') == '0' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="startio_enabled_0">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Banner Ads</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="startio_banner_1" name="startio_banner_enabled" class="custom-control-input" value="1" {{ ($result['startio_banner_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="startio_banner_1">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="startio_banner_0" name="startio_banner_enabled" class="custom-control-input" value="0" {{ ($result['startio_banner_enabled'] ?? '1') == '0' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="startio_banner_0">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Interstitial Ads</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="startio_inter_1" name="startio_interstitial_enabled" class="custom-control-input" value="1" {{ ($result['startio_interstitial_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="startio_inter_1">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="startio_inter_0" name="startio_interstitial_enabled" class="custom-control-input" value="0" {{ ($result['startio_interstitial_enabled'] ?? '1') == '0' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="startio_inter_0">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Rewarded Ads</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="startio_reward_1" name="startio_rewarded_enabled" class="custom-control-input" value="1" {{ ($result['startio_rewarded_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="startio_reward_1">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="startio_reward_0" name="startio_rewarded_enabled" class="custom-control-input" value="0" {{ ($result['startio_rewarded_enabled'] ?? '0') == '0' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="startio_reward_0">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>Android App ID</label>
                                        <input type="text" name="startio_app_id_android" class="form-control"
                                            value="{{ $result['startio_app_id_android'] ?? '' }}"
                                            placeholder="e.g. 204637737">
                                        <small class="text-muted">Set in AndroidManifest.xml — rebuild app after changing</small>
                                    </div>
                                </div>
                            </div>
                            <div class="border-top pt-3 text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="startio_android_save()">{{__('label.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- iOS --}}
                <div class="card custom-border-card mt-3">
                    <h5 class="card-header">{{__('label.ios_settings')}}</h5>
                    <div class="card-body">
                        <p class="text-muted mb-3" style="font-size:13px;">
                            The iOS App ID is set in <code>Info.plist</code> — update it there and rebuild if you change it.
                        </p>
                        <form id="startio_ios_form">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Start.io Enabled</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_startio_enabled_1" name="ios_startio_enabled" class="custom-control-input" value="1" {{ ($result['ios_startio_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="ios_startio_enabled_1">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_startio_enabled_0" name="ios_startio_enabled" class="custom-control-input" value="0" {{ ($result['ios_startio_enabled'] ?? '0') == '0' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="ios_startio_enabled_0">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Banner Ads</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_startio_banner_1" name="ios_startio_banner_enabled" class="custom-control-input" value="1" {{ ($result['ios_startio_banner_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="ios_startio_banner_1">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_startio_banner_0" name="ios_startio_banner_enabled" class="custom-control-input" value="0" {{ ($result['ios_startio_banner_enabled'] ?? '1') == '0' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="ios_startio_banner_0">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Interstitial Ads</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_startio_inter_1" name="ios_startio_interstitial_enabled" class="custom-control-input" value="1" {{ ($result['ios_startio_interstitial_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="ios_startio_inter_1">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_startio_inter_0" name="ios_startio_interstitial_enabled" class="custom-control-input" value="0" {{ ($result['ios_startio_interstitial_enabled'] ?? '1') == '0' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="ios_startio_inter_0">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Rewarded Ads</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_startio_reward_1" name="ios_startio_rewarded_enabled" class="custom-control-input" value="1" {{ ($result['ios_startio_rewarded_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="ios_startio_reward_1">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_startio_reward_0" name="ios_startio_rewarded_enabled" class="custom-control-input" value="0" {{ ($result['ios_startio_rewarded_enabled'] ?? '0') == '0' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="ios_startio_reward_0">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>iOS App ID</label>
                                        <input type="text" name="startio_app_id_ios" class="form-control"
                                            value="{{ $result['startio_app_id_ios'] ?? '' }}"
                                            placeholder="e.g. 204295105">
                                        <small class="text-muted">Set in Info.plist — rebuild app after changing</small>
                                    </div>
                                </div>
                            </div>
                            <div class="border-top pt-3 text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="startio_ios_save()">{{__('label.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tab 3: Custom Ads (placeholder for future) --}}
            <div class="tab-pane fade" id="custom" role="tabpanel" aria-labelledby="custom-tab">
                <div class="card custom-border-card mt-3">
                    <div class="card-header">
                        <h5>Custom Ads</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-0" style="font-size:13px;">
                            <em>Custom ad settings will be available here in a future update.</em>
                        </p>
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
                    $("html, body").animate({ scrollTop: 0 }, "swing");
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
                    $("html, body").animate({ scrollTop: 0 }, "swing");
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

    function startio_android_save() {
        var CheckAdmin = '<?php echo Check_Admin_Access(); ?>';
        if (CheckAdmin != 1) { toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}'); return; }
        $('#dvloader').show();
        $.ajax({
            type: 'POST',
            url: '{{ route("admob.startio.android") }}',
            data: new FormData($('#startio_android_form')[0]),
            cache: false, contentType: false, processData: false,
            success: function(resp) { $('#dvloader').hide(); $("html,body").animate({scrollTop:0},"swing"); get_responce_message(resp); },
            error: function(x, t, e) { $('#dvloader').hide(); toastr.error(e, t); }
        });
    }

    function startio_ios_save() {
        var CheckAdmin = '<?php echo Check_Admin_Access(); ?>';
        if (CheckAdmin != 1) { toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}'); return; }
        $('#dvloader').show();
        $.ajax({
            type: 'POST',
            url: '{{ route("admob.startio.ios") }}',
            data: new FormData($('#startio_ios_form')[0]),
            cache: false, contentType: false, processData: false,
            success: function(resp) { $('#dvloader').hide(); $("html,body").animate({scrollTop:0},"swing"); get_responce_message(resp); },
            error: function(x, t, e) { $('#dvloader').hide(); toastr.error(e, t); }
        });
    }
</script>
@endsection
