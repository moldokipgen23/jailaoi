@extends('admin.layout.page-app')
@section('page_title', __('label.payment'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.payment')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.payment')}}</li>
                </ol>
            </div>
        </div>
        <!-- search -->
        <div class="page-search mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass fa-xl light-gray"></i></span>
                </div>
                <input type="text" id="input_search" class="form-control" placeholder="{{__('label.search_payment')}}" aria-label="Search" aria-describedby="basic-addon1">
            </div>
        </div>
        <!-- table  -->
        <div class="table-responsive table">
            <table class="table table-striped text-center table-bordered" id="datatable">
                <thead>
                    <tr class="bg-table">
                        <th>{{__('label.#')}}</th>
                        <th>{{__('label.name')}}</th>
                        <th>{{__('label.status')}}</th>
                        <th>{{__('label.payment_environment')}}</th>
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
<script>
    // Sidebar Scroll Down
    sidebar_down($(document).height());

    $(document).ready(function() {

        var table = $('#datatable').DataTable({
            ...datatabledefault,
            ajax: {
                url: "{{ route('payment.index') }}",
                data: function(d) {
                    d.input_search = $('#input_search').val();
                },
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    visible: false
                },
                {
                    data: 'name',
                    name: 'name',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    }
                },
                {
                    data: 'visibility',
                    name: 'visibility',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, full, meta) {
                        if (data == 1) {
                            return "Active";
                        } else {
                            return "In Active";
                        }
                    }
                },
                {
                    data: 'is_live',
                    name: 'is_live',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, full, meta) {
                        if (data == 1) {
                            return "Live";
                        } else {
                            return "Sandbox";
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
        });

        $('#input_search').keyup(function() {
            table.draw();
        });
    });
</script>
@endsection