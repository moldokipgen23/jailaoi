@extends('admin.layout.page-app')
@section('page_title', __('label.live_event'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.live_event')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.live_event')}}</li>
                </ol>
            </div>
        </div>
        <!-- add live event -->
        <div class="card custom-border-card mt-3">
            <h5 class="card-header">{{__('label.add_live_event')}}</h5>
            <div class="card-body">
                <form id="live_event" enctype="multipart/form-data">
                    <input type="hidden" name="id">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.start_time')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="start_time" class="form-control timePicker" placeholder="{{__('label.start_time_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.end_time')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="end_time" class="form-control timePicker" placeholder="{{__('label.end_time_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.paid')}}<span class="text-danger">*</span></label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_paid" id="is_paid_yes" class="custom-control-input paid_value" value="1" checked>
                                                <label class="custom-control-label" for="is_paid_yes">{{__('label.yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="is_paid" id="is_paid_no" class="custom-control-input paid_value" value="0">
                                                <label class="custom-control-label" for="is_paid_no">{{__('label.no')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>{{__('label.type')}}<span class="text-danger">*</span></label>
                                        <select name="type" class="form-control">
                                            <option value="1">{{__('label.audio')}}</option>
                                            <option value="2">{{__('label.video')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4" id="price">
                                    <div class="form-group">
                                        <label>{{__('label.price')}}<span class="text-danger">*</span></label>
                                        <input type="number" name="price" class="form-control" placeholder="{{__('label.price_here')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group ml-5">
                                <label class="ml-5">{{__('label.potrait_image')}}<span class="text-danger">*</span></label>
                                <div class="avatar-upload ml-5">
                                    <div class="avatar-edit">
                                        <input type='file' name="portrait_img" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                        <label for="imageUpload" title="Select File"></label>
                                    </div>
                                    <div class="avatar-preview">
                                        <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_img.png" id="imagePreview">
                                    </div>
                                </div>
                                <label class="mt-3 ml-5 text-gray">{{__('label.image_note')}}</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group ml-5">
                                <label>{{__('label.landscape_image')}}<span class="text-danger">*</span></label>
                                <div class="avatar-upload-landscape">
                                    <div class="avatar-edit-landscape">
                                        <input type='file' name="landscape_img" id="imageUploadLandscape" accept=".png, .jpg, .jpeg" />
                                        <label for="imageUploadLandscape" title="Select File"></label>
                                    </div>
                                    <div class="avatar-preview-landscape">
                                        <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_img.png" id="imagePreviewLandscape">
                                    </div>
                                </div>
                                <label class="mt-3 text-gray">{{__('label.image_note')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{__('label.link')}}<span class="text-danger">*</span></label>
                                <input type="text" name="link" class="form-control" placeholder="{{__('label.link_here')}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{__('label.date')}}<span class="text-danger">*</span></label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control" min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('label.description')}}<span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="2" placeholder="{{__('label.description_here')}}"></textarea>
                    </div>
                </div>
            </div>
            <div class="border-top pt-3 text-right">
                <button type="button" class="btn btn-default mw-120" onclick="save_live_event()">{{__('label.save')}}</button>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </div>
            </form>
        </div>
    </div>
    <!-- search && table -->
    <div class="card custom-border-card mt-3">
        <div class="page-search mb-3">
            <div class="input-group" title="Search">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass fa-xl light-gray"></i></span>
                </div>
                <input type="text" id="input_search" class="form-control" placeholder="{{__('label.search_live_event')}}" aria-label="Search" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="table-responsive table">
            <table class="table table-striped text-center table-bordered" id="datatable">
                <thead>
                    <tr class="bg-table">
                        <th> {{__('label.#')}} </th>
                        <th> {{__('label.image')}} </th>
                        <th>{{__('label.title')}}</th>
                        <th>{{__('label.date')}}</th>
                        <th>{{__('label.start_time')}}</th>
                        <th>{{__('label.end_time')}}</th>
                        <th>{{__('label.paid')}}</th>
                        <th>{{__('label.price')}}</th>
                        <th>{{__('label.status')}}</th>
                        <th>{{__('label.join_user')}}</th>
                        <th>{{__('label.action')}}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <!-- edit model -->
    <div class="modal fade" id="EditModel" data-backdrop="static" role="dialog" aria-labelledby="exampleModallabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModallabel">{{__('label.edit_live_event')}}</h5>
                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="update_live_event" autocomplete="off">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <input type="hidden" name="old_portrait_img" id="edit_old_portrait_img">
                        <input type="hidden" name="old_landscape_img" id="edit_old_landscape_img">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="edit_title" class="form-control" placeholder="{{__('label.title_here')}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.paid')}}<span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_paid" id="IS_Paid_Yes" class="custom-control-input Paid_Value" value="1">
                                            <label class="custom-control-label" for="IS_Paid_Yes">{{__('label.yes')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_paid" id="IS_Paid_No" class="custom-control-input Paid_Value" value="0">
                                            <label class="custom-control-label" for="IS_Paid_No">{{__('label.no')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.start_time')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="start_time" id="edit_start_time" class="form-control timePicker" placeholder="{{__('label.start_time_here')}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.end_time')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="end_time" id="edit_end_time" class="form-control timePicker" placeholder="{{__('label.end_time_here')}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.type')}}<span class="text-danger">*</span></label>
                                    <select name="type" id="edit_type" class="form-control">
                                        <option value="1">{{__('label.audio')}}</option>
                                        <option value="2">{{__('label.video')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.date')}}<span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="edit_date" class="form-control" min="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6" id="edit_price">
                                <div class="form-group">
                                    <label>{{__('label.price')}}<span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="edit_price" class="form-control" placeholder="{{__('label.price_here')}}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('label.link')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="link" id="edit_link" class="form-control" placeholder="{{__('label.link_here')}}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('label.description')}}<span class="text-danger">*</span></label>
                                    <textarea name="description" id="edit_description" class="form-control" rows="1" placeholder="{{__('label.description_here')}}"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group ml-3">
                                            <label class="">{{__('label.potrait_image')}}<span class="text-danger">*</span></label>
                                            <div class="avatar-upload">
                                                <div class="avatar-edit">
                                                    <input type='file' name="portrait_img" id="imageUploadModel" accept=".png, .jpg, .jpeg" />
                                                    <label for="imageUploadModel" title="Select File"></label>
                                                </div>
                                                <div class="avatar-preview">
                                                    <img src="" alt="upload_img.png" id="imagePreviewModel">
                                                </div>
                                            </div>
                                            <label class="mt-3 text-gray">{{__('label.image_note')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.landscape_image')}}<span class="text-danger">*</span></label>
                                            <div class="avatar-upload-landscape">
                                                <div class="avatar-edit-landscape">
                                                    <input type='file' name="landscape_img" id="imageUploadLandscapeModel" accept=".png, .jpg, .jpeg" />
                                                    <label for="imageUploadLandscapeModel" title="Select File"></label>
                                                </div>
                                                <div class="avatar-preview-landscape">
                                                    <img src="" alt="upload_img.png" id="imagePreviewLandscapeModel">
                                                </div>
                                            </div>
                                            <label class="mt-3 text-gray">{{__('label.image_note')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default mw-120" onclick="update_live_event()">{{__('label.update')}}</button>
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
    var d = new Date();
    d.setHours(0, 0);
    $('.timePicker').datetimepicker({
        useCurrent: false,
        format: 'HH:mm',
        defaultDate: d,
        showClose: true,
        showTodayButton: true,
        icons: {
            up: "fa fa-angle-up",
            down: "fa fa-chevron-down",
            today: "fa fa-clock",
            close: "fa fa-times",
        }
    })

    $(document).on('change', '.paid_value', function() {

        var value = $(this).attr("value");
        if (value == 1) {
            $("#price").show();
        } else {
            $("#price").hide();
        }
    });

    $(document).ready(function() {
        var table = $('#datatable').DataTable({
            ...datatabledefault,
            ajax: {
                url: "{{ route('liveevent.index') }}",
                data: function(d) {
                    d.input_search = $('#input_search').val();
                },
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'portrait_img',
                    name: 'portrait_img',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, full, meta) {
                        return "<a href='" + data + "' target='_blank' title='Watch'><img src='" + data + "' class='img-thumbnail size-55'></a>";
                    },
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    }
                },
                {
                    data: 'date',
                    name: 'date',
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    }
                },
                {
                    data: 'start_time',
                    name: 'start_time',
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    }
                },
                {
                    data: 'end_time',
                    name: 'end_time',
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    }
                },
                {
                    data: 'is_paid',
                    name: 'is_paid',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, full, meta) {
                        if (data == 1) {
                            return "Paid";
                        } else if (data == 0) {
                            return "Free";
                        } else {
                            return "-";
                        }
                    }
                },
                {
                    data: 'price',
                    name: 'price',
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return 0;
                        }
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'join_user',
                    name: 'join_user',
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

    function save_live_event() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var formData = new FormData($("#live_event")[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route("liveevent.store") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'live_event', '{{ route("liveevent.index") }}');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        } else {
            toastr.error("{{__('label.you_have_no_right_to_add_edit_and_delete')}}");
        }
    }

    $(document).on("click", ".edit_live_event", function() {
        var id = $(this).data('id');
        var title = $(this).data('title');
        var date = $(this).data('date');
        var description = $(this).data('description');
        var start_time = $(this).data('start_time');
        var end_time = $(this).data('end_time');
        var is_paid = $(this).data('is_paid');
        var price = $(this).data('price');
        var link = $(this).data('link');
        var type = $(this).data('type');
        var portrait_img = $(this).data('portrait_img');
        var landscape_img = $(this).data('landscape_img');

        $(".modal-body #edit_id").val(id);
        $(".modal-body #edit_title").val(title);
        $(".modal-body #edit_date").val(date);
        $(".modal-body #edit_description").val(description);
        $(".modal-body #edit_start_time").val(start_time);
        $(".modal-body #edit_end_time").val(end_time);
        $(".modal-body #edit_price").val(price);
        $(".modal-body #edit_link").val(link);
        $(".modal-body #imagePreviewModel").attr("src", portrait_img);
        $(".modal-body #imagePreviewLandscapeModel").attr("src", landscape_img);
        $(".modal-body #edit_old_portrait_img").val(portrait_img);
        $(".modal-body #edit_old_landscape_img").val(landscape_img);
        $(".modal-body #edit_type").val(type).change();

        if (is_paid == 1) {
            $('.modal-body #IS_Paid_Yes').val(is_paid).prop("checked", true);
            $("#edit_price").show();
        } else {
            $('.modal-body #IS_Paid_No').val(is_paid).prop("checked", true);
            $("#edit_price").hide();
        }
    });

    $(document).on('change', '.Paid_Value', function() {

        var value = $(this).attr("value");
        if (value == 1) {
            $("#edit_price").show();
        } else {
            $("#edit_price").hide();
        }
    });

    function update_live_event() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var formData = new FormData($("#update_live_event")[0]);

            var Edit_Id = $("#edit_id").val();

            var url = '{{ route("liveevent.update", ":id") }}';
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

                    if (resp.status == 200) {
                        $('#EditModel').modal('toggle');
                    }
                    get_responce_message(resp, 'update_live_event', '{{ route("liveevent.index") }}');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        } else {
            toastr.error("{{__('label.you_have_no_right_to_add_edit_and_delete')}}");
        }
    }
</script>
@endsection