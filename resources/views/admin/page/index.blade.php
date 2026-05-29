@extends('admin.layout.page-app')
@section('page_title', __('label.pages'))
@section('tab_title', __('label.pages'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.pages')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.pages')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.page.create') }}" class="btn btn-default mw-120" style="margin-top: -14px;">{{__('label.add_page')}}</a>
                </div>
            </div>

            <!-- Page Leyout Setting -->
            <div class="card custom-border-card mb-3 ">
                <h5 class="card-header">{{__('label.page_layout_setting')}}</h5>
                <div class="card-body">
                    <form id="layout_setting" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{__('label.background_color')}}<span class="text-danger">*</span></label>
                                    <div class="input-group colorpicker-component">
                                        <input type="text" id="hexcolor-1" class="form-control hexcolor" value="{{ isset($setting_data['page_background_color']) ? $setting_data['page_background_color'] : ''}}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                        <input type="color" id="colorpicker-1" name="background_color" value="{{ isset($setting_data['page_background_color']) ? $setting_data['page_background_color'] : ''}}" class="colorpicker" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{__('label.title_color')}}<span class="text-danger">*</span></label>
                                    <div class="input-group colorpicker-component">
                                        <input type="text" id="hexcolor-2" class="form-control hexcolor" value="{{ isset($setting_data['page_title_color']) ? $setting_data['page_title_color'] : ''}}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                        <input type="color" id="colorpicker-2" name="title_color" value="{{ isset($setting_data['page_title_color']) ? $setting_data['page_title_color'] : ''}}" class="colorpicker" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="layout_setting()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search & Table -->
            <div class="card custom-border-card mb-3 ">
                <div class="page-search mb-3">
                    <div class="input-group" title="{{__('label.search')}}">
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
@endsection

@section('pagescript')
    <script>
        // Sidebar Scroll Down
        let sidebarHeight = $('.sidebar')[0].scrollHeight;
        sidebar_down(sidebarHeight);

        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                ...dataTableDefaults,
                ajax: {
                    url: "{{ route('admin.page.index') }}",
                    data: function(d) {
                        d.input_search = $('#input_search').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {
						data: 'icon',
						name: 'icon',
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

        // Layout Setting
        function layout_setting() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#layout_setting")[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    enctype: 'multipart/form-data',
                    type: 'POST',
                    url: '{{route("admin.page.layout.save")}}',
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'layout_setting', '{{ route("admin.page.index") }}');
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

        function change_status(id, status) {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var url = "{{route('admin.page.show', '')}}" + "/" + id;
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
    </script>
@endsection