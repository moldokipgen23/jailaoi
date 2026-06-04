@extends('admin.layout.page-app')
@section('page_title', __('label.page'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm"> {{__('label.page')}} </h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-11">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.page')}}</li>
                </ol>
            </div>
            <div class="col-md-1 d-flex justify-content-center align-items-center mb-3">
                <a href="{{route('page.create')}}" class="btn btn-default mw-120">{{__('label.add_page')}}</a>
            </div>
        </div>
        <!-- page layout setting  -->
        <div class="card custom-border-card mb-3 ">
            <h5 class="card-header">{{__('label.page_layout_setting')}}</h5>
            <div class="card-body">
                <form id="update_page" autocomplete="off" method="post" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{__('label.background_color')}}<span class="text-danger">*</span></label>
                                <div class="input-group colorpicker-component">
                                    <input type="text" id="hexcolor-1" class="form-control hexcolor" value="{{ isset($setting_data['page_background_color']) ? $setting_data['page_background_color'] : ''}}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                    <input type="color" id="colorpicker-1" name="background_color" value="{{ isset($setting_data['page_background_color']) ? $setting_data['page_background_color'] : ''}}" class="colorpicker" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{__('label.title_color')}}<span class="text-danger">*</span></label>
                                <div class="input-group colorpicker-component">
                                    <input type="text" id="hexcolor-2" class="form-control hexcolor" value="{{ isset($setting_data['page_title_color']) ? $setting_data['page_title_color'] : ''}}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                    <input type="color" id="colorpicker-2" name="title_color" value="{{ isset($setting_data['page_title_color']) ? $setting_data['page_title_color'] : '' }}" class="colorpicker" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="update_page()">{{__('label.save')}}</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>
            </div>
        </div>
        <!-- search && table  -->
        <div class="card custom-border-card">
            <div class="page-search">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basicaddon1"><i class="fa-solid fa-magnifying-glass fa-xl text-gray"></i></span>
                    </div>
                    <input type="text" name="input_search" id="input_search" class="form-control" placeholder="{{__('label.search_page')}}" area-label="Search" area-describedby="basicaddon1">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive table">
                    <table class="table table-striped text-center table-bordered" id="datatable">
                        <thead>
                            <tr class="bg-table">
                                <th>{{__('label.#')}}</th>
                                <th>{{__('label.icon')}}</th>
                                <th>{{__('label.title')}}</th>
                                <th>{{__('label.status')}}</th>
                                <th>{{__('label.action')}}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    // Sidebar Scroll Down
    sidebar_down($(document).height());

    $(document).ready(function() {

        var table = $('#datatable').DataTable({
            ...datatabledefault,
            ajax: {
                url: "{{ route('page.index') }}",
                data: function(d) {
                    d.input_search = $('#input_search').val();
                }

            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    visible: false
                },
                {
                    data: 'icon',
                    name: 'icon',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, full, meta) {
                        return "<a href='" + data + "' target='_blank' title='Watch'><img src='" + data + "' class='img-thumbnail size-55' ></a>";
                    },
                },
                {
                    data: 'title',
                    name: 'title'
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
        })
    });

    function update_page() {

        var isAdmin = <?php echo Check_Admin_Access(); ?>;
        if (isAdmin == 1) {

            $("#dvloader").show();
            var formData = new FormData($("#update_page")[0]);

            $.ajax({
                type: 'POST',
                url: '{{ route("page.save_setting") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'update_page', '{{ route("page.index") }}');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    // Color Picker
    $(document).ready(function() {

        // Event handler for color picker input change
        $('.colorpicker').on('input', function() {
            var target = $(this).attr('id').split('-')[1];
            $('#hexcolor-' + target).val(this.value.toUpperCase());
        });

        // Event handler for hex color input change
        $('.hexcolor').on('input', function() {
            var target = $(this).attr('id').split('-')[1];
            const hexPattern = /^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/;
            if (hexPattern.test(this.value)) {
                $('#colorpicker-' + target).val(this.value);
            }
        });
    });

    function change_status(id) {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var url = "{{route('page.show', '')}}" + "/" + id;
            $.ajax({
                type: "GET",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: id,
                success: function(resp) {
                    $("#dvloader").hide();
                    if (resp.status == 200) {
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
            toastr.error("{{__('label.you_have_no_right_to_add_edit_and_delete')}}");
        }
    };

    $(document).on('change', '.status-checkbox', function() {
        id = $(this).data('id');
        change_status(id);
    })
</script>
@endsection