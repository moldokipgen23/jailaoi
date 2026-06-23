@extends('admin.layout.page-app')
@section('page_title', __('label.settings'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.settings')}}</h1>

        <div class="border-bottom row">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.settings')}}</li>
                </ol>
            </div>
        </div>
        <!-- custom tabs  -->
        <ul class="nav nav-pills custom-tabs inline-tabs" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="app-tab" data-toggle="tab" href="#app" role="tab" aria-controls="app" aria-selected="true">{{__('label.app_settings')}}</a>
            </li>
            @if(Check_Admin_Access() == 1)
            <li class="nav-item">
                <a class="nav-link" id="smtp-tab" data-toggle="tab" href="#smtp" role="tab" aria-controls="smtp" aria-selected="false">{{__('label.smtp')}}</a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" id="social-links-tab" data-toggle="tab" href="#social-links" role="tab" aria-controls="smtp" aria-selected="true">{{__('label.social_links')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="onboarding-tab" data-toggle="tab" href="#onboarding" role="tab" aria-controls="onboarding" aria-selected="false">{{__('label.onboarding_screen')}}</a>
            </li>
            {{-- JAILAOI: Payout settings tab --}}
            <li class="nav-item">
                <a class="nav-link" id="payout-settings-tab" data-toggle="tab" href="#payout-settings" role="tab" aria-controls="payout-settings" aria-selected="false">💰 Payouts</a>
            </li>
            {{-- JAILAOI: Marketing / Ads tab --}}
            <li class="nav-item">
                <a class="nav-link" id="marketing-tab" data-toggle="tab" href="#marketing" role="tab" aria-controls="marketing" aria-selected="false">📱 Marketing</a>
            </li>
        </ul>
        <!-- custom tab panels  -->
        <div class="tab-content" id="pills-tabContent">
            <!-- app settings  -->
            <div class="tab-pane fade show active" id="app" role="tabpanel" aria-labelledby="app-tab">
                <div class="card custom-border-card">
                    <h5 class="card-header">{{__('label.app_settings')}}</h5>
                    <div class="card-body">
                        <form id="app_setting" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-row">
                                <div class="col-md-9">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>{{__('label.app_name')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="app_name" class="form-control" placeholder="{{__('label.app_name_here')}}" value="{{$result['app_name']}}" autofocus>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>{{__('label.host_email')}}<span class="text-danger">*</span></label>
                                            <input type="email" name="host_email" class="form-control" value="{{$result['host_email']}}" placeholder="{{__('label.host_email_here')}}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>{{__('label.app_version')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="app_version" class="form-control" value="{{$result['app_version']}}" placeholder="{{__('label.app_version_here')}}">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>{{__('label.author')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="author" class="form-control" value="{{$result['author']}}" placeholder="{{__('label.author_here')}}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>{{__('label.email')}}<span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" value="{{$result['email']}}" placeholder="{{__('label.email_here')}}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>{{__('label.contact')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="contact" class="form-control" value="{{$result['contact']}}" placeholder="{{__('label.contact_here')}}">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>{{__('label.website')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="website" class="form-control" value="{{$result['website']}}" placeholder="{{__('label.website_here')}}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>{{__('label.company_name')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="company_name" class="form-control" value="{{$result['company_name']}}" placeholder="{{__('label.company_name_here')}}">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label>{{__('label.app_description')}}<span class="text-danger">*</span></label>
                                            <textarea name="app_desripation" rows="1" class="form-control" placeholder="{{__('label.app_description_here')}}">{{$result['app_desripation']}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ml-5">
                                        <label class="ml-5">{{__('label.app_icon')}}<span class="text-danger">*</span></label>
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
                                    <div class="form-group ml-5">
                                        <label class="ml-5">{{__('label.company_logo')}}<span class="text-danger">*</span></label>
                                        <div class="avatar-upload ml-5">
                                            <div class="avatar-edit">
                                                <input type='file' name="company_logo" id="imageUpload2" accept=".png, .jpg, .jpeg" />
                                                <label for="imageUpload2" title="Select File"></label>
                                            </div>
                                            <div class="avatar-preview">
                                                <img src="{{$result['company_logo']}}" alt="upload_img.png" id="imagePreview2">
                                            </div>
                                        </div>
                                        <input type="hidden" name="old_company_logo" value="{{$result['company_logo']}}">
                                    </div>
                                </div>
                            </div>
                            <div class="border-top pt-3 text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="app_setting()">{{__('label.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- api configuration  -->
                <div class="form-row">
                    <div class="col-6">
                        <div class="card custom-border-card">
                            <h5 class="card-header">{{__('label.api_configurations')}}</h5>
                            <div class="card-body">
                                <div class="input-group">
                                    <div class="col-2">
                                        <label class="pt-3">{{__('label.api_path')}}</label>
                                    </div>
                                    <input type="text" readonly value="{{url('/')}}/api/" name="api_path" class="form-control" id="api_path">
                                    <div class="input-group-text ml-2" onclick="Function_Api_path()" title="Copy">
                                        <i class="fa-solid fa-copy fa-2xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card custom-border-card">
                            <h5 class="card-header">{{__('label.currency_settings')}}</h5>
                            <div class="card-body">
                                <form id="save_currency" enctype="multipart/form-data">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>{{__('label.currency_name')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="currency" class="form-control" value="{{$result['currency']}}" placeholder="{{__('label.currency_name_here')}}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>{{__('label.currency_code')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="currency_code" class="form-control" value="{{$result['currency_code']}}" placeholder="{{__('label.currency_code_here')}}">
                                        </div>
                                    </div>
                                    <div class="border-top pt-3 text-right">
                                        <button type="button" class="btn btn-default mw-120" onclick="save_currency()">{{__('label.save')}}</button>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <!-- developed by  -->
                    <div class="col-6">
                        <div class="card custom-border-card">
                            <h5 class="card-header">{{__('label.developed_by')}}</h5>
                            <div class="card-body">
                                <form id="save_dev">
                                    @csrf
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="dev_title" class="form-control" value="{{$result['dev_title']}}" placeholder="{{__('label.title_here')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group ml-4">
                                                <label class="ml-5">{{__('label.logo')}}</label>
                                                <div class="avatar-upload ml-5">
                                                    <div class="avatar-edit">
                                                        <input type='file' name="dev_logo" id="imageUploadModel" accept=".png, .jpg, .jpeg" />
                                                        <label for="imageUploadModel" title="Select File"></label>
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <img src="{{$result['dev_logo']}}" alt="upload_img.png" id="imagePreviewModel">
                                                    </div>
                                                </div>
                                                <input type="hidden" name="old_dev_logo" value="{{$result['dev_logo']}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-top pt-3 text-right">
                                        <button type="button" class="btn btn-default mw-120" onclick="save_dev()">{{__('label.save')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Screenshot Settings -->
                    <div class="col-6">
                        <div class="card custom-border-card">
                            <h5 class="card-header">{{__('label.screenshot_settings')}}</h5>
                            <div class="card-body">
                                <form id="save_screenshot">
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{__('label.screenshot_settings')}}<span class="text-danger">*</span></label>
                                                <div class="radio-group mt-2">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="enable_ss" value="1" name="screenshot" class="custom-control-input" {{$result['screenshot']==1 ? "checked" : ""}}>
                                                        <label class="custom-control-label" for="enable_ss">{{__('label.enable')}}</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="disable_ss" value="0" name="screenshot" class="custom-control-input" {{$result['screenshot']==0 ? "checked" : ""}}>
                                                        <label class="custom-control-label" for="disable_ss">{{__('label.disable')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-top pt-3 text-right">
                                        <button type="button" class="btn btn-default mw-120" onclick="save_screenshot_setting()">{{__('label.save')}}</button>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <!-- Banner Toggle -->
                    <div class="col-12">
                        <div class="card custom-border-card">
                            <h5 class="card-header">Home Banner</h5>
                            <div class="card-body">
                                <form id="banner_setting_form">
                                    <div class="form-row align-items-center">
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="d-block mb-1">Show Banner on Home Screen</label>
                                                <small class="text-muted">When enabled, the spotlight carousel appears at the top of the home screen. Manage banner items under <strong>Content → Banner</strong>.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-md-right mt-2 mt-md-0">
                                            <div class="custom-control custom-switch d-inline-block">
                                                <input type="checkbox" class="custom-control-input" id="home_banner_toggle"
                                                    name="home_banner_enabled" value="1"
                                                    {{ ($result['home_banner_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="home_banner_toggle">Enable</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-top pt-3 text-right mt-3">
                                        <button type="button" class="btn btn-default mw-120" onclick="save_banner_setting()">{{__('label.save')}}</button>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <!-- Ai Api Key -->
                    <div class="col-12">
                        <div class="card custom-border-card">
                            <h5 class="card-header">{{__('label.ai_section_settings')}}</h5>
                            <div class="card-body">
                                <form id="ai_api_key">
                                    <div class="form-row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>{{__('label.ai_section')}}<span class="text-danger">*</span></label>
                                                <div class="radio-group mt-2">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="enable_ai" value="1" name="ai_section" class="custom-control-input" {{$result['ai_section']==1 ? "checked" : ""}}>
                                                        <label class="custom-control-label" for="enable_ai">{{__('label.enable')}}</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="disable_ai" value="0" name="ai_section" class="custom-control-input" {{$result['ai_section']==0 ? "checked" : ""}}>
                                                        <label class="custom-control-label" for="disable_ai">{{__('label.disable')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7 key_drop">
                                            <div class="form-group">
                                                <label>{{__('label.api_key')}}<span class="text-danger">* </span>({{__('label.api_note')}} - <a href="https://developers.openai.com/api/docs" class="btn-link" target="_blank">{{__('label.click_here_to_get_your_api_key')}}</a>)</label>
                                                <input type="text" name="ai_api_key" value="{{$result['ai_api_key']}}" class="form-control" placeholder="{{__('label.key_here')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 key_drop">
                                            <div class="form-group">
                                                <label>AI Sections Per User <span class="text-danger">*</span></label>
                                                <input type="number" name="ai_section_count" min="1" max="20" value="{{$result['ai_section_count'] ?? 2}}" class="form-control">
                                                <small class="text-muted">How many personalized sections to generate per user (start low, increase as user base grows)</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-top pt-3 text-right">
                                        <button type="button" class="btn btn-default mw-120" onclick="api_key_save()">{{__('label.save')}}</button>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- JAILAOI: CDN / Audio Storage card --}}
                <div class="form-row">
                    <div class="col-12">
                        <div class="card custom-border-card">
                            <h5 class="card-header">🎵 Audio Storage / CDN</h5>
                            <div class="card-body">
                                <form id="cdn_storage_form">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                    {{-- Driver selector --}}
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>Storage Driver <span class="text-danger">*</span></label>
                                            <select name="audio_storage_driver" class="form-control" id="audio_storage_driver_select" onchange="toggleCdnFields()">
                                                <option value="local" {{ ($result['audio_storage_driver'] ?? 'local') == 'local' ? 'selected' : '' }}>Local Server (default)</option>
                                                <option value="bunny" {{ ($result['audio_storage_driver'] ?? 'local') == 'bunny' ? 'selected' : '' }}>🐰 Bunny CDN (recommended)</option>
                                                <option value="r2"    {{ ($result['audio_storage_driver'] ?? 'local') == 'r2'    ? 'selected' : '' }}>☁️ Cloudflare R2</option>
                                            </select>
                                            <small class="text-muted">
                                                <strong>local</strong> = files on this server (default).<br>
                                                <strong>bunny</strong> = Bunny CDN — fill credentials below.<br>
                                                <strong>r2</strong> = Cloudflare R2 — set R2_* vars in .env.
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Bunny credentials (shown only when bunny is selected) --}}
                                    <div id="bunny_fields" style="display:none;">
                                        <hr>
                                        <p class="text-muted mb-3">
                                            <strong>🐰 Bunny CDN Setup:</strong>
                                            Go to <a href="https://bunny.net" target="_blank">bunny.net</a> →
                                            Storage → Add Storage Zone → then CDN → Add Pull Zone linked to that zone.
                                        </p>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Storage Zone Name <span class="text-danger">*</span></label>
                                                <input type="text" name="bunny_storage_zone" class="form-control"
                                                    value="{{ $result['bunny_storage_zone'] ?? '' }}"
                                                    placeholder="e.g. jailaoi-audio">
                                                <small class="text-muted">The name of your Storage Zone in Bunny dashboard.</small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Storage API Key <span class="text-danger">*</span></label>
                                                <input type="password" name="bunny_storage_api_key" class="form-control"
                                                    value="{{ $result['bunny_storage_api_key'] ?? '' }}"
                                                    placeholder="Storage Zone password / API key"
                                                    autocomplete="new-password">
                                                <small class="text-muted">Found in Storage Zone → FTP & API Access → Password.</small>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>CDN Pull Zone URL <span class="text-danger">*</span></label>
                                                <input type="text" name="bunny_cdn_url" class="form-control"
                                                    value="{{ $result['bunny_cdn_url'] ?? '' }}"
                                                    placeholder="e.g. https://jailaoi.b-cdn.net">
                                                <small class="text-muted">Your Pull Zone hostname — this is what the app uses to play audio.</small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Storage Endpoint</label>
                                                <select name="bunny_storage_endpoint" class="form-control">
                                                    @php
                                                        $currentEndpoint = $result['bunny_storage_endpoint'] ?? 'https://storage.bunnycdn.com';
                                                        $endpoints = [
                                                            'https://storage.bunnycdn.com'    => 'Default (Falkenstein, EU)',
                                                            'https://uk.storage.bunnycdn.com' => 'UK (London)',
                                                            'https://ny.storage.bunnycdn.com' => 'New York, US',
                                                            'https://la.storage.bunnycdn.com' => 'Los Angeles, US',
                                                            'https://sg.storage.bunnycdn.com' => 'Singapore (Asia)',
                                                            'https://se.storage.bunnycdn.com' => 'Stockholm, SE',
                                                            'https://br.storage.bunnycdn.com' => 'São Paulo, Brazil',
                                                            'https://jh.storage.bunnycdn.com' => 'Johannesburg, Africa',
                                                        ];
                                                    @endphp
                                                    @foreach($endpoints as $val => $label)
                                                        <option value="{{ $val }}" {{ $currentEndpoint == $val ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Must match the region you chose when creating the Storage Zone.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top pt-3 text-right">
                                        <button type="button" class="btn btn-default mw-120" onclick="cdn_storage_save()">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- JAILAOI: Marketing / Ads tab-pane --}}
            <div class="tab-pane fade" id="marketing" role="tabpanel" aria-labelledby="marketing-tab">
                <div class="card custom-border-card mt-3">
                    <div class="card-header">
                        <h5>📱 Ads &amp; Marketing</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3" style="font-size:13px;">
                            Get your Start.io App ID from <a href="https://portal.start.io" target="_blank">portal.start.io</a>.
                            After saving, add the App ID to <code>AndroidManifest.xml</code> and <code>Info.plist</code> then rebuild the app.
                        </p>

                        {{-- Start.io --}}
                        <h6 class="mb-3">Start.io</h6>
                        <form id="startio_form">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Enabled</label>
                                    <select name="startio_enabled" class="form-control">
                                        <option value="0" {{ ($result['startio_enabled'] ?? '0') == '0' ? 'selected' : '' }}>Disabled</option>
                                        <option value="1" {{ ($result['startio_enabled'] ?? '0') == '1' ? 'selected' : '' }}>Enabled</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Banner Ads</label>
                                    <select name="startio_banner_enabled" class="form-control">
                                        <option value="1" {{ ($result['startio_banner_enabled'] ?? '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                        <option value="0" {{ ($result['startio_banner_enabled'] ?? '1') == '0' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Interstitial Ads</label>
                                    <select name="startio_interstitial_enabled" class="form-control">
                                        <option value="1" {{ ($result['startio_interstitial_enabled'] ?? '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                        <option value="0" {{ ($result['startio_interstitial_enabled'] ?? '1') == '0' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Rewarded Ads</label>
                                    <select name="startio_rewarded_enabled" class="form-control">
                                        <option value="1" {{ ($result['startio_rewarded_enabled'] ?? '0') == '1' ? 'selected' : '' }}>Enabled</option>
                                        <option value="0" {{ ($result['startio_rewarded_enabled'] ?? '0') == '0' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mt-2">
                                <div class="form-group col-md-6">
                                    <label>Android App ID</label>
                                    <input type="text" name="startio_app_id_android" class="form-control"
                                        value="{{ $result['startio_app_id_android'] ?? '' }}"
                                        placeholder="e.g. 204637737">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>iOS App ID</label>
                                    <input type="text" name="startio_app_id_ios" class="form-control"
                                        value="{{ $result['startio_app_id_ios'] ?? '' }}"
                                        placeholder="e.g. 204295105">
                                </div>
                            </div>
                            <div class="border-top pt-3 text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="startio_save()">Save</button>
                            </div>
                        </form>

                        <hr>

                        {{-- Placeholder for future ad networks (AdMob settings, Custom Ads, etc.) --}}
                        <p class="text-muted mb-0" style="font-size:13px;">
                            <em>AdMob and custom ad settings will appear here in a future update.</em>
                        </p>
                    </div>
                </div>
            </div>

            {{-- JAILAOI: Payout settings tab-pane --}}
            <div class="tab-pane fade" id="payout-settings" role="tabpanel" aria-labelledby="payout-settings-tab">
                <div class="card custom-border-card mt-3">
                    <h5 class="card-header">💰 Artist Payout Settings</h5>
                    <div class="card-body">
                        <form id="payout_settings_form">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Earnings Model</label>
                                    <select name="earnings_model" class="form-control" id="earnings_model">
                                        <option value="pool" {{ ($result['earnings_model'] ?? 'pool') == 'pool' ? 'selected' : '' }}>
                                            Revenue Pool (55% to Artists)
                                        </option>
                                        <option value="per_stream" {{ ($result['earnings_model'] ?? 'pool') == 'per_stream' ? 'selected' : '' }}>
                                            Per Stream (Fixed Rate)
                                        </option>
                                    </select>
                                    <small class="text-muted">
                                        <strong>Revenue Pool:</strong> 55% of subscription revenue distributed monthly by stream share.
                                        <strong>Per Stream:</strong> Fixed rate per play, paid immediately.
                                    </small>
                                </div>
                                <div class="form-group col-md-3" id="platform_cut_group">
                                    <label>Platform Cut (%)</label>
                                    <input type="number" step="1" min="0" max="100" name="platform_cut_pct"
                                        class="form-control"
                                        value="{{ $result['platform_cut_pct'] ?? '45' }}"
                                        placeholder="45">
                                    <small class="text-muted">% kept by platform. Remaining goes to artist pool.</small>
                                </div>
                                <div class="form-group col-md-2" id="settlement_day_group">
                                    <label>Settlement Day</label>
                                    <input type="number" min="1" max="28" name="settlement_day"
                                        class="form-control"
                                        value="{{ $result['settlement_day'] ?? '5' }}"
                                        placeholder="5">
                                    <small class="text-muted">Auto-run settlement on this day.</small>
                                </div>
                                <div class="form-group col-md-3" id="per_stream_fields">
                                    <label>Rate Per Stream <span class="text-danger">*</span></label>
                                    <input type="number" step="0.0001" min="0" name="payout_rate_per_stream"
                                        class="form-control"
                                        value="{{ $result['payout_rate_per_stream'] ?? '0.001' }}"
                                        placeholder="e.g. 0.001">
                                    <small class="text-muted">Amount paid to artist per 1 play. Default: $0.001</small>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Currency</label>
                                    <input type="text" name="payout_currency" class="form-control"
                                        value="{{ $result['payout_currency'] ?? 'USD' }}"
                                        placeholder="USD">
                                    <small class="text-muted">Currency code shown to artists. e.g. USD, INR, MYR</small>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Minimum Withdrawal</label>
                                    <input type="number" step="0.01" min="0" name="min_withdrawal_amount"
                                        class="form-control"
                                        value="{{ $result['min_withdrawal_amount'] ?? '10' }}"
                                        placeholder="e.g. 10">
                                    <small class="text-muted">Minimum balance artist must have before requesting payout.</small>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Backup Email</label>
                                    <input type="email" name="backup_email" class="form-control"
                                        value="{{ $result['backup_email'] ?? '' }}"
                                        placeholder="admin@jailaoi.com">
                                    <small class="text-muted">Daily DB backup will be emailed here.</small>
                                </div>
                            </div>
                            <hr>
                            <h6 class="mb-3">Withdrawal Eligibility Requirements</h6>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Minimum Streams for Payout</label>
                                    <input type="number" min="0" name="min_streams_for_payout" class="form-control"
                                        value="{{ $result['min_streams_for_payout'] ?? '50' }}"
                                        placeholder="50">
                                    <small class="text-muted">Minimum total plays an artist needs before eligible for payout.</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Minimum Earnings for Payout ({{ $result['payout_currency'] ?? 'USD' }})</label>
                                    <input type="number" step="0.01" min="0" name="min_earnings_for_payout" class="form-control"
                                        value="{{ $result['min_earnings_for_payout'] ?? '5.00' }}"
                                        placeholder="5.00">
                                    <small class="text-muted">Minimum earnings balance before eligible for payout.</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Minimum Account Age (Days)</label>
                                    <input type="number" min="0" name="min_account_days_for_payout" class="form-control"
                                        value="{{ $result['min_account_days_for_payout'] ?? '30' }}"
                                        placeholder="30">
                                    <small class="text-muted">Minimum days since registration before eligible for payout.</small>
                                </div>
                            </div>
                            <hr>
                            <h6 class="mb-3">Monetization Eligibility Rules</h6>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Min Total Plays</label>
                                    <input type="number" min="0" name="eligibility_min_plays" class="form-control"
                                        value="{{ $result['eligibility_min_plays'] ?? '100' }}"
                                        placeholder="100">
                                    <small class="text-muted">Minimum total plays to qualify for monetization.</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Min Followers</label>
                                    <input type="number" min="0" name="eligibility_min_followers" class="form-control"
                                        value="{{ $result['eligibility_min_followers'] ?? '50' }}"
                                        placeholder="50">
                                    <small class="text-muted">Minimum followers to qualify for monetization.</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Min Plays This Month</label>
                                    <input type="number" min="0" name="eligibility_min_monthly_plays" class="form-control"
                                        value="{{ $result['eligibility_min_monthly_plays'] ?? '30' }}"
                                        placeholder="30">
                                    <small class="text-muted">Minimum plays in the current month.</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Min Tracks Uploaded</label>
                                    <input type="number" min="0" name="eligibility_min_tracks" class="form-control"
                                        value="{{ $result['eligibility_min_tracks'] ?? '1' }}"
                                        placeholder="1">
                                    <small class="text-muted">Minimum published tracks.</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Min Account Age (Days)</label>
                                    <input type="number" min="0" name="eligibility_min_account_days" class="form-control"
                                        value="{{ $result['eligibility_min_account_days'] ?? '30' }}"
                                        placeholder="30">
                                    <small class="text-muted">Minimum days since registration.</small>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <div class="form-check">
                                        <input type="hidden" name="monetization_strict_eligibility" value="0">
                                        <input type="checkbox" name="monetization_strict_eligibility" class="form-check-input" id="strictEligibility" value="1"
                                            {{ isset($result['monetization_strict_eligibility']) && $result['monetization_strict_eligibility'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="strictEligibility">
                                            <strong>Strict Eligibility Enforcement</strong>
                                        </label>
                                        <small class="text-muted d-block">When enabled, artists must meet all eligibility rules before they can apply. Disable to allow any artist to apply (admin still reviews).</small>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h6 class="mb-3">Payment Methods &amp; KYC Configuration</h6>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Allowed Payment Methods</label>
                                    <div class="mt-2">
                                        @php
                                            $allowedMethods = explode(',', $result['allowed_payment_methods'] ?? 'bank,upi');
                                        @endphp
                                        <input type="hidden" name="allowed_payment_methods_hidden" value="0">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input payment-method-checkbox" type="checkbox" name="allowed_payment_methods[]" value="bank" id="pm_bank"
                                                {{ in_array('bank', $allowedMethods) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pm_bank">Bank Transfer</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input payment-method-checkbox" type="checkbox" name="allowed_payment_methods[]" value="upi" id="pm_upi"
                                                {{ in_array('upi', $allowedMethods) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pm_upi">UPI</label>
                                        </div>
                                    </div>
                                    <small class="text-muted">Select which payment methods artists can use for withdrawals.</small>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>KYC Required for Withdrawal</label>
                                    <div class="mt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden" name="kyc_required_for_withdrawal" value="0">
                                            <input type="checkbox" name="kyc_required_for_withdrawal" class="custom-control-input" id="kycRequiredToggle" value="1"
                                                {{ ($result['kyc_required_for_withdrawal'] ?? '1') == '1' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="kycRequiredToggle">{{ __('label.enable') }}</label>
                                        </div>
                                    </div>
                                    <small class="text-muted">Require artists to complete KYC before withdrawing.</small>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Allowed ID Types</label>
                                    <div class="mt-2">
                                        @php
                                            $allowedIds = explode(',', $result['allowed_id_types'] ?? 'passport,national_id,drivers_license');
                                        @endphp
                                        <div class="form-check">
                                            <input class="form-check-input id-type-checkbox" type="checkbox" name="allowed_id_types[]" value="passport" id="id_passport"
                                                {{ in_array('passport', $allowedIds) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="id_passport">Passport</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input id-type-checkbox" type="checkbox" name="allowed_id_types[]" value="national_id" id="id_national_id"
                                                {{ in_array('national_id', $allowedIds) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="id_national_id">National ID</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input id-type-checkbox" type="checkbox" name="allowed_id_types[]" value="drivers_license" id="id_drivers_license"
                                                {{ in_array('drivers_license', $allowedIds) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="id_drivers_license">Driver's License</label>
                                        </div>
                                    </div>
                                    <small class="text-muted">Select which ID types are accepted for KYC.</small>
                                </div>
                            </div>
                            <div class="alert alert-info py-2 mt-2" id="pool_info">
                                <strong>Revenue Pool:</strong>
                                55% of monthly subscription revenue goes to the artist pool.
                                Each artist earns based on their share of total streams.
                                Settlements run automatically on the
                                <strong>{{ $result['settlement_day'] ?? '5' }}th</strong> of each month.
                                <a href="{{ route('admin.earnings.settlement') }}">Manage Settlements</a>
                            </div>
                            <div class="alert alert-info py-2 mt-2" id="per_stream_info" style="display:none;">
                                <strong>Per Stream:</strong>
                                Every time a listener plays a song, the artist earns the fixed rate.
                                If a song has multiple artists, the rate is split equally.
                                Artists can request withdrawal once their balance reaches the minimum.
                            </div>
                            <div class="border-top pt-3 text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="payout_settings_save()">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- smtp settings  -->
            <div class="tab-pane fade" id="smtp" role="tabpanel" aria-labelledby="smtp-tab">
                <div class="card custom-border-card">
                    <h5 class="card-header">{{__('label.email_settings_smtp')}}</h5>
                    <div class="card-body">
                        <form id="smtp_setting">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="id" value="@if($smtp){{$smtp->id}}@endif">
                            <div class="form-row">
                                <div class="form-group  col-md-3">
                                    <label>{{__('label.is_active_smtp')}}<span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="">{{__('label.select_status')}}</option>
                                        <option value="0" @if($smtp){{ $smtp->status == 0  ? 'selected' : ''}}@endif>{{__('label.no')}}</option>
                                        <option value="1" @if($smtp){{ $smtp->status == 1  ? 'selected' : ''}}@endif>{{__('label.yes')}}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>{{__('label.host')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="host" class="form-control" value="@if($smtp){{$smtp->host}}@endif" placeholder="{{__('label.host_here')}}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>{{__('label.port')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="port" class="form-control" value="@if($smtp){{$smtp->port}}@endif" placeholder="{{__('label.port_here')}}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>{{__('label.protocol')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="protocol" class="form-control" value="@if($smtp){{$smtp->protocol}}@endif" placeholder="{{__('label.protocol_here')}}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>{{__('label.user_name')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="user" class="form-control" value="@if($smtp){{$smtp->user}}@endif" placeholder="{{__('label.user_name_here')}}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>{{__('label.password')}}<span class="text-danger">*</span></label>
                                    <input type="password" name="pass" class="form-control" value="@if($smtp){{$smtp->pass}}@endif" placeholder="{{__('label.password_here')}}">
                                    <label class="mt-1 text-gray">Search for better result <a href="https://support.google.com/mail/answer/185833?hl=en" target="_blank" class="btn-link">Click Here</a></label>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>{{__('label.from_name')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="from_name" class="form-control" value="@if($smtp){{$smtp->from_name}}@endif" placeholder="{{__('label.from_name_here')}}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>{{__('label.from_email')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="from_email" class="form-control" value="@if($smtp){{$smtp->from_email}}@endif" placeholder="{{__('label.from_email_here')}}">
                                </div>
                            </div>
                            <div class="border-top pt-3 text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="smtp_setting()">{{__('label.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                @if($smtp->status==1)
                <div class="card custom-border-card col-md-6">
                    <h5 class="card-header">{{__('label.test_smtp')}}</h5>
                    <div class="card-body">
                        <form id="test_smtp" method="POST">
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label>{{__('label.email')}}</label>
                                    <input type="text" name="email" class="form-control" placeholder="{{__('label.email_here')}}">
                                </div>
                            </div>
                            <div class="border-top pt-3 text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="test_smtp()">{{__('label.send')}}</button>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
            <!-- social link -->
            <div class="tab-pane fade" id="social-links" role="tabpanel" aria-labelledby="social-links-tab">
                <form id="edit_social_links" autocomplete="off" method="post" enctype="multipart/form-data">
                    <div class="card custom-border-card mt-3">
                        <h5 class="card-header">{{__('label.social_links')}}</h5>
                        <div class="card-body">
                            <div class="main_step form-row mt-3 mb-3">
                                <div class="col-md-3">
                                    <input type="hidden" name="step_id[]" value="">
                                    <div class="form-group">
                                        <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="step_name[]" class="form-control" placeholder="{{__('label.name_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.url')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="step_url[]" class="form-control" placeholder="{{__('label.url_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                                        <input type="file" class="form-control step-image import-file" name="step_image[]" accept="image/png, image/jpg, image/jpeg" preview-id="Uploaded-step-Image">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <div class="custom-file ml-2">
                                            <img src="{{asset('assets/imgs/upload_img.png')}}" class="img-thumbnail social-link-img" id="Uploaded-step-Image">
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
                                        <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                        <input type="text" value="{{ $result->name }}" name="step_name[]" class="form-control" placeholder="{{__('label.name_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.url')}}<span class="text-danger">*</span></label>
                                        <input type="text" value="{{ $result->url }}" name="step_url[]" class="form-control" placeholder="{{__('label.url_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                                        <input type="file" class="form-control step-image import-file" name="step_image[]" accept="image/png, image/jpg, image/jpeg" preview-id="Uploaded-step-Image-{{ $index }}">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-grup">
                                        <div class="custom-file ml-2">
                                            <img src="{{ $result->image }}" class="img-thumbnail social-link-img" id="Uploaded-step-Image-{{ $index }}">
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
                            <button type="button" class="btn btn-default mw-120" onclick="save_social_links()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </div>
                </form>
            </div>
            <!-- onboarding screen -->
            <div class="tab-pane fade" id="onboarding" role="tabpanel" aria-labelledby="onboarding-tab">
                <div class="card custom-border-card">
                    <h5 class="card-header">{{__('label.onboarding_screen')}}</h5>

                    <div class="card-body">
                        <form id="onboarding_form" enctype="multipart/form-data">
                            <div class="row col-md-12">
                                <div class="form-group col-md-6">
                                    <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="title[]" class="form-control" placeholder="{{__('label.title_here')}}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                                    <input type="file" name="image[]" class="form-control import-file on_boarding_img" id="on_boarding_img" accept=".png, .jpg, .jpeg">
                                    <input type="hidden" name="old_image[]" value="">
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="custom-file">
                                        <img src="{{asset('assets/imgs/upload_img.png')}}" class="img-thumbnail onboarding-img" id="link_img_on_boarding_img">
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
                                        <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="title[]" value="{{ $onboarding_screen[$i]['title'] }}" class="form-control" placeholder="{{__('label.title_here')}}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                                        <input type="file" name="image[]" class="form-control import-file on_boarding_img" id="on_boarding_img{{$i}}" accept=".png, .jpg, .jpeg">
                                        <input type="hidden" name="old_image[]" value="{{ basename($onboarding_screen[$i]['image']) }}">
                                    </div>
                                    <div class="form-group col-md-1">
                                        <div class="custom-file">
                                            <img src="{{$onboarding_screen[$i]['image']}}" class="img-thumbnail onboarding-img" id="link_img_on_boarding_img{{$i}}">
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
                        <button type="button" class="btn btn-default mw-120" onclick="onboarding()">{{__('label.save')}}</button>
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

    if ($('input[name=ai_section]:checked').val() == '1') {
        $('.key_drop').show();
    } else {
        $('.key_drop').hide();
    }

    $('input[name="ai_section"]').change(function() {
        if ($(this).val() == '1') {
            $('.key_drop').show();
        } else {
            $('.key_drop').hide();
        }
    });

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
        if (Check_Admin == 1) {

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
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    function save_currency() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

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
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    function save_dev() {

        var CheckAdmin = '<?php echo Check_Admin_Access(); ?>';
        if (CheckAdmin == 1) {

            $('#dvloader').show();
            var formData = new FormData($('#save_dev')[0]);

            $.ajax({
                type: 'POST',
                url: '{{route("setting.dev")}}',
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
                error: function(XMLHttpRequest, errorThrown, textStatus) {
                    $('#dvloader').hide();
                    toastr.error(textStatus, errorThrown);
                }
            });
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    function save_screenshot_setting() {

        var CheckAdmin = '<?php echo Check_Admin_Access(); ?>';
        if (CheckAdmin == 1) {

            $('#dvloader').show();
            var formData = new FormData($('#save_screenshot')[0]);

            $.ajax({
                type: 'POST',
                url: '{{route("setting.screenshot")}}',
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
                error: function(XMLHttpRequest, errorThrown, textStatus) {
                    $('#dvloader').hide();
                    toastr.error(textStatus, errorThrown);
                }
            });
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    function save_banner_setting() {
        var CheckAdmin = '<?php echo Check_Admin_Access(); ?>';
        if (CheckAdmin == 1) {
            $('#dvloader').show();
            var formData = new FormData($('#banner_setting_form')[0]);
            if (!$('#home_banner_toggle').is(':checked')) {
                formData.set('home_banner_enabled', '0');
            }
            $.ajax({
                type: 'POST',
                url: '{{route("setting.banner")}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    $("html, body").animate({ scrollTop: 0 }, "swing");
                    get_responce_message(resp);
                },
                error: function(XMLHttpRequest, errorThrown, textStatus) {
                    $('#dvloader').hide();
                    toastr.error(textStatus, errorThrown);
                }
            });
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    function api_key_save() {

        var CheckAdmin = '<?php echo Check_Admin_Access(); ?>';
        if (CheckAdmin == 1) {

            $('#dvloader').show();
            var formData = new FormData($('#ai_api_key')[0]);

            $.ajax({
                type: 'POST',
                url: '{{route("setting.save_key")}}',
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
                error: function(XMLHttpRequest, errorThrown, textStatus) {
                    $('#dvloader').hide();
                    toastr.error(textStatus, errorThrown);
                }
            });
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    // JAILAOI: CDN storage setting — show/hide Bunny fields based on driver selection
    function toggleCdnFields() {
        var driver = $('#audio_storage_driver_select').val();
        if (driver === 'bunny') {
            $('#bunny_fields').show();
        } else {
            $('#bunny_fields').hide();
        }
    }
    // Run on page load to set initial state
    $(document).ready(function() { toggleCdnFields(); });

    function cdn_storage_save() {
        var CheckAdmin = '<?php echo Check_Admin_Access(); ?>';
        if (CheckAdmin == 1) {
            $('#dvloader').show();
            var formData = new FormData($('#cdn_storage_form')[0]);
            $.ajax({
                type: 'POST',
                url: '{{route("setting.save_key")}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    $("html, body").animate({ scrollTop: 0 }, "swing");
                    get_responce_message(resp);
                },
                error: function(XMLHttpRequest, errorThrown, textStatus) {
                    $('#dvloader').hide();
                    toastr.error(textStatus, errorThrown);
                }
            });
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    function startio_save() {
        var CheckAdmin = '<?php echo Check_Admin_Access(); ?>';
        if (CheckAdmin == 1) {
            $('#dvloader').show();
            var formData = new FormData($('#startio_form')[0]);
            $.ajax({
                type: 'POST',
                url: '{{route("setting.save_key")}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    $("html, body").animate({ scrollTop: 0 }, "swing");
                    get_responce_message(resp);
                },
                error: function(XMLHttpRequest, errorThrown, textStatus) {
                    $('#dvloader').hide();
                    toastr.error(textStatus, errorThrown);
                }
            });
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    function smtp_setting() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

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
                    $("html, body").animate({
                        scrollTop: 0
                    }, "swing");
                    get_responce_message(resp);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    toastr.error(errorThrown, textStatus);
                }
            });
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    function test_smtp() {

        var Check_Admin = <?php echo Check_Admin_Access(); ?>;
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var formData = new FormData($("#test_smtp")[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route("setting.test_smtp") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'test_smtp');
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

    // Multipal Img Show 
    $(document).on('change', '.on_boarding_img', function() {
        readURL(this, this.id);
    });

    function readURL(input, id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#link_img_' + id).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // OnBoarding Screen Add-Remove Link Part
    var i = -1;

    function add_more_screen() {

        var data = '<div class="onboarding_part">';
        data += '<div class="row col-md-12">';
        data += '<div class="form-group col-md-6">';
        data += '<label>{{__("label.title")}}<span class="text-danger">*</span></label>';
        data += '<input type="text" name="title[]" class="form-control" placeholder="Enter Title">';
        data += '</div>';
        data += '<div class="form-group col-lg-3">';
        data += '<label>{{__("label.image")}}<span class="text-danger">*</span></label>';
        data += '<input type="file" name="image[]" class="form-control import-file on_boarding_img" id="on_boarding_img_' + i + '" accept=".png, .jpg, .jpeg">';
        data += '<input type="hidden" name="old_image[]" value="">';
        data += '</div>';
        data += '<div class="form-group col-md-1">';
        data += '<div class="custom-file">';
        data += '<img src="{{asset("assets/imgs/upload_img.png")}}" class="img-thumbnail onboarding-img" id="link_img_on_boarding_img_' + i + '">';
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
        $("html, body").animate({
            scrollTop: $(document).height()
        }, "slow");
    }
    $("body").on("click", ".remove_on_boarding", function(e) {
        $(this).parents('.onboarding_part').remove();
    });
    // OnBoarding Screen Save
    function onboarding() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

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
                            <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                            <input type="text" name="step_name[]" class="form-control" placeholder="{{__('label.name_here')}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{__('label.url')}}<span class="text-danger">*</span></label>
                            <input type="text" name="step_url[]" class="form-control" placeholder="{{__('label.url_here')}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                            <input type="file" class="form-control step-image import-file" name="step_image[]" accept="image/png, image/jpg, image/jpeg" preview-id="Uploaded-Image-${stepIndex}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <div class="custom-file ml-2">
                                <img src="{{asset('assets/imgs/upload_img.png')}}" class="img-thumbnail social-link-img" id="Uploaded-Image-${stepIndex}">
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

    // JAILAOI: Toggle pool vs per_stream fields
    function toggleEarningsModel() {
        var model = $('#earnings_model').val();
        if (model === 'pool') {
            $('#platform_cut_group, #settlement_day_group, #pool_info').show();
            $('#per_stream_fields, #per_stream_info').hide();
        } else {
            $('#platform_cut_group, #settlement_day_group, #pool_info').hide();
            $('#per_stream_fields, #per_stream_info').show();
        }
    }
    $(document).ready(function() {
        $('#earnings_model').on('change', toggleEarningsModel);
        toggleEarningsModel();
    });

    // JAILAOI: Payout settings save
    function payout_settings_save() {
        var CheckAdmin = '<?php echo Check_Admin_Access(); ?>';
        if (CheckAdmin != 1) {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
            return;
        }
        // Serialize checkbox arrays into comma-separated strings
        var pm = [];
        $('.payment-method-checkbox:checked').each(function() { pm.push($(this).val()); });
        var idt = [];
        $('.id-type-checkbox:checked').each(function() { idt.push($(this).val()); });

        $('#dvloader').show();
        var formData = new FormData($('#payout_settings_form')[0]);
        // Remove array fields and add serialized string versions
        formData.delete('allowed_payment_methods[]');
        formData.delete('allowed_payment_methods_hidden');
        formData.delete('allowed_id_types[]');
        formData.append('allowed_payment_methods', pm.join(','));
        formData.append('allowed_id_types', idt.join(','));
        $.ajax({
            type: 'POST',
            url: '{{route("setting.save_key")}}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(resp) {
                $('#dvloader').hide();
                get_responce_message(resp);
            },
            error: function(XMLHttpRequest, errorThrown, textStatus) {
                $('#dvloader').hide();
                toastr.error(textStatus, errorThrown);
            }
        });
    }
</script>
@endsection