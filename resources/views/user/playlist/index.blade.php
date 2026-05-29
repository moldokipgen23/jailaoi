@extends('user.layout.page-app')
@section('page_title', __('label.playlists'))
@section('tab_title', __('label.playlists'))

@section('content')
    @include('user.layout.sidebar')

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.playlists')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.playlists')}}</li>
                    </ol>
                </div>
            </div>

            <!-- Add Playlist -->
            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('label.add_playlist')}}</h5>
                <div class="card-body">
                    <form id="playlist" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="">
                        <div class="form-row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="{{__('label.title_here')}}" autofocus>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{__('label.type')}}<span class="text-danger">*</span></label>
                                    <select name="playlist_type" class="form-control">
                                        <option value="1">{{__('label.public')}}</option>
                                        <option value="2">{{__('label.private')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                  <div class="form-group">
                                    <label>{{__('label.description')}}</label>
                                    <input type="text" name="description" class="form-control" placeholder="{{__('label.description_here')}}">
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="save_playlist()">{{__('label.save')}}</button>
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
                    <div class="sorting w-50">
                        <select class="form-control" name="input_type" id="input_type">
                            <option value="0" selected>{{__('label.all_type')}}</option>
                            <option value="1">{{__('label.public')}}</option>
                            <option value="2">{{__('label.private')}}</option>
                        </select>
                    </div>  
                </div>

                <div class="table-responsive table">
                    <table class="table table-striped text-center table-bordered" id="datatable">
                        <thead>
                            <tr>
                                <th>{{__('label.#')}}</th>
                                <th>{{__('label.title')}}</th>
                                <th>{{__('label.type')}}</th>
                                <th>{{__('label.content')}}</th>
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
                            <h5 class="modal-title" id="exampleModalLabel">{{__('label.edit_playlist')}}</h5>
                            <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="edit_playlist" autocomplete="off">
                            <div class="modal-body">
                                <input type="hidden" name="id" id="edit_id">
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="title" id="edit_title" class="form-control" placeholder="{{__('label.title_here')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.type')}}<span class="text-danger">*</span></label>
                                            <select name="playlist_type" class="form-control" id="edit_playlist_type">
                                                <option value="1">{{__('label.public')}}</option>
                                                <option value="2">{{__('label.private')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('label.description')}}</label>
                                            <textarea name="description" id="edit_description" class="form-control" rows="1" placeholder="{{__('label.description_here')}}"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default mw-120" onclick="update_playlist()">{{__('label.update')}}</button>
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
    <script>
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                ...dataTableDefaults,
                ajax:
                    {
                    url: "{{ route('user.playlist.index') }}",
                    data: function(d){
                        d.input_search = $('#input_search').val();
                        d.input_type = $('#input_type').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {
                        data: 'title',
                        name: 'title',
                        render: function(data, type, full, meta) {
                            return data ? data : "-";
                        }
                    },
                    {
                        data: 'playlist_type',
                        name: 'playlist_type',
                        render: function(data, type, full, meta) {
                            if (data == 1) {
                                return `<span style="font-size: 20px; font-weight: 600;" class="primary-color">{{__('label.public')}}</span>`;
                            } else if(data == 2){
                                return `<span style="font-size: 20px; font-weight: 600;" class="primary-color">{{__('label.private')}}</span>`;
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'content',
                        name: 'content',
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

            $('#input_type').change(function() {
                table.draw();
            });
            $('#input_search').keyup(function() {
                table.draw();
            });
        });

        function save_playlist(){

            var Check_Admin = '<?php echo Demo_Mode(); ?>';
            if(Check_Admin == 1){

                $("#dvloader").show();
                var formData = new FormData($("#playlist")[0]);
                $.ajax({
                    type:'POST',
                    url:'{{ route("user.playlist.store") }}',
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'playlist', '{{ route("user.playlist.index") }}');
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

        $(document).on("click", ".edit_playlist", function() {
            var id = $(this).data('id');
            var title = $(this).data('title');
            var description = $(this).data('description');
            var playlist_type = $(this).data('playlist_type');

            $(".modal-body #edit_id").val(id);
            $(".modal-body #edit_title").val(title);
            $(".modal-body #edit_description").val(description);
            $(".modal-body #edit_playlist_type").val(playlist_type).change();
        });
        function update_playlist() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#edit_playlist")[0]);

                var Edit_Id = $("#edit_id").val();
                var url = '{{ route("user.playlist.update", ":id") }}';
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
                        get_responce_message(resp, 'edit_playlist', '{{ route("user.playlist.index") }}');
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