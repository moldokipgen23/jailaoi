@extends('admin.layout.page-app')
@section('page_title', __('label.app_settings'))
@section('tab_title', __('label.app_settings'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.app_settings')}}</h1>

            <div class="border-bottom row">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.app_settings')}}</li>
                    </ol>
                </div>
            </div>

            <ul class="nav nav-pills custom-tabs inline-tabs" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="app-tab" data-toggle="tab" href="#app" role="tab" aria-controls="app" aria-selected="true">{{__('label.app_settings')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="smtp-tab" data-toggle="tab" href="#smtp" role="tab" aria-controls="smtp" aria-selected="false">{{__('label.smtp')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="social-tab" data-toggle="tab" href="#social" role="tab" aria-controls="social" aria-selected="false">{{__('label.social_setting')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="onboarding-tab" data-toggle="tab" href="#onboarding" role="tab" aria-controls="onboarding" aria-selected="false">{{__('label.onboarding_screen')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="live-streaming-tab" data-toggle="tab" href="#live-streaming" role="tab"aria-controls="live-streaming" aria-selected="false">{{__('label.live_streaming')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="commission-tab" data-toggle="tab" href="#commission" role="tab" aria-controls="commission" aria-selected="true">{{__('label.commission')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="refer_&_earn-tab" data-toggle="tab" href="#refer_&_earn" role="tab" aria-controls="refer_&_earn" aria-selected="true">{{__('label.refer_&_earn')}}</a>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="app" role="tabpanel" aria-labelledby="app-tab">
                    <div class="card custom-border-card">
                        <h5 class="card-header">{{__('label.app_settings')}}</h5>
                        <div class="card-body">
                            <form id="app_setting" enctype="multipart/form-data">
                                <div class="form-row">
                                    <div class="col-md-9">
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>{{__('label.app_name')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="app_name" value="{{ $result['app_name'] }}" class="form-control" placeholder="{{__('label.app_name_here')}}">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>{{__('label.app_version')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="app_version" value="{{ $result['app_version'] }}" class="form-control" placeholder="{{__('label.app_version_here')}}">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>{{__('label.email')}} <span class="text-danger">*</span></label>
                                                <input type="email" name="email" value="{{ $result['email'] }}" class="form-control" placeholder="{{__('label.email_here')}}">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>{{__('label.author')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="author" value="{{ $result['author'] }}" class="form-control" placeholder="{{__('label.author_here')}}">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label> {{__('label.contact')}} <span class="text-danger">*</span></label>
                                                <input type="text" name="contact" value="{{ $result['contact'] }}" class="form-control" placeholder="{{__('label.contact_here')}}">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>{{__('label.website')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="website" value="{{ $result['website'] }}" class="form-control" placeholder="{{__('label.website_here')}}">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>{{__('label.app_description')}}<span class="text-danger">*</span></label>
                                                <textarea name="app_description" rows="1" class="form-control" placeholder="{{__('label.app_description_here')}}">{{ $result['app_description'] }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group ml-5">
                                            <label class="ml-5">{{__('label.app_logo')}}<span class="text-danger">*</span></label>
                                            <div class="avatar-upload ml-5">
                                                <div class="avatar-edit">
                                                    <input type='file' name="app_logo" id="imageUpload1" accept=".png, .jpg, .jpeg" />
                                                    <label for="imageUpload1" title="{{__('label.upload_file')}}"></label>
                                                </div>
                                                <div class="avatar-preview">
                                                    <img src="{{ $result['app_logo'] }}" id="imagePreview1">
                                                </div>
                                            </div>
                                            <input type="hidden" name="old_app_logo" value="{{ $result['app_logo'] }}">
                                            <input type="hidden" name="old_app_logo_storage_type" value="{{ $result['app_logo_storage_type'] }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="app_setting()">{{__('label.save')}}</button>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-6">
                            <!-- API Configrations -->
                            <div class="card custom-border-card">
                                <h5 class="card-header">{{__('label.api_configrations')}}</h5>
                                <div class="card-body">
                                    <div class="input-group">
                                        <div class="col-2">
                                            <label class="pt-3" style="font-size:16px; font-weight:500">{{__('label.api_path')}}</label>
                                        </div>
                                        <input type="text" readonly value="{{url('/')}}/api/" name="api_path" class="form-control" id="api_path">
                                        <div class="input-group-text ml-2" onclick="Function_Api_path()">
                                            <i class="fa-solid fa-copy fa-2xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Purchase Code -->
                            <div class="card custom-border-card">
                                <h5 class="card-header">{{__('label.purchase_code')}}</h5>
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>{{__('label.purchase_code')}}</label>
                                            <input type="text" class="form-control" value="{{env('PURCHASE_CODE')}}" readonly>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label> {{__('label.envato_name')}}</label>
                                            <input type="text" class="form-control" value="{{env('BUYER_USERNAME')}}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- DeepAR -->
                            <div class="card custom-border-card">
                                <h5 class="card-header">{{__('label.deepar')}}</h5>
                                <div class="card-body">
                                    <form id="deepar_save" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="form-group col-12">
                                                <label>{{__('label.android_key')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="deepar_android_key" value="{{ $result['deepar_android_key'] }}" class="form-control" placeholder="{{__('label.android_key_here')}}">
                                            </div>
                                            <div class="form-group col-12">
                                                <label>{{__('label.ios_key')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="deepar_ios_key" value="{{ $result['deepar_ios_key'] }}" class="form-control" placeholder="{{__('label.ios_key_here')}}">
                                                <label class="mt-1 text-gray">{{__('label.search_for_better_result')}}<a href="https://developer.deepar.ai/" target="_blank" class="btn-link">{{__('label.click_here')}}</a></label>
                                            </div>
                                        </div>
                                        <div class="border-top pt-3 text-right">
                                            <button type="button" class="btn btn-default mw-120" onclick="deepar_save()">{{__('label.save')}}</button>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <!-- Currency Settings -->
                            <div class="card custom-border-card">
                                <h5 class="card-header">{{__('label.currency_settings')}}</h5>
                                <div class="card-body">
                                    <form id="save_currency">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>{{__('label.currency_name')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="currency" class="form-control" value="{{ $result['currency'] }}" placeholder="{{__('label.currency_name_here')}}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label> {{__('label.currency_code')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="currency_code" class="form-control" value="{{ $result['currency_code'] }}" placeholder="{{__('label.currency_code_here')}}">
                                            </div>
                                        </div>
                                        <div class="border-top pt-3 text-right">
                                            <button type="button" class="btn btn-default mw-120" onclick="save_currency()">{{__('label.save')}}</button>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Vapid Key -->
                            <div class="card custom-border-card">
                                <h5 class="card-header">{{__('label.vap_id_key')}}</h5>
                                <div class="card-body">
                                    <form id="save_vap_id_key">
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <input type="text" name="vap_id_key" class="form-control" value="{{ $result['vap_id_key'] }}" placeholder="{{__('label.vap_id_key_here')}}">
                                            </div>
                                        </div>
                                        <div class="border-top pt-3 text-right">
                                            <button type="button" class="btn btn-default mw-120" onclick="save_vap_id_key()">{{__('label.save')}}</button>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- App Download Platform-->
                            <div class="card custom-border-card">
                                <h5 class="card-header">{{__('label.app_download_platform')}}</h5>
                                <div class="card-body">
                                    <form id="save_appdownload">
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>{{__('label.playstore_id')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="playstore_id" class="form-control" value="{{ $result['playstore_id'] }}" placeholder="{{__('label.playstore_id_here')}}">
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label> {{__('label.appstore_id')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="appstore_id" class="form-control" value="{{ $result['appstore_id'] }}" placeholder="{{__('label.appstore_id_here')}}">
                                            </div>
                                        </div>
                                        <div class="border-top pt-3 text-right">
                                            <button type="button" class="btn btn-default mw-120" onclick="save_appdownload()">{{__('label.save')}}</button>
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
                        <h5 class="card-header">{{__('label.email_setting_smtp')}}</h5>
                        <div class="card-body">
                            <form id="smtp_setting">
                                <input type="hidden" name="id" value="{{ $smtp['id'] }}">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.is_smtp_active')}}<span class="text-danger">*</span></label>
                                        <select name="status" class="form-control">
                                            <option value="">{{__('label.select_status')}}</option>
                                            <option value="0" {{ $smtp->status == 0 ? 'selected' : ''}}>{{__('label.no')}}</option>
                                            <option value="1" {{ $smtp->status == 1 ? 'selected' : ''}}>{{__('label.yes')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.host')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="host" class="form-control" value="{{ $smtp['host'] }}" placeholder="{{__('label.host_here')}}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.port')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="port" class="form-control" value="{{ $smtp['port'] }}" placeholder="{{__('label.port_here')}}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.protocol')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="protocol" class="form-control" value="{{ $smtp['protocol'] }}" placeholder="{{__('label.protocol_here')}}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.user_name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="user" class="form-control" value="{{ $smtp['user'] }}" placeholder="{{__('label.user_name_here')}}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.password')}}<span class="text-danger">*</span></label>
                                        <input type="password" name="pass" class="form-control" value="{{ $smtp['pass'] }}" placeholder="{{__('label.password_here')}}">
                                        <label class="mt-1 text-gray">{{__('label.search_for_better_result')}} <a href="https://support.google.com/mail/answer/185833?hl=en" target="_blank" class="btn-link">{{__('label.click_here')}}</a></label>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.from_name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="from_name" class="form-control" value="{{ $smtp['from_name'] }}" placeholder="{{__('label.from_name_here')}}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.from_email')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="from_email" class="form-control" value="{{ $smtp['from_email'] }}" placeholder="{{__('label.from_email_here')}}">
                                    </div>
                                </div>
                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="smtp_setting()">{{__('label.save')}}</button>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </div>
                            </form>
                        </div>
                    </div>
                    @if($smtp->status == 1)
                        <div class="card custom-border-card">
                            <h5 class="card-header">{{__('label.email_test')}}</h5>
                            <div class="card-body">
                                <form id="email_test">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>{{__('label.email')}}<span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" placeholder="{{__('label.email_here')}}">
                                        </div>
                                    </div>
                                    <div class="border-top pt-3 text-right">
                                        <button type="button" class="btn btn-default mw-120" onclick="email_test()">{{__('label.send_mail')}}</button>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                    <div class="card custom-border-card">
                        <h5 class="card-header">{{__('label.social_links')}}</h5>
                        <div class="card-body">
                            <form id="social_link" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="form-group col-md-2">
                                        <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="name[]" class="form-control" placeholder="{{__('label.name_here')}}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.url')}}<span class="text-danger">*</span></label>
                                        <input type="url" name="url[]" class="form-control" placeholder="{{__('label.url_here')}}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.icon')}}<span class="text-danger">*</span></label>
                                        <input type="file" name="image[]" class="form-control import-file social_img" id="social_img" accept=".png, .jpg, .jpeg">
                                        <input type="hidden" name="old_image[]" value="">
                                        <input type="hidden" name="old_storage_type[]" value="">
                                    </div>
                                    <div class="form-group col-md-1">
                                        <div class="custom-file">
                                            <img src="{{ asset('assets/imgs/upload_img.png') }}" style="height: 90px; width: 90px;" class="img-thumbnail" id="link_img_social_img">
                                        </div>
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <div class="flex-grow-1 px-5 d-inline-flex">
                                            <div class="change mr-3 mt-4" id="add_btn">
                                                <a class="btn btn-success add-more text-white" onclick="add_more_link()">+</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @for ($i=0; $i < count($social_link); $i++)
                                    <div class="social_part">
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="name[]" value="{{ $social_link[$i]['name'] }}" class="form-control" placeholder="{{__('label.name_here')}}">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>{{__('label.url')}}<span class="text-danger">*</span></label>
                                                <input type="url" name="url[]" value="{{ $social_link[$i]['url'] }}" class="form-control" placeholder="{{__('label.url_here')}}">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>{{__('label.icon')}}<span class="text-danger">*</span></label>
                                                <input type="file" name="image[]" class="form-control import-file social_img" id="social_img_{{$i}}" accept=".png, .jpg, .jpeg">
                                                <input type="hidden" name="old_image[]" value="{{ basename($social_link[$i]['image']) }}">
                                                <input type="hidden" name="old_storage_type[]" value="{{ $social_link[$i]['storage_type'] }}">
                                            </div>
                                            <div class="form-group col-md-1">
                                                <div class="custom-file">
                                                    <img src="{{ $social_link[$i]['image'] }}" style="height: 90px; width: 90px;" class="img-thumbnail" id="link_img_social_img_{{$i}}">
                                                </div>
                                            </div>
                                            <div class="col-md-1 mt-2">
                                                <div class="flex-grow-1 px-5 d-inline-flex">
                                                    <div class="change mr-3 mt-4" id="add_btn">
                                                        <a class="btn btn-danger text-white remove_link">-</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                                    
                                <div class="add-more-social-link"></div>

                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="social_link()">{{__('label.save')}}</button>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="onboarding" role="tabpanel" aria-labelledby="onboarding-tab">
                    <div class="card custom-border-card">
                        <h5 class="card-header">{{__('label.onboarding_screen')}}</h5>
                        <div class="card-body">
                            <form id="onboarding_screen" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="form-group col-md-2 ">
                                        <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="title[]" class="form-control" placeholder="{{__('label.title_here')}}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.description')}}<span class="text-danger">*</span></label>
                                        <textarea name="description[]" rows="1" class="form-control" placeholder="{{__('label.description_here')}}"></textarea>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                                        <input type="file" name="image[]" class="form-control import-file on_boarding_img" id="on_boarding_img" accept=".png, .jpg, .jpeg">
                                        <input type="hidden" name="old_image[]" value="">
                                        <input type="hidden" name="old_storage_type[]" value="">
                                    </div>
                                    <div class="form-group col-md-1">
                                        <div class="custom-file">
                                            <img src="{{ asset('assets/imgs/upload_img.png') }}" style="height: 90px; width: 90px;" class="img-thumbnail" id="link_img_on_boarding_img">
                                        </div>
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <div class="flex-grow-1 px-5 d-inline-flex">
                                            <div class="change mr-3 mt-4" id="add_btn">
                                                <a class="btn btn-success add-more text-white" onclick="add_more_screen()">+</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @for ($i=0; $i < count($onboarding_screen); $i++)
                                    <div class="onboarding_part">
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="title[]" value="{{ $onboarding_screen[$i]['title'] }}" class="form-control" placeholder="{{__('label.title_here')}}">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>{{__('label.description')}}<span class="text-danger">*</span></label>
                                                <textarea name="description[]" rows="1" class="form-control" placeholder="{{__('label.description_here')}}">{{ $onboarding_screen[$i]['description'] }}</textarea>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                                                <input type="file" name="image[]" class="form-control import-file on_boarding_img" id="on_boarding_img{{$i}}" accept=".png, .jpg, .jpeg">
                                                <input type="hidden" name="old_image[]" value="{{ basename($onboarding_screen[$i]['image']) }}">
                                                <input type="hidden" name="old_storage_type[]" value="{{ $onboarding_screen[$i]['storage_type'] }}">
                                            </div>
                                            <div class="form-group col-md-1">
                                                <div class="custom-file">
                                                    <img src="{{ $onboarding_screen[$i]['image'] }}" style="height: 90px; width: 90px;" class="img-thumbnail" id="link_img_on_boarding_img{{$i}}">
                                                </div>
                                            </div>
                                            <div class="col-md-1 mt-2">
                                                <div class="flex-grow-1 px-5 d-inline-flex">
                                                    <div class="change mr-3 mt-4" id="add_btn">
                                                        <a class="btn btn-danger text-white remove_on_boarding">-</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor

                                <div class="add-more-onboarding"></div>

                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="onboarding_screen()">{{__('label.save')}}</button>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="live-streaming" role="tabpanel" aria-labelledby="live-streaming-tab">
                    <div class="card custom-border-card">
                        <h5 class="card-header">{{__('label.live_streaming_zego_cloud')}}</h5>
                        <div class="card-body">
                            <form id="live_streaming" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="form-group col-lg-3">
                                        <label>{{__('label.app_id')}}<span class="text-danger">*</span></label>
                                        <input type="text" value="{{ $result['live_appid'] }}" name="live_appid" class="form-control" placeholder="{{__('label.app_id_here')}}">
                                        <label class="mt-1 text-gray">{{__('label.search_for_better_result')}}<a href="https://console.zegocloud.com/account/login" target="_blank" class="btn-link">{{__('label.click_here')}}</a></label>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label>{{__('label.app_sign')}}<span class="text-danger">*</span></label>
                                        <input type="text" value="{{ $result['live_appsign'] }}" name="live_appsign" class="form-control" placeholder="{{__('label.app_sign_here')}}">
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label>{{__('label.server_secret')}}<span class="text-danger">*</span></label>
                                        <input type="text" value="{{ $result['live_serversecret'] }}" name="live_serversecret" class="form-control" placeholder="{{__('label.server_secret_here')}}">
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="is_live_streaming_fake">{{__('label.is_live_streaming_fake')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_live_streaming_fake" id="is_live_streaming_fake_yes" class="custom-control-input" {{ $result['is_live_streaming_fake'] == '1' ? "checked" : "" }} value="1">
                                                <label class="custom-control-label" for="is_live_streaming_fake_yes">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_live_streaming_fake" id="is_live_streaming_fake_no" class="custom-control-input" {{ $result['is_live_streaming_fake'] == '0' ? "checked" : "" }} value="0">
                                                <label class="custom-control-label" for="is_live_streaming_fake_no">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="live_streaming()">{{__('label.save')}}</button>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="commission" role="tabpanel" aria-labelledby="commission-tab">
                    <div class="form-row">
                        <div class="col-6">
                            <!-- Ads Commission -->
                            <div class="card custom-border-card">
                                <h5 class="card-header">{{__('label.ads_commission')}}</h5>
                                <div class="card-body">
                                    <form id="save_ads_commission">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>{{__('label.commission')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="ads_commission" class="form-control" value="{{ $result['ads_commission'] }}" placeholder="{{__('label.commission_here')}}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label> {{__('label.percentage')}}<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="%" readonly>
                                            </div>
                                        </div>
                                        <div class="border-top pt-3 text-right">
                                            <button type="button" class="btn btn-default mw-120" onclick="save_ads_commission()">{{__('label.save')}}</button>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <!-- Rent Commission -->
                            <div class="card custom-border-card">
                                <h5 class="card-header">{{__('label.rent_commission')}}</h5>
                                <div class="card-body">
                                    <form id="save_rent_commission">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>{{__('label.commission')}}<span class="text-danger">*</span></label>
                                                <input type="text" name="rent_commission" class="form-control" value="{{ $result['rent_commission'] }}" placeholder="{{__('label.commission_here')}}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label> {{__('label.percentage')}}<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="%" readonly>
                                            </div>
                                        </div>
                                        <div class="border-top pt-3 text-right">
                                            <button type="button" class="btn btn-default mw-120" onclick="save_rent_commission()">{{__('label.save')}}</button>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="refer_&_earn" role="tabpanel" aria-labelledby="refer_&_earn-tab">
                    <div class="card custom-border-card">
                        <h5 class="card-header">{{__('label.refer_&_earn')}}</h5>
                        <div class="card-body">
                            <form id="save_refer_earn">
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label>{{__('label.refer_&_earn_status')}}<span class="text-danger">*</span></label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="refer_and_earn_status" id="refer_and_earn_status_on" class="custom-control-input" {{ $result['refer_and_earn_status'] == '1' ? "checked" : "" }} value="1">
                                                <label class="custom-control-label" for="refer_and_earn_status_on">{{__('label.on')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="refer_and_earn_status" id="refer_and_earn_status_off" class="custom-control-input" {{ $result['refer_and_earn_status'] == '0' ? "checked" : "" }} value="0">
                                                <label class="custom-control-label" for="refer_and_earn_status_off">{{__('label.off')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.parent_user_coin')}}<span class="text-danger">*</span></label>
                                        <input type="number" name="parent_user_earn" class="form-control" value="{{ $result['parent_user_earn'] }}" placeholder="{{__('label.coin_here')}}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{__('label.child_user_coin')}}<span class="text-danger">*</span></label>
                                        <input type="number" name="child_user_earn" class="form-control" value="{{ $result['child_user_earn'] }}" placeholder="{{__('label.coin_here')}}">
                                    </div>
                                </div>
                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="save_refer_earn()">{{__('label.save')}}</button>
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

        function app_setting() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                var formData = new FormData($("#app_setting")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.appsetting.app") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'app_setting', '{{ route("admin.appsetting.index") }}');
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
        function save_currency() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                var formData = new FormData($("#save_currency")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.appsetting.currency") }}',
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
        function save_vap_id_key() {

            var formData = new FormData($("#save_vap_id_key")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("admin.appsetting.vapidkey") }}',
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
        }
        function smtp_setting() {

            var formData = new FormData($("#smtp_setting")[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route("admin.appsetting.smtp") }}',
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
        }

        // Multipal Img Show 
        $(document).on('change', '.social_img', function() {
            readURL(this, this.id);
        });
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

        // Social Link Add-Remove Link Part
        var i = -1;
        function add_more_link() {

            var data = '<div class="social_part">';
            data += '<div class="row">';
            data += '<div class="form-group col-md-2">';
            data += '<label>{{__("label.name")}}<span class="text-danger">*</span></label>';
            data += '<input type="text" name="name[]" class="form-control" placeholder="{{__("label.name_here")}}">';
            data += '</div>';
            data += '<div class="form-group col-md-3">';
            data += '<label>{{__("label.url")}}<span class="text-danger">*</span></label>';
            data += '<input type="url" name="url[]" class="form-control" placeholder="{{__("label.url_here")}}">';
            data += '</div>';
            data += '<div class="form-group col-lg-3">';
            data += '<label>{{__("label.icon")}}<span class="text-danger">*</span></label>';
            data += '<input type="file" name="image[]" class="form-control import-file social_img" id="social_img_' + i + '" accept=".png, .jpg, .jpeg">';
            data += '<input type="hidden" name="old_image[]" value="">';
            data += '<input type="hidden" name="old_storage_type[]" value="">';
            data += '</div>';
            data += '<div class="form-group col-md-1">';
            data += '<div class="custom-file">';
            data += '<img src="{{ asset("assets/imgs/upload_img.png") }}" style="height: 90px; width: 90px;" class="img-thumbnail" id="link_img_social_img_' + i + '">';
            data += '</div>';
            data += '</div>';
            data += '<div class="col-md-1 mt-2">';
            data += '<div class="flex-grow-1 px-5 d-inline-flex">';
            data += '<div class="change mr-3 mt-4" id="add_btn">';
            data += '<a class="btn btn-danger add-more text-white remove_link">-</a>';
            data += '</div>';
            data += '</div>';
            data += '</div>';
            data += '</div>';
            data += '</div>';

            $('.add-more-social-link').append(data);
            i--;
            $("html, body").animate({
                scrollTop: $(document).height()
            }, "slow");
        }
        $("body").on("click", ".remove_link", function(e) {
            $(this).parents('.social_part').remove();
        });
        // Social Link Save
        function social_link() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                $("#dvloader").show();
                var formData = new FormData($("#social_link")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.appsetting.sociallink") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'social_link', '{{ route("admin.appsetting.index") }}');
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

        // OnBoarding Screen Add-Remove Link Part
        var i = -1;
        function add_more_screen() {

            var data = '<div class="onboarding_part">';
            data += '<div class="row">';
            data += '<div class="form-group col-md-2">';
            data += '<label>{{__("label.title")}}<span class="text-danger">*</span></label>';
            data += '<input type="text" name="title[]" class="form-control" placeholder="{{__("label.title_here")}}">';
            data += '</div>';
            data += '<div class="form-group col-md-3">';
            data += '<label>{{__("label.description")}}<span class="text-danger">*</span></label>';
            data += '<textarea name="description[]" rows="1" class="form-control" placeholder="{{__("label.description_here")}}"></textarea>';
            data += '</div>';
            data += '<div class="form-group col-lg-3">';
            data += '<label>{{__("label.image")}}<span class="text-danger">*</span></label>';
            data += '<input type="file" name="image[]" class="form-control import-file on_boarding_img" id="on_boarding_img_' + i + '" accept=".png, .jpg, .jpeg">';
            data += '<input type="hidden" name="old_image[]" value="">';
            data += '<input type="hidden" name="old_storage_type[]" value="">';
            data += '</div>';
            data += '<div class="form-group col-md-1">';
            data += '<div class="custom-file">';
            data += '<img src="{{ asset("assets/imgs/upload_img.png") }}" style="height: 90px; width: 90px;" class="img-thumbnail" id="link_img_on_boarding_img_' + i + '">';
            data += '</div>';
            data += '</div>';
            data += '<div class="col-md-1 mt-2">';
            data += '<div class="flex-grow-1 px-5 d-inline-flex">';
            data += '<div class="change mr-3 mt-4" id="add_btn">';
            data += '<a class="btn btn-danger add-more text-white remove_on_boarding">-</a>';
            data += '</div>';
            data += '</div>';
            data += '</div>';
            data += '</div>';
            data += '</div>';

            $('.add-more-onboarding').append(data);
            i--;
            $("html, body").animate({
                scrollTop: $(document).height()
            }, "slow");
        }
        $("body").on("click", ".remove_on_boarding", function(e) {
            $(this).parents('.onboarding_part').remove();
        });
        // OnBoarding Screen Save
        function onboarding_screen() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                $("#dvloader").show();
                var formData = new FormData($("#onboarding_screen")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.appsetting.onboardingscreen") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'onboarding_screen', '{{ route("admin.appsetting.index") }}');
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

        // Live Streaming
        function live_streaming() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                var formData = new FormData($("#live_streaming")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.appsetting.livestreaming") }}',
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
        // DeepAR
        function deepar_save() {

            var formData = new FormData($("#deepar_save")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("admin.appsetting.deepar") }}',
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
        }
        // Commission
        function save_ads_commission() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                var formData = new FormData($("#save_ads_commission")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.appsetting.adscommission") }}',
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
        function save_rent_commission() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                var formData = new FormData($("#save_rent_commission")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.appsetting.rentcommission") }}',
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
        // Refer & Earn
        function save_refer_earn() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                var formData = new FormData($("#save_refer_earn")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.appsetting.referearn") }}',
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
        // Email Test
        function email_test() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                var formData = new FormData($("#email_test")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.appsetting.emailtest") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'email_test', '{{ route("admin.appsetting.index") }}');
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
        // App Download
        function save_appdownload() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                var formData = new FormData($("#save_appdownload")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.appsetting.appdownload") }}',
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