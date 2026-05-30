@extends('admin.layout.page-app')
@section('page_title', __('label.artists'))
@section('tab_title', __('label.artists'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">{{__('label.artists')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.artists')}}</li>
                    </ol>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('label.add_artist')}}</h5>
                <div class="card-body">
                    <form id="artist" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="id">
                        <div class="form-row">
                            <div class="col-md-5">
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" placeholder="{{__('label.name_here')}}" autofocus>
                                        </div>
                                        <div class="form-group">
                                            <label>{{__('label.bio')}}<span class="text-danger">*</span></label>
                                            <textarea name="bio" class="form-control" rows="2" placeholder="{{__('label.bio_here')}}"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ml-5">
                                    <label class="ml-5">{{__('label.image')}}<span class="text-danger">*</span></label>
                                    <div class="avatar-upload ml-5">
                                        <div class="avatar-edit">
                                            <input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                            <label for="imageUpload" title="Select File"></label>
                                        </div>
                                        <div class="avatar-preview">
                                            <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_img.png" id="imagePreview">
                                        </div>
                                    </div>
                                    <label class="mt-3 ml-5 text-gray">{{__('label.image_note')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="save_artist()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <div class="page-search mb-3">
                    <div class="input-group" title="Search">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass fa-xl light-gray"></i></span>
                        </div>
                        <input type="text" id="input_search" class="form-control" placeholder="{{__('label.search')}}" aria-label="Search" aria-describedby="basic-addon1">
                    </div>
                </div>

                <div class="table-responsive table">
                    <table class="table table-striped text-center table-bordered" id="datatable">
                        <thead>
                            <tr style="background: #F9FAFF;">
                                <th> {{__('label.#')}} </th>
                                <th> {{__('label.image')}} </th>
                                <th> {{__('label.name')}} </th>
                                <th> {{__('label.bio')}} </th>
                                <th> {{__('label.linked_user')}} </th>
                                <th> {{__('label.type')}} </th>
                                <th> {{__('label.action')}} </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="EditModel" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{__('label.edit')}} {{__('label.artist')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="edit_form" enctype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="id" id="edit_id">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="edit_name" class="form-control" placeholder="{{__('label.name_here')}}">
                                        </div>
                                        <div class="form-group">
                                            <label>{{__('label.bio')}}<span class="text-danger">*</span></label>
                                            <textarea name="bio" id="edit_bio" class="form-control" rows="2" placeholder="{{__('label.bio_here')}}"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.image')}}</label>
                                            <div class="avatar-upload">
                                                <div class="avatar-edit">
                                                    <input type='file' name="image" id="edit_imageUpload" accept=".png, .jpg, .jpeg" />
                                                    <label for="edit_imageUpload" title="Select File"></label>
                                                </div>
                                                <div class="avatar-preview">
                                                    <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_img.png" id="edit_imagePreview" height="200">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-top pt-3 text-right">
                                    <button type="button" class="btn btn-default mw-120" onclick="update_artist()">{{__('label.update')}}</button>
                                </div>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </form>
                        </div>
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
            dom: "<'top'f>rt<'row'<'col-2'i><'col-1'l><'col-9'p>>",
            "responsive": true,
            "autoWidth": false,
            "searching": false,
            processing: true,
            serverSide: true,
            language: {
                paginate: {
                    previous: "<i class='fa-solid fa-chevron-left'></i>",
                    next: "<i class='fa-solid fa-chevron-right'></i>"
                }
            },
            "ajax": {
                "url": "{{ route('admin.artist.index') }}",
                "data": function(d) {
                    d.input_search = $('#input_search').val();
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'image', name: 'image', orderable: false, searchable: false,
                    render: function(data, type, full, meta) {
                        return `<a href='${data}' target='_blank'>
                                    <img src='${data}' class='img-thumbnail' style='height:55px; width:55px'>
                                </a>`;
                    },
                },
                { data: 'name', name: 'name' },
                { data: 'bio', name: 'bio' },
                { data: 'linked_user', name: 'linked_user', orderable: false, searchable: false },
                { data: 'type_badge', name: 'type_badge', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        $('#input_search').on('keyup', function() {
            table.draw();
        });
    });

    function save_artist() {
        var formData = new FormData($('#artist')[0]);
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.artist.store') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status == 200) {
                    toastr.success(res.success);
                    $('#artist')[0].reset();
                    $('#imagePreview').attr('src', "{{asset('assets/imgs/upload_img.png')}}");
                    $('#datatable').DataTable().draw();
                } else {
                    toastr.error(res.errors);
                }
            },
            error: function(data) {
                toastr.error('Something went wrong');
            }
        });
    }

    $(document).on('click', '.edit_artist', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_bio').val($(this).data('bio'));
        var img = $(this).data('image');
        if (img) {
            $('#edit_imagePreview').attr('src', img);
        }
    });

    function update_artist() {
        var formData = new FormData($('#edit_form')[0]);
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.artist.update') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status == 200) {
                    toastr.success(res.success);
                    $('#EditModel').modal('hide');
                    $('#datatable').DataTable().draw();
                } else {
                    toastr.error(res.errors);
                }
            },
            error: function(data) {
                toastr.error('Something went wrong');
            }
        });
    }

    /* Upload Image Preview */
    function readUrl(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imageUpload").change(function() {
        readUrl(this, 'imagePreview');
    });
    $("#edit_imageUpload").change(function() {
        readUrl(this, 'edit_imagePreview');
    });
</script>
@endsection
