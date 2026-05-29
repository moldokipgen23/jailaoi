@extends('artist.layout.page-app')
@section('page_title', 'Upload Song')

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
    </style>

    <div class="artist-sidebar">
        <div class="logo">
            <h3>{{ App_Name() }}</h3>
            <small style="color:#888;">Artist Panel</small>
        </div>
        <a href="{{ route('artist.dashboard') }}" class="nav-item">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
        <a href="{{ route('artist.songs.index') }}" class="nav-item">
            <i class="fa-solid fa-music"></i> My Songs
        </a>
        <a href="{{ route('artist.songs.create') }}" class="nav-item active">
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
            <h1>Upload New Song</h1>
            <a href="{{ route('artist.songs.index') }}" class="btn btn-cancel mw-120">Back to Songs</a>
        </div>

        <div class="card custom-border-card">
            <form id="song_form" autocomplete="off" enctype="multipart/form-data">
                <input type="hidden" name="id" value="">
                <div class="form-row">
                    <div class="col-md-8">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Song Name<span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Song Name" required autofocus>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category<span class="text-danger">*</span></label>
                                    <select class="form-control" name="category_id" id="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach ($category as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Song File<span class="text-danger">*</span></label>
                                    <input type="file" name="song_url" class="form-control" required accept="audio/*">
                                    <small class="text-muted">Upload MP3, WAV or other audio formats</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group ml-5">
                            <label>Image<span class="text-danger">*</span></label>
                            <div class="avatar-upload">
                                <div class="avatar-edit">
                                    <input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" required />
                                    <label for="imageUpload" title="Select File"></label>
                                </div>
                                <div class="avatar-preview">
                                    <img src="{{asset('assets/imgs/upload_img.png')}}" alt="upload_img.png" id="imagePreview">
                                </div>
                            </div>
                            <label class="mt-3 text-gray">Recommended: 500x500px, max 2MB</label>
                        </div>
                    </div>
                </div>
                <div class="border-top pt-3 text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_song()">Upload</button>
                    <a href="{{route('artist.songs.index')}}" class="btn btn-cancel mw-120 ml-2">Cancel</a>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        $("#category_id").select2();

        function save_song() {
            $("#dvloader").show();
            var formData = new FormData($("#song_form")[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route("artist.songs.store") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'song_form', '{{ route("artist.songs.index") }}');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        }
    </script>
@endsection
