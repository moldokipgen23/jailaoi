@extends('admin.layout.page-app')
@section('page_title', 'Support Tickets')

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Support Tickets</li>
                </ol>
            </div>
        </div>

        <div class="card custom-border-card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <select id="status-filter" class="form-control" onchange="window.location='{{ route('admin.support-tickets.index') }}?status='+this.value">
                            <option value="">All Statuses</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                </div>

                <table class="table table-striped text-center table-bordered" id="datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
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
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            ajax: {
                url: '{{ route("admin.support-tickets.index") }}',
                data: function(d) {
                    d.status = $('#status-filter').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                { data: 'user_name', name: 'user_name' },
                { data: 'type_label', name: 'type' },
                { data: 'subject', name: 'subject' },
                { data: 'status_badge', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', searchable: false },
            ]
        });

        $('#status-filter').on('change', function() {
            table.ajax.reload();
        });
    });
</script>
@endsection
