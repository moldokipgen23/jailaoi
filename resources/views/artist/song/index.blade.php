@extends('artist.layout.page-app')
@section('page_title', 'My Songs')

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
        .artist-header .btn { margin-left: 10px; }
        .song-card { background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .song-card img { width: 100%; height: 160px; object-fit: cover; }
        .song-card .body { padding: 15px; }
        .song-card .body h5 { font-size: 16px; margin-bottom: 5px; }
        .song-card .body .plays { color: #888; font-size: 13px; }
    </style>

    <div class="artist-sidebar">
        <div class="logo">
            <h3>{{ App_Name() }}</h3>
            <small style="color:#888;">Artist Panel</small>
        </div>
        <a href="{{ route('artist.dashboard') }}" class="nav-item">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
        <a href="{{ route('artist.songs.index') }}" class="nav-item active">
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
            <h1>My Songs</h1>
            <a href="{{ route('artist.songs.create') }}" class="btn btn-default mw-120">+ Upload New</a>
        </div>

        @if($data->count() > 0)
            <div class="row">
                @foreach ($data as $value)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="song-card">
                        <img src="{{$value->image}}" alt="{{$value->name}}">
                        <div class="body">
                            <h5>{{$value->name}}</h5>
                            <div class="plays">
                                <i class="fa-solid fa-play"></i> {{No_Format($value->total_play ?? 0)}} plays
                            </div>
                            @if($value->status == 1)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                            <div class="mt-2">
                                <a href="{{route('artist.songs.edit', [$value->id])}}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <a href="{{route('artist.songs.destroy', [$value->id])}}" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this song?')">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div> Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of total {{$data->total()}} entries </div>
                <div class="pb-5"> {{ $data->links('pagination::bootstrap-4') }} </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fa-solid fa-music fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No songs yet</h4>
                <a href="{{ route('artist.songs.create') }}" class="btn btn-default mw-120 mt-3">Upload Your First Song</a>
            </div>
        @endif
    </div>
@endsection
