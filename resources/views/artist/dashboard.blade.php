@extends('artist.layout.page-app')
@section('page_title', 'Artist Dashboard')

@section('content')
    <style>
        .artist-sidebar { width: 250px; min-height: 100vh; background: #1a1a2e; position: fixed; top: 0; left: 0; padding: 20px 0; z-index: 100; }
        .artist-sidebar .logo { color: #fff; text-align: center; padding: 15px; border-bottom: 1px solid #2d2d5e; margin-bottom: 15px; }
        .artist-sidebar .logo h3 { font-size: 20px; margin: 0; }
        .artist-sidebar .nav-item { padding: 12px 25px; color: #b0b0c8; display: block; text-decoration: none; transition: 0.2s; }
        .artist-sidebar .nav-item:hover, .artist-sidebar .nav-item.active { background: #2d2d5e; color: #fff; text-decoration: none; }
        .artist-sidebar .nav-item i { width: 25px; margin-right: 10px; }
        .artist-content { margin-left: 250px; padding: 20px; background: #f5f5f9; min-height: 100vh; }
        .artist-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: #fff; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .artist-header h1 { font-size: 22px; margin: 0; color: #333; }
        .artist-header .user-info { display: flex; align-items: center; gap: 15px; }
        .artist-header .user-info span { color: #666; }
        .stat-card { background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .stat-card .number { font-size: 32px; font-weight: bold; color: #4e45b8; }
        .stat-card .label-text { color: #888; font-size: 14px; margin-top: 5px; }
    </style>

    <div class="artist-sidebar">
        <div class="logo">
            <h3>{{ App_Name() }}</h3>
            <small style="color:#888;">Artist Panel</small>
        </div>
        <a href="{{ route('artist.dashboard') }}" class="nav-item active">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
        <a href="{{ route('artist.songs.index') }}" class="nav-item">
            <i class="fa-solid fa-music"></i> My Songs
        </a>
        <a href="{{ route('artist.songs.create') }}" class="nav-item">
            <i class="fa-solid fa-upload"></i> Upload Song
        </a>
        <hr style="border-color:#2d2d5e; margin: 15px 0;">
        <a href="{{ route('artist.logout') }}" class="nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa-solid fa-sign-out-alt"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('artist.logout') }}" method="GET" class="d-none"></form>
    </div>

    <div class="artist-content">
        <div class="artist-header">
            <h1>Dashboard</h1>
            <div class="user-info">
                <span>Welcome, {{ $artist->name ?? $user->full_name ?? 'Artist' }}</span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <div class="number">{{ $songCount }}</div>
                    <div class="label-text">Total Songs</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <div class="number">{{ $followerCount }}</div>
                    <div class="label-text">Followers</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <div class="number">{{ $totalPlays }}</div>
                    <div class="label-text">Total Plays</div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <h5 class="mb-3">Recent Songs</h5>
            @if($recentSongs->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Plays</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSongs as $song)
                        <tr>
                            <td>{{ $song->name }}</td>
                            <td>{{ $song->total_play ?? 0 }}</td>
                            <td>
                                @if($song->status == 1)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>{{ $song->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">No songs yet. <a href="{{ route('artist.songs.create') }}">Upload your first song</a></p>
            @endif
        </div>
    </div>
@endsection
