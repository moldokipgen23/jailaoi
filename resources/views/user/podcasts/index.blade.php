@extends('user.layout.page-app')
@section('page_title', __('label.podcasts'))
@section('tab_title', __('label.podcasts'))

@section('content')
    @include('user.layout.sidebar')

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.podcasts')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.podcasts')}}</li>
                    </ol>
                </div>
            </div>

            <!-- Add Podcasts -->
            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('label.add_podcasts')}}</h5>
                <div class="card-body">
                    <form id="podcasts" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="{{__('label.title_here')}}" autofocus>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.category')}}<span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-control category_id" style="width:100%!important;">
                                                <option value="">{{__('label.select_category')}}</option>
                                                @foreach ($category as $key => $value)
                                                <option value="{{ $value['id'] }}">
                                                    {{ $value['name'] }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.language')}}<span class="text-danger">*</span></label>
                                            <select name="language_id" class="form-control language_id" style="width:100%!important;">
                                                <option value="">{{__('label.select_language')}}</option>
                                                @foreach ($language as $key => $value)
                                                <option value="{{ $value['id'] }}">
                                                    {{ $value['name'] }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('label.description')}}</label>
                                            <textarea name="description" class="form-control" rows="1" placeholder="{{__('label.description_here')}}"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group ml-5">
                                    <label class="ml-5">{{__('label.portrait_image')}}</label>
                                    <div class="avatar-upload ml-5">
                                        <div class="avatar-edit">
                                            <input type='file' name="portrait_img" id="imageUpload1" accept=".png, .jpg, .jpeg, .webp" />
                                            <label for="imageUpload1" title="{{__('label.upload_file')}}"></label>
                                        </div>
                                        <div class="avatar-preview">
                                            <img src="{{ asset('assets/imgs/upload_img.png') }}" id="imagePreview1">
                                        </div>
                                    </div>
                                    <label class="mt-3 ml-5 text-gray">{{__('label.max_size_5mb')}}</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group ml-5">
                                    <label>{{__('label.landscape_image')}}</label>
                                    <div class="avatar-upload-landscape">
                                        <div class="avatar-edit-landscape">
                                            <input type='file' name="landscape_img" id="imageUpload2" accept=".png, .jpg, .jpeg, .webp" />
                                            <label for="imageUpload2" title="{{__('label.upload_file')}}"></label>
                                        </div>
                                        <div class="avatar-preview-landscape">
                                            <img src="{{ asset('assets/imgs/upload_img.png ')}}" id="imagePreview2">
                                        </div>
                                    </div>
                                    <label class="mt-3 text-gray">{{__('label.max_size_5mb')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="save_podcasts()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search && Table -->
            <div class="card custom-border-card mt-3">
                <div class="page-search mb-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass fa-xl"></i></span>
                        </div>
                        <input type="text" id="input_search" class="form-control" placeholder="{{__('label.search')}}" aria-label="Search" aria-describedby="basic-addon1">
                    </div>
                </div>

                <div class="table-responsive table">
                    <table class="table table-striped text-center table-bordered" id="datatable">
                        <thead>
                            <tr>
                                <th>{{__('label.#')}}</th>
                                <th>{{__('label.image')}}</th>
                                <th>{{__('label.title')}}</th>
                                <th>{{__('label.episodes')}}</th>
                                <th>{{__('label.status')}}</th>
                                <th>{{__('label.action')}}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Model -->
            <div class="modal fade" id="EditModel" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{__('label.edit_podcasts')}}</h5>
                            <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="edit_podcasts" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="id" id="edit_id">
                                <input type="hidden" name="old_hashtag_id" id="edit_old_hashtag_id">
                                <input type="hidden" name="old_portrait_img" id="edit_old_portrait_img">
                                <input type="hidden" name="old_landscape_img" id="edit_old_landscape_img">
                                <input type="hidden" name="old_portrait_img_storage_type" id="edit_old_portrait_img_storage_type">
                                <input type="hidden" name="old_landscape_img_storage_type" id="edit_old_landscape_img_storage_type">
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="title" id="edit_title" class="form-control" placeholder="{{__('label.title_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.category')}}<span class="text-danger">*</span></label>
                                            <select name="category_id" id="edit_category_id" class="form-control category_id" style="width:100%!important;">
                                                <option value="">{{__('label.select_category')}}</option>
                                                @foreach ($category as $key => $value)
                                                    <option value="{{$value['id']}}">
                                                        {{ $value['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.language')}}<span class="text-danger">*</span></label>
                                            <select name="language_id" id="edit_language_id" class="form-control language_id" style="width:100%!important;">
                                                <option value="">{{__('label.select_language')}}</option>
                                                @foreach ($language as $key => $value)
                                                    <option value="{{$value['id']}}">
                                                        {{ $value['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('label.description')}}</label>
                                            <textarea name="description" id="edit_description" class="form-control" rows="1" placeholder="{{__('label.description_here')}}"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="">{{__('label.portrait_image')}}</label>
                                                    <div class="avatar-upload">
                                                        <div class="avatar-edit">
                                                            <input type='file' name="portrait_img" id="imageUpload3" accept=".png, .jpg, .jpeg, .webp" />
                                                            <label for="imageUpload3" title="{{__('label.upload_file')}}"></label>
                                                        </div>
                                                        <div class="avatar-preview">
                                                            <img src="" id="imagePreview3">
                                                        </div>
                                                    </div>
                                                    <label class="mt-3 text-gray">{{__('label.max_size_5mb')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{__('label.landscape_image')}}</label>
                                                    <div class="avatar-upload-landscape">
                                                        <div class="avatar-edit-landscape">
                                                            <input type='file' name="landscape_img" id="imageUpload4" accept=".png, .jpg, .jpeg, .webp" />
                                                            <label for="imageUpload4" title="{{__('label.upload_file')}}"></label>
                                                        </div>
                                                        <div class="avatar-preview-landscape">
                                                            <img src="" id="imagePreview4">
                                                        </div>
                                                    </div>
                                                    <label class="mt-3 text-gray">{{__('label.max_size_5mb')}}</label>                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default mw-120" onclick="update_podcasts()">{{__('label.update')}}</button>
                                <button type="button" class="btn btn-cancel mw-120" data-dismiss="modal">{{__('label.close')}}</button>
                                <input type="hidden" name="_method" value="PATCH">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(".category_id").select2();
        $(".language_id").select2();

        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                ...dataTableDefaults,
                ajax:
                    {
                    url: "{{ route('user.podcasts.index') }}",
                    data: function(d){
                        d.input_search = $('#input_search').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {
						data: 'portrait_img',
						name: 'portrait_img',
						orderable: false,
						searchable: false,
						render: function(data, type, full, meta) {
                            return `<a href='${data}' target='_blank'>
                                        <img src='${data}' class='img-thumbnail' style='height:55px; width:55px'>
                                    </a>`;
						},
					},
                    {
                        data: 'title',
                        name: 'title',
                        render: function(data, type, full, meta) {
                            return data ? data : "-";
                        }
                    },
                    {
                        data: 'episode',
                        name: 'episode',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            $('#input_search').keyup(function() {
                table.draw();
            });
        });

        function save_podcasts(){

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#podcasts")[0]);
                $.ajax({
                    type:'POST',
                    url:'{{ route("user.podcasts.store") }}',
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'podcasts', '{{ route("user.podcasts.index") }}');
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

        $(document).on("click", ".edit_podcasts", function() {
            var id = $(this).data('id');
            var title = $(this).data('title');
            var hashtag_id = $(this).data('hashtag_id');
            var description = $(this).data('description');
            var portrait_img = $(this).data('portrait_img');
            var landscape_img = $(this).data('landscape_img');
            var category_id = $(this).data('category_id');
            var language_id = $(this).data('language_id');
            var portrait_img_storage_type = $(this).data('portrait_img_storage_type');
            var landscape_img_storage_type = $(this).data('landscape_img_storage_type');

            $(".modal-body #edit_id").val(id);
            $(".modal-body #edit_title").val(title);
            $(".modal-body #edit_description").val(description);
            $(".modal-body #edit_old_hashtag_id").val(hashtag_id);
            $(".modal-body #edit_old_portrait_img_storage_type").val(portrait_img_storage_type);
            $(".modal-body #edit_old_landscape_img_storage_type").val(landscape_img_storage_type);
            $(".modal-body #edit_category_id").val(category_id).change();
            $(".modal-body #edit_language_id").val(language_id).change();

            $(".modal-body #imagePreview3").attr("src", portrait_img);
            $(".modal-body #imagePreview4").attr("src", landscape_img);
            $(".modal-body #edit_old_portrait_img").val(portrait_img);
            $(".modal-body #edit_old_landscape_img").val(landscape_img);
        });
        function update_podcasts() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#edit_podcasts")[0]);

                var Edit_Id = $("#edit_id").val();
                var url = '{{ route("user.podcasts.update", ":id") }}';
                    url = url.replace(':id', Edit_Id);

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

                        if(resp.status == 200){
                            $('#EditModel').modal('toggle');
                        }
                        get_responce_message(resp, 'edit_podcasts', '{{ route("user.podcasts.index") }}');
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