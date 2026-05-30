<header class="header">
    <div class="header-left">
        <button class="header-toggle" id="mobileToggle" onclick="toggleMobileSidebar()">
            <i class="fa-solid fa-bars"></i>
        </button>
        <button class="header-toggle d-md-none" id="sidebarToggle" onclick="toggleSidebar()">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h1 class="header-title">@yield('page_title')</h1>
    </div>

    <div class="header-right">
        @if( env('DEMO_MODE') == 'ON')
        <span class="header-badge">{{__('label.demo_mode')}}</span>
        @endif

        <a href="{{ route('user.login') }}" target="_blank" class="btn btn-outline" title="Go to Artist Dashboard">
            <i class="fa-solid fa-display"></i>
        </a>

        <div class="dropdown header-dropdown">
            <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-language"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="{{ route('change.language', ['locale' => 'en']) }}">English</a>
                <a class="dropdown-item" href="{{ route('change.language', ['locale' => 'hi']) }}">Hindi</a>
                <a class="dropdown-item" href="{{ route('change.language', ['locale' => 'fr']) }}">French</a>
            </div>
        </div>

        <div class="dropdown header-dropdown">
            <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="{{ route('admin.profile.index') }}">
                    <i class="fa-solid fa-user fa-lg mr-2"></i> {{__('label.profile')}}
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                    <i class="fa-solid fa-arrow-right-from-bracket fa-lg mr-2"></i> {{__('label.logout')}}
                </a>
                <form id="logout-form-header" action="{{ route('admin.logout') }}" method="GET" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</header>
