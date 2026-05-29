@extends('admin.layout.page-app')
@section('page_title', __('Label.section'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm"> {{__('Label.section')}} </h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-11">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('Label.section')}}</li>
                    </ol>
                </div>
                <div class="col-sm-1 d-flex justify-content-start mb-3" title="{{__('Label.sortable')}}">
                    <button type="button" data-toggle="modal" data-target="#sortableModal" onclick="sortableBTN()" class="btn btn-default" style="border-radius: 10px;">
                        <i class="fa-solid fa-sort fa-1x"></i>
                    </button>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('Label.add_section')}}</h5>
                <div class="card-body">
                    <form id="section" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="">
                        <div class="form-row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('Label.Title')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="{{__('Label.enter_section_title')}}" autofocus>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('Label.sub_title')}}</label>
                                    <input type="text" name="sub_title" class="form-control" placeholder="{{__('Label.enter_section_sub_title')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('Label.Type')}}<span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" id="type">
                                        <option value="">{{__('Label.select_type')}}</option>
                                        <option value="1">{{__('Label.radio_station')}}</option>
                                        <option value="2">{{__('Label.poadcast')}}</option>
                                        <option value="3">{{__('Label.live_event')}}</option>
                                        <option value="4">{{__('Label.Artist')}}</option>
                                        <option value="5">{{__('Label.Category')}}</option>
                                        <option value="6">{{__('Label.Language')}}</option>
                                        <option value="7">{{__('Label.City')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 screen_layout">
                                <div class="form-group">
                                    <label>{{__('Label.screen_layout')}}<span class="text-danger">*</span></label>
                                    <select name="screen_layout" class="form-control" id="screen_layout">
                                        <option value="">{{__('Label.select_screen_layout')}}</option>
                                        <option value="landscape">{{__('Label.landscape')}}</option>
                                        <option value="portrait">{{__('Label.portrait')}}</option>
                                        <option value="sqaure">{{__('Label.sqaure')}}</option>
                                        <option value="live_event">{{__('Label.live_event')}}</option>
                                        <option value="artist">{{__('Label.Artist')}}</option>
                                        <option value="category">{{__('Label.Category')}}</option>
                                        <option value="language">{{__('Label.Language')}}</option>
                                        <option value="city">{{__('Label.City')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 artist_drop">
                                <div class="form-group ">
                                    <label>{{__('Label.Artist')}}<span class="text-danger">*</span></label>
                                    <select class="form-control" style="width:100%!important;" name="artist_id" id="artist_id">
                                        <option value="0">{{__('Label.all_artist')}}</option>
                                        @foreach ($artist as $key => $value)
                                        <option value="{{ $value->id}}">
                                            {{ $value->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 category_drop">
                                <div class="form-group ">
                                    <label>{{__('Label.Category')}}<span class="text-danger">*</span></label>
                                    <select class="form-control" style="width:100%!important;" name="category_id" id="category_id">
                                        <option value="0">{{__('Label.all_category')}}</option>
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
                                    <label>{{__('Label.Language')}}<span class="text-danger">*</span></label>
                                    <select class="form-control" style="width:100%!important;" name="language_id" id="language_id">
                                        <option value="0">{{__('Label.all_language')}}</option>
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
                                    <label>{{__('Label.City')}}<span class="text-danger">*</span></label>
                                    <select class="form-control" style="width:100%!important;" name="city_id" id="city_id">
                                        <option value="0">{{__('Label.all_city')}}</option>
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
                                    <label>{{__('Label.no_of_content')}}<span class="text-danger">*</span></label>
                                    <input type="number" name="no_of_content" class="form-control" placeholder="{{__('Label.enter_no_of_content')}}">
                                </div>
                            </div>
                            <div class="col-md-2 view_all">
                                <div class="form-group ml-1">
                                    <label>{{__('Label.view_all')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="view_all" id="view_all_yes" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="view_all_yes">{{__('Label.Yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="view_all" id="view_all_no" class="custom-control-input" value="0" checked>
                                            <label class="custom-control-label" for="view_all_no">{{__('Label.No')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 is_premium">
                                <div class="form-group  ml-1">
                                    <label>{{__('Label.is_premium')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_premium" id="is_premium_yes" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="is_premium_yes">{{__('Label.Yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_premium" id="is_premium_no" class="custom-control-input" value="0" checked>
                                            <label class="custom-control-label" for="is_premium_no">{{__('Label.No')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 order_by_upload">
                                <div class="form-group  ml-1">
                                    <label>{{__('Label.order_by_upload')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="order_by_upload" id="order_by_upload_asc" class="custom-control-input" value="0">
                                            <label class="custom-control-label" for="order_by_upload_asc">{{__('Label.asc')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="order_by_upload" id="order_by_upload_desc" class="custom-control-input" value="1" checked>
                                            <label class="custom-control-label" for="order_by_upload_desc">{{__('Label.desc')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 order_by_play">
                                <div class="form-group  ml-3">
                                    <label>{{__('Label.order_by_play')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="order_by_play" id="order_by_play_asc" class="custom-control-input" value="0">
                                            <label class="custom-control-label" for="order_by_play_asc">{{__('Label.asc')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="order_by_play" id="order_by_play_desc" class="custom-control-input" value="1" checked>
                                            <label class="custom-control-label" for="order_by_play_desc">{{__('Label.desc')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 is_paid">
                                <div class="form-group  ml-4">
                                    <label>{{__('Label.is_paid')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_paid" id="is_paid_yes" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="is_paid_yes">{{__('Label.Yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_paid" id="is_paid_no" class="custom-control-input" value="0" checked>
                                            <label class="custom-control-label" for="is_paid_no">{{__('Label.No')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="save_section()">{{__('Label.SAVE')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>
            <div class="after-add-more"></div>

            <!-- edit section -->
            <div class="modal fade" id="editsectioneModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="editsectioneModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editsectioneModalLabel">{{__('Label.edit_section')}}</h5>
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
                                            <label>{{__('Label.Title')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="title" id="edit_title" class="form-control" placeholder="{{__('Label.enter_section_title')}}" autofocus>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('Label.sub_title')}}</label>
                                            <input type="text" name="sub_title" id="edit_sub_title" class="form-control" placeholder="{{__('Label.enter_section_sub_title')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('Label.Type')}}<span class="text-danger">*</span></label>
                                            <select name="type" class="form-control" id="edit_type">
                                            <option value="1">{{__('Label.radio_station')}}</option>
                                            <option value="2">{{__('Label.poadcast')}}</option>
                                            <option value="3">{{__('Label.live_event')}}</option>
                                            <option value="4">{{__('Label.Artist')}}</option>
                                            <option value="5">{{__('Label.Category')}}</option>
                                            <option value="6">{{__('Label.Language')}}</option>
                                            <option value="7">{{__('Label.City')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_screen_layout">
                                        <div class="form-group">
                                            <label>{{__('Label.screen_layout')}}<span class="text-danger">*</span></label>
                                            <select name="screen_layout" class="form-control" id="edit_screen_layout">
                                                <option value="">{{__('Label.select_screen_layout')}}</option>
                                                <option value="landscape">{{__('Label.landscape')}}</option>
                                                <option value="portrait">{{__('Label.portrait')}}</option>
                                                <option value="sqaure">{{__('Label.sqaure')}}</option>
                                                <option value="live_event">{{__('Label.live_event')}}</option>
                                                <option value="artist">{{__('Label.Artist')}}</option>
                                                <option value="category">{{__('Label.Category')}}</option>
                                                <option value="language">{{__('Label.Language')}}</option>
                                                <option value="city">{{__('Label.City')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_artist_drop">
                                        <div class="form-group ">
                                            <label>{{__('Label.Artist')}}<span class="text-danger">*</span></label>
                                            <select class="form-control" style="width:100%!important;" name="artist_id" id="edit_artist_id">
                                                <option value="0">{{__('Label.all_artist')}}</option>
                                                @foreach ($artist as $key => $value)
                                                <option value="{{ $value->id}}">
                                                    {{ $value->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_category_drop">
                                        <div class="form-group ">
                                            <label>{{__('Label.Category')}}<span class="text-danger">*</span></label>
                                            <select class="form-control" style="width:100%!important;" name="category_id" id="edit_category_id">
                                                <option value="0">{{__('Label.all_category')}}</option>
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
                                            <label>{{__('Label.Language')}}<span class="text-danger">*</span></label>
                                            <select class="form-control" style="width:100%!important;" name="language_id" id="edit_language_id">
                                                <option value="0">{{__('Label.all_language')}}</option>
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
                                            <label>{{__('Label.City')}}<span class="text-danger">*</span></label>
                                            <select class="form-control" style="width:100%!important;" name="city_id" id="edit_city_id">
                                                <option value="0">{{__('Label.all_city')}}</option>
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
                                            <label>{{__('Label.no_of_content')}}<span class="text-danger">*</span></label>
                                            <input type="number" name="no_of_content" id="edit_no_of_content" class="form-control" placeholder="{{__('Label.enter_no_of_content')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 edit_view_all">
                                        <div class="form-group ml-1">
                                            <label>{{__('Label.view_all')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="view_all" id="edit_view_all_yes" class="custom-control-input" value="1">
                                                    <label class="custom-control-label" for="edit_view_all_yes">{{__('Label.Yes')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="view_all" id="edit_view_all_no" class="custom-control-input" value="0">
                                                    <label class="custom-control-label" for="edit_view_all_no">{{__('Label.No')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 edit_is_premium">
                                        <div class="form-group  ml-1">
                                            <label>{{__('Label.is_premium')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="is_premium" id="edit_is_premium_yes" class="custom-control-input" value="1">
                                                    <label class="custom-control-label" for="edit_is_premium_yes">{{__('Label.Yes')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="is_premium" id="edit_is_premium_no" class="custom-control-input" value="0">
                                                    <label class="custom-control-label" for="edit_is_premium_no">{{__('Label.No')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 edit_order_by_upload">
                                        <div class="form-group  ml-1">
                                            <label>{{__('Label.order_by_upload')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_upload" id="edit_order_by_upload_asc" class="custom-control-input" value="0">
                                                    <label class="custom-control-label" for="edit_order_by_upload_asc">{{__('Label.asc')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_upload" id="edit_order_by_upload_desc" class="custom-control-input" value="1">
                                                    <label class="custom-control-label" for="edit_order_by_upload_desc">{{__('Label.desc')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 edit_order_by_play">
                                        <div class="form-group  ml-1">
                                            <label>{{__('Label.order_by_play')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_play" id="edit_order_by_play_asc" class="custom-control-input" value="0">
                                                    <label class="custom-control-label" for="edit_order_by_play_asc">{{__('Label.asc')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_play" id="edit_order_by_play_desc" class="custom-control-input" value="1">
                                                    <label class="custom-control-label" for="edit_order_by_play_desc">{{__('Label.desc')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 edit_is_paid">
                                        <div class="form-group  ml-1">
                                            <label>{{__('Label.is_paid')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="is_paid" id="edit_is_paid_yes" class="custom-control-input" value="1">
                                                    <label class="custom-control-label" for="edit_is_paid_yes">{{__('Label.Yes')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="is_paid" id="edit_is_paid_no" class="custom-control-input" value="0">
                                                    <label class="custom-control-label" for="edit_is_paid_no">{{__('Label.No')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default mw-120" onclick="update_section()">{{__('Label.UPDATE')}}</button>
                                <button type="button" class="btn btn-cancel mw-120" data-dismiss="modal">{{__('Label.CLOSE')}}</button>
                                <input type="hidden" name="_method" value="PATCH">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- sortableModal -->
            <div class="modal fade" id="sortableModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="sortableModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title w-100 text-center" id="sortableModalLabel">{{__('Label.section_sortable_list')}}</h5>
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
                                    <button type="button" class="btn btn-default mw-120" onclick="save_section_sortable()">{{__('Label.SAVE')}}</button>
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
        $('#artist_id').select2();
        $('#category_id').select2();
        $('#language_id').select2();
        $('#city_id').select2();
        $('#edit_artist_id').select2({
            dropdownParent: $('#editsectioneModal') 
        });
        $('#edit_category_id').select2({
            dropdownParent: $('#editsectioneModal') 
        });
        $('#edit_language_id').select2({
            dropdownParent: $('#editsectioneModal') 
        });
        $('#edit_city_id').select2({
            dropdownParent: $('#editsectioneModal') 
        });

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


            $('#type').change(function() {

                var optionValue = $(this).val();

                if (optionValue == 1) {

                    $(".is_paid").hide();

                    $(".screen_layout").show();
                    $("#screen_layout").children().removeAttr("selected");
                    $("#screen_layout option[value='landscape']").show();
                    $("#screen_layout option[value='portrait']").show();
                    $("#screen_layout option[value='sqaure']").show();
                    $("#screen_layout option[value='live_event']").hide();
                    $("#screen_layout option[value='artist']").hide();
                    $("#screen_layout option[value='category']").hide();
                    $("#screen_layout option[value='language']").hide();
                    $("#screen_layout option[value='city']").hide();

                    $(".artist_drop").show();
                    $(".category_drop").show();
                    $(".language_drop").show();
                    $(".city_drop").show();
                    $(".no_of_content").show();
                    $(".view_all").show();
                    $(".order_by_upload").show();
                    $(".order_by_play").show();
                    $(".is_premium").show();
                } else if (optionValue == 2){

                    $(".artist_drop").hide();
                    $(".city_drop").hide();
                    $(".is_paid").hide();

                    $(".screen_layout").show();
                    $("#screen_layout").children().removeAttr("selected");
                    $("#screen_layout option[value='landscape']").show();
                    $("#screen_layout option[value='portrait']").show();
                    $("#screen_layout option[value='sqaure']").show();
                    $("#screen_layout option[value='live_event']").hide();
                    $("#screen_layout option[value='artist']").hide();
                    $("#screen_layout option[value='category']").hide();
                    $("#screen_layout option[value='language']").hide();
                    $("#screen_layout option[value='city']").hide();

                    $(".category_drop").show();
                    $(".language_drop").show();
                    $(".no_of_content").show();
                    $(".view_all").show();
                    $(".order_by_upload").show();
                    $(".order_by_play").show();
                    $(".is_premium").show();
                } else if (optionValue == 3 || optionValue == 4 || optionValue == 5 || optionValue == 6 || optionValue == 7){

                    $(".artist_drop").hide();
                    $(".city_drop").hide();
                    $(".category_drop").hide();
                    $(".language_drop").hide();
                    $(".order_by_upload").hide();
                    $(".order_by_play").hide();
                    $(".is_premium").hide();

                    $(".screen_layout").show();
                    $("#screen_layout").children().removeAttr("selected");
                    $("#screen_layout option[value='landscape']").hide();
                    $("#screen_layout option[value='portrait']").hide();
                    $("#screen_layout option[value='sqaure']").hide();
                    
                    if(optionValue == 3){

                        $("#screen_layout option[value='live_event']").show();
                        $("#screen_layout option[value='artist']").hide();
                        $("#screen_layout option[value='category']").hide();
                        $("#screen_layout option[value='language']").hide();
                        $("#screen_layout option[value='city']").hide();

                        $(".no_of_content").show();
                        $(".view_all").show();
                        $(".is_paid").show();  
                    } else if (optionValue == 4){

                        $("#screen_layout option[value='live_event']").hide();
                        $("#screen_layout option[value='artist']").show();
                        $("#screen_layout option[value='category']").hide();
                        $("#screen_layout option[value='language']").hide();
                        $("#screen_layout option[value='city']").hide();

                        $(".no_of_content").show();
                        $(".order_by_upload").show();
                        $(".view_all").show();
                        $(".is_paid").hide();  
                    } else if (optionValue == 5){

                        $("#screen_layout option[value='live_event']").hide();
                        $("#screen_layout option[value='artist']").hide();
                        $("#screen_layout option[value='category']").show();
                        $("#screen_layout option[value='language']").hide();
                        $("#screen_layout option[value='city']").hide();

                        $(".no_of_content").show();
                        $(".order_by_upload").show();
                        $(".view_all").show();
                        $(".is_paid").hide();  
                    } else if (optionValue == 6){

                        $("#screen_layout option[value='live_event']").hide();
                        $("#screen_layout option[value='artist']").hide();
                        $("#screen_layout option[value='category']").hide();
                        $("#screen_layout option[value='language']").show();
                        $("#screen_layout option[value='city']").hide();

                        $(".no_of_content").show();
                        $(".order_by_upload").show();
                        $(".view_all").show();
                        $(".is_paid").hide();  
                    } else if (optionValue == 7){

                        $("#screen_layout option[value='live_event']").hide();
                        $("#screen_layout option[value='artist']").hide();
                        $("#screen_layout option[value='category']").hide();
                        $("#screen_layout option[value='language']").hide();
                        $("#screen_layout option[value='city']").show();

                        $(".no_of_content").show();
                        $(".order_by_upload").show();
                        $(".view_all").show();
                        $(".is_paid").hide();  
                    }
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
                toastr.error('{{__("Label.you_have_no_right_to_add_edit_and_delete")}}');
            }
        }

         // sections list 
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{ route("section.data") }}',
            data: {

            },
            success: function(resp) {
                $('.after-add-more').html('');
                for (var i = 0; i < resp.result.length; i++) {

                    if (resp.result[i].type == 1) {
                        var type = "{{__('Label.radio_station')}}";
                    } else if (resp.result[i].type == 2) {
                        var type = "{{__('Label.poadcast')}}";
                    } else if (resp.result[i].type == 3) {
                        var type = "{{__('Label.live_event')}}";
                    } else if (resp.result[i].type == 4) {
                        var type = "{{__('Label.Artist')}}";
                    } else if (resp.result[i].type == 5) {
                        var type = "{{__('Label.Category')}}";
                    } else if (resp.result[i].type == 6) {
                        var type = "{{__('Label.Language')}}";
                    } else if (resp.result[i].type == 7) {
                        var type = "{{__('Label.City')}}";
                    }  else {
                        var type = "-";
                    }

                    if (resp.result[i].screen_layout == "landscape") {
                        var screen_layout = "{{__('Label.landscape')}}";
                    } else if (resp.result[i].screen_layout == "portrait") {
                        var screen_layout = "{{__('Label.portrait')}}";
                    } else if (resp.result[i].screen_layout == "sqaure") {
                        var screen_layout = "{{__('Label.sqaure')}}";
                    }  else if (resp.result[i].screen_layout == "live_event") {
                        var screen_layout = "{{__('Label.live_event')}}";
                    } else if (resp.result[i].screen_layout == "artist") {
                        var screen_layout = "{{__('Label.Artist')}}";
                    } else if (resp.result[i].screen_layout == "category") {
                        var screen_layout = "{{__('Label.Category')}}";
                    } else if (resp.result[i].screen_layout == "language") {
                        var screen_layout = "{{__('Label.Language')}}";
                    } else if (resp.result[i].screen_layout == "city") {
                        var screen_layout = "{{__('Label.City')}}";
                    } else {
                        var screen_layout = "-";
                    }

                    var data = '<div class="card custom-border-card mt-3">' +
                        '<div class="card-header d-flex justify-content-between">' +
                        '<h5>{{__("Label.edit_section")}}</h5>';
                    if (resp.result[i].status == 1) {
                        data += '<button class="btn" id="' + resp.result[i].id + '" onclick="change_status(' + resp.result[i].id + ')" style="background:#058f00; font-weight:bold; border: none; color: white;">{{__("Label.show")}}</button>';
                    } else {
                        data += '<button class="btn" id="' + resp.result[i].id + '" onclick="change_status(' + resp.result[i].id + ')" style="background:#e3000b; font-weight:bold; border: none; color: white;">{{__("Label.hide")}}</button>';
                    }
                    data += '</div>' +
                        '<div class="card-body">' +
                        '<div class="form-row">' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("Label.Title")}}</label>' +
                        '<input type="text" value="' + resp.result[i].title + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("Label.sub_title")}}</label>' +
                        '<input type="text" value="' + resp.result[i].sub_title + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("Label.Type")}}</label>' +
                        '<input type="text" value="' + type + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label>{{__("Label.screen_layout")}}</label>' +
                        '<input type="text" value="' + screen_layout + '" class="form-control" readonly>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="border-top pt-3 text-right">' +
                        '<button type="button" data-toggle="modal" data-target="#editsectioneModal" class="btn btn-default mw-120" onclick="edit_section(' + resp.result[i].id + ')">{{__("Label.UPDATE")}}</button>' +
                        '<button type="button" class="btn btn-cancel mw-120 ml-2" onclick="delete_section(' + resp.result[i].id + ')">{{__("Label.Delete")}}</button>' +
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

                    if (resp.result.type == 1) {

                        $(".edit_screen_layout").show();
                        $("#edit_screen_layout option[value='landscape']").show();
                        $("#edit_screen_layout option[value='portrait']").show();
                        $("#edit_screen_layout option[value='sqaure']").show();
                        $("#edit_screen_layout option[value='live_event']").hide();
                        $("#edit_screen_layout option[value='artist']").hide();
                        $("#edit_screen_layout option[value='category']").hide();
                        $("#edit_screen_layout option[value='language']").hide();
                        $("#edit_screen_layout option[value='city']").hide();

                        $(".edit_artist_drop").show();
                        $(".edit_category_drop").show();
                        $(".edit_language_drop").show();
                        $(".edit_city_drop").show();
                        $(".edit_no_of_content").show();
                        $(".edit_view_all").show();
                        $(".edit_order_by_upload").show();
                        $(".edit_order_by_play").show();
                        $(".edit_is_premium").show();
                    } else if (resp.result.type == 2) {

                        $(".edit_screen_layout").show();
                        $("#edit_screen_layout option[value='landscape']").show();
                        $("#edit_screen_layout option[value='portrait']").show();
                        $("#edit_screen_layout option[value='sqaure']").show();
                        $("#edit_screen_layout option[value='live_event']").hide();
                        $("#edit_screen_layout option[value='artist']").hide();
                        $("#edit_screen_layout option[value='category']").hide();
                        $("#edit_screen_layout option[value='language']").hide();
                        $("#edit_screen_layout option[value='city']").hide();

                        $(".edit_category_drop").show();
                        $(".edit_language_drop").show();
                        $(".edit_no_of_content").show();
                        $(".edit_view_all").show();
                        $(".edit_is_premium").show();
                        $(".edit_order_by_upload").show();
                        $(".edit_order_by_play").show();
                    } else if (resp.result.type == 3 || resp.result.type == 4 || resp.result.type == 5 || resp.result.type == 6 || resp.result.type == 7) {

                        $(".edit_screen_layout").show();
                        $("#edit_screen_layout option[value='landscape']").hide();
                        $("#edit_screen_layout option[value='portrait']").hide();
                        $("#edit_screen_layout option[value='sqaure']").hide();
                        $("#edit_screen_layout option[value='live_event']").hide();
                        $("#edit_screen_layout option[value='artist']").hide();
                        $("#edit_screen_layout option[value='category']").hide();
                        $("#edit_screen_layout option[value='language']").hide();
                        $("#edit_screen_layout option[value='city']").hide();

                        $(".edit_no_of_content").show();
                        $(".edit_view_all").show();
                        if(resp.result.type == 3){
                            $(".edit_is_paid").show();
                            $("#edit_screen_layout option[value='live_event']").show();

                        } else if(resp.result.type == 4){
                            $(".edit_order_by_upload").show();
                            $("#edit_screen_layout option[value='artist']").show();
                        } else if(resp.result.type == 5){
                            $(".edit_order_by_upload").show();
                            $("#edit_screen_layout option[value='category']").show();
                        } else if(resp.result.type == 6){
                            $(".edit_order_by_upload").show();
                            $("#edit_screen_layout option[value='language']").show();
                        } else if(resp.result.type == 7){
                            $(".edit_order_by_upload").show();
                            $("#edit_screen_layout option[value='city']").show();
                        }
                    }
                    
                    $('#edit_type').change(function() {

                        var type = $(this).val();
                        if (type == 1) {

                            $(".edit_is_paid").hide();

                            $(".edit_screen_layout").show();
                            $("#edit_screen_layout").children().removeAttr("selected");
                            $("#edit_screen_layout option[value='landscape']").show();
                            $("#edit_screen_layout option[value='portrait']").show();
                            $("#edit_screen_layout option[value='sqaure']").show();
                            $("#edit_screen_layout option[value='live_event']").hide();
                            $("#edit_screen_layout option[value='artist']").hide();
                            $("#edit_screen_layout option[value='category']").hide();
                            $("#edit_screen_layout option[value='language']").hide();
                            $("#edit_screen_layout option[value='city']").hide();

                            $(".edit_artist_drop").show();
                            $(".edit_category_drop").show();
                            $(".edit_language_drop").show();
                            $(".edit_city_drop").show();
                            $(".edit_no_of_content").show();
                            $(".edit_view_all").show();
                            $(".edit_order_by_upload").show();
                            $(".edit_order_by_play").show();
                            $(".edit_is_premium").show();
                        } else if (type == 2){

                            $(".edit_artist_drop").hide();
                            $(".edit_city_drop").hide();
                            $(".edit_is_paid").hide();

                            $(".edit_screen_layout").show();
                            $("#edit_screen_layout").children().removeAttr("selected");
                            $("#edit_screen_layout option[value='landscape']").show();
                            $("#edit_screen_layout option[value='portrait']").show();
                            $("#edit_screen_layout option[value='sqaure']").show();
                            $("#edit_screen_layout option[value='live_event']").hide();
                            $("#edit_screen_layout option[value='artist']").hide();
                            $("#edit_screen_layout option[value='category']").hide();
                            $("#edit_screen_layout option[value='language']").hide();
                            $("#edit_screen_layout option[value='city']").hide();

                            $(".edit_category_drop").show();
                            $(".edit_language_drop").show();
                            $(".edit_no_of_content").show();
                            $(".edit_view_all").show();
                            $(".edit_order_by_upload").show();
                            $(".edit_order_by_play").show();
                            $(".edit_is_premium").show();
                        } else if (type == 3 || type == 4 || type == 5 || type == 6 || type == 7){

                            $(".edit_artist_drop").hide();
                            $(".edit_city_drop").hide();
                            $(".edit_category_drop").hide();
                            $(".edit_language_drop").hide();
                            $(".edit_order_by_upload").hide();
                            $(".edit_order_by_play").hide();
                            $(".edit_is_premium").hide();

                            $(".edit_screen_layout").show();
                            $("#edit_screen_layout").children().removeAttr("selected");
                            $("#edit_screen_layout option[value='live_event']").show();
                            $("#edit_screen_layout option[value='landscape']").hide();
                            $("#edit_screen_layout option[value='portrait']").hide();
                            $("#edit_screen_layout option[value='sqaure']").hide();
                            $("#edit_screen_layout option[value='artist']").hide();
                            $("#edit_screen_layout option[value='category']").hide();
                            $("#edit_screen_layout option[value='language']").hide();
                            $("#edit_screen_layout option[value='city']").hide();

                            if(type == 3) {

                                $("#edit_screen_layout option[value='live_event']").show();
                                $("#edit_screen_layout option[value='artist']").hide();
                                $("#edit_screen_layout option[value='category']").hide();
                                $("#edit_screen_layout option[value='language']").hide();
                                $("#edit_screen_layout option[value='city']").hide();

                                $(".edit_no_of_content").show();
                                $(".edit_view_all").show();
                                $(".edit_is_paid").show();  
                            } else if (type == 4) {

                                $("#edit_screen_layout option[value='live_event']").hide();
                                $("#edit_screen_layout option[value='artist']").show();
                                $("#edit_screen_layout option[value='category']").hide();
                                $("#edit_screen_layout option[value='language']").hide();
                                $("#edit_screen_layout option[value='city']").hide();

                                $(".edit_no_of_content").show();
                                $(".edit_view_all").show();
                                $(".edit_order_by_upload").show();
                                $(".edit_is_paid").hide();  
                            } else if (type == 5) {

                                $("#edit_screen_layout option[value='live_event']").hide();
                                $("#edit_screen_layout option[value='artist']").hide();
                                $("#edit_screen_layout option[value='category']").show();
                                $("#edit_screen_layout option[value='language']").hide();
                                $("#edit_screen_layout option[value='city']").hide();

                                $(".edit_no_of_content").show();
                                $(".edit_view_all").show();
                                $(".edit_order_by_upload").show();
                                $(".edit_is_paid").hide();  
                            } else if (type == 6) {

                                $("#edit_screen_layout option[value='live_event']").hide();
                                $("#edit_screen_layout option[value='artist']").hide();
                                $("#edit_screen_layout option[value='category']").hide();
                                $("#edit_screen_layout option[value='language']").show();
                                $("#edit_screen_layout option[value='city']").hide();

                                $(".edit_no_of_content").show();
                                $(".edit_view_all").show();
                                $(".edit_order_by_upload").show();
                                $(".edit_is_paid").hide();  
                            } else if (type == 7) {

                                $("#edit_screen_layout option[value='live_event']").hide();
                                $("#edit_screen_layout option[value='artist']").hide();
                                $("#edit_screen_layout option[value='category']").hide();
                                $("#edit_screen_layout option[value='language']").show();
                                $("#edit_screen_layout option[value='city']").hide();

                                $(".edit_no_of_content").show();
                                $(".edit_view_all").show();
                                $(".edit_order_by_upload").show();
                                $(".edit_is_paid").hide();  
                            }
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
                toastr.error('{{__("Label.you_have_no_right_to_add_edit_and_delete")}}');
            }
        }

         // Delete Section
         function delete_section(id) {

            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if (Check_Admin == 1) {

                var result = confirm('{{__("Label.are_you_sure_you_want_to_delete_this_section")}}');
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
                toastr.error('{{__("Label.you_have_no_right_to_add_edit_and_delete")}}');
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

                            if (resp.Status == 1) {
                                $('#' + id).text('Show');
                                $('#' + id).css({
                                    "background": "#058f00",
                                    "color": "white",
                                    "font-weight": "bold",
                                    "border": "none"
                                });
                            } else {
                                $('#' + id).text('Hide');
                                $('#' + id).css({
                                    "background": "#e3000b",
                                    "color": "white",
                                    "font-weight": "bold",
                                    "border": "none"
                                });
                            }
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
                toastr.error('{{__("Label.you_have_no_right_to_add_edit_and_delete")}}');
            }
        };

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
                success: function(resp) {
                    $("#dvloader").hide();
                    $('#imageListId').html('');
                    for (var i = 0; i < resp.result.length; i++) {

                        var data = '<div id="' + resp.result[i].id + '" class="listitemClass mb-2" style="background-color: #e9ecef;border: 1px solid black; cursor: s-resize;">' +
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
                toastr.error('{{__("Label.you_have_no_right_to_add_edit_and_delete")}}');
            }
        }
    </script>
@endsection