@extends('admin.layout.page-app')
@section('page_title', __('label.withdrawal'))
@section('tab_title', __('label.withdrawal'))

@section('content')
    @include('admin.layout.sidebar')

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.withdrawal')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.withdrawal')}}</li>
                    </ol>
                </div>
            </div>

            <!-- Min Withdrawal Amount -->
            <div class="card custom-border-card">
                <h5 class="card-header">{{__('label.minimum_withdrawal_amount')}}</h5>
                <div class="card-body">
                    <form id="save_min_withdrawal_amount">
                        <div class="row col-lg-12">
                            <div class="form-group col-lg-3">
                                <label>{{__('label.minimum_amount')}}<span class="text-danger">*</span></label>
                                <input type="number" name="min_withdrawal_amount" value="{{ $setting['min_withdrawal_amount'] }}" class="form-control" min="0" placeholder="{{__('label.minimum_amount_here')}}" autofocus>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="save_min_withdrawal_amount()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search && Table -->
            <div class="card custom-border-card">
                <div class="page-search mb-3">
                    <div class="sorting mr-2 w-50">
                        <label>{{__('label.sort_by')}}</label>
                        <select class="form-control" name="input_user" id="input_user">
                            <option value="0" selected>{{__('label.all_users')}}</option>
                            @for ($i = 0; $i < count($user); $i++) 
                                <option value="{{ $user[$i]['id'] }}" @if(isset($_GET['input_user'])){{ $_GET['input_user'] == $user[$i]['id'] ? 'selected' : ''}} @endif>
                                    {{ $user[$i]['channel_name'] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="sorting w-25">
                        <select class="form-control" name="input_status" id="input_status">
                            <option value="all">{{__('label.all_status')}}</option>
                            <option value="0" @if(isset($_GET['input_status'])){{ $_GET['input_status'] == 0 ? 'selected' : ''}} @endif>{{__('label.pending')}}</option>
                            <option value="1" @if(isset($_GET['input_status'])){{ $_GET['input_status'] == 1 ? 'selected' : ''}} @endif>{{__('label.completed')}}</option>
                        </select>
                    </div>  
                </div>

                <div class="table-responsive">
                    <table class="table table-striped text-center table-bordered" id="datatable">
                        <thead>
                            <tr>
                                <th>{{__('label.#')}}</th>
                                <th>{{__('label.channel')}}</th>
                                <th>{{__('label.contact')}}</th>
                                <th>{{__('label.amount')}}</th>
                                <th>{{__('label.type')}}</th>
                                <th>{{__('label.details')}}</th>
                                <th>{{__('label.date')}}</th>
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
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        // Sidebar Scroll Down
        let sidebarHeight = $('.sidebar')[0].scrollHeight;
        sidebar_down(sidebarHeight);

        $("#input_user").select2();

        const demo_mode = '{{ Demo_Mode() }}';
        function maskEmail(email) {
            const [user, domain] = email.split('@');
            const maskedUser = user.charAt(0) + '******';
            return maskedUser + '@' + domain;
        }

        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                ...dataTableDefaults,
                ajax: {
                    url: "{{ route('admin.withdrawal.index') }}",
                    data: function(d) {
                        d.input_user = $('#input_user').val();
                        d.input_status = $('#input_status').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {
                        data: 'channel_name',
                        name: 'channel_name',
                        render: function(data, type, row) {
                            return `<div style="text-align: left;">${row.user.channel_name || ''}<br><span style="font-size: 14px; font-weight: 600;">${row.user.full_name || ''}</span>`;
                        }
                    },
                    {
                        data: 'email',
                        name: 'email',
                        render: function(data, type, row) {
                            if (demo_mode == 0) {
                                const mobile = row.user.mobile_number ? row.user.country_code + ' ******' + row.user.mobile_number.slice(-4) : '';
                                const email = row.user.email ? maskEmail(row.user.email) : '';

                                return `<div style="text-align: left;">${mobile}<br><span style="font-size: 14px; font-weight: 600;">${email}</span></div>`;
                            } else {
                                return `<div style="text-align: left;">${row.user.country_code || ''} ${row.user.mobile_number || ''}<br><span style="font-size: 14px; font-weight: 600;">${row.user.email || ''}</span></div>`;
                            }
                        }
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        orderable: false,
                        searchable: false,
						render: function(data) {
                            return `<span style="font-size: 18px; font-weight: 600;" class="primary-color">${data || 0}</span>`;
                        }
                    },
                    {
                        data: 'payment_type',
                        name: 'payment_type',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data ? data : "-";
                        }
                    },
                    {
                        data: 'payment_detail',
                        name: 'payment_detail',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data ? data : "-";
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            $('#input_user, #input_status').change(function() {
                table.draw();
            });
        });

        function change_status(id) {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var url = `{{ route('admin.withdrawal.show', '') }}/${id}`;

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
                                $('#' + id).text('{{__("label.completed")}}').removeClass('hide-btn').addClass('show-btn');
                            } else {
                                $('#' + id).text('{{__("label.pending")}}').removeClass('show-btn').addClass('hide-btn');
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

        function save_min_withdrawal_amount() {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                var formData = new FormData($("#save_min_withdrawal_amount")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.withdrawal.save.amount") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        $("html, body").animate({scrollTop: 0}, "swing");
                        get_responce_message(resp);
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