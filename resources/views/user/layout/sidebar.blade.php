<div class="sidebar">
    <div class="side-head">
        <a href="{{ route('user.dashboard') }}" class="primary-color side-logo">
            <h3>{{ App_Name() }}</h3>
            <small class="text-muted d-block" style="font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:1px;margin-top:-4px">{{__('label.artist_portal')}}</small>
        </a>
        <button class="btn side-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    @php $settings = Setting_Data(); @endphp

    <ul class="side-menu mt-4">
        <li class="side_line {{ request()->routeIs('user.dashboard*') ? 'active' : '' }}{{ request()->routeIs('user.profile*') ? 'active' : '' }}{{ request()->routeIs('user.password*') ? 'active' : '' }}">
            <a href="{{ route('user.dashboard') }}">
                <i class="fa-solid fa-house fa-2xl menu-icon"></i>
                <span>{{__('label.dashboard')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.my_content')}}</span></p>
        <li class="side_line {{ request()->routeIs('user.music*') ? 'active' : '' }}">
            <a href="{{ route('user.music.index') }}">
                <i class="fa-solid fa-music fa-2xl menu-icon"></i>
                <span>{{__('label.my_music')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('user.playlist*') ? 'active' : '' }}">
            <a href="{{ route('user.playlist.index') }}">
                <i class="fa-solid fa-headphones fa-2xl menu-icon"></i>
                <span>{{__('label.my_playlists')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('user.radio*') ? 'active' : '' }}">
            <a href="{{ route('user.radio.index') }}">
                <i class="fa-solid fa-radio fa-2xl menu-icon"></i>
                <span>{{__('label.my_radio')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.earnings')}}</span></p>
        <li class="side_line {{ request()->routeIs('user.earnings*') ? 'active' : '' }}">
            <a href="{{ route('user.earnings.index') }}">
                <i class="fa-solid fa-wallet fa-2xl menu-icon"></i>
                <span>{{__('label.earnings')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.ads')}}</span></p>
        <li class="side_line {{ request()->routeIs('user.ads*') ? 'active' : '' }}">
            <a href="{{ route('user.ads.index') }}">
                <i class="fa-solid fa-rectangle-ad fa-2xl menu-icon"></i>
                <span>{{__('label.my_ads')}}</span>
            </a>
        </li>

        {{-- JAILAOI: Only show for non-artists --}}
        @if(auth()->guard('user')->check() && auth()->guard('user')->user()->role !== 'artist')
        <p class="partition"><span>{{__('label.become_an_artist')}}</span></p>
        <li class="side_line {{ request()->routeIs('user.become.artist') ? 'active' : '' }}">
            <a href="{{ route('user.become.artist') }}">
                <i class="fa-solid fa-star fa-2xl menu-icon"></i>
                <span>{{__('label.become_an_artist')}}</span>
            </a>
        </li>
        @endif

        <p class="partition"><span>{{__('label.logout')}}</span></p>
        <li>
            <a href="{{ route('user.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-arrow-right-from-bracket fa-2xl menu-icon"></i>
                <span>{{__('label.logout')}}</span>
            </a>

            <form id="logout-form" action="{{ route('user.logout') }}" method="GET" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</div>
