@extends('admin.layout.page-app')
@section('page_title', __('label.rent_section'))
@section('tab_title', __('label.rent_section'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <!-- Select2 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.rent_section')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-11">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Rent Section</li>
                    </ol>
                </div>
                <div class="col-sm-1 d-flex justify-content-start mb-3">
                    <button type="button" data-toggle="modal" data-target="#sortOrderModal" onclick="sortableBTN()" class="btn btn-default" style="border-radius: 10px;">
                        <i class="fa-solid fa-arrow-up-wide-short fa-1x"></i>
                    </button>
                </div>
            </div>

            <div class="card custom-border-card">
                <h5 class="card-header">{{__('label.add_rent_section')}}</h5>
                <div class="card-body">
                    <form id="save_content_section" enctype="multipart/form-data">
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
                                    <label>{{__('label.category')}}<span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control" id="category_id">
                                        <option value="">{{__('label.select_category')}}</option>
                                        @for ($i = 0; $i < count($category); $i++) 
                                            <option value="{{ $category[$i]['id'] }}">
                                                {{ $category[$i]['name'] }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('label.no_of_content')}}<span class="text-danger">*</span></label>
                                    <input type="number" min="1" name="no_of_content" class="form-control" placeholder="{{__('label.no_of_content_here')}}" value="1">
                                </div>
                            </div>
                            <div class="col-md-3">
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
                            <button type="button" class="btn btn-default mw-120" onclick="save_section()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>

            @for ($i = 0; $i < count($data); $i++)
                <div class="card custom-border-card mt-3">
                    <h5 class="card-header">{{__('label.edit_section')}}</h5>
                    <div class="card-body">
                        <form id="edit_section_{{ $data[$i]['id'] }}" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="{{ $data[$i]['id'] }}">
                            <div class="form-row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.title')}}</label>
                                        <input type="text" name="title" value="{{ $data[$i]['title'] }}" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.category')}}</label>
                                        <input type="text" name="category" value="{{ $data[$i]['category']['name'] ?? '' }}" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{__('label.no_of_content')}}</label>
                                        <input type="text" name="no_of_content" value="{{ $data[$i]['no_of_content'] }}" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="border-top pt-3 text-right">
                                <button type="button" data-toggle="modal" data-target="#updateModal" class="btn btn-default mw-120" onclick="edit_section('{{ $data[$i]['id'] }}')">{{__('label.update')}}</button>
                                <button type="button" class="btn btn-cancel mw-120 ml-2" onclick="delete_section('{{ $data[$i]['id'] }}')">{{__('label.delete')}}</button>
                                <input type="hidden" name="_method" value="PATCH">
                            </div>
                        </form>
                    </div>
                </div>
            @endfor

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
                        <form id="edit_content_section" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="id" id="edit_id" value="">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="title" id="edit_title" class="form-control" placeholder="{{__('label.title_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_category_drop">
                                        <div class="form-group">
                                            <label>{{__('label.category')}}<span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-control" id="edit_category_id" style="width:100%!important;">
                                                <option value="">{{__('label.select_category')}}</option>
                                                @for ($i = 0; $i < count($category); $i++) 
                                                    <option value="{{ $category[$i]['id'] }}">
                                                        {{ $category[$i]['name'] }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 edit_no_of_content_drop">
                                        <div class="form-group">
                                            <label>{{__('label.no_of_content')}}<span class="text-danger">*</span></label>
                                            <input type="number" min="1" name="no_of_content" id="edit_no_of_content" class="form-control" placeholder="{{__('label.no_of_content_here')}}">
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

            <!-- Sort Order Modal -->
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
                                @for ($i = 0; $i < count($data); $i++)
                                    <div id="{{ $data[$i]['id']}}" class="listitemClass mb-2" style="background-color: #e9ecef;border: 1px solid black;cursor: s-resize;">
                                        <p class="m-2">{{ $data[$i]['title']}}</p>
                                    </div>
                                @endfor
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
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Sort Order -->
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

    <script>
        // Sidebar Scroll Down
		sidebar_down(850);

        $("#category_id").select2();
        $("#edit_category_id").select2();

        // Save Section
        function save_section(){

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                $("#dvloader").show();
                var formData = new FormData($("#save_content_section")[0]);
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.rentsection.store") }}',
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_content_section', '{{ route("admin.rentsection.index") }}');
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

        // Update Section
        function edit_section(id){

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route("admin.rentsection.content.edit") }}',
                data: {
                    id: id,
                },
                success: function(resp) {

                    if(resp.result != null){

                        $("#edit_id").val(resp.result.id);                        
                        $("#edit_title").val(resp.result.title);
                        $('#edit_category_id').val(resp.result.category_id).trigger('change');
                        $("#edit_no_of_content").val(resp.result.no_of_content);
                        if(resp.result.view_all == 1){
                            $("#edit_view_all_yes").attr('checked','checked');
                        } else {
                            $("#edit_view_all_no").attr('checked','checked');
                        }
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    toastr.error(errorThrown, textStatus);
                }
            });
        }
        function update_section(){

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                $("#dvloader").show();
                var id = $('#edit_id').val();
                var formData = new FormData($("#edit_content_section")[0]);

                var url = '{{ route("admin.rentsection.update", ":id") }}';
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
                        if(resp.status == 200){
                            $('#updateModal').modal('toggle');
                        }
                        get_responce_message(resp, 'edit_content_section', '{{ route("admin.rentsection.index") }}');
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
        function delete_section(id){

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                var result = confirm("{{__('label.delete_section')}}");
                if(result){

                    $("#dvloader").show();
    
                    var url = '{{ route("admin.rentsection.show", ":id") }}';
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
                            get_responce_message(resp, '', '{{ route("admin.rentsection.index") }}');
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

        // Sortorder Section
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
        function save_section_sortable() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#save_section_sortable")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.rentsection.content.sortorder.save") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_section_sortable', '{{ route("admin.rentsection.index") }}');
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