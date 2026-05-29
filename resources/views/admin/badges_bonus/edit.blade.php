@extends('admin.layout.page-app')
@section('page_title', __('label.edit_badges_&_bonus'))
@section('tab_title', __('label.edit_badges_&_bonus'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.edit_badges_&_bonus')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.badgesbonus.index') }}">{{__('label.badges_&_bonus')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.edit_badges_&_bonus')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.badgesbonus.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('label.badges_&_bonus_list')}}</a>
                </div>
            </div>

            <div class="card custom-border-card">
                <form id="badges_bonus" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="{{ $data['id'] }}">
                    <input type="hidden" name="old_storage_type" value="{{ $data['storage_type'] }}">
                    <div class="form-row">
                        <div class="col-md-10">
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{ $data['name'] }}" class="form-control" placeholder="{{__('label.name_here')}}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>{{__('label.description')}}<span class="text-danger">*</span></label>
                                        <textarea name="description" rows="1" class="form-control" placeholder="{{__('label.description_here')}}">{{ $data['description'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.type')}}<span class="text-danger">*</span></label>
                                        <select name="type" id="type" class="form-control">
                                            <option value="">{{__('label.select_type')}}</option>
                                            <option value="1" {{ $data['type'] == 1 ? 'selected' : ''}}>{{__('label.badges')}}</option>
                                            <option value="2" {{ $data['type'] == 2 ? 'selected' : ''}}>{{__('label.bonus')}}</option>
                                            <option value="0" {{ $data['type'] == 0 ? 'selected' : ''}}>{{__('label.badges_&_bonus')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 bonus_coin">
                                    <div class="form-group">
                                        <label>{{__('label.bonus_coin')}}<span class="text-danger">*</span></label>
                                        <input type="number" name="bonus_coin" value="{{ $data['bonus_coin'] }}" min="0" class="form-control" placeholder="{{__('label.coin_here')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.condition_type')}}<span class="text-danger">*</span></label>
                                        <select name="condition_type" id="condition_type" class="form-control">
                                            <option value="">{{__('label.select_type')}}</option>
                                            <option value="subscriber_count" {{ $data['condition_type'] == "subscriber_count" ? 'selected' : ''}}>{{__('label.x_number_of_subscriber')}}</option>
                                            <option value="refer_user" {{ $data['condition_type'] == "refer_user" ? 'selected' : ''}}>{{__('label.x_number_of_refer_user')}}</option>
                                            <option value="content_views" {{ $data['condition_type'] == "content_views" ? 'selected' : ''}}>{{__('label.x_number_of_views_on_x_content')}}</option>
                                            <option value="content_likes" {{ $data['condition_type'] == "content_likes" ? 'selected' : ''}}>{{__('label.x_number_of_likes_on_x_content')}}</option>
                                            <option value="video_upload" {{ $data['condition_type'] == "video_upload" ? 'selected' : ''}}>{{__('label.x_number_of_video_upload')}}</option>
                                            <option value="music_upload" {{ $data['condition_type'] == "music_upload" ? 'selected' : ''}}>{{__('label.x_number_of_music_upload')}}</option>
                                            <option value="reels_upload" {{ $data['condition_type'] == "reels_upload" ? 'selected' : ''}}>{{__('label.x_number_of_reels_upload')}}</option>
                                            <option value="podcasts_upload" {{ $data['condition_type'] == "podcasts_upload" ? 'selected' : ''}}>{{__('label.x_number_of_podcasts_upload')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 x_number">
                                    <div class="form-group">
                                        <label>{{__('label.min_x_number')}}<span class="text-danger">*</span></label>
                                        <input type="number" name="x_number" value="{{ $data['x_number'] }}" class="form-control" placeholder="{{__('label.number_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-4 x_content">
                                    <div class="form-group">
                                        <label>{{__('label.min_x_content')}}<span class="text-danger">*</span></label>
                                        <input type="number" name="x_content" value="{{ $data['x_content'] }}" class="form-control" placeholder="{{__('label.number_here')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group ml-5">
                                <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                                <div class="avatar-upload">
                                    <div class="avatar-edit">
                                        <input type='file' name="image" id="imageUpload1" accept=".png, .jpg, .jpeg" />
                                        <label for="imageUpload1" title="{{__('label.upload_file')}}"></label>
                                    </div>
                                    <div class="avatar-preview">
                                        <img src="{{ $data['image'] }}" id="imagePreview1">
                                    </div>
                                </div>
                                <input type="hidden" name="old_image" value="{{ $data['image'] }}">
                                <label class="mt-3 text-gray">{{__('label.max_size_5mb')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="update_badges_bonus()">{{__('label.update')}}</button>
                        <a href="{{route('admin.badgesbonus.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
                        <input type="hidden" name="_method" value="PATCH">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        // Sidebar Scroll Down
        let sidebarHeight = $('.sidebar')[0].scrollHeight;
        sidebar_down(sidebarHeight);

        $(document).ready(function () {
            var type = "<?php echo $data['type']; ?>";
            if (type == 2 || type == 0) {
                $(".bonus_coin").show();
            } else {
                $(".bonus_coin").hide();
            }
            var condition_type = "<?php echo $data['condition_type']; ?>";
            if (condition_type != 'content_views' && condition_type != 'content_likes') {
                $(".x_content").hide();
            }

            $('#type').change(function() {

                var optionValue = $(this).val();
                if(optionValue === '2' || optionValue === '0') {
                    $(".bonus_coin").show();
                } else {
                    $(".bonus_coin").hide();
                }
            });
            $('#condition_type').change(function() {

                var optionValue = $(this).val();
                if (optionValue === 'content_views' || optionValue === 'content_likes') {
                    $(".x_number").show();
                    $(".x_content").show();
                } else if(optionValue === "") {
                    $(".x_number").hide();
                    $(".x_content").hide();
                } else {
                    $(".x_number").show();
                    $(".x_content").hide();
                }
            });
        });

        function update_badges_bonus() {
			var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#badges_bonus")[0]);
                $.ajax({
                    type: 'POST',
                    url:'{{ route("admin.badgesbonus.update", [ $data->id ]) }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'badges_bonus', '{{ route("admin.badgesbonus.index") }}');
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