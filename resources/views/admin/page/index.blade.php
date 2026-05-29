@extends('admin.layout.page-app')
@section('page_title', __('Label.Pages'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm"> {{__('Label.Page')}} </h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('Label.Page')}}</li>
                    </ol>
                </div>
            </div>
            
            <div class="card custom-border-card mb-3 ">
                <h5 class="card-header">{{__('Label.page_layout_setting')}}</h5>
                <div class="card-body">
                    <form id="update_page" autocomplete="off" method="post" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{__('Label.background_color')}}<span class="text-danger">*</span></label>
                                    <div class="input-group colorpicker-component">
                                        <input type="text" id="hexcolor-1"  class="form-control hexcolor"value="{{ isset($setting_data['page_background_color']) ? $setting_data['page_background_color'] : ''}}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                        <input type="color" id="colorpicker-1" name="background_color" value="{{ isset($setting_data['page_background_color']) ? $setting_data['page_background_color'] : ''}}"   class="colorpicker" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{__('Label.title_color')}}<span class="text-danger">*</span></label>
                                    <div class="input-group colorpicker-component">
                                        <input type="text" id="hexcolor-2"  class="form-control hexcolor"  value="{{ isset($setting_data['page_title_color']) ? $setting_data['page_title_color'] : ''}}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                        <input type="color" id="colorpicker-2" name="title_color"  value="{{ isset($setting_data['page_title_color']) ? $setting_data['page_title_color'] : '' }}"   class="colorpicker" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="update_page()">{{__('Label.SAVE')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>

            <div class="card custom-border-card">
                <div class="card-body">
                    <div class="table-responsive table">
                        <table class="table table-striped text-center table-bordered" id="datatable">
                            <thead>
                                <tr style="background: #F9FAFF;">
                                    <th>{{__('Label.#')}}</th>
                                    <th>Icon</th>
                                    <th>{{__('Label.Title')}}</th>
                                    <th>{{__('Label.Action')}}</th>
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
                dom: "<'top'f>rt<'row'<'col-2'i><'col-1'l><'col-9'p>>",
                autoWidth: false,
                responsive: true,
                processing: true,
                serverSide: true,
                lengthMenu: [
                    [10, 100, 500, -1],
                    [10, 100, 500, "All"]
                ],
                language: {
                    paginate: {
                        previous: "<i class='fa-solid fa-chevron-left'></i>",
                        next: "<i class='fa-solid fa-chevron-right'></i>"
                    }
                },
                ajax:  "{{ route('page.index') }}",
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
                            return "<a href='" + data + "' target='_blank' title='Watch'><img src='" + data + "' class='img-thumbnail' style='height:55px; width:55px'></a>";
                        },
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        });

        function update_page() {

            var isAdmin = <?php echo Check_Admin_Access(); ?>;
            if (isAdmin == 1) {

                $("#dvloader").show();
                var formData = new FormData($("#update_page")[0]);

                $.ajax({
                    type: 'POST',
                    url: '{{ route("page.store") }}',
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
                toastr.error('{{__("Label.you_have_no_right_to_add_edit_and_delete")}}');
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
    </script>
@endsection