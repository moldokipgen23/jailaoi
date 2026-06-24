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
                    <li class="breadcrumb-item active">Ticket #{{ $ticket->id }}</li>
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
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="row">
            {{-- LEFT: Ticket info + Thread --}}
            <div class="col-md-8">

                {{-- Ticket info card --}}
                <div class="card custom-border-card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $ticket->subject }}</h5>
                        @php
                            $statusMap    = ['open' => 'warning', 'in_progress' => 'primary', 'resolved' => 'success', 'closed' => 'secondary'];
                            $statusLabels = ['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'];
                            $typeLabels   = [
                                'account'  => 'Account & Login',
                                'billing'  => 'Subscription & Billing',
                                'playback' => 'Playback / Technical',
                                'content'  => 'Artist / Content Report',
                                'refund'   => 'Refund Request',
                                'other'    => 'Other',
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusMap[$ticket->status] ?? 'secondary' }}" style="font-size:13px;padding:6px 12px;">
                            {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                        </span>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td style="width:110px;color:#888;">User</td>
                                <td><strong>{{ $ticket->user ? ($ticket->user->full_name ?: $ticket->user->user_name) : 'Deleted User' }}</strong></td>
                            </tr>
                            <tr>
                                <td style="color:#888;">Email</td>
                                <td>{{ $ticket->user->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="color:#888;">Type</td>
                                <td>{{ $typeLabels[$ticket->type] ?? ucfirst($ticket->type) }}</td>
                            </tr>
                            <tr>
                                <td style="color:#888;">Opened</td>
                                <td>{{ $ticket->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Conversation thread --}}
                <div class="card custom-border-card mb-3">
                    <div class="card-header"><h5 class="mb-0">Conversation</h5></div>
                    <div class="card-body" style="max-height:520px;overflow-y:auto;" id="thread-container">
                        @forelse($ticket->replies as $reply)
                            @if($reply->sender_type === 'user')
                            {{-- User bubble (left) --}}
                            <div class="d-flex mb-3">
                                <div style="width:36px;height:36px;border-radius:50%;background:#E01E75;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">
                                    {{ strtoupper(substr($ticket->user->user_name ?? 'U', 0, 1)) }}
                                </div>
                                <div style="margin-left:10px;max-width:80%;">
                                    <div style="background:#f0f0f0;border-radius:0 12px 12px 12px;padding:12px 16px;">
                                        <p style="margin:0;color:#333;line-height:1.6;white-space:pre-wrap;font-size:14px;">{{ $reply->message }}</p>
                                    </div>
                                    <small style="color:#aaa;font-size:11px;margin-left:4px;">{{ $ticket->user ? ($ticket->user->full_name ?: $ticket->user->user_name) : 'User' }} &bull; {{ $reply->created_at->format('d M, h:i A') }}</small>
                                </div>
                            </div>
                            @else
                            {{-- Admin bubble (right) --}}
                            <div class="d-flex mb-3 justify-content-end">
                                <div style="margin-right:10px;max-width:80%;text-align:right;">
                                    <div style="background:#E01E75;border-radius:12px 0 12px 12px;padding:12px 16px;display:inline-block;text-align:left;">
                                        <p style="margin:0;color:#fff;line-height:1.6;white-space:pre-wrap;font-size:14px;">{{ $reply->message }}</p>
                                    </div>
                                    <div><small style="color:#aaa;font-size:11px;margin-right:4px;">Support Team &bull; {{ $reply->created_at->format('d M, h:i A') }}</small></div>
                                </div>
                                <div style="width:36px;height:36px;border-radius:50%;background:#333;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">A</div>
                            </div>
                            @endif
                        @empty
                            <p class="text-muted text-center py-3">No messages yet.</p>
                        @endforelse
                    </div>

                    {{-- Reply form --}}
                    @if($ticket->status !== 'closed')
                    <div class="card-footer" style="background:#fafafa;">
                        <form action="{{ route('admin.support-tickets.reply', $ticket->id) }}" method="POST">
                            @csrf
                            <div class="form-row align-items-end">
                                <div class="col">
                                    <textarea name="message" class="form-control" rows="3" placeholder="Type your reply..." required></textarea>
                                </div>
                                <div class="col-auto">
                                    <div class="mb-2">
                                        <select name="status" class="form-control form-control-sm">
                                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-default btn-block">Send</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="card-footer text-center text-muted">
                        This ticket is closed. <a href="{{ route('admin.support-tickets.status', [$ticket->id, 'open']) }}">Reopen it</a> to reply.
                    </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT: Actions --}}
            <div class="col-md-4">
                <div class="card custom-border-card mb-3">
                    <div class="card-header"><h5 class="mb-0">Ticket Actions</h5></div>
                    <div class="card-body">
                        <p class="text-muted mb-2" style="font-size:12px;text-transform:uppercase;letter-spacing:1px;">Change Status</p>
                        <a href="{{ route('admin.support-tickets.status', [$ticket->id, 'open']) }}"
                           class="btn btn-sm btn-warning btn-block mb-2 {{ $ticket->status === 'open' ? 'disabled' : '' }}">
                            <i class="fa-solid fa-circle-dot"></i> Mark Open
                        </a>
                        <a href="{{ route('admin.support-tickets.status', [$ticket->id, 'in_progress']) }}"
                           class="btn btn-sm btn-primary btn-block mb-2 {{ $ticket->status === 'in_progress' ? 'disabled' : '' }}">
                            <i class="fa-solid fa-spinner"></i> Mark In Progress
                        </a>
                        <a href="{{ route('admin.support-tickets.status', [$ticket->id, 'resolved']) }}"
                           class="btn btn-sm btn-success btn-block mb-2 {{ $ticket->status === 'resolved' ? 'disabled' : '' }}">
                            <i class="fa-solid fa-check"></i> Mark Resolved
                        </a>

                        <hr>
                        <p class="text-muted mb-2" style="font-size:12px;text-transform:uppercase;letter-spacing:1px;">Close Ticket</p>
                        @if($ticket->status === 'closed')
                            <a href="{{ route('admin.support-tickets.status', [$ticket->id, 'open']) }}"
                               class="btn btn-sm btn-outline-secondary btn-block">
                                <i class="fa-solid fa-lock-open"></i> Reopen Ticket
                            </a>
                        @else
                            <a href="{{ route('admin.support-tickets.status', [$ticket->id, 'closed']) }}"
                               class="btn btn-sm btn-secondary btn-block"
                               onclick="return confirm('Close this ticket? The user will not be able to reply until it is reopened.')">
                                <i class="fa-solid fa-lock"></i> Close Ticket
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card custom-border-card">
                    <div class="card-header"><h5 class="mb-0">Summary</h5></div>
                    <div class="card-body">
                        <p class="mb-1"><span style="color:#888;font-size:12px;">TICKET ID</span><br><strong>#{{ $ticket->id }}</strong></p>
                        <p class="mb-1"><span style="color:#888;font-size:12px;">REPLIES</span><br><strong>{{ $ticket->replies->count() }}</strong></p>
                        <p class="mb-0"><span style="color:#888;font-size:12px;">LAST ACTIVITY</span><br><strong>{{ $ticket->updated_at->format('d M Y, h:i A') }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // auto-scroll thread to bottom
    var tc = document.getElementById('thread-container');
    if (tc) tc.scrollTop = tc.scrollHeight;
</script>
@endpush
@endsection
