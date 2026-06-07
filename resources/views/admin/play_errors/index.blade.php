@extends('admin.layout.app')
@section('css')
<link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Play Errors</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Play Errors</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="play-error-table" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Content ID</th>
                                <th>Type</th>
                                <th>URL</th>
                                <th>Error</th>
                                <th>HTTP</th>
                                <th>Date</th>
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
@push('scripts')
<script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#play-error-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.play-errors') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'user_info', name: 'user_info' },
            { data: 'content_id', name: 'content_id' },
            { data: 'content_type', name: 'content_type' },
            { data: 'url', name: 'url', render: function(d) { return d ? '<code>' + d.substring(0, 60) + '...</code>' : '-'; } },
            { data: 'error_message', name: 'error_message', render: function(d) { return d ? d.substring(0, 80) : '-'; } },
            { data: 'http_status_badge', name: 'http_status', orderable: false },
            { data: 'created_at_fmt', name: 'created_at' },
        ],
        order: [[7, 'desc']],
    });
});
</script>
@endpush
