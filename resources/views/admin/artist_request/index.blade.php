@extends('admin.layout.page-app')
@section('page_title', __('label.artist_requests'))
@section('tab_title', __('label.artist_requests'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">{{__('label.artist_requests')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.artist_requests')}}</li>
                    </ol>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <div class="page-search mb-3">
                    <div class="input-group" title="Search">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass fa-xl light-gray"></i></span>
                        </div>
                        <input type="text" id="input_search" class="form-control" placeholder="{{__('label.search_by_artist_name')}}" aria-label="Search">
                    </div>
                    <div class="input-group ml-2" title="{{__('label.filter_by_status')}}">
                        <select id="status_filter" class="form-control" style="max-width: 200px;">
                            <option value="">{{__('label.all_status')}}</option>
                            <option value="pending">{{__('label.pending')}}</option>
                            <option value="approved">{{__('label.approved')}}</option>
                            <option value="rejected">{{__('label.rejected')}}</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive table">
                    <table class="table table-striped text-center table-bordered" id="datatable">
                        <thead>
                            <tr style="background: #F9FAFF;">
                                <th> {{__('label.#')}} </th>
                                <th> {{__('label.user')}} </th>
                                <th> {{__('label.artist_name')}} </th>
                                <th> {{__('label.bio')}} </th>
                                <th> {{__('label.status')}} </th>
                                <th> {{__('label.action')}} </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="RejectModal" tabindex="-1" data-backdrop="static" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('label.reject')}} {{__('label.artist_request')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="reject_form">
                        <input type="hidden" name="request_id" id="reject_request_id">
                        <div class="form-group">
                            <label>{{__('label.admin_note')}}</label>
                            <textarea name="admin_note" id="reject_admin_note" class="form-control" rows="3" placeholder="Optional note..."></textarea>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-danger" onclick="reject_request()">{{__('label.reject')}}</button>
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
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
                "url": "{{ route('admin.artist-requests.index') }}",
                "data": function(d) {
                    d.input_search = $('#input_search').val();
                    d.status_filter = $('#status_filter').val();
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_info', name: 'user_info' },
                { data: 'artist_name', name: 'artist_name' },
                { data: 'bio', name: 'bio' },
                { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        $('#input_search, #status_filter').on('change keyup', function() {
            table.draw();
        });
    });

    $(document).on('click', '.approve_request', function() {
        var requestId = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.artist-requests.approve') }}",
            data: {
                request_id: requestId,
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                if (res.status == 200) {
                    toastr.success(res.success);
                    $('#datatable').DataTable().draw();
                } else {
                    toastr.error(res.errors);
                }
            },
            error: function() {
                toastr.error('Something went wrong');
            }
        });
    });

    $(document).on('click', '.reject_request', function() {
        $('#reject_request_id').val($(this).data('id'));
        $('#RejectModal').modal('show');
    });

    function reject_request() {
        var requestId = $('#reject_request_id').val();
        var adminNote = $('#reject_admin_note').val();
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.artist-requests.reject') }}",
            data: {
                request_id: requestId,
                admin_note: adminNote,
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                if (res.status == 200) {
                    toastr.success(res.success);
                    $('#RejectModal').modal('hide');
                    $('#datatable').DataTable().draw();
                } else {
                    toastr.error(res.errors);
                }
            },
            error: function() {
                toastr.error('Something went wrong');
            }
        });
    }
</script>
@endsection
