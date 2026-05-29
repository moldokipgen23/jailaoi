@extends('user.layout.page-app')
@section('page_title', __('label.add_feed'))
@section('tab_title', __('label.add_feed'))

@section('content')
    @include('user.layout.sidebar')

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.add_feed')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.feed.index') }}">{{__('label.feeds')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.add_feed')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('user.feed.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('label.feed_list')}}</a>
                </div>
            </div>

            <div class="card custom-border-card">
                <form id="feed" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('label.description')}}<span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="2" placeholder="{{__('label.description_here')}}"></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{__('label.is_like')}}<span class="text-danger">*</span></label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_like" id="is_like_yes" class="custom-control-input" value="1" checked>
                                        <label class="custom-control-label" for="is_like_yes">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_like" id="is_like_no" class="custom-control-input" value="0">
                                        <label class="custom-control-label" for="is_like_no">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{__('label.is_comment')}}<span class="text-danger">*</span></label>
                                <div class="radio-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_comment" id="is_comment_yes" class="custom-control-input" value="1" checked>
                                        <label class="custom-control-label" for="is_comment_yes">{{__('label.yes')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_comment" id="is_comment_no" class="custom-control-input" value="0">
                                        <label class="custom-control-label" for="is_comment_no">{{__('label.no')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{__('label.content_type')}}<span class="text-danger">*</span></label>
                                <select name="content_type[]" class="form-control content_type" id="content_type">
                                    <option value="1">{{__('label.image')}}</option>
                                    <option value="2">{{__('label.video')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" id="list_content_type">
                            <div class="form-group">
                                <label>{{__('label.video')}}<span class="text-danger">*</span></label>
                                <input type="file" name="content_video[]" class="form-control" accept=".mp4">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                                <input type="file" name="content_image[]" class="form-control content_img" id="content_img" accept=".png, .jpg, .jpeg">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <div class="custom-file">
                                    <img src="{{ asset('assets/imgs/upload_img.png') }}" style="height: 90px; width: 90px;" class="img-thumbnail" id="list_img_content_img">
                                </div>
                            </div>
                        </div>
                        <div class="col-1 mt-2">
                            <div class="flex-grow-1 d-inline-flex">
                                <div class="change mt-4">
                                    <a class="btn btn-success text-white" onclick="add_more()">+</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="add-more"></div>

                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="save_feed()">{{__('label.save')}}</button>
                        <a href="{{route('user.feed.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>
            </div>
        </div>
    </div>                
@endsection

@section('pagescript')
	<script>
        // Content Image-Video
        $(document).on('change', '.content_img', function(){
            readURL(this, this.id);
        });
        function readURL(input, id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                 
                reader.onload = function (e) {
                    $('#list_img_'+id).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#list_content_type').hide();
        $(document).on('change', '.content_type', function(){
            videoContent(this.value, this.id);
        });
        function videoContent(value, id) {
            if(value == 1){
                $('#list_'+id).hide();
            } else {
                $('#list_'+id).show();
            }
        }

        // Content Add-Remove 
        var i = 1;
        function add_more() {
            var data = `
                <div class="form-row row-${i}">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{__('label.content_type')}}<span class="text-danger">*</span></label>
                            <select name="content_type[]" class="form-control content_type" id="content_type_${i}">
                                <option value="1">{{__('label.image')}}</option>
                                <option value="2">{{__('label.video')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3" id="list_content_type_${i}">
                        <div class="form-group">
                            <label>{{__('label.video')}}<span class="text-danger">*</span></label>
                            <input type="file" name="content_video[]" class="form-control" accept=".mp4">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                            <input type="file" name="content_image[]" id="content_img_${i}" class="form-control content_img" accept=".png, .jpg, .jpeg">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <div class="custom-file">
                                <img src="{{ asset('assets/imgs/upload_img.png') }}" style="height: 90px; width: 90px;" class="img-thumbnail" id="list_img_content_img_${i}">
                            </div>
                        </div>
                    </div>
                    <div class="col-1 mt-2">
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-danger text-white remove-content mt-3" data-row-id="row-${i}">-</button>
                        </div>
                    </div>
                </div>`;
            
            $('.add-more').append(data);
            $('#list_content_type_' + i).hide();
            i++;

            // Scroll to the bottom
            $("html, body").animate({ scrollTop: $(document).height() }, "slow");
        }
        $(document).on('click', '.remove-content', function () {
            var rowId = $(this).data('row-id');
            $(`.${rowId}`).remove();
        });

		function save_feed(){

            var Check_Admin = '<?php echo Demo_Mode(); ?>';
            if(Check_Admin == 1){

                $("#dvloader").show();
                var formData = new FormData($("#feed")[0]);
                $.ajax({
                    type:'POST',
                    url:'{{ route("user.feed.store") }}',
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'feed', '{{ route("user.feed.index") }}');
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