@extends('admin.layout.app')
@section('page_title', 'KYC Requests')
@section('tab_title', 'KYC Requests')

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <div class="card custom-border-card">
            <h5 class="card-header">KYC Verification Requests</h5>
            <div class="card-body">
                <div class="form-row align-items-end mb-3">
                    <div class="form-group col-md-3">
                        <label>Status Filter</label>
                        <select id="status_filter" class="form-control" onchange="$('#kycTable').DataTable().draw();">
                            <option value="">All</option>
                            <option value="submitted">Submitted</option>
                            <option value="under_review">Under Review</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="kycTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Artist Name</th>
                                <th>Full Name</th>
                                <th>ID Type</th>
                                <th>Payment Method</th>
                                <th>Submitted Date</th>
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

{{-- View Detail Modal --}}
<div class="modal fade" id="kycViewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">KYC Details</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="kycViewBody">
            </div>
        </div>
    </div>
</div>

{{-- Approve/Reject Note Modal --}}
<div class="modal fade" id="kycActionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kycActionTitle">Action</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="kycActionId">
                <input type="hidden" id="kycActionType">
                <div class="form-group" id="kycNoteGroup">
                    <label>Admin Note <span class="text-danger">*</span></label>
                    <textarea id="kycAdminNote" class="form-control" rows="3"></textarea>
                </div>
                <button type="button" class="btn btn-default" onclick="confirmKycAction()">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    var table = $('#kycTable').DataTable({
        ...dataTableDefaults,
        ajax: {
            url: "{{ route('admin.kyc.index') }}",
            data: function(d) {
                d.status_filter = $('#status_filter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'artist_name', name: 'artist_name' },
            { data: 'full_name', name: 'full_name' },
            { data: 'id_type', name: 'id_type' },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'submitted_date', name: 'submitted_date' },
            { data: 'status_badge', name: 'status_badge', orderable: false },
            { data: 'action', name: 'action', orderable: false },
        ]
    });

    // View
    $(document).on('click', '.view-kyc', function() {
        var id = $(this).data('id');
        $.ajax({
            url: "{{ route('admin.kyc.view', '') }}/" + id,
            type: 'GET',
            success: function(resp) {
                if (resp.status == 200) {
                    var d = resp.data;
                    var html = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Identity</h6>
                                <p><strong>Name:</strong> ${d.legal_first_name} ${d.legal_last_name}</p>
                                <p><strong>DOB:</strong> ${d.date_of_birth}</p>
                                <p><strong>Nationality:</strong> ${d.nationality}</p>
                                <p><strong>ID Type:</strong> ${d.id_type}</p>
                                <p><strong>ID Number:</strong> ${d.id_number}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Address</h6>
                                <p><strong>Address:</strong> ${d.address}</p>
                                <p><strong>City:</strong> ${d.city}</p>
                                <p><strong>Country:</strong> ${d.country}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Payment</h6>
                                <p><strong>Method:</strong> ${d.payment_method}</p>
                                <p><strong>Details:</strong> ${JSON.stringify(d.payment_details_display)}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Status</h6>
                                <p><strong>Status:</strong> ${d.status}</p>
                                ${d.admin_note ? '<p><strong>Admin Note:</strong> ' + d.admin_note + '</p>' : ''}
                                ${d.reviewed_at ? '<p><strong>Reviewed:</strong> ' + d.reviewed_at + '</p>' : ''}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <h6>ID Front</h6>
                                <a href="${d.id_front_img_url}" target="_blank">
                                    <img src="${d.id_front_img_url}" style="max-width:100%;max-height:200px;border:1px solid #ddd;border-radius:8px;">
                                </a>
                            </div>
                            <div class="col-md-6 text-center">
                                <h6>ID Back</h6>
                                <a href="${d.id_back_img_url}" target="_blank">
                                    <img src="${d.id_back_img_url}" style="max-width:100%;max-height:200px;border:1px solid #ddd;border-radius:8px;">
                                </a>
                            </div>
                        </div>
                    `;
                    $('#kycViewBody').html(html);
                    $('#kycViewModal').modal('show');
                }
            }
        });
    });

    // Approve
    $(document).on('click', '.approve-kyc', function() {
        $('#kycActionId').val($(this).data('id'));
        $('#kycActionType').val('approve');
        $('#kycActionTitle').text('Approve KYC');
        $('#kycNoteGroup').hide();
        $('#kycActionModal').modal('show');
    });

    // Reject
    $(document).on('click', '.reject-kyc', function() {
        $('#kycActionId').val($(this).data('id'));
        $('#kycActionType').val('reject');
        $('#kycActionTitle').text('Reject KYC');
        $('#kycNoteGroup').show();
        $('#kycActionModal').modal('show');
    });
});

function confirmKycAction() {
    var id = $('#kycActionId').val();
    var action = $('#kycActionType').val();
    var note = $('#kycAdminNote').val();
    var url = action === 'approve' ? "{{ route('admin.kyc.approve') }}" : "{{ route('admin.kyc.reject') }}";
    var data = { kyc_id: id, _token: "{{ csrf_token() }}" };
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
            $('#kycActionModal').modal('hide');
            $('#kycAdminNote').val('');
            get_responce_message(resp);
            $('#kycTable').DataTable().ajax.reload();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#dvloader').hide();
            toastr.error(errorThrown, textStatus);
        }
    });
}
</script>
@endsection
