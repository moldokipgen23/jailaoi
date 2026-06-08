@extends('admin.layout.page-app')
@section('page_title', __('label.withdrawals'))
@section('tab_title', __('label.withdrawals'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">{{ __('label.artist_withdrawals') }}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('label.dashboard') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('label.artist_withdrawals') }}</li>
                    </ol>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <div class="page-search mb-3">
                    <div class="input-group ml-2" title="{{ __('label.filter_by_status') }}">
                        <select id="status_filter" class="form-control" style="max-width: 200px;">
                            <option value="">{{ __('label.all_status') }}</option>
                            <option value="pending">{{ __('label.pending') }}</option>
                            <option value="approved">{{ __('label.approved') }}</option>
                            <option value="rejected">{{ __('label.rejected') }}</option>
                            <option value="paid">{{ __('label.paid') }}</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive table">
                    <table class="table table-striped text-center table-bordered" id="datatable">
                        <thead>
                            <tr style="background: #F9FAFF;">
                                <th>{{ __('label.#') }}</th>
                                <th>{{ __('label.artist') }}</th>
                                <th>{{ __('label.user') }}</th>
                                <th>{{ __('label.amount') }}</th>
                                <th>{{ __('label.payment_method') }}</th>
                                <th>{{ __('label.payment_details') }}</th>
                                <th>{{ __('label.status') }}</th>
                                <th>{{ __('label.action') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- JAILAOI: Withdrawal Detail View Modal --}}
    <div class="modal fade" id="withdrawalDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Withdrawal Detail</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="withdrawalDetailBody">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="NoteModal" tabindex="-1" data-backdrop="static" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="NoteModalTitle">{{ __('label.admin_note') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="note_form">
                        <input type="hidden" id="modal_request_id">
                        <input type="hidden" id="modal_action">
                        <div class="form-group">
                            <label>{{ __('label.admin_note') }}</label>
                            <textarea id="modal_admin_note" class="form-control" rows="3" placeholder="Optional note..."></textarea>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-primary" onclick="submitWithdrawalAction()">{{ __('label.submit') }}</button>
                        </div>
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
        responsive: true,
        autoWidth: false,
        searching: false,
        processing: true,
        serverSide: true,
        language: {
            paginate: {
                previous: "<i class='fa-solid fa-chevron-left'></i>",
                next: "<i class='fa-solid fa-chevron-right'></i>"
            }
        },
        ajax: {
            url: "{{ route('admin.withdrawals.index') }}",
            data: function(d) {
                d.status_filter = $('#status_filter').val();
            },
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'artist_name', name: 'artist_name' },
            { data: 'user_info', name: 'user_info' },
            { data: 'amount_fmt', name: 'amount_fmt' },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'payment_details', name: 'payment_details' },
            { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    });

    $('#status_filter').on('change', function() { table.draw(); });
});

$(document).on('click', '.approve_withdrawal', function() {
    $('#modal_request_id').val($(this).data('id'));
    $('#modal_action').val('approve');
    $('#NoteModalTitle').text("{{ __('label.approve') }}");
    $('#NoteModal').modal('show');
});
$(document).on('click', '.reject_withdrawal', function() {
    $('#modal_request_id').val($(this).data('id'));
    $('#modal_action').val('reject');
    $('#NoteModalTitle').text("{{ __('label.reject') }}");
    $('#NoteModal').modal('show');
});
$(document).on('click', '.mark_paid', function() {
    $('#modal_request_id').val($(this).data('id'));
    $('#modal_action').val('mark-paid');
    $('#NoteModalTitle').text("{{ __('label.mark_paid') }}");
    $('#NoteModal').modal('show');
});

