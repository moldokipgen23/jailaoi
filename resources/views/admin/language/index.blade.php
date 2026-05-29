@extends('admin.layout.page-app')
@section('page_title', __('label.language'))
@section('tab_title', __('label.language'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.language')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-11">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.language')}}</li>
                    </ol>
                </div>
                <div class="col-sm-1 d-flex justify-content-start mb-3">
                    <button type="button" data-toggle="modal" data-target="#sortOrderModal" onclick="sortOrderBTN()" class="btn btn-default" style="border-radius: 10px;">
                        <i class="fa-solid fa-arrow-up-wide-short fa-1x"></i>
                    </button>
                </div>
            </div>

            <!-- Add Language -->
            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('label.add_language')}}</h5>
                <div class="card-body">
                    <form id="language" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="id" value="">
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" placeholder="{{__('label.name_here')}}" autofocus>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ml-5">
                                    <label class="ml-5">{{__('label.image')}}<span class="text-danger">*</span></label>
                                    <div class="avatar-upload ml-5">
                                        <div class="avatar-edit">
                                            <input type='file' name="image" id="imageUpload1" accept=".png, .jpg, .jpeg" />
                                            <label for="imageUpload1" title="{{__('label.upload_file')}}"></label>
                                        </div>
                                        <div class="avatar-preview">
                                            <img src="{{ asset('assets/imgs/upload_img.png') }}" id="imagePreview1">
                                        </div>
                                    </div>
                                    <label class="mt-3 ml-5 text-gray">{{__('label.max_size_5mb')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="save_language()">{{__('label.save')}}</button>
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
                                <th>{{__('label.name')}}</th>
                                <th>{{__('label.status')}}</th>
                                <th>{{__('label.action')}}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Model -->
            <div class="modal fade" id="EditModel" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{__('label.edit_language')}}</h5>
                            <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="edit_language" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="id" id="edit_id">
                                <input type="hidden" name="old_storage_type" id="edit_storage_type">
                                <div class="form-row">
                                    <div class="col-md-8">
                                        <div class="form-row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                                    <input type="text" name="name" id="edit_name" class="form-control" placeholder="{{__('label.name_here')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group ml-3">
                                            <label>{{__('label.image')}}<span class="text-danger">*</span></label>
                                            <div class="avatar-upload">
                                                <div class="avatar-edit">
                                                    <input type='file' name="image" id="imageUpload2" accept=".png, .jpg, .jpeg" />
                                                    <label for="imageUpload2" title="{{__('label.upload_file')}}"></label>
                                                </div>
                                                <div class="avatar-preview">
                                                    <img src="" id="imagePreview2">
                                                </div>
                                            </div>
                                            <label class="mt-3 text-gray">{{__('label.max_size_5mb')}}</label>
                                            <input type="hidden" name="old_image" id="edit_old_image">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default mw-120" onclick="update_language()">{{__('label.update')}}</button>
                                <button type="button" class="btn btn-cancel mw-120" data-dismiss="modal">{{__('label.close')}}</button>
                                <input type="hidden" name="_method" value="PATCH">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sortorder Modal -->
            <div class="modal fade" id="sortOrderModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="sortOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title w-100 text-center" id="sortOrderModalLabel">{{__('label.language_sortorder_list')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close">
                                <span aria-hidden="true" class="text-dark">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="contentListId">    
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <form enctype="multipart/form-data" id="save_language_sortorder">
                                @csrf
                                <input id="outputvalues" type="hidden" name="ids" value="" />
                                <div class="w-100 text-center">
                                    <button type="button" class="btn btn-default mw-120" onclick="save_language_sortorder()">{{__('label.save')}}</button>
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
    <!-- Sortorder -->
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                ...dataTableDefaults,
                ajax:
                    {
                    url: "{{ route('admin.language.index') }}",
                    data: function(d){
                        d.input_search = $('#input_search').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {
						data: 'image',
						name: 'image',
						orderable: false,
						searchable: false,
						render: function(data, type, full, meta) {
                            return `<a href='${data}' target='_blank'>
                                        <img src='${data}' class='img-thumbnail' style='height:55px; width:55px'>
                                    </a>`;
						},
					},
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data) {
                            return data ? data : "-";
                        }
                    },
                    {
						data: 'status',
						name: 'status',
                        orderable: false,
						searchable: false,
					},
					{ data: 'action', name: 'action', orderable: false, searchable: false }
                ],
            });

            $('#input_search').keyup(function() {
                table.draw();
            });
        });

        function save_language(){

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#language")[0]);
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.language.store") }}',
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'language', '{{ route("admin.language.index") }}');
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

        $(document).on("click", ".edit_language", function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var image = $(this).data('image');
            var storage_type = $(this).data('storage_type');

            $(".modal-body #edit_id").val(id);
            $(".modal-body #edit_name").val(name);
            $(".modal-body #imagePreview2").attr("src", image);
            $(".modal-body #edit_old_image").val(image);
            $(".modal-body #edit_storage_type").val(storage_type);
        });
        function update_language() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#edit_language")[0]);
                
                var Edit_Id = $("#edit_id").val();
                var url = '{{ route("admin.language.update", ":id") }}';
                    url = url.replace(':id', Edit_Id);

                $.ajax({
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
                        get_responce_message(resp, 'edit_language', '{{ route("admin.language.index") }}');
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

        function change_status(id) {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var url = `{{ route('admin.language.show', '') }}/${id}`;

                $.ajax({
                    type: "GET",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(resp) {
                        $("#dvloader").hide();

                        if (resp.status == 200) {
                            if (resp.status_code == 1) {
                                $('#' + id).text('{{__("label.show")}}').removeClass('hide-btn').addClass('show-btn');
                            } else {
                                $('#' + id).text('{{__("label.hide")}}').removeClass('show-btn').addClass('hide-btn');
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
        };

        // Sortorder Language
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

        var language_data = JSON.parse('<?php echo json_encode($data); ?>');
        function sortOrderBTN() {
            var listContainer = $('#contentListId');
            listContainer.html('');

            var htmlContent = ''; 
            for (var i = 0; i < language_data.length; i++) { 
                htmlContent += '<div id="'+ language_data[i].id +'" class="listitemClass mb-2" style="background-color: #e9ecef; border: 1px solid black; cursor: s-resize;">' +
                                '<p class="m-2">'+ language_data[i].name +'</p>' +
                            '</div>';
            }
            listContainer.append(htmlContent);
        }

        function save_language_sortorder() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#save_language_sortorder")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.language.sortorder.save") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_language_sortorder', '{{ route("admin.language.index") }}');
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