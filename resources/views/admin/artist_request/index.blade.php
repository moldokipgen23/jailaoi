@extends('admin.layout.page-app')
@section('page_title', 'Artist Requests')

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">Artist Requests</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Artist Requests</li>
                    </ol>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <div class="page-search mb-3">
                    <div class="input-group" title="Search">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass fa-xl light-gray"></i></span>
                        </div>
                        <input type="text" id="input_search" class="form-control" placeholder="Search by artist name" aria-label="Search">
                    </div>
                    <div class="input-group ml-2" title="Filter by status">
                        <select id="status_filter" class="form-control" style="max-width: 200px;">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive table">
                    <table class="table table-striped text-center table-bordered" id="datatable">
                        <thead>
                            <tr style="background: #F9FAFF;">
                                <th> # </th>
                                <th> User </th>
                                <th> Artist Name </th>
                                <th> Bio </th>
                                <th> Status </th>
                                <th> Action </th>
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
    <script>
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                dom: "<'top'f>rt<'row'<'col-2'i><'col-1'l><'col-9'p>>",
                "responsive": true,
                "autoWidth": false,
                "searching": false,
                processing: true,
                serverSide: true,
                language: {
                    paginate: {
                        previous: "<i class='fa-solid fa-chevron-left'></i>",
                        next: "<i class='fa-solid fa-chevron-right'></i>"
                    }
                },
                "ajax": {
                    "url": "{{ route('artist-requests.index') }}",
                    "data": function(d) {
                        d.input_search = $('#input_search').val();
                        d.status_filter = $('#status_filter').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'user_info', name: 'user_info' },
                    { data: 'artist_name', name: 'artist_name' },
                    { 
                        data: 'bio', 
                        name: 'bio',
                        "render": function(data, type, full, meta) {
                            if (data) {
                                return (data.length > 75) ? data.substring(0, 75) + '...' : data;
                            }
                            return "-";
                        }
                    },
                    { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
            });

            $('#input_search').keyup(function() { table.draw(); });
            $('#status_filter').change(function() { table.draw(); });
        });

        $(document).on("click", ".approve_request", function() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if (Check_Admin != 1) {
                toastr.error('You have no right to approve requests.');
                return;
            }
            if (!confirm('Are you sure you want to approve this artist request?')) return;

            $("#dvloader").show();
            var request_id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: '{{ route("artist-requests.approve") }}',
                data: {
                    request_id: request_id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(resp) {
                    $("#dvloader").hide();
                    if (resp.status == 200) {
                        toastr.success(resp.success);
                        $('#datatable').DataTable().draw();
                    } else {
                        toastr.error(resp.errors);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        });

        $(document).on("click", ".reject_request", function() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if (Check_Admin != 1) {
                toastr.error('You have no right to reject requests.');
                return;
            }
            var note = prompt('Enter rejection reason (optional):');
            if (note === null) return;

            $("#dvloader").show();
            var request_id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: '{{ route("artist-requests.reject") }}',
                data: {
                    request_id: request_id,
                    admin_note: note || '',
                    _token: '{{ csrf_token() }}'
                },
                success: function(resp) {
                    $("#dvloader").hide();
                    if (resp.status == 200) {
                        toastr.success(resp.success);
                        $('#datatable').DataTable().draw();
                    } else {
                        toastr.error(resp.errors);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        });
    </script>
@endsection
