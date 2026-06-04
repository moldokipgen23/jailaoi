@extends('admin.layout.page-app')
@section('page_title',__('label.join_user'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.join_user')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('liveevent.index') }}">{{__('label.live_event')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.join_user')}}</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('liveevent.index') }}" class="btn btn-default mw-120 mb-3">{{__('label.live_event')}}</a>
            </div>
        </div>
        <!-- search-->
        <div class="page-search mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass fa-xl light-gray"></i></span>
                </div>
                <input type="text" id="input_search" class="form-control" placeholder="Search Users" aria-label="Search" aria-describedby="basic-addon1">
            </div>
            <div class="sorting mr-4">
                <label>Sort by :</label>
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
                        <th>{{__('label.transaction_id')}}</th>
                        <th>{{__('label.price')}}</th>
                        <th>{{__('label.description')}}</th>
                        <th>{{__('label.date')}}</th>
                        <th>{{__('label.action')}}</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr class="bg-table">
                        <td colspan="9" class="text-center"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    $(document).ready(function() {

        var table = $('#datatable').DataTable({
            ...datatabledefault,
            ajax: {
                url: "{{ route('liveevent.user.index', $liveevent_id) }}",
                data: function(d) {
                    d.input_type = $('#input_type').val();
                    d.input_search = $('#input_search').val();
                },
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'user',
                    name: 'user',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data.full_name ?? "-";
                        } else {
                            return "-";
                        }
                    },
                },
                {
                    data: 'user',
                    name: 'user',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data.email ?? "-";
                        } else {
                            return "-";
                        }
                    },
                },
                {
                    data: 'user',
                    name: 'user',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data.mobile_number ?? "-";
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
                        return data;
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
                    .column(5)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer by showing the total with the reference of the column index 
                $(api.column(1).footer()).html("Total Price =&nbsp" + Total);
            },
        });
        $('#input_type').change(function() {
            table.draw();
        });
        $('#input_search').keyup(function() {
            table.draw();
        });
    });
</script>
@endsection