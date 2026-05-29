@extends('admin.layout.page-app')
@section('page_title', __('label.add_custom_ads'))
@section('tab_title', __('label.add_custom_ads'))

@section('content')
    @include('admin.layout.sidebar')

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.add_custom_ads')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.ads.index') }}">{{__('label.custom_ads')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.add_custom_ads')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.ads.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('label.custom_ads_list')}}</a>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <form id="ads" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="{{__('label.title_here')}}" autofocus>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('label.users')}}<span class="text-danger">*</span></label>
                                <select name="user_id" class="form-control" id="user_id" style="width:100%!important;">
                                    <option value="">{{__('label.select_user')}}</option>
                                    @foreach ($user as $key => $value)
                                        <option value="{{$value->id}}">
                                            {{$value->channel_name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('label.redirect_url')}}<span class="text-danger">*</span></label>
                                <input type="url" name="redirect_uri" class="form-control" placeholder="{{__('label.redirect_url_here')}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('label.ads_budget')}}<span class="text-danger">*</span></label>
                                <input type="number" name="budget" min="0" class="form-control" placeholder="{{__('label.ads_budget_here')}}">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{__('label.ads_type')}}<span class="text-danger">*</span></label>
                                <select name="type" class="form-control" id="type">
                                    <option value="1">{{__('label.banner_ads')}}</option>
                                    <option value="2">{{__('label.interstital_ads')}}</option>
                                    <option value="3">{{__('label.reward_ads')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 video_box">
                            <div class="form-group">
                                <div style="display: block;">
                                    <label>{{__('label.video')}}<span class="text-danger">*</span></label>
                                    <div id="filelist2"></div>
                                    <div id="container2" style="position: relative;">
                                        <div class="form-group">
                                            <input type="file" id="uploadFile2" name="uploadFile2" class="form-control import-file p-2">
                                        </div>
                                        <input type="hidden" name="video" id="mp3_file_name2" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 mt-4 video_box">
                            <div class="form-group mt-3">
                                <a id="upload2" class="btn text-white primary-bg">{{__('label.upload_file')}}</a>
                            </div>
                        </div>
                        <div class="col-md-4 s3_video_box">
                            <div class="form-group">
                                <label>{{__('label.video')}}<span class="text-danger">*</span></label>
                                <input type="file" name="video" class="form-control import-file" accept=".mp4">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group ml-5">
                                <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                                <div class="avatar-upload">
                                    <div class="avatar-edit">
                                        <input type='file' name="image" id="imageUpload1" accept=".png, .jpg, .jpeg" />
                                        <label for="imageUpload1" title="{{__('label.upload_file')}}"></label>
                                    </div>
                                    <div class="avatar-preview">
                                        <img src="{{ asset('assets/imgs/upload_img.png') }}" id="imagePreview1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="save_ads()">{{__('label.save')}}</button>
                        <a href="{{route('admin.ads.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>  
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Chunk JS -->
    <script src="{{ asset('/assets/js/plupload.full.min.js')}}"></script>
    <script src="{{ asset('/assets/js/common.js')}}"></script>

	<script>
        // Sidebar Scroll Down
        let sidebarHeight = $('.sidebar')[0].scrollHeight;
        sidebar_down(sidebarHeight);

        $("#user_id").select2();

        $(document).ready(function() {

            var storage_type = "<?php echo Storage_Type(); ?>";

            $(".video_box").hide();
            $(".s3_video_box").hide();
            $('#type').change(function() {

                var optionValue = $(this).val();
                if (optionValue == 3) {

                    if (storage_type == 1) {
                        $(".video_box").show();
                    } else if (storage_type == 2) {
                        $(".s3_video_box").show();            
                    }
                } else {
                    $(".video_box").hide();
                    $(".s3_video_box").hide();
                }
            });
        });

		function save_ads(){
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#ads")[0]);
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.ads.store") }}',
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'ads', '{{ route("admin.ads.index") }}');
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