<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ Tab_Icon() }}">
    <title>@yield('tab_title') | {{ App_Name() }} Artist</title>
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <input type="hidden" value="{{URL('')}}" id="base_url">
    <style>
        .artist-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 0; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); transition: 0.3s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .sidebar { width: 250px; background: #1e1e2d; min-height: 100vh; position: fixed; top: 0; left: 0; padding-top: 20px; }
        .sidebar a { color: #a2a3b7; padding: 12px 20px; display: block; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { color: white; background: #2a2a3d; border-left: 3px solid #667eea; }
        .main-content { margin-left: 250px; padding: 20px; }
        .top-bar { background: white; padding: 15px 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
    </style>
    <script>
        var globalSiteUrl = '<?php echo url('/'); ?>'
        var currentRouteName = '<?php echo request()->route()->getName(); ?>'
    </script>
</head>
<body>
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4 style="color:white">{{ App_Name() }}</h4>
            <small style="color:#a2a3b7">Artist Portal</small>
        </div>
        <a href="{{ route('artist.dashboard') }}" class="{{ request()->routeIs('artist.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house me-2"></i> Dashboard
        </a>
        <a href="{{ route('artist.music.index') }}" class="{{ request()->routeIs('artist.music*') ? 'active' : '' }}">
            <i class="fa-solid fa-music me-2"></i> My Music
        </a>
        <a href="{{ route('artist.video.index') }}" class="{{ request()->routeIs('artist.video*') ? 'active' : '' }}">
            <i class="fa-solid fa-video me-2"></i> My Videos
        </a>
        <a href="{{ route('artist.analytics.index') }}" class="{{ request()->routeIs('artist.analytics*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line me-2"></i> Analytics
        </a>
        <a href="{{ route('artist.profile.index') }}" class="{{ request()->routeIs('artist.profile*') ? 'active' : '' }}">
            <i class="fa-solid fa-user me-2"></i> Profile
        </a>
        <hr style="border-color:#2a2a3d">
        <a href="{{ route('artist.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('artist.logout') }}" method="GET" class="d-none"></form>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h5 class="mb-0">@yield('page_title', 'Dashboard')</h5>
            <div>
                <i class="fa-solid fa-user-circle me-1"></i>
                {{ Auth::guard('artist')->user()->channel_name ?? Auth::guard('artist')->user()->email }}
            </div>
        </div>
        <div class="mt-4">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/js.js')}}"></script>
    <script src="{{ asset('assets/js/toastr.min.js')}}"></script>
    <script>
        function get_responce_message(resp, form_name="", url="") {
            if (resp.status == '200') {
                toastr.success(resp.success);
                if(form_name != ""){ document.getElementById(form_name).reset(); }
                if(url != ""){ setTimeout(function() { window.location.replace(url); }, 500); }
            } else {
                var obj = resp.errors;
                if (typeof obj === 'string') { toastr.error(obj); }
                else { $.each(obj, function(i, e) { toastr.error(e); }); }
            }
        }
        @if(Session::has('error')) toastr.error('{{ Session::get("error") }}');
        @elseif(Session::has('success')) toastr.success('{{ Session::get("success") }}');
        @endif
    </script>
    @yield('pagescript')
</body>
</html>
