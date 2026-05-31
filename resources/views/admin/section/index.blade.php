@extends('admin.layout.page-app')
@section('page_title', __('label.section'))
@section('tab_title', __('label.section'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <!-- Select2 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.section')}}</h1>

            <div class="border-bottom row">
                <div class="col-sm-11">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.section')}}</li>
                    </ol>
                </div>
                <div class="col-sm-1 d-flex justify-content-start mb-3">
                    <button type="button" data-toggle="modal" data-target="#sortOrderModal" onclick="sortOrderBTN()" class="btn btn-default" style="border-radius: 10px;">
                        <i class="fa-solid fa-arrow-up-wide-short fa-1x"></i>
                    </button>
                </div>
            </div>

            <ul class="tabs nav nav-pills custom-tabs inline-tabs " id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="app-tab" onclick="Top_Content('1', '0')" data-is_home_screen="1" data-content_type="0" data-toggle="pill" href="#app" role="tab" aria-controls="home" aria-selected="true">{{__('label.home')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="music-tab" onclick="Top_Content('2', '1')" data-is_home_screen="2" data-content_type="1" data-toggle="pill" href="#music" role="tab" aria-controls="music" aria-selected="true">{{__('label.music')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="radio-tab" onclick="Top_Content('2', '3')" data-is_home_screen="2" data-content_type="3" data-toggle="pill" href="#radio" role="tab" aria-controls="radio" aria-selected="true">{{__('label.radio')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="podcasts-tab" onclick="Top_Content('2', '2')" data-is_home_screen="2" data-content_type="2" data-toggle="pill" href="#podcasts" role="tab" aria-controls="podcasts" aria-selected="true">{{__('label.podcasts')}}</a>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="app" role="tabpanel" aria-labelledby="app-tab">
                    <div class="card custom-border-card">
                        <h5 class="card-header">{{__('label.add_section')}}</h5>
                        <div class="card-body">
                            <form id="save_section" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="">
                                <input type="hidden" name="is_home_screen" id="is_home_screen" value="">
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="{{__('label.title_here')}}" autofocus>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{__('label.short_title')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="short_title" class="form-control" placeholder="{{__('label.short_title_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 content_type_drop">
                                        <div class="form-group">
                                            <label>{{__('label.content_type')}}<span class="text-danger">*</span></label>
                                            <select name="content_type" class="form-control" id="content_type">
                                                <option value="">{{__('label.select_type')}}</option>
                                                <option value="1">{{__('label.music')}}</option>
                                                <option value="2">{{__('label.podcasts')}}</option>
                                                <option value="3">{{__('label.radio')}}</option>
                                                <option value="4">{{__('label.playlist')}}</option>
                                                <option value="5">{{__('label.category')}}</option>
                                                <option value="6">{{__('label.language')}}</option>
                                                <option value="7">{{__('label.artist')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 screen_layout_drop">
                                        <div class="form-group">
                                            <label>{{__('label.screen_layout')}}<span class="text-danger">*</span></label>
                                            <select name="screen_layout" class="form-control" id="screen_layout">
                                                <option value="">{{__('label.select_screen_layout')}}</option>
                                                <option value="list_view">{{__('label.list_view')}}</option>
                                                <option value="portrait">{{__('label.portrait')}}</option>
                                                <option value="landscape">{{__('label.landscape')}}</option>
                                                <option value="square">{{__('label.square')}}</option>
                                                <option value="playlist">{{__('label.playlist')}}</option>
                                                <option value="category">{{__('label.category')}}</option>
                                                <option value="language">{{__('label.language')}}</option>
                                                <option value="round">{{__('label.round')}}</option>
                                                <option value="banner_view">{{__('label.banner_view')}}</option>
                                                <option value="podcast_list_view">{{__('label.podcast_list_view')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-3 category_drop">
                                        <div class="form-group">
                                            <label>{{__('label.category')}}<span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-control" id="category_id">
                                                <option value="0">{{__('label.all_category')}}</option>
                                                @for ($i = 0; $i < count($category); $i++) 
                                                <option value="{{ $category[$i]['id'] }}">
                                                    {{ $category[$i]['name'] }}
                                                </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 language_drop">
                                        <div class="form-group">
                                            <label>{{__('label.language')}}<span class="text-danger">*</span></label>
                                            <select name="language_id" class="form-control" id="language_id">
                                                <option value="0">{{__('label.all_language')}}</option>
                                                @for ($i = 0; $i < count($language); $i++) 
                                                <option value="{{ $language[$i]['id'] }}">
                                                    {{ $language[$i]['name'] }}
                                                </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 no_of_content_drop">
                                        <div class="form-group">
                                            <label>{{__('label.no_of_content')}}<span class="text-danger">*</span></label>
                                            <input type="number" name="no_of_content" min="1" value="1" class="form-control" placeholder="{{__('label.no_of_content_here')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-3 order_by_upload_drop">
                                        <div class="form-group">
                                            <label>{{__('label.order_by_upload')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_upload" id="order_by_upload_asc" class="custom-control-input" value="1">
                                                    <label class="custom-control-label" for="order_by_upload_asc">{{__('label.asc')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_upload" id="order_by_upload_desc" class="custom-control-input" value="2" checked>
                                                    <label class="custom-control-label" for="order_by_upload_desc">{{__('label.desc')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 order_by_view_drop">
                                        <div class="form-group">
                                            <label>{{__('label.order_by_view')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_view" id="order_by_view_asc" class="custom-control-input" value="1">
                                                    <label class="custom-control-label" for="order_by_view_asc">{{__('label.asc')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_view" id="order_by_view_desc" class="custom-control-input" value="2" checked>
                                                    <label class="custom-control-label" for="order_by_view_desc">{{__('label.desc')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 order_by_like_drop">
                                        <div class="form-group">
                                            <label>{{__('label.order_by_like')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_like" id="order_by_like_asc" class="custom-control-input" value="1">
                                                    <label class="custom-control-label" for="order_by_like_asc">{{__('label.asc')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_like" id="order_by_like_desc" class="custom-control-input" value="2" checked>
                                                    <label class="custom-control-label" for="order_by_like_desc">{{__('label.desc')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 view_all_drop">
                                        <div class="form-group">
                                            <label>{{__('label.view_all')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="view_all" id="view_all_yes" class="custom-control-input" value="1" checked>
                                                    <label class="custom-control-label" for="view_all_yes">{{__('label.yes')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="view_all" id="view_all_no" class="custom-control-input" value="0">
                                                    <label class="custom-control-label" for="view_all_no">{{__('label.no')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="save_section(Is_home_screen, Content_type)">{{__('label.save')}}</button>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="after-add-more"></div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="updateModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateModalLabel">{{__('label.edit_section')}}</h5>
                            <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="edit_section" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="id" id="edit_id" value="">
                                <input type="hidden" name="is_home_screen" id="edit_is_home_screen" value="">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="title" id="edit_title" class="form-control" placeholder="{{__('label.title_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.short_title')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="short_title" id="edit_short_title" class="form-control" placeholder="{{__('label.short_title_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_content_type_drop">
                                        <div class="form-group">
                                            <label>{{__('label.content_type')}}<span class="text-danger">*</span></label>
                                            <select name="content_type" class="form-control" id="edit_content_type">
                                                <option value="">{{__('label.select_type')}}</option>
                                                <option value="1">{{__('label.music')}}</option>
                                                <option value="2">{{__('label.podcasts')}}</option>
                                                <option value="3">{{__('label.radio')}}</option>
                                                <option value="4">{{__('label.playlist')}}</option>
                                                <option value="5">{{__('label.category')}}</option>
                                                <option value="6">{{__('label.language')}}</option>
                                                <option value="7">{{__('label.artist')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_screen_layout_drop">
                                        <div class="form-group">
                                            <label>{{__('label.screen_layout')}}<span class="text-danger">*</span></label>
                                            <select name="screen_layout" class="form-control" id="edit_screen_layout">
                                                <option value="">{{__('label.select_screen_layout')}}</option>
                                                <option value="list_view">{{__('label.list_view')}}</option>
                                                <option value="portrait">{{__('label.portrait')}}</option>
                                                <option value="square">{{__('label.square')}}</option>
                                                <option value="playlist">{{__('label.playlist')}}</option>
                                                <option value="category">{{__('label.category')}}</option>
                                                <option value="language">{{__('label.language')}}</option>
                                                <option value="round">{{__('label.round')}}</option>
                                                <option value="banner_view">{{__('label.banner_view')}}</option>
                                                <option value="landscape">{{__('label.landscape')}}</option>
                                                <option value="podcast_list_view">{{__('label.podcast_list_view')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_category_drop">
                                        <div class="form-group">
                                            <label>{{__('label.category')}}<span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-control" id="edit_category_id" style="width:100%!important;">
                                                <option value="0">{{__('label.all_category')}}</option>
                                                @for ($i = 0; $i < count($category); $i++) 
                                                <option value="{{ $category[$i]['id'] }}">
                                                    {{ $category[$i]['name'] }}
                                                </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_language_drop">
                                        <div class="form-group">
                                            <label>{{__('label.language')}}<span class="text-danger">*</span></label>
                                            <select name="language_id" class="form-control" id="edit_language_id" style="width:100%!important;">
                                                <option value="0">{{__('label.all_language')}}</option>
                                                @for ($i = 0; $i < count($language); $i++) 
                                                <option value="{{ $language[$i]['id'] }}">
                                                    {{ $language[$i]['name'] }}
                                                </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_no_of_content_drop">
                                        <div class="form-group">
                                            <label>{{__('label.no_of_content')}}<span class="text-danger">*</span></label>
                                            <input type="number" name="no_of_content" min="1" id="edit_no_of_content" class="form-control" placeholder="{{__('label.no_of_content_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_order_by_upload_drop">
                                        <div class="form-group">
                                            <label>{{__('label.order_by_upload')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_upload" id="edit_order_by_upload_asc" class="custom-control-input" value="1" checked>
                                                    <label class="custom-control-label" for="edit_order_by_upload_asc">{{__('label.asc')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_upload" id="edit_order_by_upload_desc" class="custom-control-input" value="2">
                                                    <label class="custom-control-label" for="edit_order_by_upload_desc">{{__('label.desc')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_order_by_view_drop">
                                        <div class="form-group">
                                            <label>{{__('label.order_by_view')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_view" id="edit_order_by_view_asc" class="custom-control-input" value="1" checked>
                                                    <label class="custom-control-label" for="edit_order_by_view_asc">{{__('label.asc')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_view" id="edit_order_by_view_desc" class="custom-control-input" value="2">
                                                    <label class="custom-control-label" for="edit_order_by_view_desc">{{__('label.desc')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_order_by_like_drop">
                                        <div class="form-group">
                                            <label>{{__('label.order_by_like')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_like" id="edit_order_by_like_asc" class="custom-control-input" value="1" checked>
                                                    <label class="custom-control-label" for="edit_order_by_like_asc">{{__('label.asc')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="order_by_like" id="edit_order_by_like_desc" class="custom-control-input" value="2">
                                                    <label class="custom-control-label" for="edit_order_by_like_desc">{{__('label.desc')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_view_all_drop">
                                        <div class="form-group">
                                            <label>{{__('label.view_all')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="view_all" id="edit_view_all_yes" class="custom-control-input" value="1" checked>
                                                    <label class="custom-control-label" for="edit_view_all_yes">{{__('label.yes')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="view_all" id="edit_view_all_no" class="custom-control-input" value="0">
                                                    <label class="custom-control-label" for="edit_view_all_no">{{__('label.no')}}</label>
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

            <!-- sortOrder Modal -->
            <div class="modal fade" id="sortOrderModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="sortOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title w-100 text-center" id="sortOrderModalLabel">{{__('label.section_sort_order_list')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close">
                                <span aria-hidden="true" class="text-dark">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="contentListId">
                                
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <form enctype="multipart/form-data" id="save_section_sortorder">
                                @csrf
                                <input id="outputvalues" type="hidden" name="ids" value="" />
                                <div class="w-100 text-center">
                                    <button type="button" class="btn btn-default mw-120" onclick="save_section_sortorder()">{{__('label.save')}}</button>
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
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Sort Order -->
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

    <script>
        $("#language_id").select2();
        $("#category_id").select2();
        $("#edit_language_id").select2();
        $("#edit_category_id").select2();

        $(".category_drop").hide();
        $(".language_drop").hide();
        $(".no_of_content_drop").hide();
        $(".order_by_upload_drop").hide();
        $(".order_by_view_drop").hide();
        $(".order_by_like_drop").hide();
        $(".view_all_drop").hide();
        $(".screen_layout_drop").hide();

        var Tab = $("ul.tabs li a.active");
        var Is_home_screen = Tab.data("is_home_screen");
        var Content_type = 0;
        $("#is_home_screen").val(Is_home_screen);

        $("#content_type").change(function() {

            var content_type = $(this).children("option:selected").val();
            if(content_type == 1 || content_type == 2){

                $(".category_drop").show();
                $(".language_drop").show();
                $(".no_of_content_drop").show();
                $(".order_by_upload_drop").show();
                $(".order_by_view_drop").show();
                $(".order_by_like_drop").show();
                $(".view_all_drop").show();

                $(".screen_layout_drop").show();
                $("#screen_layout").children().removeAttr("selected");
                $("#screen_layout option[value='playlist']").hide();
                $("#screen_layout option[value='round']").hide();
                $("#screen_layout option[value='category']").hide();
                $("#screen_layout option[value='language']").hide();
                if(content_type == 1){
                    $("#screen_layout option[value='list_view']").show();
                    $("#screen_layout option[value='portrait']").show();
                    $("#screen_layout option[value='square']").show();
                    $("#screen_layout option[value='banner_view']").hide();
                    $("#screen_layout option[value='landscape']").hide();
                    $("#screen_layout option[value='podcast_list_view']").hide();
                } else {
                    $("#screen_layout option[value='list_view']").hide();
                    $("#screen_layout option[value='portrait']").hide();
                    $("#screen_layout option[value='square']").hide();
                    $("#screen_layout option[value='banner_view']").show();
                    $("#screen_layout option[value='landscape']").show();
                    $("#screen_layout option[value='podcast_list_view']").show();
                }
            } else if(content_type == 3){

                $(".category_drop").hide();
                $(".language_drop").hide();
                $(".no_of_content_drop").show();
                $(".order_by_upload_drop").show();
                $(".order_by_view_drop").hide();
                $(".order_by_like_drop").hide();
                $(".view_all_drop").show();

                $(".screen_layout_drop").show();
                $("#screen_layout").children().removeAttr("selected");
                $("#screen_layout option[value='list_view']").hide();
                $("#screen_layout option[value='portrait']").hide();
                $("#screen_layout option[value='square']").show();
                $("#screen_layout option[value='playlist']").hide();
                $("#screen_layout option[value='category']").hide();
                $("#screen_layout option[value='language']").hide();
                $("#screen_layout option[value='round']").show();
                $("#screen_layout option[value='banner_view']").hide();
                $("#screen_layout option[value='landscape']").hide();
                $("#screen_layout option[value='podcast_list_view']").hide();

            } else if(content_type == 4){

                $(".category_drop").hide();
                $(".language_drop").hide();
                $(".no_of_content_drop").show();
                $(".order_by_upload_drop").show();
                $(".order_by_view_drop").hide();
                $(".order_by_like_drop").hide();
                $(".view_all_drop").show();

                $(".screen_layout_drop").show();
                $("#screen_layout").children().removeAttr("selected");
                $("#screen_layout option[value='list_view']").hide();
                $("#screen_layout option[value='portrait']").hide();
                $("#screen_layout option[value='square']").hide();
                $("#screen_layout option[value='playlist']").show();
                $("#screen_layout option[value='category']").hide();
                $("#screen_layout option[value='language']").hide();
                $("#screen_layout option[value='round']").hide();
                $("#screen_layout option[value='banner_view']").hide();
                $("#screen_layout option[value='landscape']").hide();
                $("#screen_layout option[value='podcast_list_view']").hide();
            } else if(content_type == 5 || content_type == 6){

                $(".category_drop").hide();
                $(".language_drop").hide();
                $(".no_of_content_drop").hide();
                $(".order_by_upload_drop").hide();
                $(".order_by_view_drop").hide();
                $(".order_by_like_drop").hide();
                $(".view_all_drop").hide();

                $(".screen_layout_drop").show();
                $("#screen_layout").children().removeAttr("selected");
                $("#screen_layout option[value='list_view']").hide();
                $("#screen_layout option[value='portrait']").hide();
                $("#screen_layout option[value='square']").hide();
                $("#screen_layout option[value='playlist']").hide();
                if(content_type == 5){
                    $("#screen_layout option[value='category']").show();
                    $("#screen_layout option[value='language']").hide();
                } else {
                    $("#screen_layout option[value='category']").hide();
                    $("#screen_layout option[value='language']").show();
                }
                $("#screen_layout option[value='round']").hide();
                $("#screen_layout option[value='banner_view']").hide();
                $("#screen_layout option[value='landscape']").hide();
                $("#screen_layout option[value='podcast_list_view']").hide();
            } else if(content_type == 7){

                $(".category_drop").hide();
                $(".language_drop").hide();
                $(".no_of_content_drop").hide();
                $(".order_by_upload_drop").hide();
                $(".order_by_view_drop").hide();
                $(".order_by_like_drop").hide();
                $(".view_all_drop").hide();

                $(".screen_layout_drop").show();
                $("#screen_layout").children().removeAttr("selected");
                $("#screen_layout option[value='list_view']").hide();
                $("#screen_layout option[value='portrait']").hide();
                $("#screen_layout option[value='square']").hide();
                $("#screen_layout option[value='playlist']").hide();
                $("#screen_layout option[value='category']").hide();
                $("#screen_layout option[value='language']").hide();
                $("#screen_layout option[value='round']").show();
                $("#screen_layout option[value='banner_view']").hide();
                $("#screen_layout option[value='landscape']").hide();
                $("#screen_layout option[value='podcast_list_view']").hide();
            } else {

                $(".category_drop").hide();
                $(".language_drop").hide();
                $(".no_of_content_drop").hide();
                $(".order_by_upload_drop").hide();
                $(".order_by_view_drop").hide();
                $(".order_by_like_drop").hide();
                $(".view_all_drop").hide();
                $(".screen_layout_drop").hide();
            }
        });

        // Save Section
        function save_section(is_home_screen, content_type){

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                $("#dvloader").show();
                var formData = new FormData($("#save_section")[0]);
                formData.append('is_home_screen', is_home_screen);
                if(is_home_screen == 2){

                    var ContentType = content_type;
                    if(content_type == 1){
                        var ContentType = $('#content_type').find(":selected").val();
                    }
                    formData.append('content_type', ContentType);
                }

                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.section.store") }}',
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_section', '{{ route("admin.section.index") }}');
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

        // List Section
        if(Is_home_screen == 1) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route("admin.section.content.data") }}',
                data: {
                    is_home_screen: Is_home_screen,
                },
                success: function(resp) {
                    $('.after-add-more').html('');
                    for (var i = 0; i < resp.result.length; i++) {

                        if (resp.result[i].content_type == 1) {
                            var content_type = "{{ __('label.music') }}";
                        } else if (resp.result[i].content_type == 2) {
                            var content_type = "{{ __('label.podcasts') }}";
                        } else if (resp.result[i].content_type == 3) {
                            var content_type = "{{ __('label.radio') }}";
                        } else if (resp.result[i].content_type == 4) {
                            var content_type = "{{ __('label.playlist') }}";
                        } else if (resp.result[i].content_type == 5) {
                            var content_type = "{{ __('label.category') }}";
                        } else if (resp.result[i].content_type == 6) {
                            var content_type = "{{ __('label.language') }}";
                        } else if (resp.result[i].content_type == 7) {
                            var content_type = "{{ __('label.artist') }}";
                        }

                        if (resp.result[i].screen_layout == "list_view") {
                            var screen_layout = "{{ __('label.list_view') }}";"List View";
                        } else if (resp.result[i].screen_layout == "portrait") {
                            var screen_layout = "{{ __('label.portrait') }}";
                        } else if (resp.result[i].screen_layout == "square") {
                            var screen_layout = "{{ __('label.square') }}";
                        } else if (resp.result[i].screen_layout == "playlist") {
                            var screen_layout = "{{ __('label.playlist') }}";
                        } else if (resp.result[i].screen_layout == "category") {
                            var screen_layout = "{{ __('label.category') }}";
                        } else if (resp.result[i].screen_layout == "language") {
                            var screen_layout = "{{ __('label.language') }}";
                        } else if (resp.result[i].screen_layout == "round") {
                            var screen_layout = "{{ __('label.round') }}";
                        } else if (resp.result[i].screen_layout == "banner_view") {
                            var screen_layout = "{{ __('label.banner_view') }}";
                        } else if (resp.result[i].screen_layout == "landscape") {
                            var screen_layout = "{{ __('label.landscape') }}";
                        } else if (resp.result[i].screen_layout == "podcast_list_view") {
                            var screen_layout = "{{ __('label.podcast_list_view') }}";
                        }

                        var data = '<div class="card custom-border-card mt-3">'+
                                '<div class="card-header d-flex justify-content-between">'+
                                    '<div>'+
                                        '<h5 class="d-inline">{{__("label.edit_section")}}</h5>'+
                                        '<button class="btn btn-sm ml-2" id="pin_'+resp.result[i].id+'" onclick="toggle_pin('+resp.result[i].id+')" style="'+(resp.result[i].is_fixed == 1 ? 'background:#ffc107;color:#000;' : 'background:transparent;border:1px solid #ccc;color:#999;')+'">📌</button>'+
                                    '</div>';
                                    if(resp.result[i].status == 1){
                                        data += '<button class="btn show-btn" id="'+resp.result[i].id+'" onclick="change_status('+resp.result[i].id+')">{{__("label.show")}}</button>';
                                    } else {
                                        data += '<button class="btn hide-btn" id="'+resp.result[i].id+'" onclick="change_status('+resp.result[i].id+')">{{__("label.hide")}}</button>';
                                    }
                                data += '</div>'+
                                '<div class="card-body">'+
                                    '<form id="edit_section_'+resp.result[i].id+'" enctype="multipart/form-data">'+
                                        '<input type="hidden" name="id" value="'+resp.result[i].id+'">'+
                                        '<div class="form-row">'+
                                            '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                    '<label>{{__("label.title")}}</label>'+
                                                    '<input type="text" name="title" value="'+resp.result[i].title+'" class="form-control" readonly>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                    '<label>{{__("label.short_title")}}</label>'+
                                                    '<input type="text" name="short_title" value="'+resp.result[i].short_title+'" class="form-control" readonly>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-md-2">'+
                                                '<div class="form-group">'+
                                                    '<label>{{__("label.content_type")}}</label>'+
                                                    '<input type="text" name="content_type" value="'+content_type+'" class="form-control" readonly>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-md-2">'+
                                                '<div class="form-group">'+
                                                    '<label>{{__("label.screen_layout")}}</label>'+
                                                    '<input type="text" name="screen_layout" value="'+screen_layout+'" class="form-control" readonly>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="border-top pt-3 text-right">'+
                                            '<button type="button" data-toggle="modal" data-target="#updateModal" class="btn btn-default mw-120" onclick="edit_section('+resp.result[i].id+')">{{__("label.update")}}</button>'+
                                            '<button type="button" class="btn btn-cancel mw-120 ml-2" onclick="delete_section('+resp.result[i].id+')">{{__("label.delete")}}</button>'+
                                            '<input type="hidden" name="_method" value="PATCH">'+
                                        '</div>'+
                                    '</form>'+
                                '</div>'+
                            '</div>';

                        $('.after-add-more').append(data);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    toastr.error(errorThrown, textStatus);
                }
            });
        }
        function Top_Content(is_home_screen, content_type) {

            Is_home_screen = is_home_screen;
            Content_type = content_type;
            $("#is_home_screen").val(is_home_screen);

            document.getElementById("save_section").reset();
            $("#language_id").val(0).trigger("change");
            $("#category_id").val(0).trigger("change"); 

            if(is_home_screen == 1){

                $(".content_type_drop").show();
                $(".content_type_drop option[value='1']").show();
                $(".content_type_drop option[value='2']").show();
                $(".content_type_drop option[value='3']").show();
                $(".content_type_drop option[value='4']").show();
                $(".content_type_drop option[value='5']").show();
                $(".content_type_drop option[value='6']").show();
                $(".content_type_drop option[value='7']").show();

                $(".category_drop").hide();
                $(".language_drop").hide();
                $(".no_of_content_drop").hide();
                $(".order_by_upload_drop").hide();
                $(".order_by_view_drop").hide();
                $(".order_by_like_drop").hide();
                $(".view_all_drop").hide();
                $(".screen_layout_drop").hide();
            } else if(is_home_screen == 2) {

                if(content_type == 1){

                    $(".content_type_drop").show();
                    $(".content_type_drop option[value='1']").show();
                    $(".content_type_drop option[value='2']").hide();
                    $(".content_type_drop option[value='3']").hide();
                    $(".content_type_drop option[value='4']").show();
                    $(".content_type_drop option[value='5']").hide();
                    $(".content_type_drop option[value='6']").hide();

                    $(".category_drop").show();
                    $(".language_drop").show();
                    $(".no_of_content_drop").show();
                    $(".order_by_upload_drop").show();
                    $(".order_by_view_drop").show();
                    $(".order_by_like_drop").show();
                    $(".view_all_drop").show();
                    $(".screen_layout_drop").hide();
                } else if(content_type == 2){

                    $(".content_type_drop").hide();
                    $(".content_type_drop option[value='1']").hide();
                    $(".content_type_drop option[value='2']").show();
                    $(".content_type_drop option[value='3']").hide();
                    $(".content_type_drop option[value='4']").hide();
                    $(".content_type_drop option[value='5']").hide();
                    $(".content_type_drop option[value='6']").hide();

                    $(".category_drop").show();
                    $(".language_drop").show();
                    $(".no_of_content_drop").show();
                    $(".order_by_upload_drop").show();
                    $(".order_by_view_drop").show();
                    $(".order_by_like_drop").show();
                    $(".view_all_drop").show();

                    $(".screen_layout_drop").show();
                    $("#screen_layout").children().removeAttr("selected");
                    $("#screen_layout option[value='list_view']").hide();
                    $("#screen_layout option[value='portrait']").hide();
                    $("#screen_layout option[value='square']").hide();
                    $("#screen_layout option[value='playlist']").hide();
                    $("#screen_layout option[value='category']").hide();
                    $("#screen_layout option[value='language']").hide();
                    $("#screen_layout option[value='round']").hide();
                    $("#screen_layout option[value='banner_view']").show();
                    $("#screen_layout option[value='landscape']").show();
                    $("#screen_layout option[value='podcast_list_view']").show();
                } else if(content_type == 3){

                    $(".content_type_drop").hide();
                    $(".content_type_drop option[value='1']").hide();
                    $(".content_type_drop option[value='2']").hide();
                    $(".content_type_drop option[value='3']").show();
                    $(".content_type_drop option[value='4']").hide();
                    $(".content_type_drop option[value='5']").hide();
                    $(".content_type_drop option[value='6']").hide();

                    $(".category_drop").hide();
                    $(".language_drop").hide();
                    $(".no_of_content_drop").show();
                    $(".order_by_upload_drop").show();
                    $(".order_by_view_drop").hide();
                    $(".order_by_like_drop").hide();
                    $(".view_all_drop").show();

                    $(".screen_layout_drop").show();
                    $("#screen_layout").children().removeAttr("selected");
                    $("#screen_layout option[value='list_view']").hide();
                    $("#screen_layout option[value='portrait']").hide();
                    $("#screen_layout option[value='square']").show();
                    $("#screen_layout option[value='playlist']").hide();
                    $("#screen_layout option[value='category']").hide();
                    $("#screen_layout option[value='language']").hide();
                    $("#screen_layout option[value='round']").show();
                    $("#screen_layout option[value='banner_view']").hide();
                    $("#screen_layout option[value='landscape']").hide();
                    $("#screen_layout option[value='podcast_list_view']").hide();
                } else {

                    $(".content_type_drop").hide();
                    $(".category_drop").hide();
                    $(".language_drop").hide();
                    $(".no_of_content_drop").hide();
                    $(".order_by_upload_drop").hide();
                    $(".order_by_view_drop").hide();
                    $(".order_by_like_drop").hide();
                    $(".view_all_drop").hide();
                    $(".screen_layout_drop").hide();
                }
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route("admin.section.content.data") }}',
                data: {
                    is_home_screen: Is_home_screen,
                    content_type: Content_type,
                },
                success: function(resp) {
                    $('.after-add-more').html('');
                    for (var i = 0; i < resp.result.length; i++) {

                        if (resp.result[i].content_type == 1) {
                            var content_type = "{{ __('label.music') }}";
                        } else if (resp.result[i].content_type == 2) {
                            var content_type = "{{ __('label.podcasts') }}";
                        } else if (resp.result[i].content_type == 3) {
                            var content_type = "{{ __('label.radio') }}";
                        } else if (resp.result[i].content_type == 4) {
                            var content_type = "{{ __('label.playlist') }}";
                        } else if (resp.result[i].content_type == 5) {
                            var content_type = "{{ __('label.category') }}";
                        } else if (resp.result[i].content_type == 6) {
                            var content_type = "{{ __('label.language') }}";
                        } else if (resp.result[i].content_type == 7) {
                            var content_type = "{{ __('label.artist') }}";
                        }

                        if (resp.result[i].screen_layout == "list_view") {
                            var screen_layout = "{{ __('label.list_view') }}";"List View";
                        } else if (resp.result[i].screen_layout == "portrait") {
                            var screen_layout = "{{ __('label.portrait') }}";
                        } else if (resp.result[i].screen_layout == "square") {
                            var screen_layout = "{{ __('label.square') }}";
                        } else if (resp.result[i].screen_layout == "playlist") {
                            var screen_layout = "{{ __('label.playlist') }}";
                        } else if (resp.result[i].screen_layout == "category") {
                            var screen_layout = "{{ __('label.category') }}";
                        } else if (resp.result[i].screen_layout == "language") {
                            var screen_layout = "{{ __('label.language') }}";
                        } else if (resp.result[i].screen_layout == "round") {
                            var screen_layout = "{{ __('label.round') }}";
                        } else if (resp.result[i].screen_layout == "banner_view") {
                            var screen_layout = "{{ __('label.banner_view') }}";
                        } else if (resp.result[i].screen_layout == "landscape") {
                            var screen_layout = "{{ __('label.landscape') }}";
                        } else if (resp.result[i].screen_layout == "podcast_list_view") {
                            var screen_layout = "{{ __('label.podcast_list_view') }}";
                        }

                        var data = '<div class="card custom-border-card mt-3">'+
                                '<div class="card-header d-flex justify-content-between">'+
                                    '<div>'+
                                        '<h5 class="d-inline">{{__("label.edit_section")}}</h5>'+
                                        '<button class="btn btn-sm ml-2" id="pin_'+resp.result[i].id+'" onclick="toggle_pin('+resp.result[i].id+')" style="'+(resp.result[i].is_fixed == 1 ? 'background:#ffc107;color:#000;' : 'background:transparent;border:1px solid #ccc;color:#999;')+'">📌</button>'+
                                    '</div>';
                                    if(resp.result[i].status == 1){
                                        data += '<button class="btn show-btn" id="'+resp.result[i].id+'" onclick="change_status('+resp.result[i].id+')">{{__("label.show")}}</button>';
                                    } else {
                                        data += '<button class="btn hide-btn" id="'+resp.result[i].id+'" onclick="change_status('+resp.result[i].id+')">{{__("label.hide")}}</button>';
                                    }
                                data += '</div>'+
                                '<div class="card-body">'+
                                    '<form id="edit_section_'+resp.result[i].id+'" enctype="multipart/form-data">'+
                                        '<input type="hidden" name="id" value="'+resp.result[i].id+'">'+
                                        '<div class="form-row">'+
                                            '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                    '<label>{{__("label.title")}}</label>'+
                                                    '<input type="text" name="title" value="'+resp.result[i].title+'" class="form-control" readonly>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                    '<label>{{__("label.short_title")}}</label>'+
                                                    '<input type="text" name="short_title" value="'+resp.result[i].short_title+'" class="form-control" readonly>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-md-2">'+
                                                '<div class="form-group">'+
                                                    '<label>{{__("label.content_type")}}</label>'+
                                                    '<input type="text" name="content_type" value="'+content_type+'" class="form-control" readonly>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-md-2">'+
                                                '<div class="form-group">'+
                                                    '<label>{{__("label.screen_layout")}}</label>'+
                                                    '<input type="text" name="screen_layout" value="'+screen_layout+'" class="form-control" readonly>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="border-top pt-3 text-right">'+
                                            '<button type="button" data-toggle="modal" data-target="#updateModal" class="btn btn-default mw-120" onclick="edit_section('+resp.result[i].id+')">{{__("label.update")}}</button>'+
                                            '<button type="button" class="btn btn-cancel mw-120 ml-2" onclick="delete_section('+resp.result[i].id+')">{{__("label.delete")}}</button>'+
                                            '<input type="hidden" name="_method" value="PATCH">'+
                                        '</div>'+
                                    '</form>'+
                                '</div>'+
                            '</div>';
                        $('.after-add-more').append(data);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    toastr.error(errorThrown, textStatus);
                }
            });
        };

        // Pin/Unpin Section
        function toggle_pin(id) {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){
                $("#dvloader").show();
                var url = `{{ route('admin.section.pin', '') }}/${id}`;
                $.ajax({
                    type: "GET",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(resp) {
                        $("#dvloader").hide();
                        if (resp.status == 200) {
                            if (resp.is_fixed == 1) {
                                $('#pin_' + id).css({'background':'#ffc107','color':'#000','border':'none'});
                            } else {
                                $('#pin_' + id).css({'background':'transparent','border':'1px solid #ccc','color':'#999'});
                            }
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
                showError();
            }
        }

        // Sort Order Section
        $("#contentListId").sortable({
            update: function(event, ui) {
                getIdsOfContent();
            }
        });

        function getIdsOfContent() {
            var values = [];
            $('.listitemClass').each(function(index) {
                values.push($(this).attr("id")
                    .replace("imageNo", ""));
            });
            $('#outputvalues').val(values);
        }
        function sortOrderBTN(){
            var Tab = $("ul.tabs li a.active");
            var Is_home_screen = Tab.data("is_home_screen");
            var Content_type = Tab.data("content_type");
            
            $("#dvloader").show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route("admin.section.content.sortorder") }}',
                data: {
                    is_home_screen: Is_home_screen,
                    content_type: Content_type,
                },
                success: function(resp) {
                    $("#dvloader").hide();

                    $('#contentListId').html('');
                    for (var i = 0; i < resp.result.length; i++) {

                        var data = '<div id="'+ resp.result[i].id+'" class="listitemClass mb-2" style="background-color: #e9ecef;border: 1px solid black;cursor: s-resize;">'+
                                    '<p class="m-2">'+resp.result[i].title+'</p>'+
                                '</div>';

                        $('#contentListId').append(data);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        }
        function save_section_sortorder() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#save_section_sortorder")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.section.content.sortorder.save") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_section_sortorder', '{{ route("admin.section.index") }}');
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

        // Edit Section - populate modal
        function edit_section(id) {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {
                $("#dvloader").show();
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: '{{ route("admin.section.content.edit") }}',
                    data: { id: id },
                    success: function(resp) {
                        $("#dvloader").hide();
                        if (resp.status == 200 && resp.result) {
                            var s = resp.result;
                            $('#edit_id').val(s.id);
                            $('#edit_is_home_screen').val(s.is_home_screen);
                            $('#edit_title').val(s.title);
                            $('#edit_short_title').val(s.short_title);

                            // Set content_type dropdown
                            $('#edit_content_type').val(s.content_type).trigger('change');

                            // Trigger change to show correct screen_layout options
                            $('#edit_content_type').trigger('change');

                            // Set screen_layout after options are filtered
                            setTimeout(function() {
                                $('#edit_screen_layout').val(s.screen_layout);
                            }, 100);

                            // Set conditional fields
                            $('#edit_category_id').val(s.category_id).trigger('change');
                            $('#edit_language_id').val(s.language_id).trigger('change');
                            $('#edit_no_of_content').val(s.no_of_content);

                            // Radio buttons
                            if (s.order_by_upload == 1) {
                                $('#edit_order_by_upload_asc').prop('checked', true);
                            } else {
                                $('#edit_order_by_upload_desc').prop('checked', true);
                            }
                            if (s.order_by_view == 1) {
                                $('#edit_order_by_view_asc').prop('checked', true);
                            } else {
                                $('#edit_order_by_view_desc').prop('checked', true);
                            }
                            if (s.order_by_like == 1) {
                                $('#edit_order_by_like_asc').prop('checked', true);
                            } else {
                                $('#edit_order_by_like_desc').prop('checked', true);
                            }
                            if (s.view_all == 1) {
                                $('#edit_view_all_yes').prop('checked', true);
                            } else {
                                $('#edit_view_all_no').prop('checked', true);
                            }

                            $('#updateModal').modal('show');
                        } else {
                            toastr.error('{{ __("label.data_not_found") }}');
                        }
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

        // Update Section - submit edit modal
        function update_section() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {
                $("#dvloader").show();
                var formData = new FormData($("#edit_section")[0]);
                var id = $('#edit_id').val();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.section.update", '') }}/' + id,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'edit_section', '{{ route("admin.section.index") }}');
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

        // Delete Section
        function delete_section(id) {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {
                if (confirm('{{ __("label.are_you_sure") }}')) {
                    $("#dvloader").show();
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        type: 'GET',
                        url: '{{ route("admin.section.show", '') }}/' + id,
                        success: function(resp) {
                            $("#dvloader").hide();
                            get_responce_message(resp, 'delete_section', '{{ route("admin.section.index") }}');
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            $("#dvloader").hide();
                            toastr.error(errorThrown, textStatus);
                        }
                    });
                }
            } else {
                showError();
            }
        }

        // Toggle Show/Hide Status
        function change_status(id) {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {
                $("#dvloader").show();
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'GET',
                    url: '{{ route("admin.section.status", '') }}/' + id,
                    success: function(resp) {
                        $("#dvloader").hide();
                        if (resp.status == 200) {
                            toastr.success(resp.success);
                            // Reload the current tab
                            Top_Content(Is_home_screen, Content_type);
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
                showError();
            }
        }

        // Add screen_layout filtering for edit modal content_type changes
        $("#edit_content_type").change(function() {
            var ct = $(this).children("option:selected").val();
            var $layout = $("#edit_screen_layout");
            $layout.children().hide();

            if (ct == 1 || ct == 2) {
                if (ct == 1) {
                    $layout.find("option[value='list_view']").show();
                    $layout.find("option[value='portrait']").show();
                    $layout.find("option[value='square']").show();
                } else {
                    $layout.find("option[value='banner_view']").show();
                    $layout.find("option[value='landscape']").show();
                    $layout.find("option[value='podcast_list_view']").show();
                }
            } else if (ct == 3) {
                $layout.find("option[value='square']").show();
                $layout.find("option[value='round']").show();
            } else if (ct == 4) {
                $layout.find("option[value='playlist']").show();
            } else if (ct == 5) {
                $layout.find("option[value='category']").show();
            } else if (ct == 6) {
                $layout.find("option[value='language']").show();
            } else if (ct == 7) {
                $layout.find("option[value='round']").show();
            }
        });
    </script>
@endsection