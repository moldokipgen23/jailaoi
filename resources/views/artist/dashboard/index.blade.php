@extends('artist.layout.page-app')
@section('tab_title', __('label.dashboard'))
@section('page_title', 'Artist Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-3" style="background:#e8f5e9;color:#4caf50">
                <i class="fa-solid fa-music"></i>
            </div>
            <h3 class="mb-1">{{ $total_content }}</h3>
            <small class="text-muted">Total Content</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-3" style="background:#e3f2fd;color:#2196f3">
                <i class="fa-solid fa-eye"></i>
            </div>
            <h3 class="mb-1">{{ $total_views }}</h3>
            <small class="text-muted">Total Views</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-3" style="background:#fce4ec;color:#e91e63">
                <i class="fa-solid fa-heart"></i>
            </div>
            <h3 class="mb-1">{{ $total_likes }}</h3>
            <small class="text-muted">Total Likes</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-3" style="background:#fff3e0;color:#ff9800">
                <i class="fa-solid fa-users"></i>
            </div>
            <h3 class="mb-1">{{ $total_followers }}</h3>
            <small class="text-muted">Followers</small>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="stat-card">
            <h5 class="mb-3">Content Breakdown</h5>
            <div class="d-flex justify-content-between mb-2">
                <span>Videos</span>
                <span class="badge bg-primary">{{ $total_video }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Music</span>
                <span class="badge bg-success">{{ $total_music }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <h5 class="mb-3">Recent Content</h5>
            @if(count($recent_content) > 0)
                @foreach($recent_content as $item)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <small class="fw-bold">{{ $item->title ?? 'Untitled' }}</small>
                            <br>
                            <small class="text-muted">{{ $item->created_at->format('d M Y') }}</small>
                        </div>
                        <span class="badge bg-secondary">{{ $item->total_view }} views</span>
                    </div>
                @endforeach
            @else
                <p class="text-muted">No content yet</p>
            @endif
        </div>
    </div>
</div>
@endsection
