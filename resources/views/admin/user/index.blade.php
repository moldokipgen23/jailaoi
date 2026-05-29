@extends('admin.layout.page-app')
@section('page_title', __('label.users'))
@section('tab_title', __('label.users'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.users')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.users')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.user.create') }}" class="btn btn-default mw-120" style="margin-top: -14px;">{{__('label.add_user')}}</a>
                </div>
            </div>

            <!-- Export Files -->
            <div class="page-search mb-3">
                <div class="col-8">
                    <label class="text-gray pt-2 font-weight-bold"><i class="fa-solid fa-circle-info fa-2xl mr-3"></i>{{__('label.only_the_following_data_will_be_captured_in_this_file')}}</label>
                </div>
                <div class="col-4">
                    <div class="d-flex justify-content-around">
                        <button id="ms_excel" class="btn btn-default"><i class="fa-sharp fa-solid fa-file-excel mr-2 font-weight-bold"></i>{{__('label.ms_excel')}}</button>
                        <button id="csv" class="btn btn-default"><i class="fa-solid fa-file-csv mr-2 font-weight-bold"></i>{{__('label.csv')}}</button>
                        <button id="pdf" class="btn btn-default"><i class="fa-solid fa-file-pdf mr-2 font-weight-bold"></i>{{__('label.pdf')}}</button>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="page-search mb-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass fa-xl"></i></span>
                    </div>
                    <input type="text" id="input_search" class="form-control" placeholder="{{__('label.search')}}" aria-label="Search" aria-describedby="basic-addon1">
                </div>
                <div class="sorting mr-2 w-50">
                    <label>{{__('label.sort_by')}}</label>
                    <select class="form-control" id="input_type">
                        <option value="all">{{__('label.all_users')}}</option>
                        <option value="today">{{__('label.today')}}</option>
                        <option value="month">{{__('label.month')}}</option>
                        <option value="year">{{__('label.year')}}</option>
                    </select>
                </div>
                <div class="sorting w-50">
                    <select class="form-control" id="input_login_type">
                        <option value="all">{{__('label.all_type')}}</option>
                        <option value="1">{{__('label.otp')}}</option>
                        <option value="2">{{__('label.google')}}</option>
                        <option value="3">{{__('label.apple')}}</option>
                        <option value="4">{{__('label.normal')}}</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive table">
                <table class="table table-striped text-center table-bordered" id="datatable">
                    <thead>
                        <tr>
                            <th>{{__('label.#')}}</th>
                            <th>{{__('label.image')}}</th>
                            <th>{{__('label.full_name')}}</th>
                            <th>{{__('label.email')}}</th>
                            <th>{{__('label.mobile_number')}}</th>
                            <th>{{__('label.channel')}}</th>
                            <th>{{__('label.contact')}}</th>
                            <th>{{__('label.register_date')}}</th>
                            <th>{{__('label.type')}}</th>
                            <th>{{__('label.login_type_1_OTP_2_Google_3_Apple_4_Normal')}}</th>
                            <th>{{__('label.panel_status')}}</th>
                            <th>{{__('label.status')}}</th>
                            <th>{{__('label.action')}}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Export Files LInk (PDF, CSV, MS-Excel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>

    <script>
        const demo_mode = '{{ Demo_Mode() }}';
        function maskEmail(email) {
            const [user, domain] = email.split('@');
            const maskedUser = user.charAt(0) + '******';
            return maskedUser + '@' + domain;
        }

        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                ...dataTableDefaults,
                ajax:
                    {
                    url: "{{ route('admin.user.index') }}",
                    data: function(d){
                        d.input_type = $('#input_type').val();
                        d.input_login_type = $('#input_login_type').val();
                        d.input_search = $('#input_search').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return "<a href='" + data + "' target='_blank'><img src='" + data + "' class='rounded-circle' style='height:55px; width:55px'></a>";
                        },
                    },
                    {
                        data: 'full_name',
                        name: 'full_name',
                        orderable: false,
                        searchable: false,
                        visible: false,
                        render: function(data) {
                            return data ? data : "-";
                        }
                    },
                    {
                        data: 'email',
                        name: 'email',
                        orderable: false,
                        searchable: false,
                        visible: false,
                        render: function(data) {
                            return data ? data : "-";
                        }
                    },
                    {
                        data: 'mobile_number',
                        name: 'mobile_number',
                        orderable: false,
                        searchable: false,
                        visible: false,
                       render: function(data, type, full, meta) {
                            if (data && full.country_code) {
                                return full.country_code + ' ' + data;
                            } else if (data) {
                                return data;
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'channel_name',
                        name: 'channel_name',
                        render: function(data, type, row) {
                            return `<div style="text-align: left;">${row.channel_name || ''}<br><span style="font-size: 14px; font-weight: 600;">${row.full_name || ''}</span>`;
                        }
                    },
                    {
                        data: 'email',
                        name: 'email',
                        render: function(data, type, row) {
                            if (demo_mode == 0) {
                                const mobile = row.mobile_number ? row.country_code + ' ******' + row.mobile_number.slice(-4) : '';
                                const email = row.email ? maskEmail(row.email) : '';

                                return `<div style="text-align: left;">${mobile}<br><span style="font-size: 14px; font-weight: 600;">${email}</span></div>`;
                            } else {
                                return `<div style="text-align: left;">${row.country_code || ''} ${row.mobile_number || ''}<br><span style="font-size: 14px; font-weight: 600;">${row.email || ''}</span></div>`;
                            }
                        }
                    },
                    {
                        data: 'date',
                        name: 'date',
                        render: function(data) {
                            return data ? data : "-";
                        }
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            if (data == 1) {
                                return "<i class='fa-solid fa-mobile-screen-button fa-3x'></i>";
                            } else if (data == 2) {
                                return "<i class='fa-brands fa-google fa-3x'></i>";
                            } else if (data == 3) {
                                return "<i class='fa-brands fa-apple fa-3x'></i>";
                            } else if (data == 4) {
                                return "<i class='fa-solid fa-lock fa-3x'></i>";
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'type',
                        name: 'Login Type',
                        orderable: false,
                        searchable: false,
                        visible: false,
                        render: function(data) {
                            return data ? data : "-";
                        }
                    },
                    {
                        data: 'penal_status',
                        name: 'penal_status',
                        orderable: false,
                        searchable: false,
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
                buttons: [{
                        extend: 'excel',
                        filename: "{{App_Name()}} - {{__('label.users')}}",
                        exportOptions: {
                            columns: [0, 2, 3, 4, 7, 9]
                        },
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            $('row:first c', sheet).attr('s', '2');
                        },
                    },
                    {
                        extend: 'csv',
                        filename: "{{App_Name()}} - {{__('label.users')}}",
                        exportOptions: {
                            columns: [0, 2, 3, 4, 7, 9]
                        },
                    },
                    {
                        extend: 'pdf',
                        title: "{{App_Name()}} - {{__('label.users')}}",
                        filename: "{{App_Name()}} - {{__('label.users')}}",
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 7, 9]
                        },
                        customize: function(doc) {
                            doc.styles.tableHeader.fontSize = 10;
                            doc.defaultStyle.fontSize = 8;
                            doc.content[1].table.widths = ['5%', '20%', '20%', '20%', '15%', '20%'];
                            doc.content[1].layout = "borders";
                            doc.styles.title.fontSize = 22;
                            doc.styles.title.alignment = 'center';
                            doc.defaultStyle.alignment = 'center';

                            // Create a header
                            doc['header'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            bold: true,
                                            text: "{{App_Name()}}",
                                        },
                                        {
                                            alignment: 'right',
                                            bold: true,
                                            text: ['Total Page ', {
                                                text: pages.toString()
                                            }],
                                        }
                                    ],
                                    margin: [20, 20],
                                }
                            });
                            // Create a footer
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                        alignment: 'center',
                                        bold: true,
                                        text: ['Page ', {
                                            text: page.toString()
                                        }, ' of ', {
                                            text: pages.toString()
                                        }],
                                    }],
                                }
                            });
                        }
                    }
                ],
            });

            $('#ms_excel').on('click', function() {

                var check_access = '{{Demo_Mode()}}';
                if (check_access == 1) {
                    var table = $('#datatable').DataTable();
                    table.button('0').trigger();
                } else {
                    showError();
                }
            });
            $('#csv').on('click', function() {

                var check_access = '{{Demo_Mode()}}';
                if (check_access == 1) {
                    var table = $('#datatable').DataTable();
                    table.button('1').trigger();
                } else {
                    showError();
                }
            });
            $('#pdf').on('click', function() {

                var check_access = '{{Demo_Mode()}}';
                if (check_access == 1) {
                    var table = $('#datatable').DataTable();
                    table.button('2').trigger();
                } else {
                    showError();
                }
            });

            $('#input_type, #input_login_type').change(function(){
                table.draw();
            });
            $('#input_search').keyup(function(){
                table.draw();
            });
        });

        function change_status(id) {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var url = `{{ route('admin.user.show', '') }}/${id}`;

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
                                $('#' + id).text('{{__("label.active")}}').removeClass('hide-btn').addClass('show-btn');
                            } else {
                                $('#' + id).text('{{__("label.inactive")}}').removeClass('show-btn').addClass('hide-btn');
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
        function change_panel_status(id) {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var url = `{{ route('admin.user.penal.status', '') }}/${id}`;

                $.ajax({
                    type: "POST",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(resp) {
                        $("#dvloader").hide();

                        if (resp.status == 200) {
                            if (resp.status_code == 1) {
                                $('#panel_add_' + id).text('{{__("label.on")}}').removeClass('hide-btn').addClass('show-btn');
                            } else {
                                $('#panel_add_' + id).text('{{__("label.off")}}').removeClass('show-btn').addClass('hide-btn');
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