{{-- JAILAOI: Admin sidebar dark theme --}}
<div class="sidebar">
    <div class="side-head">
        <a href="{{route('admin.dashboard')}}" class="side-logo">
            <span style="font-size:22px;font-weight:700;color:#6c47ff;">JailaOi</span>
            <span style="display:block;font-size:10px;letter-spacing:2px;color:#64648a;margin-top:-2px;">ADMIN PANEL</span>
        </a>
        <button class="btn side-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <ul class="side-menu mt-4">

        {{-- JAILAOI: Section label --}}
        <li class="side-section-label">OVERVIEW</li>
        <li class="side_line {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}{{ request()->routeIs('profile*') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard')}}">
                <i class="fa-solid fa-house fa-2xl menu-icon"></i>
                <span>{{__('label.dashboard')}}</span>
            </a>
        </li>

        {{-- JAILAOI: CONTENT section --}}
        <li class="side-section-label">CONTENT</li>
        <li class="side_line {{ request()->routeIs('music.*') ? 'active' : '' }}">
            <a href="{{ route('music.index') }}">
                <i class="fa-solid fa-music fa-2xl menu-icon"></i>
                <span>{{__('label.music')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('song*') ? 'active' : '' }}">
            <a href="{{ route('song.index') }}">
                <i class="fa-solid fa-radio fa-2xl menu-icon"></i>
                <span>{{__('label.radio_station')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('podcast.*') ? 'active' : '' }}">
            <a href="{{ route('podcast.index') }}">
                <i class="fa-solid fa-podcast fa-2xl menu-icon"></i>
                <span>{{__('label.podcast')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('liveevent*') ? 'active' : '' }}">
            <a href="{{ route('liveevent.index') }}">
                <i class="fa-solid fa-calendar-week fa-2xl menu-icon"></i>
                <span>{{__('label.live_event')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('section*') ? 'active' : '' }}">
            <a href="{{ route('section.index') }}">
                <i class="fa-solid fa-bars-staggered fa-2xl menu-icon"></i>
                <span>{{__('label.section')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('category*') ? 'active' : '' }}">
            <a href="{{ route('category.index') }}">
                <i class="fa-solid fa-list fa-2xl menu-icon"></i>
                <span>{{__('label.category')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('language*') ? 'active' : '' }}">
            <a href="{{ route('language.index') }}">
                <i class="fa-solid fa-globe fa-2xl menu-icon"></i>
                <span>{{__('label.language')}}</span>
            </a>
        </li>

        {{-- JAILAOI: MONETIZATION section --}}
        <li class="side-section-label">MONETIZATION</li>
        <li class="side_line {{ request()->routeIs('admin.withdrawals*') ? 'active' : '' }}">
            <a href="{{ route('admin.withdrawals.index') }}">
                <i class="fa-solid fa-money-bill-transfer fa-2xl menu-icon"></i>
                <span>{{__('label.artist_withdrawals')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.kyc*') ? 'active' : '' }}">
            <a href="{{ route('admin.kyc.index') }}">
                <i class="fa-solid fa-shield-halved fa-2xl menu-icon"></i>
                <span>KYC Requests</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.monetization*') ? 'active' : '' }}">
            <a href="{{ route('admin.monetization.index') }}">
                <i class="fa-solid fa-rocket fa-2xl menu-icon"></i>
                <span>Monetization Apps</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.earnings*') ? 'active' : '' }}">
            <a href="{{ route('admin.earnings.index') }}">
                <i class="fa-solid fa-chart-line fa-2xl menu-icon"></i>
                <span>Earnings</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.artist-analytics*') ? 'active' : '' }}">
            <a href="{{ route('admin.artist-analytics.index') }}">
                <i class="fa-solid fa-chart-bar fa-2xl menu-icon"></i>
                <span>Artist Analytics</span>
            </a>
        </li>

        {{-- JAILAOI: USERS section --}}
        <li class="side-section-label">USERS</li>
        <li class="side_line {{ request()->routeIs('user*') ? 'active' : '' }}">
            <a href="{{ route('user.index') }}">
                <i class="fa-solid fa-users fa-2xl menu-icon"></i>
                <span>{{__('label.users')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('artist*') && !request()->routeIs('artist-requests*') ? 'active' : '' }}">
            <a href="{{ route('artist.index') }}">
                <i class="fa-solid fa-user-tie fa-2xl menu-icon"></i>
                <span>{{__('label.artist_rj')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.artist-requests*') ? 'active' : '' }}">
            <a href="{{ route('admin.artist-requests.index') }}">
                <i class="fa-solid fa-user-plus fa-2xl menu-icon"></i>
                <span>{{__('label.artist_requests')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('comment*') ? 'active' : '' }}">
            <a href="{{ route('comment.index') }}">
                <i class="fa-solid fa-comments fa-2xl menu-icon"></i>
                <span>{{__('label.comment')}}</span>
            </a>
        </li>

        {{-- JAILAOI: MARKETING section --}}
        <li class="side-section-label">MARKETING</li>
        <li class="side_line {{ request()->routeIs('banner*') ? 'active' : '' }}">
            <a href="{{ route('banner.index') }}">
                <i class="fa-solid fa-images fa-2xl menu-icon"></i>
                <span>{{__('label.banner')}}</span>
            </a>
        </li>
        <li class="side_line {{ (request()->routeIs('notification.*')) ? 'active' : '' }}">
            <a href="{{ route('notification.index') }}">
                <i class="fa-solid fa-bell fa-2xl menu-icon"></i>
                <span>{{__('label.notification')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admob*') ? 'active' : '' }}">
            <a href="{{ route('admob.index') }}">
                <i class="fa-brands fa-square-google-plus fa-2xl menu-icon"></i>
                <span>{{__('label.admob')}}</span>
            </a>
        </li>

        {{-- JAILAOI: SETTINGS section --}}
        <li class="side-section-label">SETTINGS</li>
        <li class="side_line {{ request()->routeIs('setting*') ? 'active' : '' }}">
            <a href="{{ route('setting') }}">
                <i class="fa-solid fa-gear fa-2xl menu-icon"></i>
                <span>{{__('label.app_settings')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('system.setting*') ? 'active' : '' }}">
            <a href="{{ route('system.setting.index') }}">
                <i class="fa-solid fa-screwdriver-wrench fa-2xl menu-icon"></i>
                <span>{{__('label.system_settings')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('panel_setting*') ? 'active' : '' }}">
            <a href="{{ route('panel_setting.index') }}">
                <i class="fa-solid fa-palette fa-2xl menu-icon"></i>
                <span>{{ __('label.panel_settings') }}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('notification_configuration.*') ? 'active' : '' }}">
            <a href="{{ route('notification_configuration.index') }}">
                <i class="fa-solid fa-bell fa-2xl menu-icon"></i>
                <span>{{__('label.notification_configuration')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('page*') ? 'active' : '' }}">
            <a href="{{ route('page.index') }}">
                <i class="fa-solid fa-book-open-reader fa-2xl menu-icon"></i>
                <span>{{__('label.pages')}}</span>
            </a>
        </li>
        <li class="dropdown {{ request()->routeIs('package*') ? 'active' : '' }}{{ request()->routeIs('transaction*') ? 'active' : '' }}{{ request()->routeIs('payment*') ? 'active' : '' }}">
            <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-credit-card fa-2xl menu-icon"></i>
                <span>{{__('label.subscription')}}</span>
            </a>
            <ul class="dropdown-menu side-submenu {{ request()->routeIs('package*') ? 'show' : '' }}{{ request()->routeIs('transaction*') ? 'show' : '' }}{{ request()->routeIs('payment*') ? 'show' : '' }}">
                <li class="side_line {{ request()->routeIs('package*') ? 'active' : '' }}">
                    <a href="{{ route('package.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-box-archive fa-2xl submenu-icon"></i>
                        <span>{{__('label.package')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->routeIs('transaction*') ? 'active' : '' }}">
                    <a href="{{ route('transaction.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-wallet fa-2xl submenu-icon"></i>
                        <span>{{__('label.transaction')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->routeIs('payment*') ? 'active' : '' }}">
                    <a href="{{ route('payment.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-money-bill-wave fa-2xl submenu-icon"></i>
                        <span>{{__('label.payment')}}</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- JAILAOI: Spacer + logout --}}
        <li style="margin-top:auto;padding-top:20px;">
            <a href="{{ route('admin.logout') }}" onclick="event.preventDefault();
                       document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-arrow-right-from-bracket fa-2xl menu-icon"></i>
                <span>{{__('label.logout')}}</span>
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="GET" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</div>