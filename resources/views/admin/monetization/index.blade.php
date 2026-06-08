@extends('admin.layout.page-app')
@section('page_title', 'Monetization Applications')
@section('tab_title', 'Monetization Applications')

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <div class="border-bottom row mb-3">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Monetization Applications</li>
                </ol>
            </div>
        </div>

        <div class="card custom-border-card">
            <h5 class="card-header">Monetization Applications</h5>
            <div class="card-body">
                <div class="form-row align-items-end mb-3">
                    <div class="form-group col-md-3">
                        <label>Status Filter</label>
                        <select id="status_filter" class="form-control" onchange="$('#monoTable').DataTable().draw();">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="monoTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Artist Name</th>
                                <th>Email</th>
                                <th title="At time of application">📸 Plays</th>
                                <th title="Current live value">🟢 Plays Now</th>
                                <th title="At time of application">📸 Followers</th>
                                <th title="Current live value">🟢 Followers Now</th>
                                <th title="At time of application">📸 Monthly</th>
                                <th title="Current live value">🟢 Monthly Now</th>
                                <th title="At time of application">📸 Tracks</th>
                                <th title="Current live value">🟢 Tracks Now</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Approve/Reject Modal --}}
<div class="modal fade" id="monoActionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="monoActionTitle">Action</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="monoActionId">
                <input type="hidden" id="monoActionType">
                <div class="form-group" id="monoNoteGroup">
                    <label>Admin Note <span class="text-danger">*</span></label>
                    <textarea id="monoAdminNote" class="form-control" rows="3"></textarea>
                </div>
                <button type="button" class="btn btn-default" onclick="confirmMonoAction()">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    var table = $('#monoTable').DataTable({
        ...dataTableDefaults,
        ajax: {
            url: "{{ route('admin.monetization.index') }}",
            data: function(d) {
                d.status_filter = $('#status_filter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'artist_name', name: 'artist_name' },
            { data: 'user_email', name: 'user_email' },
            { data: 'snapshot_plays', name: 'snapshot_plays' },
            { data: 'current_plays', name: 'current_plays', orderable: false },
            { data: 'snapshot_followers', name: 'snapshot_followers' },
            { data: 'current_followers', name: 'current_followers', orderable: false },
            { data: 'snapshot_monthly_plays', name: 'snapshot_monthly_plays' },
            { data: 'current_monthly_plays', name: 'current_monthly_plays', orderable: false },
            { data: 'snapshot_tracks', name: 'snapshot_tracks' },
            { data: 'current_tracks', name: 'current_tracks', orderable: false },
            { data: 'applied_at', name: 'applied_at' },
            { data: 'status_badge', name: 'status_badge', orderable: false },
            { data: 'action', name: 'action', orderable: false },
        ]
    });

    // Approve
    $(document).on('click', '.approve-mono', function() {
        $('#monoActionId').val($(this).data('id'));
        $('#monoActionType').val('approve');
        $('#monoActionTitle').text('Approve Monetization');
        $('#monoNoteGroup').hide();
        $('#monoActionModal').modal('show');
    });

    // Reject
    $(document).on('click', '.reject-mono', function() {
        $('#monoActionId').val($(this).data('id'));
        $('#monoActionType').val('reject');
        $('#monoActionTitle').text('Reject Monetization');
        $('#monoNoteGroup').show();
        $('#monoActionModal').modal('show');
    });
});

function confirmMonoAction() {
    var id = $('#monoActionId').val();
    var action = $('#monoActionType').val();
    var note = $('#monoAdminNote').val();
    var url = action === 'approve' ? "{{ route('admin.monetization.approve') }}" : "{{ route('admin.monetization.reject') }}";
    var data = { application_id: id, _token: "{{ csrf_token() }}" };
    if (action === 'reject') {
        if (!note.trim()) { toastr.error('Please provide a reason for rejection.'); return; }
        data.admin_note = note;
    }
    $('#dvloader').show();
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        success: function(resp) {
            $('#dvloader').hide();
            $('#monoActionModal').modal('hide');
            $('#monoAdminNote').val('');
            get_responce_message(resp);
            $('#monoTable').DataTable().ajax.reload();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#dvloader').hide();
            toastr.error(errorThrown, textStatus);
        }
    });
}
</script>
@endsection
