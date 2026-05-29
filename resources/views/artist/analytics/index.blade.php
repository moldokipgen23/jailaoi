@extends('artist.layout.page-app')
@section('tab_title', 'Analytics')
@section('page_title', 'Analytics')

@section('content')
<div class="row g-4">
    <div class="col-md-3">
        <div class="stat-card text-center">
            <h3 class="mb-1">{{ $total_views }}</h3>
            <small class="text-muted">Total Views</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <h3 class="mb-1">{{ $total_likes }}</h3>
            <small class="text-muted">Total Likes</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <h3 class="mb-1">{{ $total_followers }}</h3>
            <small class="text-muted">Followers</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <h3 class="mb-1">{{ $total_content }}</h3>
            <small class="text-muted">Content</small>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Views by Content Type</h5>
                @if(count($views_by_type) > 0)
                    @foreach($views_by_type as $v)
                        <div class="d-flex justify-content-between mb-2">
                            <span>
                                @if($v->content_type == 1) <i class="fa-solid fa-video"></i> Video
                                @elseif($v->content_type == 2) <i class="fa-solid fa-music"></i> Music
                                @elseif($v->content_type == 3) <i class="fa-solid fa-film"></i> Reels
                                @else <i class="fa-solid fa-question"></i> Other
                                @endif
                            </span>
                            <span class="fw-bold">{{ $v->total }}</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No data yet</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Top Content by Views</h5>
                @if(count($recent_views) > 0)
                    @foreach($recent_views as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small>{{ $item->title ?? 'Untitled' }}</small>
                            <span class="badge bg-primary">{{ $item->total_view }} views</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No data yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
