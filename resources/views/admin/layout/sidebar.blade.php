<div class="sidebar">
    <div class="side-head">
        <a href="{{route('admin.dashboard')}}" class="primary-color side-logo">
            <h3>{{App_Name()}}</h3>
        </a>
        <button class="btn side-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <ul class="side-menu mt-4">
        <li class="side_line  {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}{{ request()->routeIs('profile*') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard')}}">
                <i class="fa-solid fa-house fa-2xl menu-icon"></i>
                <span>{{__('Label.Dashboard')}}</span>
            </a>
        </li>
        <p class="partition"><span>{{__('Label.basic_element')}}</span></p>
        <li class="dropdown {{ request()->routeIs('category*') ? 'active' : '' }}{{ request()->routeIs('language*') ? 'active' : '' }}{{ request()->routeIs('city*') ? 'active' : '' }}">
            <a class="dropdown-toggle" id="dropdownMenuClickable" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-sliders fa-2xl menu-icon"></i>
                <span>Basic Items</span>
            </a>
            <ul class="dropdown-menu side-submenu {{ request()->routeIs('category*') ? 'show' : '' }}{{ request()->routeIs('language*') ? 'show' : '' }}{{ request()->routeIs('city*') ? 'show' : '' }}" aria-labelledby="dropdownMenuClickable">
                <li class="side_line {{ request()->routeIs('category*') ? 'active' : '' }}">
                    <a href="{{ route('category.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-list fa-2xl submenu-icon"></i>
                        <span>{{__('Label.Category')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->routeIs('language*') ? 'active' : '' }}">
                    <a href="{{ route('language.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-globe fa-2xl submenu-icon"></i>
                        <span>{{__('Label.Language')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->routeIs('city*') ? 'active' : '' }}">
                    <a href="{{ route('city.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-city fa-2xl submenu-icon"></i>
                        <span>{{__('Label.City')}}</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="dropdown {{ request()->routeIs('artist*') ? 'active' : '' }}{{ request()->routeIs('artist-requests*') ? 'active' : '' }}">
            <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-user-tie fa-2xl menu-icon"></i>
                <span>Artists</span>
            </a>
            <ul class="dropdown-menu side-submenu {{ request()->routeIs('artist*') ? 'show' : '' }}{{ request()->routeIs('artist-requests*') ? 'show' : '' }}">
                <li class="side_line {{ request()->routeIs('artist.index') ? 'active' : '' }}">
                    <a href="{{ route('artist.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-list fa-2xl submenu-icon"></i>
                        <span>All Artists</span>
                    </a>
                </li>
                <li class="side_line {{ request()->routeIs('artist-requests*') ? 'active' : '' }}">
                    <a href="{{ route('artist-requests.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-user-plus fa-2xl submenu-icon"></i>
                        <span>Artist Requests</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="side_line {{ request()->routeIs('user*') ? 'active' : '' }}">
            <a href="{{ route('user.index') }}">
                <i class="fa-solid fa-users fa-2xl menu-icon"></i>
                <span>{{__('Label.Users')}}</span>
            </a>
        </li>
        <p class="partition"><span>Configuration</span></p>
        <li class="side_line {{ request()->routeIs('banner*') ? 'active' : '' }}">
            <a href="{{ route('banner.index') }}">
                <i class="fa-solid fa-images fa-2xl menu-icon"></i>
                <span>Banner</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('section*') ? 'active' : '' }}">
            <a href="{{ route('section.index') }}">
                <i class="fa-solid fa-bars fa-2xl menu-icon"></i>
                <span>{{__('Label.section')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('podcastsection*') ? 'active' : '' }}">
            <a href="{{ route('podcastsection.index') }}">
                <i class="fa-solid fa-microphone-alt fa-2xl menu-icon"></i>
                <span>{{__('Label.podcast_section')}}</span>
            </a>
        </li>
        <p class="partition"><span>{{__('Label.content')}}</span></p>
        <li class="side_line {{ request()->routeIs('song*') ? 'active' : '' }}">
            <a href="{{ route('song.index') }}">
                <i class="fa-solid fa-film fa-2xl menu-icon"></i>
                <span>Radio Station</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('podcast.*') ? 'active' : '' }}">
            <a href="{{ route('podcast.index') }}">
                <i class="fa-solid fa-podcast fa-2xl menu-icon"></i>
                <span>{{__('Label.poadcast')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('liveevent*') ? 'active' : '' }}">
            <a href="{{ route('liveevent.index') }}">
                <i class="fa-solid fa-calendar-week fa-2xl menu-icon"></i>
                <span>Live Event</span>
            </a>
        </li>
        <p class="partition"><span>{{__('Label.interaction')}}</span></p>
        <li class="side_line {{ request()->routeIs('comment*') ? 'active' : '' }}">
            <a href="{{ route('comment.index') }}">
                <i class="fa-solid fa-comments fa-2xl menu-icon"></i>
                <span>Comment</span>
            </a>
        </li>
        <li class="side_line {{ (request()->routeIs('notification*')) ? 'active' : '' }}">
            <a href="{{ route('notification.index') }}">
                <i class="fa-solid fa-bell fa-2xl menu-icon"></i>
                <span>{{__('Label.Notification')}}</span>
            </a>
        </li>
        <p class="partition"><span>{{__('Label.financial')}}</span></p>
        <li class="dropdown {{ request()->routeIs('package*') ? 'active' : '' }}{{ request()->routeIs('transaction*') ? 'active' : '' }}{{ request()->routeIs('payment*') ? 'active' : '' }}">
            <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-money-bill fa-2xl menu-icon"></i>
                <span>{{__('Label.Subscription')}}</span>
            </a>
            <ul class="dropdown-menu side-submenu {{ request()->routeIs('package*') ? 'show' : '' }}{{ request()->routeIs('transaction*') ? 'show' : '' }}{{ request()->routeIs('payment*') ? 'show' : '' }}">
                <li class="side_line {{ request()->routeIs('package*') ? 'active' : '' }}">
                    <a href="{{ route('package.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-box-archive fa-2xl submenu-icon"></i>
                        <span>{{__('Label.Package')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->routeIs('transaction*') ? 'active' : '' }}">
                    <a href="{{ route('transaction.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-wallet fa-2xl submenu-icon"></i>
                        <span>{{__('Label.Transactions')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->routeIs('payment*') ? 'active' : '' }}">
                    <a href="{{ route('payment.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-money-bill-wave fa-2xl submenu-icon"></i>
                        <span>{{__('Label.Payment')}}</span>
                    </a>
                </li>
            </ul>
        </li>
        <p class="partition"><span>{{__('Label.ads')}}</span></p>
        <li class="side_line {{ request()->routeIs('admob*') ? 'active' : '' }}">
            <a href="{{ route('admob.index') }}">
                <i class="fa-brands fa-square-google-plus fa-2xl menu-icon"></i>
                <span>AdMob</span>
            </a>
        </li>
        <p class="partition"><span>{{__('Label.Setting')}}</span></p>
        <li class="side_line {{ request()->routeIs('setting*') ? 'active' : '' }}">
            <a href="{{ route('setting') }}">
                <i class="fa-solid fa-gear fa-2xl menu-icon"></i>
                <span>App Settings</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('system.setting*') ? 'active' : '' }}">
            <a href="{{ route('system.setting.index') }}">
                <i class="fa-solid fa-screwdriver-wrench fa-2xl menu-icon"></i>
                <span>System Settings</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('page*') ? 'active' : '' }}">
            <a href="{{ route('page.index') }}">
                <i class="fa-solid fa-book-open-reader fa-2xl menu-icon"></i>
                <span>{{__('Label.Pages')}}</span>
            </a>
        </li>
        <p class="partition"><span>{{__('Label.Logout')}}</span></p>
        <li>
            <a href="{{ route('admin.logout') }}" onclick="event.preventDefault();
                       document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-arrow-right-from-bracket fa-2xl menu-icon"></i>
                <span>{{__('Label.Logout')}}</span>
            </a>

            <form id="logout-form" action="{{ route('admin.logout') }}" method="GET" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</div>