// JAILAOI: Withdrawal detail view
$(document).on('click', '.view_withdrawal', function() {
    var id = $(this).data('id');
    $.ajax({
        url: "{{ route('admin.withdrawals.show', ':id') }}".replace(':id', id),
        type: 'GET',
        success: function(resp) {
            if (resp.status == 200) {
                var d = resp.data;
                var w = d.withdrawal;
                var kyc = d.kyc;
                var pd = d.payment_details;

                // Payment details formatting
                var pdHtml = '-';
                if (pd && typeof pd === 'object') {
                    var rows = Object.entries(pd).map(function(e) {
                        return '<strong>' + e[0].replace(/_/g, ' ') + ':</strong> ' + e[1];
                    }).join('<br>');
                    pdHtml = rows;
                }

                // KYC info
                var kycHtml = kyc ? '<p><strong>Name:</strong> ' + (kyc.legal_first_name || '') + ' ' + (kyc.legal_last_name || '') + '</p><p><strong>ID Type:</strong> ' + (kyc.id_type || '-') + '</p><p><strong>ID Number:</strong> ' + (kyc.id_number || '-') + '</p>' : '<p class="text-muted">No approved KYC record found for this user.</p>';

                // ID images
                var idImgHtml = '';
                if (d.id_front_img_url && d.id_front_img_url.indexOf('placeholder') === -1) {
                    idImgHtml += '<div class="col-md-6 text-center"><h6>ID Front</h6><a href="' + d.id_front_img_url + '" target="_blank"><img src="' + d.id_front_img_url + '" style="max-width:100%;max-height:180px;border:1px solid #ddd;border-radius:8px;"></a></div>';
                }
                if (d.id_back_img_url && d.id_back_img_url.indexOf('placeholder') === -1) {
                    idImgHtml += '<div class="col-md-6 text-center"><h6>ID Back</h6><a href="' + d.id_back_img_url + '" target="_blank"><img src="' + d.id_back_img_url + '" style="max-width:100%;max-height:180px;border:1px solid #ddd;border-radius:8px;"></a></div>';
                }

                var html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Request Info</h6>
                            <p><strong>ID:</strong> ${w.id}</p>
                            <p><strong>Artist:</strong> ${w.artist ? w.artist.name : '-'}</p>
                            <p><strong>Amount:</strong> ${parseFloat(w.amount).toFixed(2)}</p>
                            <p><strong>Payment Method:</strong> ${w.payment_method || '-'}</p>
                            <p><strong>Payment Details:</strong> ${w.payment_details || '-'}</p>
                            <p><strong>Status:</strong> ${w.status}</p>
                            <p><strong>Created:</strong> ${w.created_at ? new Date(w.created_at).toLocaleString() : '-'}</p>
                            ${w.processed_at ? '<p><strong>Processed:</strong> ' + new Date(w.processed_at).toLocaleString() + '</p>' : ''}
                            ${w.paid_at ? '<p><strong>Paid At:</strong> ' + new Date(w.paid_at).toLocaleString() + '</p>' : ''}
                            ${w.admin_note ? '<p><strong>Admin Note:</strong> ' + w.admin_note + '</p>' : ''}
                            ${w.payment_note ? '<p><strong>Payment Note:</strong> ' + w.payment_note + '</p>' : ''}
                        </div>
                        <div class="col-md-6">
                            <h6>User</h6>
                            <p><strong>Name:</strong> ${w.user ? (w.user.full_name || w.user.name || '-') : '-'}</p>
                            <p><strong>Email:</strong> ${w.user ? (w.user.email || '-') : '-'}</p>
                            <hr>
                            <h6>Payment Details (from KYC)</h6>
                            <p>${pdHtml}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>KYC Info</h6>
                            ${kycHtml}
                        </div>
                        <div class="col-md-6">
                            <h6>Artist Earnings Stats</h6>
                            <p><strong>Total Earned:</strong> ${parseFloat(d.total_earned).toFixed(2)}</p>
                            <p><strong>Paid Out:</strong> ${parseFloat(d.paid_out).toFixed(2)}</p>
                            <p><strong>Available Balance:</strong> ${parseFloat(d.available).toFixed(2)}</p>
                        </div>
                    </div>
                    ${idImgHtml ? '<hr><div class="row">' + idImgHtml + '</div>' : ''}
                `;
                $('#withdrawalDetailBody').html(html);
                $('#withdrawalDetailModal').modal('show');
            }
        }
    });
});

function submitWithdrawalAction() {
    var id = $('#modal_request_id').val();
    var action = $('#modal_action').val();
    var note = $('#modal_admin_note').val();
    var urls = {
        'approve': "{{ route('admin.withdrawals.approve') }}",
        'reject': "{{ route('admin.withdrawals.reject') }}",
        'mark-paid': "{{ route('admin.withdrawals.mark-paid') }}",
    };
    $.ajax({
        type: 'POST',
        url: urls[action],
        data: { request_id: id, admin_note: note, _token: "{{ csrf_token() }}" },
        success: function(res) {
            if (res.status == 200) {
                toastr.success(res.success);
                $('#NoteModal').modal('hide');
                $('#modal_admin_note').val('');
                $('#datatable').DataTable().draw();
            } else {
                toastr.error(res.errors);
            }
        },
        error: function() { toastr.error('Something went wrong'); }
    });
}
</script>
@endsection
