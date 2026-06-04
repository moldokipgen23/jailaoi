@extends('admin.layout.page-app')
@section('page_title', __('label.transactions'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.transactions')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.transactions')}}</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end mb-3">
                <a href="{{ route('transaction.create') }}" class="btn btn-default mw-120">{{__('label.add_transaction')}}</a>
            </div>
        </div>
        <!-- search -->
        <div class="page-search mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-magnifying-glass fa-xl light-gray"></i>
                    </span>
                </div>
                <input type="text" id="input_search" class="form-control" placeholder="{{__('label.search_by_transaction_id_or_user')}}" aria-label="Search" aria-describedby="basic-addon1">
            </div>
            <div class="sorting mr-4" style="width: 40%;">
                <label>Sort by :</label>
                <select class="form-control" name="input_package" id="input_package">
                    <option value="0" selected>{{__('label.all_package')}}</option>
                    @foreach($package as $key=>$value)
                    <option value="{{$value['id']}}">{{$value['name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="sorting">
                <select class="form-control" id="input_type">
                    <option value="all">{{__('label.all')}}</option>
                    <option value="today">{{__('label.today')}}</option>
                    <option value="month">{{__('label.month')}}</option>
                    <option value="year">{{__('label.year')}}</option>
                </select>
            </div>
        </div>
        <!-- table  -->
        <div class="table-responsive table">
            <table class="table table-striped text-center table-bordered" id="datatable">
                <thead>
                    <tr class="bg-table">
                        <th>{{__('label.#')}}</th>
                        <th>{{__('label.user')}}</th>
                        <th>{{__('label.email')}}</th>
                        <th>{{__('label.mobile')}}</th>
                        <th>{{__('label.package')}}</th>
                        <th>{{__('label.transaction_id')}}</th>
                        <th>{{__('label.price')}}</th>
                        <th>{{__('label.description')}}</th>
                        <th>{{__('label.date')}}</th>
                        <th>{{__('label.expiry_date')}}</th>
                        <th>{{__('label.status')}}</th>
                        <th>{{__('label.action')}}</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr class="bg-table">
                        <td colspan="12" class="text-center"></td>
                    </tr>
                </tfoot>
            </table>
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
                url: "{{ route('transaction.index') }}",
                data: function(d) {
                    d.input_type = $('#input_type').val();
                    d.input_package = $('#input_package').val();
                    d.input_search = $('#input_search').val();
                },
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'user.full_name',
                    name: 'user.full_name',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    },
                },
                {
                    data: 'user.email',
                    name: 'user.email',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    },
                },
                {
                    data: 'user.mobile_number',
                    name: 'user.mobile_number',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    },
                },
                {
                    data: 'package.name',
                    name: 'package.name',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    },
                },
                {
                    data: 'transaction_id',
                    name: 'transaction_id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    },
                },
                {
                    data: 'price',
                    name: 'price',
                    orderable: false,
                    render: function(data, type, row, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "0";
                        }
                    }
                },
                {
                    data: 'description',
                    name: 'description',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    },
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
                    data: 'expiry_date',
                    name: 'expiry_date'
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
            footerCallback: function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                // converting to interger to find total
                var intVal = function(i) {
                    return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                };

                // computing column Total of the complete result 
                var Total = api
                    .column(6)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer by showing the total with the reference of the column index 
                $(api.column(1).footer()).html("Total Price =&nbsp" + Total);
            },
        });
        $('#input_type, #input_package').change(function() {
            table.draw();
        });
        $('#input_search').keyup(function() {
            table.draw();
        });
    });
</script>
@endsection