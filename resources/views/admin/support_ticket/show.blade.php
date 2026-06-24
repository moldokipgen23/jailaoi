@extends('admin.layout.page-app')
@section('page_title', 'Ticket #' . $ticket->id)

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.support-tickets.index') }}">Support Tickets</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ticket #{{ $ticket->id }}</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-default mw-120 mb-3">Back to List</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card custom-border-card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $ticket->subject }}</h5>
                        <div>
                            @php
                                $statusMap = ['open' => 'warning', 'in_progress' => 'primary', 'resolved' => 'success'];
                                $statusLabels = ['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved'];
                            @endphp
                            <span class="badge badge-{{ $statusMap[$ticket->status] ?? 'secondary' }}" style="font-size:14px;">
                                {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-3">
                            <tr>
                                <td style="width:120px;color:#888;">User</td>
                                <td><strong>{{ $ticket->user ? ($ticket->user->full_name ?: $ticket->user->user_name) : 'Deleted User' }}</strong></td>
                            </tr>
                            <tr>
                                <td style="color:#888;">Email</td>
                                <td>{{ $ticket->user->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="color:#888;">Type</td>
                                <td>
                                    @php
                                        $typeLabels = [
                                            'account' => 'Account & Login',
                                            'billing' => 'Subscription & Billing',
                                            'playback' => 'Playback / Technical',
                                            'content' => 'Artist / Content Report',
                                            'refund' => 'Refund Request',
                                            'other' => 'Other',
                                        ];
                                    @endphp
                                    {{ $typeLabels[$ticket->type] ?? ucfirst($ticket->type) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#888;">Submitted</td>
                                <td>{{ $ticket->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            @if($ticket->replied_at)
                            <tr>
                                <td style="color:#888;">Replied</td>
                                <td>{{ $ticket->replied_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            @endif
                        </table>

                        <hr>
                        <h6 class="mb-2">User Message:</h6>
                        <div style="background:#f8f9fa;border-radius:8px;padding:16px;color:#333;line-height:1.7;white-space:pre-wrap;">{{ $ticket->message }}</div>

                        @if($ticket->admin_reply)
                        <hr>
                        <h6 class="mb-2">Admin Reply:</h6>
                        <div style="background:#f0f4ff;border-radius:8px;padding:16px;color:#333;line-height:1.7;white-space:pre-wrap;">{{ $ticket->admin_reply }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                @if($ticket->status !== 'resolved')
                <div class="card custom-border-card mb-3">
                    <div class="card-header"><h5 class="mb-0">Reply</h5></div>
                    <div class="card-body">
                        <form action="{{ route('admin.support-tickets.reply', $ticket->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <textarea name="admin_reply" class="form-control" rows="6" placeholder="Type your reply..." required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Status after reply:</label>
                                <select name="status" class="form-control">
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-default btn-block">Send Reply</button>
                        </form>
                    </div>
                </div>
                @endif

                <div class="card custom-border-card">
                    <div class="card-header"><h5 class="mb-0">Change Status</h5></div>
                    <div class="card-body">
                        <a href="{{ route('admin.support-tickets.status', [$ticket->id, 'open']) }}" class="btn btn-sm btn-warning btn-block mb-2 {{ $ticket->status === 'open' ? 'disabled' : '' }}">Mark Open</a>
                        <a href="{{ route('admin.support-tickets.status', [$ticket->id, 'in_progress']) }}" class="btn btn-sm btn-primary btn-block mb-2 {{ $ticket->status === 'in_progress' ? 'disabled' : '' }}">Mark In Progress</a>
                        <a href="{{ route('admin.support-tickets.status', [$ticket->id, 'resolved']) }}" class="btn btn-sm btn-success btn-block {{ $ticket->status === 'resolved' ? 'disabled' : '' }}">Mark Resolved</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
