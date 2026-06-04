@extends('admin.layout.page-app')
@section('page_title', __('label.package'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm"> {{__('label.package')}} </h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.package')}}</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end mb-3">
                <a href="{{ route('package.create') }}" class="btn btn-default mw-120">{{__('label.add_package')}}</a>
            </div>
        </div>
        <!-- search -->
        <div class="page-search mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass fa-xl light-gray"></i></span>
                </div>
                <input type="text" id="input_search" class="form-control" placeholder="{{__('label.search_package')}}" aria-label="Search" aria-describedby="basic-addon1">
            </div>
        </div>
        <!-- table  -->
        <div class="table-responsive table">
            <table class="table table-striped text-center table-bordered" id="datatable">
                <thead>
                    <tr class="bg-table">
                        <th>{{__('label.#')}}</th>
                        <th>{{__('label.image')}}</th>
                        <th>{{__('label.name')}}</th>
                        <th>{{__('label.price')}}</th>
                        <th>{{__('label.duration')}}</th>
                        <th>{{__('label.color')}}</th>
                        <th>{{__('label.device_limit')}}</th>
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
                url: "{{ route('package.index') }}",
                data: function(d) {
                    d.input_search = $('#input_search').val();
                },
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'image',
                    name: 'image',
                    orderable: false,
                    searchable: false,
                    "render": function(data, type, full, meta) {
                        return "<a href='" + data + "' target='_blank' title='Watch'><img src='" + data + "' class='img-thumbnail size-55'></a>";
                    },
                },
                {
                    data: 'name',
                    name: 'name',
                    render: function(data) {
                        return data ? data : "-";
                    }
                },
                {
                    data: 'price',
                    name: 'price',
                    render: function(data) {
                        return data ? data : 0;
                    }
                },
                {
                    data: 'time',
                    name: 'time',
                    render: function(data, type, row, meta) {
                        return row.time + " " + row.type;
                    }
                },
                {
                    data: 'color',
                    name: 'color',
                    render: function(data, type, row, meta) {
                        return data ? "<p class='color-box'><span style='background-color:" + data + "'></span>" + data + "</p>" : '-';

                    }
                },
                {
                    data: 'device_limit',
                    name: 'device_limit',
                    render: function(data) {
                        return data ? data : 0;
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