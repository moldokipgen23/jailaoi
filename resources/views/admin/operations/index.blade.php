@extends('admin.layout.page-app')
@section('page_title', 'Operations health')
@section('tab_title', 'Operations health')
@section('content')
<div class="container-fluid p-4">
    <h2>Operations health</h2>
    <p>Last checked: {{ $report['checked_at'] ?? 'Not checked yet' }}. Checks run every five minutes.</p>
    @if($report)
    <div class="table-responsive"><table class="table table-striped"><thead><tr><th>Check</th><th>Status</th><th>Details</th></tr></thead><tbody>
    @foreach($report['checks'] as $name => $check)
    <tr><td>{{ ucfirst(str_replace('_', ' ', $name)) }}</td><td>{{ $check['ok'] ? 'Check passed' : 'Needs attention' }}</td><td>{{ $check['message'] }}</td></tr>
    @endforeach
    </tbody></table></div>
    @endif
    <p>Configuration checks do not prove actual device delivery, successful payouts, or a complete restore.</p>
</div>
@endsection
