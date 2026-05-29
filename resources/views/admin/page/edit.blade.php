@extends('admin.layout.page-app')
@section('page_title', __('label.edit_page'))
@section('tab_title', __('label.edit_page'))

@section('content')
	@include('admin.layout.sidebar')

	<div class="right-content">
		@include('admin.layout.header')

        <!-- Summer notes -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">

        <!-- summernote background color  -->
        <style>
            :root{
                --page-background-color : {{ $settings['page_background_color'] }} ;
            }
            .note-editable {
                background-color: var(--page-background-color) !important;
            }
        </style>

		<div class="body-content">
			<!-- mobile title -->
			<h1 class="page-title-sm">{{__('label.edit_page')}}</h1>

			<div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.page.index') }}">{{__('label.pages')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.edit_page')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.page.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('label.page_list')}}</a>
                </div>
            </div>

			<div class="card custom-border-card mt-3">
                <form id="page_update" enctype="multipart/form-data">				 
                    <input type="hidden" name="id" value="{{ $data['id'] }}">
                    <input type="hidden" name="old_storage_type" value="{{ $data['storage_type'] }}">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                        <input name="title" type="text" class="form-control" value="{{ $data['title'] }}" placeholder="{{__('label.title_here')}}" autofocus>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group ml-5">
                                <label class="ml-5">{{__('label.icon')}}<span class="text-danger">*</span></label>
                                <div class="avatar-upload ml-5">
                                    <div class="avatar-edit">
                                        <input type='file' name="icon" id="imageUpload1" accept=".png, .jpg, .jpeg" />
                                        <label for="imageUpload1" title="{{__('label.upload_file')}}"></label>
                                    </div>
                                    <div class="avatar-preview">
                                        <img src="{{ $data['icon'] }}" id="imagePreview1">
                                    </div>
                                </div>
                                <label class="mt-3 ml-5 text-gray">{{__('label.max_size_5mb')}}</label>
                                <input type="hidden" name="old_icon" value="{{ $data['icon'] }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{__('label.description')}}<span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="summernote">{{ $data['description'] }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="border-top mt-2 pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="edit_page()">{{__('label.update')}}</button>
                        <a href="{{route('admin.page.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
                        <input type="hidden" name="_method" value="PATCH">
                    </div>
                </form>
            </div>
		</div>
	</div>
@endsection

@section('pagescript')
    <!-- Summer notes -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <script>
        // Sidebar Scroll Down
        let sidebarHeight = $('.sidebar')[0].scrollHeight;
        sidebar_down(sidebarHeight);

        $(document).ready(function() {
            $('#summernote').summernote({
                placeholder: "{{__('label.description_here')}}",
                height: 500,
                toolbar: [
                    // Style Formatting
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    // Font Options
                    ['font', ['fontname', 'fontsize']],
                    ['color', ['forecolor']],
                    // Paragraph Formatting
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    // Insert Options
                    ['insert', ['link', 'picture', 'video']],
                    // Additional Formatting
                    ['table', ['table']],
                    // View Options
                    ['view', ['fullscreen', 'codeview', 'help']],
                ],
                fontSizes: ['8', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', '40', '44', '48', '52', '56', '60', '64', '68', '72', '78', '82', '86', '90', '94', '100'],
                lineHeights: ['0.2', '0.3', '0.4', '0.5', '0.6', '0.8', '1.0', '1.2', '1.4', '1.5', '1.8', '2.0', '3.0'],   
            });
            // Remove tooltip attributes from toolbar buttons
            $('.note-toolbar button').removeAttr('title data-original-title');
        });

        function edit_page(){

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#page_update")[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    enctype: 'multipart/form-data',
                    type: 'POST',
                    url: '{{route("admin.page.update", [$data->id])}}',
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'page_update', '{{ route("admin.page.index") }}');
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