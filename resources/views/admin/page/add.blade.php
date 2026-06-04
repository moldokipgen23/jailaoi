@extends('admin.layout.page-app')
@section('page_title',__('label.add_page'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.add_page')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.add_page')}}</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex justify-content-end align-items-center">
                <a href="{{route('page.index')}}" class="btn btn-default mt-14px">{{__('label.page_list')}}</a>
            </div>
        </div>
        <!-- add page  -->
        <div class="card custom-border-card">
            <form id="add_page_form" autocomplete="off" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="">
                <div class="form-row">
                    <div class="col-sm-9">
                        <div class="form-row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="{{__('label.title_here')}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>{{__('label.description')}}<span class="text-danger">*</span></label>
                                    <textarea id="description" name="description" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group ml-4">
                            <label class="ml-4">{{__('label.icon')}}<span class="text-danger">*</span></label>
                            <div class="avatar-upload ml-4">
                                <div class="avatar-edit">
                                    <input type="file" name="icon" id="imageUpload" accept=".png,.jpg.,.jpeg">

                                    <label for="imageUpload" title="Select file"></label>
                                </div>
                                <div class="avatar-preview">
                                    <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_image.png" id="imagePreview">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-top mt-2 pt-3 text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="add_page()">{{__('label.save')}}</button>
                    <a href="{{route('page.index')}}" class="btn btn-cancel mw-120">{{__('label.cancel')}}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    sidebar_down('1500px');

    $(document).ready(function() {

        $('#description').summernote({
            placeholder: 'Type your text here....',
            height: 500,
            toolbar: [
                // style 
                ['style', ['bold', 'italic', 'underline']],
                // fontoptions 
                ['font', ['fontname', 'fontsize']],
                // color 
                ['color', ['forecolor']],
                // paragraph formating 
                ['para', ['ul', 'ol', 'paragraph']],
                // height 
                ['height', ['height']],
                // insert options 
                ['insert', ['link', 'picture', 'video']],
                // table 
                ['table', ['table']],
                // view setting 
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            fontSizes: ['8', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', '40', '44', '48', '52', '56', '60', '64', '68', '72', '78', '82', '86', '90', '94', '100'],
            lineHeights: ['0.2', '0.3', '0.4', '0.5', '0.6', '0.8', '1.0', '1.2', '1.4', '1.5', '1.8', '2.0', '3.0'],

        })
        $('.note-toolbar button').removeAttr('title data-original-title');
    });

    function add_page() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $('#dvloader').show();
            var formData = new FormData($('#add_page_form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                type: 'POST',
                url: '{{route("page.store")}}',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(resp) {
                    $('#dvloader').hide();
                    get_responce_message(resp, 'add_page_form', '{{route("page.index")}}');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $('#dvloader').hide();
                    toastr.error(textStatus, errorThrown);
                }
            })
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }
</script>
@endsection