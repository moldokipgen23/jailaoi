@extends('admin.layout.page-app')
@section('page_title', __('label.gift_transactions'))
@section('tab_title', __('label.gift_transactions'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <!-- Select2 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.gift_transactions')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.gift_transactions')}}</li>
                    </ol>
                </div>
            </div>

            <!-- Search -->
            <div class="page-search mb-3">
                <div class="sorting mr-2 w-75">
                    <label>{{__('label.sort_by')}}</label>
                    <select class="form-control" name="input_user" id="input_user">
                        <option value="0" selected>{{__('label.all_channel')}}</option>
                        @for ($i = 0; $i < count($user); $i++) 
                            <option value="{{ $user[$i]['id'] }}" @if(isset($_GET['input_user'])){{ $_GET['input_user'] == $user[$i]['id'] ? 'selected' : ''}} @endif>
                                {{ $user[$i]['channel_name'] }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="sorting mr-2 w-50">
                    <select class="form-control" name="input_gift" id="input_gift">
                        <option value="0" selected>{{__('label.all_gift')}}</option>
                        @for ($i = 0; $i < count($gift); $i++) 
                            <option value="{{ $gift[$i]['id'] }}" @if(isset($_GET['input_gift'])){{ $_GET['input_gift'] == $gift[$i]['id'] ? 'selected' : ''}} @endif>
                                {{ $gift[$i]['name'] }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="sorting w-25">
                    <select class="form-control" id="input_type">
                        <option value="all">{{__('label.all_type')}}</option>
                        <option value="today">{{__('label.today')}}</option>
                        <option value="month">{{__('label.month')}}</option>
                        <option value="year">{{__('label.year')}}</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive table">
                <table class="table table-striped text-center table-bordered" id="datatable">
                    <thead>
                        <tr>
                            <th>{{__('label.#')}}</th>
                            <th>{{__('label.channel')}}</th>
                            <th>{{__('label.gift')}}</th>
                            <th>{{__('label.coin')}}</th>
                            <th>{{__('label.date')}}</th>
                            <th>{{__('label.action')}}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"></td>
                            <td class="text-center"></td>
                            <td colspan="2" class="text-center"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        // Sidebar Scroll Down
		sidebar_down(1330);

        $("#input_user").select2();

        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                ...dataTableDefaults,
                ajax: {
                    url: "{{ route('admin.gifttransaction.index') }}",
                    data: function(d) {
                        d.input_user = $('#input_user').val();
                        d.input_gift = $('#input_gift').val();
                        d.input_type = $('#input_type').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {
                        data: 'user',
                        name: 'user',
                        render: function(data, type, row) {                            
                            return `<div style="text-align: left;">${data.channel_name || ''}<br><span style="font-size: 14px; font-weight: 600;">${data.full_name || ''}</span>`;
                        }
                    },
                    {
                        data: 'gift',
						name: 'gift',
						render: function(data) {
                            return data ? data.name : "-";
                        }
					},
                    {
						data: 'coin',
						name: 'coin',
						render: function(data) {
                            return `<span style="font-size: 18px; font-weight: 600;" class="primary-color">${data || 0}</span>`;
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
                footerCallback: function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // converting to interger to find total
                    var intVal = function ( i ) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
                    };

                    // computing column Total of the complete result 
                    var price = api.column(3).data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer by showing the total with the reference of the column index
                    $(api.column(1).footer() ).html("<span style='font-size: 18px; font-weight: 600;' class='primary-color'>{{__('label.total')}}</span>");
                    $(api.column(3).footer() ).html("<span style='font-size: 18px; font-weight: 600;' class='primary-color'>" + price + "</span>");
                },
            });

            $('#input_type, #input_gift, #input_user').change(function() {
                table.draw();
            });
        });
    </script>
@endsection