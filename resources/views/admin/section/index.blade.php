@extends('admin.layout.page-app')
@section('page_title', __('label.section'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm"> {{__('label.section')}} </h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-11">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.section')}}</li>
                </ol>
            </div>
            <div class="col-sm-1 d-flex justify-content-start mb-3" title="{{__('label.sortable')}}">
                <button type="button" data-toggle="modal" data-target="#sortableModal" onclick="sortableBTN()" class="btn btn-default rounded-10">
                    <i class="fa-solid fa-sort fa-1x"></i>
                </button>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <!-- upper tabs  -->
            <ul class="nav nav-pills custom-tabs inline-tabs" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" onclick="change_section(1)" data-id="1" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">{{__('label.home')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" onclick="change_section(2)" data-id="2" id="music-tab" data-toggle="tab" href="#music" role="tab" aria-controls="music" aria-selected="false">{{__('label.music')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" onclick="change_section(3)" data-id="3" id="radio-station-tab" data-toggle="tab" href="#radio-station" role="tab" aria-controls="radio_station" aria-selected="true">{{__('label.radio_station')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" onclick="change_section(4)" data-id="4" id="podcast-tab" data-toggle="tab" href="#podcast" role="tab" aria-controls="podcast" aria-selected="false">{{__('label.podcast')}}</a>
                </li>
            </ul>

            <div class="col-md-3">
                <select class="form-control user-filter" id="search_user">
                    <option value="0">{{__('label.all_user')}}</option>
                    @foreach($users as $key=>$value)
                    <option value="{{$value['id']}}">{{$value['full_name']}}({{$value['user_name']}})</option>
                    @endforeach
                </select>
            </div>

        </div>
        <!-- add section  -->
        <div class="card custom-border-card mt-3">
            <h5 class="card-header">{{__('label.add_section')}}</h5>
            <div class="card-body">
                <form id="section" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="">
                    <div class="form-row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="{{__('label.title_here')}}" autofocus>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{__('label.sub_title')}}</label>
                                <input type="text" name="sub_title" class="form-control" placeholder="{{__('label.sub_title_here')}}">
                            </div>
                        </div>
                        <div class="col-md-3 type_drop">
                            <div class="form-group">
                                <label>{{__('label.type')}}<span class="text-danger">*</span></label>
                                <select name="type" class="form-control" id="type">
                                    <option value="">{{__('label.select_type')}}</option>
                                    <option value="1">{{__('label.radio_station')}}</option>
                                    <option value="2">{{__('label.podcast')}}</option>
                                    <option value="3">{{__('label.live_event')}}</option>
                                    <option value="4">{{__('label.artist')}}</option>
                                    <option value="5">{{__('label.category')}}</option>
                                    <option value="6">{{__('label.language')}}</option>
                                    <option value="7">{{__('label.city')}}</option>
                                    <option value="8">{{__('label.music')}}</option>
                                    <option value="9">Continue Listening</option>
                                    <option value="10">Liked Songs</option>
                                    <option value="11">From Artists You Follow</option>
                                    <option value="12">Based on Your Top Category</option>
                                    <option value="13">New in Your Language</option>
                                    <option value="14">Hidden Gems</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{__('label.user')}}</label>
                                <select class="form-control" id="user_id" name="user_id">
                                    <option value="0">{{__('label.all_user')}}</option>
                                    @foreach($users as $key=>$value)
                                    <option value="{{$value['id']}}">{{$value['full_name']}} ({{$value['user_name']}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 screen_layout">
                            <div class="form-group">
                                <label>{{__('label.screen_layout')}}<span class="text-danger">*</span></label>
                                <select name="screen_layout" class="form-control" id="screen_layout">
                                    <option value="">{{__('label.select_screen_layout')}}</option>
                                    <option value="landscape">{{__('label.landscape')}}</option>
                                    <option value="square">{{__('label.square')}}</option>
                                    <option value="small_square">{{__('label.small_square')}}</option>
                                    <option value="round">{{__('label.round')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 artist_drop">
                            <div class="form-group ">
                                <label>{{__('label.artist')}}</label>
                                <select class="form-control" name="artist_id" id="artist_id">
                                    <option value="0">{{__('label.all_artist')}}</option>
                                    @foreach ($artist as $key => $value)
                                    <option value="{{ $value->id}}" data-type="{{$value->type}}">
                                        {{ $value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 category_drop">
                            <div class="form-group ">
                                <label>{{__('label.category')}}</label>
                                <select class="form-control" name="category_id" id="category_id">
                                    <option value="0">{{__('label.all_category')}}</option>
                                    @foreach ($category as $key => $value)
                                    <option value="{{ $value->id}}">
                                        {{ $value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 language_drop">
                            <div class="form-group ">
                                <label>{{__('label.language')}}</label>
                                <select class="form-control" name="language_id" id="language_id">
                                    <option value="0">{{__('label.all_language')}}</option>
                                    @foreach ($language as $key => $value)
                                    <option value="{{ $value->id}}">
                                        {{ $value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 city_drop">
                            <div class="form-group ">
                                <label>{{__('label.city')}}</label>
                                <select class="form-control" name="city_id" id="city_id">
                                    <option value="0">{{__('label.all_city')}}</option>
                                    @foreach ($city as $key => $value)
                                    <option value="{{ $value->id}}">
                                        {{ $value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 no_of_content">
                            <div class="form-group">
                                <label>{{__('label.no_of_content')}}<span class="text-danger">*</span></label>
                                <input type="number" name="no_of_content" class="form-control" placeholder="{{__('label.no_of_content_here')}}">
                            </div>
                        </div>
                        <div class="col-md-2 view_all">
                            <div class="form-group ml-1">
                                <label>{{__('label.view_all')}}<span class="text-danger">*</span></label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="view_all" id="view_all_yes" class="custom-control-input" value="1">
                                        <label class="custom-control-label" for="view_all_yes">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="view_all" id="view_all_no" class="custom-control-input" value="0" checked>
                                        <label class="custom-control-label" for="view_all_no">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 is_premium">
                            <div class="form-group  ml-1">
                                <label>{{__('label.is_premium')}}<span class="text-danger">*</span></label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_premium" id="is_premium_yes" class="custom-control-input" value="1">
                                        <label class="custom-control-label" for="is_premium_yes">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_premium" id="is_premium_no" class="custom-control-input" value="0" checked>
                                        <label class="custom-control-label" for="is_premium_no">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 order_by_upload">
                            <div class="form-group  ml-1">
                                <label>{{__('label.order_by_upload')}}<span class="text-danger">*</span></label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="order_by_upload" id="order_by_upload_asc" class="custom-control-input" value="0">
                                        <label class="custom-control-label" for="order_by_upload_asc">{{__('label.asc')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="order_by_upload" id="order_by_upload_desc" class="custom-control-input" value="1" checked>
                                        <label class="custom-control-label" for="order_by_upload_desc">{{__('label.desc')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 order_by_play">
                            <div class="form-group  ml-3">
                                <label>{{__('label.order_by_play')}}<span class="text-danger">*</span></label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="order_by_play" id="order_by_play_asc" class="custom-control-input" value="0">
                                        <label class="custom-control-label" for="order_by_play_asc">{{__('label.asc')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="order_by_play" id="order_by_play_desc" class="custom-control-input" value="1" checked>
                                        <label class="custom-control-label" for="order_by_play_desc">{{__('label.desc')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 time_window">
                            <div class="form-group">
                                <label>{{__('label.time_window')}}</label>
                                <select name="time_window_days" class="form-control">
                                    <option value="0">{{__('label.all_time')}}</option>
                                    <option value="7">{{__('label.last_7_days')}}</option>
                                    <option value="30">{{__('label.last_30_days')}}</option>
                                    <option value="90">{{__('label.last_90_days')}}</option>
                                </select>
                                <small style="color:#888;font-size:11px;display:block;margin-top:3px;">{{__('label.time_window_help')}}</small>
                            </div>
                        </div>
                        <div class="col-md-2 is_paid">
                            <div class="form-group  ml-4">
                                <label>{{__('label.is_paid')}}<span class="text-danger">*</span></label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_paid" id="is_paid_yes" class="custom-control-input" value="1">
                                        <label class="custom-control-label" for="is_paid_yes">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_paid" id="is_paid_no" class="custom-control-input" value="0" checked>
                                        <label class="custom-control-label" for="is_paid_no">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 is_title">
                            <div class="form-group ml-1">
                                <label>{{__('label.is_title')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_title" id="is_title_yes" class="custom-control-input" value="1">
                                        <label class="custom-control-label" for="is_title_yes">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_title" id="is_title_no" class="custom-control-input" value="0" checked>
                                        <label class="custom-control-label" for="is_title_no">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 is_category">
                            <div class="form-group  ml-1">
                                <label>{{__('label.is_category')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_category" id="is_category_yes" class="custom-control-input" value="1">
                                        <label class="custom-control-label" for="is_category_yes">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_category" id="is_category_no" class="custom-control-input" value="0" checked>
                                        <label class="custom-control-label" for="is_category_no">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 is_artist_name">
                            <div class="form-group  ml-1">
                                <label>{{__('label.is_artist_name')}}</label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_artist_name" id="is_artist_name_yes" class="custom-control-input" value="1">
                                        <label class="custom-control-label" for="is_artist_name_yes">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_artist_name" id="is_artist_name_no" class="custom-control-input" value="0" checked>
                                        <label class="custom-control-label" for="is_artist_name_no">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="save_section()">{{__('label.save')}}</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>
            </div>
        </div>
        <!-- section  list  -->
        <div class="after-add-more"></div>
        <!-- edit section -->
        <div class="modal fade" id="editsectioneModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="editsectioneModallabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editsectioneModallabel">{{__('label.edit_section')}}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="edit_content_section" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="edit_id" value="">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="edit_title" class="form-control" placeholder="{{__('label.title_here')}}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('label.sub_title')}}</label>
                                        <input type="text" name="sub_title" id="edit_sub_title" class="form-control" placeholder="{{__('label.sub_title_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-6 edit_type_drop">
                                    <div class="form-group">
                                        <label>{{__('label.type')}}<span class="text-danger">*</span></label>
                                        <select name="type" class="form-control" id="edit_type">
                                            <option value="">{{__('label.select_type')}}</option>
                                            <option value="1">{{__('label.radio_station')}}</option>
                                            <option value="2">{{__('label.podcast')}}</option>
                                            <option value="3">{{__('label.live_event')}}</option>
                                            <option value="4">{{__('label.artist')}}</option>
                                            <option value="5">{{__('label.category')}}</option>
                                            <option value="6">{{__('label.language')}}</option>
                                            <option value="7">{{__('label.city')}}</option>
                                            <option value="8">{{__('label.music')}}</option>
                                            <option value="9">Continue Listening</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('label.user')}}</label>
                                        <select name="user_id" class="form-control" id="edit_user_id" style="width: 100%!important;">
                                            <option value="0">{{__('label.all_user')}}</option>
                                            @foreach($users as $key=>$value)
                                            <option value="{{$value['id']}}">{{$value['full_name']}} ({{$value['user_name']}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 edit_screen_layout">
                                    <div class="form-group">
                                        <label>{{__('label.screen_layout')}}<span class="text-danger">*</span></label>
                                        <select name="screen_layout" class="form-control" id="edit_screen_layout">
                                            <option value="">{{__('label.select_screen_layout')}}</option>
                                            <option value="landscape">{{__('label.landscape')}}</option>
                                            <option value="square">{{__('label.square')}}</option>
                                            <option value="small_square">{{__('label.small_square')}}</option>
                                            <option value="round">{{__('label.round')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 edit_artist_drop">
                                    <div class="form-group ">
                                        <label>{{__('label.artist')}}</label>
                                        <select class="form-control" name="artist_id" id="edit_artist_id" style="width: 100%!important;">
                                            <option value="0">{{__('label.all_artist')}}</option>
                                            @foreach ($artist as $key => $value)
                                            <option value="{{ $value->id}}" data-type="{{$value->type}}">
                                                {{ $value->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 edit_category_drop">
                                    <div class="form-group ">
                                        <label>{{__('label.category')}}</label>
                                        <select class="form-control" name="category_id" id="edit_category_id" style="width: 100%!important;">
                                            <option value="0">{{__('label.all_category')}}</option>
                                            @foreach ($category as $key => $value)
                                            <option value="{{ $value->id}}">
                                                {{ $value->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 edit_language_drop">
                                    <div class="form-group ">
                                        <label>{{__('label.language')}}</label>
                                        <select class="form-control" name="language_id" id="edit_language_id" style="width: 100%!important;">
                                            <option value="0">{{__('label.all_language')}}</option>
                                            @foreach ($language as $key => $value)
                                            <option value="{{ $value->id}}">
                                                {{ $value->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 edit_city_drop">
                                    <div class="form-group ">
                                        <label>{{__('label.city')}}</label>
                                        <select class="form-control" name="city_id" id="edit_city_id" style="width: 100%!important;">
                                            <option value="0">{{__('label.all_city')}}</option>
                                            @foreach ($city as $key => $value)
                                            <option value="{{ $value->id}}">
                                                {{ $value->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 edit_no_of_content">
                                    <div class="form-group">
                                        <label>{{__('label.no_of_content')}}<span class="text-danger">*</span></label>
                                        <input type="number" name="no_of_content" id="edit_no_of_content" class="form-control" placeholder="{{__('label.no_of_content_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-4 edit_view_all">
                                    <div class="form-group ml-1">
                                        <label>{{__('label.view_all')}}<span class="text-danger">*</span></label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="view_all" id="edit_view_all_yes" class="custom-control-input" value="1">
                                                <label class="custom-control-label" for="edit_view_all_yes">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="view_all" id="edit_view_all_no" class="custom-control-input" value="0">
                                                <label class="custom-control-label" for="edit_view_all_no">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 edit_is_premium">
                                    <div class="form-group  ml-1">
                                        <label>{{__('label.is_premium')}}<span class="text-danger">*</span></label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_premium" id="edit_is_premium_yes" class="custom-control-input" value="1">
                                                <label class="custom-control-label" for="edit_is_premium_yes">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_premium" id="edit_is_premium_no" class="custom-control-input" value="0">
                                                <label class="custom-control-label" for="edit_is_premium_no">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 edit_order_by_upload">
                                    <div class="form-group  ml-1">
                                        <label>{{__('label.order_by_upload')}}<span class="text-danger">*</span></label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="order_by_upload" id="edit_order_by_upload_asc" class="custom-control-input" value="0">
                                                <label class="custom-control-label" for="edit_order_by_upload_asc">{{__('label.asc')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="order_by_upload" id="edit_order_by_upload_desc" class="custom-control-input" value="1">
                                                <label class="custom-control-label" for="edit_order_by_upload_desc">{{__('label.desc')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 edit_order_by_play">
                                    <div class="form-group  ml-1">
                                        <label>{{__('label.order_by_play')}}<span class="text-danger">*</span></label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="order_by_play" id="edit_order_by_play_asc" class="custom-control-input" value="0">
                                                <label class="custom-control-label" for="edit_order_by_play_asc">{{__('label.asc')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="order_by_play" id="edit_order_by_play_desc" class="custom-control-input" value="1">
                                                <label class="custom-control-label" for="edit_order_by_play_desc">{{__('label.desc')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 edit_is_paid">
                                    <div class="form-group  ml-1">
                                        <label>{{__('label.is_paid')}}<span class="text-danger">*</span></label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_paid" id="edit_is_paid_yes" class="custom-control-input" value="1">
                                                <label class="custom-control-label" for="edit_is_paid_yes">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_paid" id="edit_is_paid_no" class="custom-control-input" value="0">
                                                <label class="custom-control-label" for="edit_is_paid_no">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 edit_is_title">
                                    <div class="form-group  ml-1">
                                        <label>{{__('label.is_title')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_title" id="edit_is_title_yes" class="custom-control-input" value="1">
                                                <label class="custom-control-label" for="edit_is_title_yes">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_title" id="edit_is_title_no" class="custom-control-input" value="0">
                                                <label class="custom-control-label" for="edit_is_title_no">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 edit_is_category">
                                    <div class="form-group  ml-1">
                                        <label>{{__('label.is_category')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_category" id="edit_is_category_yes" class="custom-control-input" value="1">
                                                <label class="custom-control-label" for="edit_is_category_yes">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_category" id="edit_is_category_no" class="custom-control-input" value="0">
                                                <label class="custom-control-label" for="edit_is_category_no">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 edit_is_artist_name">
                                    <div class="form-group  ml-1">
                                        <label>{{__('label.is_artist_name')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_artist_name" id="edit_is_artist_name_yes" class="custom-control-input" value="1">
                                                <label class="custom-control-label" for="edit_is_artist_name_yes">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_artist_name" id="edit_is_artist_name_no" class="custom-control-input" value="0">
                                                <label class="custom-control-label" for="edit_is_artist_name_no">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default mw-120" onclick="update_section()">{{__('label.update')}}</button>
                            <button type="button" class="btn btn-cancel mw-120" data-dismiss="modal">{{__('label.close')}}</button>
                            <input type="hidden" name="_method" value="PATCH">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- sortableModal -->
        <div class="modal fade" id="sortableModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="sortableModallabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title w-100 text-center" id="sortableModallabel">{{__('label.section_sortable_list')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close">
                            <span aria-hidden="true" class="text-dark">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="imageListId">

                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <form enctype="multipart/form-data" id="save_section_sortable">
                            @csrf
                            <input id="outputvalues" type="hidden" name="ids" value="" />
                            <div class="w-100 text-center">
                                <button type="button" class="btn btn-default mw-120" onclick="save_section_sortable()">{{__('label.save')}}</button>
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
    function hide_artist(array, element, parent = null) {

        var options = {
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }

                const type = $(data.element).data('type');

                if (type !== undefined && array.includes(type)) {
                    return null;
                }

                return data.text;
            }
        };

        if (parent) {
            options.dropdownParent = $(parent);
        }
        $(element).select2(options);
    }

    $('#category_id').select2();
    $('#language_id').select2();
    $('#city_id').select2();
    $('#user_id').select2();
    $('#search_user').select2();
    $('#edit_category_id').select2({
        dropdownParent: $('#editsectioneModal')
    });
    $('#edit_language_id').select2({
        dropdownParent: $('#editsectioneModal')
    });
    $('#edit_city_id').select2({
        dropdownParent: $('#editsectioneModal')
    });
    $('#edit_user_id').select2({
        dropdownParent: $('#editsectioneModal')
    });

    var section_type = $('#pills-tab .nav-link.active').data("id");
    $('.nav-item a').click(function() {
        section_type = $(this).data("id");
    })
    let user_id = $("#search_user").val();
    if (section_type == 1) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{ route("section.data") }}',
            data: {
                section_type: section_type,
                user_id: user_id,

            },
            success: function(resp) {
                $('.after-add-more').html('');
                for (var i = 0; i < resp.result.length; i++) {

                    if (resp.result[i].type == 1) {
                        var type = "{{__('label.radio_station')}}";
                    } else if (resp.result[i].type == 2) {
                        var type = "{{__('label.podcast')}}";
                    } else if (resp.result[i].type == 3) {
                        var type = "{{__('label.live_event')}}";
                    } else if (resp.result[i].type == 4) {
                        var type = "{{__('label.artist')}}";
                    } else if (resp.result[i].type == 5) {
                        var type = "{{__('label.category')}}";
                    } else if (resp.result[i].type == 6) {
                        var type = "{{__('label.language')}}";
                    } else if (resp.result[i].type == 7) {
                        var type = "{{__('label.city')}}";
                    } else if (resp.result[i].type == 8) {
                        var type = "{{__('label.music')}}";
                    } else if (resp.result[i].type == 9) {
                        var type = "Continue Listening";
                    } else if (resp.result[i].type == 10) {
                        var type = "Liked Songs";
                    } else if (resp.result[i].type == 11) {
                        var type = "From Artists You Follow";
                    } else if (resp.result[i].type == 12) {
                        var type = "Based on Your Top Category";
                    } else if (resp.result[i].type == 13) {
                        var type = "New in Your Language";
                    } else if (resp.result[i].type == 14) {
                        var type = "Hidden Gems";
                    } else {
                        var type = "-";
                    }

                    if (resp.result[i].screen_layout == "landscape") {
                        var screen_layout = "{{__('label.landscape')}}";
                    } else if (resp.result[i].screen_layout == "square") {
                        var screen_layout = "{{__('label.square')}}";
                    } else if (resp.result[i].screen_layout == "small_square") {
                        var screen_layout = "{{__('label.small_square')}}";
                    } else if (resp.result[i].screen_layout == "round") {
                        var screen_layout = "{{__('label.round')}}";
                    } else {
                        var screen_layout = "-";
                    }

                    let status = resp.result[i].status;
                    let statusLabel = "";
                    if (status == 1) {
                        statusLabel = "checked";
                    }
                    var data = '<div class="card custom-border-card mt-3">' +
                        '<div class="card-header d-flex justify-content-between">' +
                        '<h5>{{__("label.edit_section")}}</h5>' +
                        '<div class="switch">' +
                        '<input type="checkbox" class="status-checkbox" data-id="' + resp.result[i].id + '" id="checkbox' + resp.result[i].id + '" ' + statusLabel + '>' +
                        '<label for="checkbox' + resp.result[i].id + '"> </label>' +
                        '<span class="toggle-text" data-on="{{__("label.show")}}" data-off="{{__("label.hide")}}"></span>' +
                        '</div>';

                    data += '</div>' +
                        '<div class="card-body">' +
                        '<div class="form-row">' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("label.title")}}</label>' +
                        '<input type="text" value="' + resp.result[i].title + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("label.sub_title")}}</label>' +
                        '<input type="text" value="' + resp.result[i].sub_title + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("label.type")}}</label>' +
                        '<input type="text" value="' + type + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("label.screen_layout")}}</label>' +
                        '<input type="text" value="' + screen_layout + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="border-top pt-3 text-right">' +
                        '<button type="button" id="pin-btn-' + resp.result[i].id + '" class="btn ' + (resp.result[i].is_pinned == 1 ? 'btn-warning' : 'btn-outline-secondary') + ' mw-120" onclick="toggle_pin(' + resp.result[i].id + ')">' + (resp.result[i].is_pinned == 1 ? '&#128204; Pinned' : '&#128204; Pin') + '</button>' +
                        '<button type="button" data-toggle="modal" data-target="#editsectioneModal" class="btn btn-default mw-120 ml-2" onclick="edit_section(' + resp.result[i].id + ')">{{__("label.update")}}</button>' +
                        '<button type="button" class="btn btn-cancel mw-120 ml-2" onclick="delete_section(' + resp.result[i].id + ')">{{__("label.delete")}}</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    $('.after-add-more').append(data);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("#dvloader").hide();
                toastr.error(errorThrown, textStatus);
            }
        });
    }

    $("#search_user").on('change', function() {
        user_id = $(this).val();
        $("#dvloader").show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{ route("section.data") }}',
            data: {
                section_type: section_type,
                user_id: user_id,

            },
            success: function(resp) {
                $("#dvloader").hide();
                $('.after-add-more').html('');
                for (var i = 0; i < resp.result.length; i++) {

                    if (resp.result[i].type == 1) {
                        var type = "{{__('label.radio_station')}}";
                    } else if (resp.result[i].type == 2) {
                        var type = "{{__('label.podcast')}}";
                    } else if (resp.result[i].type == 3) {
                        var type = "{{__('label.live_event')}}";
                    } else if (resp.result[i].type == 4) {
                        var type = "{{__('label.artist')}}";
                    } else if (resp.result[i].type == 5) {
                        var type = "{{__('label.category')}}";
                    } else if (resp.result[i].type == 6) {
                        var type = "{{__('label.language')}}";
                    } else if (resp.result[i].type == 7) {
                        var type = "{{__('label.city')}}";
                    } else if (resp.result[i].type == 8) {
                        var type = "{{__('label.music')}}";
                    } else if (resp.result[i].type == 9) {
                        var type = "Continue Listening";
                    } else if (resp.result[i].type == 10) {
                        var type = "Liked Songs";
                    } else if (resp.result[i].type == 11) {
                        var type = "From Artists You Follow";
                    } else if (resp.result[i].type == 12) {
                        var type = "Based on Your Top Category";
                    } else if (resp.result[i].type == 13) {
                        var type = "New in Your Language";
                    } else if (resp.result[i].type == 14) {
                        var type = "Hidden Gems";
                    } else {
                        var type = "-";
                    }

                    if (resp.result[i].screen_layout == "landscape") {
                        var screen_layout = "{{__('label.landscape')}}";
                    } else if (resp.result[i].screen_layout == "square") {
                        var screen_layout = "{{__('label.square')}}";
                    } else if (resp.result[i].screen_layout == "small_square") {
                        var screen_layout = "{{__('label.small_square')}}";
                    } else if (resp.result[i].screen_layout == "round") {
                        var screen_layout = "{{__('label.round')}}";
                    } else {
                        var screen_layout = "-";
                    }

                    let status = resp.result[i].status;
                    let statusLabel = "";
                    if (status == 1) {
                        statusLabel = "checked";
                    }
                    let isPinned = resp.result[i].is_pinned == 1;
                    let pinBtnClass = isPinned ? 'btn-warning' : 'btn-outline-secondary';
                    let pinBtnText = isPinned ? '&#128204; Pinned' : '&#128204; Pin';
                    var data = '<div class="card custom-border-card mt-3" id="section-card-' + resp.result[i].id + '">' +
                        '<div class="card-header d-flex justify-content-between">' +
                        '<h5>{{__("label.edit_section")}}</h5>' +
                        '<div class="switch">' +
                        '<input type="checkbox" class="status-checkbox" data-id="' + resp.result[i].id + '" id="checkbox' + resp.result[i].id + '" ' + statusLabel + '>' +
                        '<label for="checkbox' + resp.result[i].id + '"> </label>' +
                        '<span class="toggle-text" data-on="{{__("label.show")}}" data-off="{{__("label.hide")}}"></span>' +
                        '</div>';

                    data += '</div>' +
                        '<div class="card-body">' +
                        '<div class="form-row">' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("label.title")}}</label>' +
                        '<input type="text" value="' + resp.result[i].title + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("label.sub_title")}}</label>' +
                        '<input type="text" value="' + resp.result[i].sub_title + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("label.type")}}</label>' +
                        '<input type="text" value="' + type + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("label.screen_layout")}}</label>' +
                        '<input type="text" value="' + screen_layout + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="border-top pt-3 text-right">' +
                        '<button type="button" id="pin-btn-' + resp.result[i].id + '" class="btn ' + pinBtnClass + ' mw-120" onclick="toggle_pin(' + resp.result[i].id + ')">' + pinBtnText + '</button>' +
                        '<button type="button" data-toggle="modal" data-target="#editsectioneModal" class="btn btn-default mw-120 ml-2" onclick="edit_section(' + resp.result[i].id + ')">{{__("label.update")}}</button>' +
                        '<button type="button" class="btn btn-cancel mw-120 ml-2" onclick="delete_section(' + resp.result[i].id + ')">{{__("label.delete")}}</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    $('.after-add-more').append(data);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("#dvloader").hide();
                toastr.error(errorThrown, textStatus);
            }
        });
    })

    $(document).ready(function() {

        $(".screen_layout").hide();
        $(".artist_drop").hide();
        $(".category_drop").hide();
        $(".language_drop").hide();
        $(".city_drop").hide();
        $(".no_of_content").hide();
        $(".view_all").hide();
        $(".is_premium").hide();
        $(".order_by_upload").hide();
        $(".order_by_play").hide();
        $(".is_paid").hide();
        $(".is_title").hide();
        $(".is_category").hide();
        $(".is_artist_name").hide();

        $('#type').change(function() {

            var optionValue = $(this).val();

            if (optionValue == 1) {

                $(".is_paid").hide();

                $(".screen_layout").show();
                $("#screen_layout").children().removeAttr("selected");
                $("#screen_layout option[value='landscape']").show();
                $("#screen_layout option[value='square']").show();
                $("#screen_layout option[value='small_square']").show();
                $("#screen_layout option[value='round']").hide();

                $(".artist_drop").show();
                hide_artist([2, 3], '#artist_id');
                $("#artist_id").val("0").trigger("change");
                $(".category_drop").show();
                $(".language_drop").show();
                $(".city_drop").show();
                $(".no_of_content").show();
                $(".view_all").show();
                $(".order_by_upload").show();
                $(".order_by_play").show();
                $(".is_premium").show();
                $(".is_title").show();
                $(".is_category").show();
                $(".is_artist_name").show();
            } else if (optionValue == 2) {

                $(".city_drop").hide();
                $(".is_paid").hide();

                $(".artist_drop").show();
                hide_artist([1, 3], '#artist_id');
                $("#artist_id").val("0").trigger("change");
                $(".screen_layout").show();
                $("#screen_layout").children().removeAttr("selected");
                $("#screen_layout option[value='landscape']").show();
                $("#screen_layout option[value='square']").show();
                $("#screen_layout option[value='small_square']").hide();
                $("#screen_layout option[value='round']").hide();

                $(".category_drop").show();
                $(".language_drop").show();
                $(".no_of_content").show();
                $(".view_all").show();
                $(".order_by_upload").show();
                $(".order_by_play").show();
                $(".is_premium").show();
                $(".is_title").show();
                $(".is_category").show();
                $(".is_artist_name").show();

            } else if (optionValue == 3 || optionValue == 4 || optionValue == 5 || optionValue == 6 || optionValue == 7) {

                $(".artist_drop").hide();
                $(".city_drop").hide();
                $(".category_drop").hide();
                $(".language_drop").hide();
                $(".order_by_upload").hide();
                $(".order_by_play").hide();
                $(".is_premium").hide();
                $(".is_title").hide();
                $(".is_category").hide();
                $(".is_artist_name").hide();

                $(".screen_layout").show();
                $("#screen_layout").children().removeAttr("selected");
                $("#screen_layout option[value='landscape']").hide();
                $("#screen_layout option[value='small_square']").hide();
                $("#screen_layout option[value='square']").hide();
                $("#screen_layout option[value='round']").hide();

                if (optionValue == 3) {

                    $("#screen_layout option[value='landscape']").hide();
                    $("#screen_layout option[value='square']").show();
                    $("#screen_layout option[value='small_square']").hide();
                    $("#screen_layout option[value='round']").hide();

                    $(".no_of_content").show();
                    $(".view_all").show();
                    $(".is_paid").show();
                } else if (optionValue == 4) {

                    $("#screen_layout option[value='landscape']").hide();
                    $("#screen_layout option[value='square']").show();
                    $("#screen_layout option[value='small_square']").hide();
                    $("#screen_layout option[value='round']").show();

                    $(".no_of_content").show();
                    $(".order_by_upload").show();
                    $(".view_all").show();
                    $(".is_paid").hide();
                } else if (optionValue == 5) {

                    $("#screen_layout option[value='landscape']").hide();
                    $("#screen_layout option[value='square']").show();
                    $("#screen_layout option[value='small_square']").hide();
                    $("#screen_layout option[value='round']").hide();

                    $(".no_of_content").show();
                    $(".order_by_upload").show();
                    $(".view_all").show();
                    $(".is_paid").hide();
                } else if (optionValue == 6) {

                    $("#screen_layout option[value='landscape']").hide();
                    $("#screen_layout option[value='square']").show();
                    $("#screen_layout option[value='small_square']").hide();
                    $("#screen_layout option[value='round']").hide();

                    $(".no_of_content").show();
                    $(".order_by_upload").show();
                    $(".view_all").show();
                    $(".is_paid").hide();
                } else if (optionValue == 7) {

                    $("#screen_layout option[value='landscape']").hide();
                    $("#screen_layout option[value='square']").show();
                    $("#screen_layout option[value='small_square']").hide();
                    $("#screen_layout option[value='round']").hide();

                    $(".no_of_content").show();
                    $(".order_by_upload").show();
                    $(".view_all").show();
                    $(".is_paid").hide();
                }
            } else if (optionValue == 8) {

                $(".city_drop").hide();
                $(".is_paid").hide();

                $(".screen_layout").show();
                $("#screen_layout").children().removeAttr("selected");
                $("#screen_layout option[value='landscape']").show();
                $("#screen_layout option[value='square']").show();
                $("#screen_layout option[value='small_square']").show();
                $("#screen_layout option[value='round']").hide();

                $(".artist_drop").show();
                hide_artist([1, 2], '#artist_id');
                $("#artist_id").val("0").trigger("change");
                $(".category_drop").show();
                $(".language_drop").show();
                $(".no_of_content").show();
                $(".view_all").show();
                $(".order_by_upload").show();
                $(".order_by_play").show();
                $(".is_premium").show();
                $(".is_title").show();
                $(".is_category").show();
                $(".is_artist_name").show();
            } else if (optionValue == 9 || optionValue == 10 || optionValue == 11 || optionValue == 12 || optionValue == 13) {

                $(".artist_drop").hide();
                $(".category_drop").hide();
                $(".language_drop").hide();
                $(".city_drop").hide();
                $(".is_paid").hide();
                $(".is_premium").hide();
                $(".order_by_upload").hide();
                $(".order_by_play").hide();
                $(".is_title").show();
                $(".is_category").hide();
                $(".is_artist_name").hide();

                $(".screen_layout").show();
                $("#screen_layout").children().removeAttr("selected");
                $("#screen_layout option[value='landscape']").show();
                $("#screen_layout option[value='square']").show();
                $("#screen_layout option[value='small_square']").show();
                $("#screen_layout option[value='round']").hide();

                $(".no_of_content").show();
                $(".view_all").show();
            } else {

                $(".screen_layout").hide();
                $(".artist_drop").hide();
                $(".category_drop").hide();
                $(".language_drop").hide();
                $(".city_drop").hide();
                $(".no_of_content").hide();
                $(".view_all").hide();
                $(".is_premium").hide();
                $(".order_by_upload").hide();
                $(".order_by_play").hide();
                $(".is_paid").hide();
            }
        });
    });

    // section save
    function save_section() {
        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var formData = new FormData($("#section")[0]);
            formData.append('section_type', section_type);
            $.ajax({
                type: 'POST',
                url: '{{ route("section.store") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'section', '{{ route("section.index") }}');
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

    // sections list 
    function change_section(section_type) {
        $(".screen_layout").hide();
        $(".artist_drop").hide();
        $(".category_drop").hide();
        $(".language_drop").hide();
        $(".city_drop").hide();
        $(".no_of_content").hide();
        $(".view_all").hide();
        $(".is_premium").hide();
        $(".order_by_upload").hide();
        $(".order_by_play").hide();
        $(".is_paid").hide();
        $(".is_title").hide();
        $(".is_category").hide();
        $(".is_artist_name").hide();

        // All tabs show all content types — the type dropdown onChange handler controls dependent fields
        $('.type_drop').show();
        $("#type").children().removeAttr("selected");
        $("#type option").show();

        $("#dvloader").show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{ route("section.data") }}',
            data: {
                section_type: section_type,
                user_id: user_id

            },
            success: function(resp) {
                $("#dvloader").hide();
                $('.after-add-more').html('');
                for (var i = 0; i < resp.result.length; i++) {

                    if (resp.result[i].type == 1) {
                        var type = "{{__('label.radio_station')}}";
                    } else if (resp.result[i].type == 2) {
                        var type = "{{__('label.podcast')}}";
                    } else if (resp.result[i].type == 3) {
                        var type = "{{__('label.live_event')}}";
                    } else if (resp.result[i].type == 4) {
                        var type = "{{__('label.artist')}}";
                    } else if (resp.result[i].type == 5) {
                        var type = "{{__('label.category')}}";
                    } else if (resp.result[i].type == 6) {
                        var type = "{{__('label.language')}}";
                    } else if (resp.result[i].type == 7) {
                        var type = "{{__('label.city')}}";
                    } else if (resp.result[i].type == 8) {
                        var type = "{{__('label.music')}}";
                    } else if (resp.result[i].type == 9) {
                        var type = "Continue Listening";
                    } else if (resp.result[i].type == 10) {
                        var type = "Liked Songs";
                    } else if (resp.result[i].type == 11) {
                        var type = "From Artists You Follow";
                    } else if (resp.result[i].type == 12) {
                        var type = "Based on Your Top Category";
                    } else if (resp.result[i].type == 13) {
                        var type = "New in Your Language";
                    } else if (resp.result[i].type == 14) {
                        var type = "Hidden Gems";
                    } else {
                        var type = "-";
                    }

                    if (resp.result[i].screen_layout == "landscape") {
                        var screen_layout = "{{__('label.landscape')}}";
                    } else if (resp.result[i].screen_layout == "square") {
                        var screen_layout = "{{__('label.square')}}";
                    } else if (resp.result[i].screen_layout == "small_square") {
                        var screen_layout = "{{__('label.small_square')}}";
                    } else if (resp.result[i].screen_layout == "round") {
                        var screen_layout = "{{__('label.round')}}";
                    } else {
                        var screen_layout = "-";
                    }

                    let status = resp.result[i].status;
                    let statusLabel = "";
                    if (status == 1) {
                        statusLabel = "checked";
                    }
                    let isPinnedC = resp.result[i].is_pinned == 1;
                    let pinBtnClassC = isPinnedC ? 'btn-warning' : 'btn-outline-secondary';
                    let pinBtnTextC = isPinnedC ? '&#128204; Pinned' : '&#128204; Pin';
                    var data = '<div class="card custom-border-card mt-3" id="section-card-' + resp.result[i].id + '">' +
                        '<div class="card-header d-flex justify-content-between">' +
                        '<h5>{{__("label.edit_section")}}</h5>' +
                        '<div class="switch">' +
                        '<input type="checkbox" class="status-checkbox" data-id="' + resp.result[i].id + '" id="checkbox' + resp.result[i].id + '" ' + statusLabel + '>' +
                        '<label for="checkbox' + resp.result[i].id + '"> </label>' +
                        '<span class="toggle-text" data-on="{{__("label.show")}}" data-off="{{__("label.hide")}}"></span>' +
                        '</div>';

                    data += '</div>' +
                        '<div class="card-body">' +
                        '<div class="form-row">' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("label.title")}}</label>' +
                        '<input type="text" value="' + resp.result[i].title + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("label.sub_title")}}</label>' +
                        '<input type="text" value="' + resp.result[i].sub_title + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("label.type")}}</label>' +
                        '<input type="text" value="' + type + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("label.screen_layout")}}</label>' +
                        '<input type="text" value="' + screen_layout + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="border-top pt-3 text-right">' +
                        '<button type="button" id="pin-btn-' + resp.result[i].id + '" class="btn ' + pinBtnClassC + ' mw-120" onclick="toggle_pin(' + resp.result[i].id + ')">' + pinBtnTextC + '</button>' +
                        '<button type="button" data-toggle="modal" data-target="#editsectioneModal" class="btn btn-default mw-120 ml-2" onclick="edit_section(' + resp.result[i].id + ')">{{__("label.update")}}</button>' +
                        '<button type="button" class="btn btn-cancel mw-120 ml-2" onclick="delete_section(' + resp.result[i].id + ')">{{__("label.delete")}}</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    $('.after-add-more').append(data);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("#dvloader").hide();
                toastr.error(errorThrown, textStatus);
            }
        });

    }

    // Update Section
    function edit_section(id) {

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{ route("section.edit") }}',
            data: {
                id: id,
            },
            success: function(resp) {
                if (resp.result != null) {

                    $("#edit_id").val(resp.result.id);
                    $("#edit_title").val(resp.result.title);
                    $("#edit_sub_title").val(resp.result.sub_title);
                    $("#edit_type").val(resp.result.type).attr("selected", "selected");
                    $("#edit_screen_layout").val(resp.result.screen_layout).attr("selected", "selected");
                    $('#edit_artist_id').val(resp.result.artist_id).trigger('change');
                    $('#edit_category_id').val(resp.result.category_id).trigger('change');
                    $('#edit_language_id').val(resp.result.language_id).trigger('change');
                    $('#edit_city_id').val(resp.result.city_id).trigger('change');
                    $("#edit_no_of_content").val(resp.result.no_of_content);
                    $('#edit_user_id').val(resp.result.user_id).trigger('change');

                    $("#edit_order_by_upload_asc").prop('checked', false);
                    $("#edit_order_by_upload_desc").prop('checked', false);
                    if (resp.result.order_by_upload == 0) {
                        $("#edit_order_by_upload_asc").prop('checked', true);
                    } else {
                        $("#edit_order_by_upload_desc").prop('checked', true);
                    }

                    $("#edit_order_by_play_asc").prop('checked', false);
                    $("#edit_order_by_play_desc").prop('checked', false);
                    if (resp.result.order_by_play == 0) {
                        $("#edit_order_by_play_asc").prop('checked', true);
                    } else {
                        $("#edit_order_by_play_desc").prop('checked', true);
                    }

                    $("#edit_is_premium_no").prop('checked', false);
                    $("#edit_is_premium_yes").prop('checked', false);
                    if (resp.result.is_premium == 0) {
                        $("#edit_is_premium_no").prop('checked', true);
                    } else {
                        $("#edit_is_premium_yes").prop('checked', true);
                    }

                    $("#edit_is_paid_yes").prop('checked', false);
                    $("#edit_is_paid_no").prop('checked', false);
                    if (resp.result.is_paid == 0) {
                        $("#edit_is_paid_no").prop('checked', true);
                    } else {
                        $("#edit_is_paid_yes").prop('checked', true);
                    }

                    $("#edit_view_all_yes").prop('checked', false);
                    $("#edit_view_all_no").prop('checked', false);
                    if (resp.result.view_all == 1) {
                        $("#edit_view_all_yes").prop('checked', true);
                    } else {
                        $("#edit_view_all_no").prop('checked', true);
                    }

                    $("#edit_is_title_yes").prop('checked', false);
                    $("#edit_is_title_no").prop('checked', false);
                    if (resp.result.is_title == 1) {
                        $("#edit_is_title_yes").prop('checked', true);
                    } else {
                        $("#edit_is_title_no").prop('checked', true);
                    }

                    $("#edit_is_category_yes").prop('checked', false);
                    $("#edit_is_category_no").prop('checked', false);
                    if (resp.result.is_category == 1) {
                        $("#edit_is_category_yes").prop('checked', true);
                    } else {
                        $("#edit_is_category_no").prop('checked', true);
                    }

                    $("#edit_is_artist_name_yes").prop('checked', false);
                    $("#edit_is_artist_name_no").prop('checked', false);
                    if (resp.result.is_artist_name == 1) {
                        $("#edit_is_artist_name_yes").prop('checked', true);
                    } else {
                        $("#edit_is_artist_name_no").prop('checked', true);
                    }
                }

                $(".edit_screen_layout").hide();
                $(".edit_artist_drop").hide();
                $(".edit_category_drop").hide();
                $(".edit_language_drop").hide();
                $(".edit_city_drop").hide();
                $(".edit_no_of_content").hide();
                $(".edit_view_all").hide();
                $(".edit_is_premium").hide();
                $(".edit_order_by_upload").hide();
                $(".edit_order_by_play").hide();
                $(".edit_is_paid").hide();
                $(".edit_is_title").hide();
                $(".edit_is_category").hide();
                $(".edit_is_artist_name").hide();

                // All tabs show all content types in edit modal too
                $(".edit_type_drop").show();
                $("#edit_type option").show();

                if (resp.result.type == 1) {

                    $(".edit_screen_layout").show();
                    $("#edit_screen_layout option[value='landscape']").show();
                    $("#edit_screen_layout option[value='square']").show();
                    $("#edit_screen_layout option[value='small_square']").show();
                    $("#edit_screen_layout option[value='round']").hide();

                    $(".edit_artist_drop").show();
                    hide_artist([2, 3], '#edit_artist_id', '#editsectioneModal');
                    $(".edit_category_drop").show();
                    $(".edit_language_drop").show();
                    $(".edit_city_drop").show();
                    $(".edit_no_of_content").show();
                    $(".edit_view_all").show();
                    $(".edit_order_by_upload").show();
                    $(".edit_order_by_play").show();
                    $(".edit_is_premium").show();
                    $(".edit_is_title").show();
                    $(".edit_is_category").show();
                    $(".edit_is_artist_name").show();
                } else if (resp.result.type == 2) {

                    $(".edit_screen_layout").show();
                    $("#edit_screen_layout option[value='landscape']").show();
                    $("#edit_screen_layout option[value='square']").show();
                    $("#edit_screen_layout option[value='small_square']").hide();
                    $("#edit_screen_layout option[value='round']").hide();

                    $(".edit_artist_drop").show();
                    hide_artist([1, 3], '#edit_artist_id', '#editsectioneModal');
                    $(".edit_category_drop").show();
                    $(".edit_language_drop").show();
                    $(".edit_no_of_content").show();
                    getMessage
                    $(".edit_view_all").show();
                    $(".edit_is_premium").show();
                    $(".edit_order_by_upload").show();
                    $(".edit_order_by_play").show();
                    $(".edit_is_title").show();
                    $(".edit_is_category").show();
                    $(".edit_is_artist_name").show();
                } else if (resp.result.type == 3 || resp.result.type == 4 || resp.result.type == 5 || resp.result.type == 6 || resp.result.type == 7) {

                    $(".edit_screen_layout").show();
                    $("#edit_screen_layout option[value='landscape']").hide();
                    $("#edit_screen_layout option[value='square']").show();
                    $("#edit_screen_layout option[value='small_square']").hide();
                    $("#edit_screen_layout option[value='round']").hide();
                    $(".edit_is_title").hide();
                    $(".edit_is_category").hide();
                    $(".edit_is_artist_name").hide();

                    $(".edit_no_of_content").show();
                    $(".edit_view_all").show();
                    if (resp.result.type == 3) {
                        $(".edit_is_paid").show();
                    } else if (resp.result.type == 4) {
                        $(".edit_order_by_upload").show();
                        $("#edit_screen_layout option[value='round']").show();
                    } else if (resp.result.type == 5) {
                        $(".edit_order_by_upload").show();
                    } else if (resp.result.type == 6) {
                        $(".edit_order_by_upload").show();
                    } else if (resp.result.type == 7) {
                        $(".edit_order_by_upload").show();
                    }
                } else if (resp.result.type == 8) {

                    $(".edit_screen_layout").show();
                    $("#edit_screen_layout option[value='landscape']").show();
                    $("#edit_screen_layout option[value='square']").show();
                    $("#edit_screen_layout option[value='small_square']").show();
                    $("#edit_screen_layout option[value='round']").hide();
                    $(".edit_artist_drop").show();
                    hide_artist([1, 2], '#edit_artist_id', '#editsectioneModal');
                    $(".edit_category_drop").show();
                    $(".edit_language_drop").show();
                    $(".edit_no_of_content").show();
                    $(".edit_view_all").show();
                    $(".edit_is_premium").show();
                    $(".edit_order_by_upload").show();
                    $(".edit_order_by_play").show();
                    $(".edit_is_title").show();
                    $(".edit_is_category").show();
                    $(".edit_is_artist_name").show();
                } else if (resp.result.type == 9 || resp.result.type == 10 || resp.result.type == 11 || resp.result.type == 12 || resp.result.type == 13 || resp.result.type == 14) {

                    $(".edit_screen_layout").show();
                    $("#edit_screen_layout option[value='landscape']").show();
                    $("#edit_screen_layout option[value='square']").show();
                    $("#edit_screen_layout option[value='small_square']").show();
                    $("#edit_screen_layout option[value='round']").hide();
                    $(".edit_no_of_content").show();
                    $(".edit_view_all").show();
                    $(".edit_is_title").show();
                }

                $('#edit_type').change(function() {

                    var type = $(this).val();
                    if (type == 1) {

                        $(".edit_is_paid").hide();

                        $(".edit_screen_layout").show();
                        $("#edit_screen_layout").children().removeAttr("selected");
                        $("#edit_screen_layout option[value='landscape']").show();
                        $("#edit_screen_layout option[value='square']").show();
                        $("#edit_screen_layout option[value='small_square']").show();
                        $("#edit_screen_layout option[value='round']").hide();

                        $(".edit_artist_drop").show();
                        hide_artist([2, 3], '#edit_artist_id', '#editsectioneModal');
                        $("#edit_artist_id").val("0").trigger("change");
                        $(".edit_category_drop").show();
                        $(".edit_language_drop").show();
                        $(".edit_city_drop").show();
                        $(".edit_no_of_content").show();
                        $(".edit_view_all").show();
                        $(".edit_order_by_upload").show();
                        $(".edit_order_by_play").show();
                        $(".edit_is_premium").show();
                        $(".edit_is_title").show();
                        $(".edit_is_category").show();
                        $(".edit_is_artist_name").show();
                    } else if (type == 2) {

                        $(".edit_city_drop").hide();
                        $(".edit_is_paid").hide();

                        $(".edit_artist_drop").show();
                        hide_artist([1, 3], '#edit_artist_id', '#editsectioneModal');
                        $("#edit_artist_id").val("0").trigger("change");
                        $(".edit_screen_layout").show();
                        $("#edit_screen_layout").children().removeAttr("selected");
                        $("#edit_screen_layout option[value='landscape']").show();
                        $("#edit_screen_layout option[value='square']").show();
                        $("#edit_screen_layout option[value='small_square']").hide();
                        $("#edit_screen_layout option[value='round']").hide();

                        $(".edit_category_drop").show();
                        $(".edit_language_drop").show();
                        $(".edit_no_of_content").show();
                        $(".edit_view_all").show();
                        $(".edit_order_by_upload").show();
                        $(".edit_order_by_play").show();
                        $(".edit_is_premium").show();
                        $(".edit_is_title").show();
                        $(".edit_is_category").show();
                        $(".edit_is_artist_name").show();
                    } else if (type == 3 || type == 4 || type == 5 || type == 6 || type == 7) {

                        $(".edit_artist_drop").hide();
                        $(".edit_city_drop").hide();
                        $(".edit_category_drop").hide();
                        $(".edit_language_drop").hide();
                        $(".edit_order_by_upload").hide();
                        $(".edit_order_by_play").hide();
                        $(".edit_is_premium").hide();
                        $(".edit_is_title").hide();
                        $(".edit_is_category").hide();
                        $(".edit_is_artist_name").hide();

                        $(".edit_screen_layout").show();
                        $("#edit_screen_layout").children().removeAttr("selected");
                        $("#edit_screen_layout option[value='landscape']").hide();
                        $("#edit_screen_layout option[value='square']").show();
                        $("#edit_screen_layout option[value='small_square']").hide();
                        $("#edit_screen_layout option[value='round']").hide();
                        $(".edit_no_of_content").show();
                        $(".edit_view_all").show();
                        $(".edit_order_by_upload").show();
                        $(".edit_is_paid").hide();
                        if (type == 3) {
                            $(".edit_order_by_upload").hide();
                            $(".edit_is_paid").show();
                        } else if (type == 4) {
                            $("#edit_screen_layout option[value='round']").show();

                        }

                    } else if (type == 8) {

                        $(".edit_city_drop").hide();
                        $(".edit_is_paid").hide();

                        $(".edit_screen_layout").show();
                        $("#edit_screen_layout").children().removeAttr("selected");
                        $("#edit_screen_layout option[value='landscape']").show();
                        $("#edit_screen_layout option[value='square']").show();
                        $("#edit_screen_layout option[value='small_square']").show();
                        $("#edit_screen_layout option[value='round']").hide();

                        $(".edit_artist_drop").show();
                        hide_artist([1, 2], '#edit_artist_id', '#editsectioneModal');
                        $("#edit_artist_id").val("0").trigger("change");
                        $(".edit_category_drop").show();
                        $(".edit_language_drop").show();
                        $(".edit_no_of_content").show();
                        $(".edit_view_all").show();
                        $(".edit_order_by_upload").show();
                        $(".edit_order_by_play").show();
                        $(".edit_is_premium").show();
                        $(".edit_is_title").show();
                        $(".edit_is_category").show();
                        $(".edit_is_artist_name").show();
                    } else if (type == 9 || type == 10 || type == 11 || type == 12 || type == 13 || type == 14) {

                        $(".edit_artist_drop").hide();
                        $(".edit_category_drop").hide();
                        $(".edit_language_drop").hide();
                        $(".edit_city_drop").hide();
                        $(".edit_is_paid").hide();
                        $(".edit_is_premium").hide();
                        $(".edit_order_by_upload").hide();
                        $(".edit_order_by_play").hide();
                        $(".edit_is_title").show();
                        $(".edit_is_category").hide();
                        $(".edit_is_artist_name").hide();

                        $(".edit_screen_layout").show();
                        $("#edit_screen_layout").children().removeAttr("selected");
                        $("#edit_screen_layout option[value='landscape']").show();
                        $("#edit_screen_layout option[value='square']").show();
                        $("#edit_screen_layout option[value='small_square']").show();
                        $("#edit_screen_layout option[value='round']").hide();

                        $(".edit_no_of_content").show();
                        $(".edit_view_all").show();
                    } else {

                        $(".edit_screen_layout").hide();
                        $(".edit_artist_drop").hide();
                        $(".edit_category_drop").hide();
                        $(".edit_language_drop").hide();
                        $(".edit_city_drop").hide();
                        $(".edit_no_of_content").hide();
                        $(".edit_view_all").hide();
                        $(".edit_is_premium").hide();
                        $(".edit_order_by_upload").hide();
                        $(".edit_order_by_play").hide();
                        $(".edit_is_paid").hide();
                        $(".edit_is_title").hide();
                        $(".edit_is_category").hide();
                        $(".edit_is_artist_name").hide();
                    }
                });
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("#dvloader").hide();
                toastr.error(errorThrown, textStatus);
            }
        });
    }

    function update_section() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var id = $('#edit_id').val();
            var formData = new FormData($("#edit_content_section")[0]);

            var url = '{{ route("section.update", ":id") }}';
            url = url.replace(':id', id);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                enctype: 'multipart/form-data',
                type: 'POST',
                url: url,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {

                    $("#dvloader").hide();
                    if (resp.status == 200) {
                        $('#editsectioneModal').modal('toggle');
                    }
                    get_responce_message(resp, 'edit_content_section', '{{ route("section.index") }}');
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

    // Delete Section
    function delete_section(id) {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            var result = confirm("{{__('label.delete_section')}}");
            if (result) {

                $("#dvloader").show();

                var url = '{{ route("section.show", ":id") }}';
                url = url.replace(':id', id);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'GET',
                    url: url,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, '', '{{ route("section.index") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            }
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    // Change Status
    function change_status(id) {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{route('section.status')}}",
                data: {
                    id: id
                },
                success: function(resp) {
                    $("#dvloader").hide();
                    if (resp.status == 200) {
                        toastr.success(resp.success);
                    } else {
                        toastr.error(resp.errors);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    };

    $(document).on('change', '.status-checkbox', function() {
        id = $(this).data('id');
        change_status(id);
    })

    function toggle_pin(id) {
        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin != 1) {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
            return;
        }
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: '{{route("section.pin")}}',
            data: { id: id },
            success: function(resp) {
                if (resp.status == 200) {
                    var btn = $('#pin-btn-' + id);
                    if (resp.is_pinned == 1) {
                        btn.removeClass('btn-outline-secondary').addClass('btn-warning').html('&#128204; Pinned');
                        toastr.success('Section pinned — it will always appear at the top.');
                    } else {
                        btn.removeClass('btn-warning').addClass('btn-outline-secondary').html('&#128204; Pin');
                        toastr.success('Section unpinned.');
                    }
                } else {
                    toastr.error(resp.errors);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                toastr.error(errorThrown, textStatus);
            }
        });
    }

    // Sortable Section
    $("#imageListId").sortable({
        update: function(event, ui) {
            getIdsOfImages();
        } //end update
    });

    function getIdsOfImages() {
        var values = [];
        $('.listitemClass').each(function(index) {
            values.push($(this).attr("id")
                .replace("imageNo", ""));
        });
        $('#outputvalues').val(values);
    }

    // Sortable section get data 
    function sortableBTN() {
        $("#dvloader").show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{ route("section.sortable") }}',
            data: {
                section_type: section_type,
                user_id: user_id,
            },
            success: function(resp) {
                $("#dvloader").hide();
                $('#imageListId').html('');
                for (var i = 0; i < resp.result.length; i++) {

                    var data = '<div id="' + resp.result[i].id + '" class="listitemClass mb-2">' +
                        '<p class="m-2">' + resp.result[i].title + '</p>' +
                        '</div>';

                    $('#imageListId').append(data);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("#dvloader").hide();
                toastr.error(errorThrown, textStatus);
            }
        });
    }

    // Sortable save
    function save_section_sortable() {
        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {
            $("#dvloader").show();
            var formData = new FormData($("#save_section_sortable")[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route("section.sortable.save") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'save_section_sortable', '{{ route("section.index") }}');
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