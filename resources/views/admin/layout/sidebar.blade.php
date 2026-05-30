<div class="sidebar">
    @php $sidebar_settings = Setting_Data(); @endphp
    <div class="side-head">
        <a href="{{ route('admin.dashboard') }}" class="primary-color side-logo">
            <h3>{{ App_Name() }}</h3>
        </a>
        <button class="btn side-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <ul class="side-menu mt-4">
        <li class="side_line {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}{{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-house fa-2xl menu-icon"></i>
                <span>{{__('label.dashboard')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.earning.dashboard*') ? 'active' : '' }}">
            <a href="{{ route('admin.earning.dashboard') }}">
                <i class="fa-solid fa-chart-line fa-2xl menu-icon"></i>
                <span>{{__('label.earnings')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.basic_element')}}</span></p>
        <li class="dropdown {{ request()->routeIs('admin.category*') ? 'active' : '' }}{{ request()->routeIs('admin.language*') ? 'active' : '' }}{{ request()->routeIs('admin.hashtag*') ? 'active' : '' }}">
            <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-sliders fa-2xl menu-icon"></i>
                <span>{{__('label.basic_items')}}</span>
            </a>
            <ul class="dropdown-menu side-submenu {{ request()->routeIs('admin.category*') ? 'show' : '' }}{{ request()->routeIs('admin.language*') ? 'show' : '' }}{{ request()->routeIs('admin.hashtag*') ? 'show' : '' }}">
                <li class="side_line {{ request()->routeIs('admin.category*') ? 'active' : '' }}">
                    <a href="{{ route('admin.category.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-shapes fa-2xl submenu-icon"></i>
                        <span>{{__('label.category')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->routeIs('admin.language*') ? 'active' : '' }}">
                    <a href="{{ route('admin.language.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-globe fa-2xl submenu-icon"></i>
                        <span>{{__('label.language')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->routeIs('admin.hashtag*') ? 'active' : '' }}">
                    <a href="{{ route('admin.hashtag.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-hashtag fa-2xl submenu-icon"></i>
                        <span>{{__('label.hashtag')}}</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="side_line {{ request()->routeIs('admin.user*') ? 'active' : '' }}">
            <a href="{{ route('admin.user.index') }}">
                <i class="fa-solid fa-users fa-2xl menu-icon"></i>
                <span>{{__('label.users')}}</span>
            </a>
        </li>
        <li class="dropdown {{ request()->routeIs('admin.artist*') ? 'active' : '' }}{{ request()->routeIs('admin.artist-requests*') ? 'active' : '' }}">
            <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-microphone fa-2xl menu-icon"></i>
                <span>{{__('label.artists')}}</span>
            </a>
            <ul class="dropdown-menu side-submenu {{ request()->routeIs('admin.artist*') ? 'show' : '' }}{{ request()->routeIs('admin.artist-requests*') ? 'show' : '' }}">
                <li class="side_line {{ request()->routeIs('admin.artist.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.artist.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-microphone fa-2xl submenu-icon"></i>
                        <span>{{__('label.artists')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->routeIs('admin.artist-requests*') ? 'active' : '' }}">
                    <a href="{{ route('admin.artist-requests.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-pen-to-square fa-2xl submenu-icon"></i>
                        <span>{{__('label.artist_requests')}}</span>
                    </a>
                </li>
            </ul>
        </li>

        <p class="partition"><span>{{__('label.app_interface')}}</span></p>
        <li class="side_line {{ request()->routeIs('admin.section*') ? 'active' : '' }}">
            <a href="{{ route('admin.section.index') }}">
                <i class="fa-solid fa-bars-staggered fa-2xl menu-icon"></i>
                <span>{{__('label.section')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.content')}}</span></p>
        <li class="side_line {{ request()->routeIs('admin.video*') ? 'active' : '' }}" @if(($sidebar_settings['video_status'] ?? '1') == '0') style="display:none" @endif>
            <a href="{{ route('admin.video.index') }}">
                <i class="fa-solid fa-video fa-2xl menu-icon"></i>
                <span>{{__('label.videos')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.music*') ? 'active' : '' }}">
            <a href="{{ route('admin.music.index') }}">
                <i class="fa-solid fa-music fa-2xl menu-icon"></i>
                <span>{{__('label.music')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.reels*') ? 'active' : '' }}" @if(($sidebar_settings['reels_status'] ?? '1') == '0') style="display:none" @endif>
            <a href="{{ route('admin.reels.index') }}">
                <i class="fa-solid fa-film fa-2xl menu-icon"></i>
                <span>{{__('label.reels')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.podcast*') ? 'active' : '' }}">
            <a href="{{ route('admin.podcasts.index') }}">
                <i class="fa-solid fa-podcast fa-2xl menu-icon"></i>
                <span>{{__('label.podcasts')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.playlist*') ? 'active' : '' }}">
            <a href="{{ route('admin.playlist.index') }}">
                <i class="fa-solid fa-headphones fa-2xl menu-icon"></i>
                <span>{{__('label.playlists')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.radio*') ? 'active' : '' }}">
            <a href="{{ route('admin.radio.index') }}">
                <i class="fa-solid fa-radio fa-2xl menu-icon"></i>
                <span>{{__('label.radio')}}</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('admin.feed.*')) ? 'active' : '' }}" @if(($sidebar_settings['feed_status'] ?? '1') == '0') style="display:none" @endif>
            <a href="{{ route('admin.feed.index') }}">
                <i class="fa-solid fa-camera-retro fa-2xl menu-icon"></i>
                <span>{{__('label.feeds')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.rent_control')}}</span></p>
        <li class="side_line {{ request()->routeIs('admin.rentsection*') ? 'active' : '' }}">
            <a href="{{ route('admin.rentsection.index') }}">
                <i class="fa-solid fa-bars-staggered fa-2xl menu-icon"></i>
                <span>{{__('label.rent_section')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.renttransaction*') ? 'active' : '' }}">
            <a href="{{ route('admin.renttransaction.index') }}">
                <i class="fa-solid fa-wallet fa-2xl menu-icon"></i>
                <span>{{__('label.transactions')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.interaction')}}</span></p>
        <li class="side_line {{ request()->routeIs('admin.notification*') ? 'active' : '' }}">
            <a href="{{ route('admin.notification.index') }}">
                <i class="fa-solid fa-bell fa-2xl menu-icon"></i>
                <span>{{__('label.notification')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.comment*') ? 'active' : '' }}">
            <a href="{{ route('admin.comment.index') }}">
                <i class="fa-solid fa-comments fa-2xl menu-icon"></i>
                <span>{{__('label.content_comments')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.feedcomment*') ? 'active' : '' }}" @if(($sidebar_settings['feed_status'] ?? '1') == '0') style="display:none" @endif>
            <a href="{{ route('admin.feedcomment.index') }}">
                <i class="fa-solid fa-comments fa-2xl menu-icon"></i>
                <span>{{__('label.feed_comments')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.reports')}}</span></p>
        <li class="side_line  {{ request()->routeIs('admin.reason*') ? 'active' : '' }}">
            <a href="{{ route('admin.reason.index') }}">
                <i class="fa-solid fa-list fa-2xl menu-icon"></i>
                <span>{{__('label.reason')}}</span>
            </a>
        </li>
        <li class="side_line  {{ request()->routeIs('admin.contentreport*') ? 'active' : '' }}">
            <a href="{{ route('admin.contentreport.index') }}">
                <i class="fa-solid fa-clapperboard fa-2xl menu-icon"></i>
                <span>{{__('label.content')}}</span>
            </a>
        </li>
        <li class="side_line  {{ request()->routeIs('admin.feedreport*') ? 'active' : '' }}" @if(($sidebar_settings['feed_status'] ?? '1') == '0') style="display:none" @endif>
            <a href="{{ route('admin.feedreport.index') }}">
                <i class="fa-solid fa-camera-retro fa-2xl menu-icon"></i>
                <span>{{__('label.feeds')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.subscription')}}</span></p>
        <li class="side_line {{ request()->routeIs('admin.package*') ? 'active' : '' }}">
            <a href="{{ route('admin.package.index') }}">
                <i class="fa-solid fa-box-archive fa-2xl menu-icon"></i>
                <span>{{__('label.package')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.transaction*') ? 'active' : '' }}">
            <a href="{{ route('admin.transaction.index') }}">
                <i class="fa-solid fa-wallet fa-2xl menu-icon"></i>
                <span>{{__('label.transactions')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.coin')}}</span></p>
        <li class="side_line {{ request()->routeIs('admin.coinpackage*') ? 'active' : '' }}">
            <a href="{{ route('admin.coinpackage.index') }}">
                <i class="fa-solid fa-box-archive fa-2xl menu-icon"></i>
                <span>{{__('label.package')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.cointransaction*') ? 'active' : '' }}">
            <a href="{{ route('admin.cointransaction.index') }}">
                <i class="fa-solid fa-wallet fa-2xl menu-icon"></i>
                <span>{{__('label.transactions')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.gift')}}</span></p>
        <li class="side_line {{ request()->routeIs('admin.gift.*') ? 'active' : '' }}">
            <a href="{{ route('admin.gift.index') }}">
                <i class="fa-solid fa-gift fa-2xl menu-icon"></i>
                <span>{{__('label.gift')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.gifttransaction*') ? 'active' : '' }}">
            <a href="{{ route('admin.gifttransaction.index') }}">
                <i class="fa-solid fa-wallet fa-2xl menu-icon"></i>
                <span>{{__('label.transactions')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.withdrawal')}}</span></p>
        <li class="side_line {{ request()->routeIs('admin.withdrawal*') ? 'active' : '' }}">
            <a href="{{ route('admin.withdrawal.index') }}">
                <i class="fa-solid fa-right-left fa-2xl menu-icon"></i>
                <span>{{__('label.withdrawal')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.ads')}}</span></p>
        <li class="side_line {{ request()->routeIs('admin.ads.*') ? 'active' : '' }}">
            <a href="{{ route('admin.ads.index') }}">
                <i class="fa-solid fa-rectangle-ad fa-2xl menu-icon"></i>
                <span>{{__('label.custom_ads')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.admob*') ? 'active' : '' }}">
            <a href="{{ route('admin.admob.index') }}">
                <i class="fa-brands fa-square-google-plus fa-2xl menu-icon"></i>
                <span>{{__('label.admob')}}</span>
            </a>
        </li>
        <!-- <li class="side_line {{ request()->routeIs('admin.fbads*') ? 'active' : '' }}">
            <a href="{{ route('admin.fbads.index') }}">
                <i class="fa-brands fa-square-facebook fa-2xl menu-icon"></i>
                <span>{{__('label.facebook_ads')}}</span>
            </a>
        </li> -->

        <p class="partition"><span>{{__('label.settings')}}</span></p>
        <li class="side_line {{ request()->routeIs('admin.appsetting*') ? 'active' : '' }}">
            <a href="{{ route('admin.appsetting.index') }}">
                <i class="fa-solid fa-gear fa-2xl menu-icon"></i>
                <span>{{__('label.app_settings')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.page*') ? 'active' : '' }}">
            <a href="{{ route('admin.page.index') }}">
                <i class="fa-solid fa-book-open-reader fa-2xl menu-icon"></i>
                <span>{{__('label.pages')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.payment*') ? 'active' : '' }}">
            <a href="{{ route('admin.payment.index') }}">
                <i class="fa-solid fa-money-bill-wave fa-2xl menu-icon"></i>
                <span>{{__('label.payment')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.badgesbonus*') ? 'active' : '' }}">
            <a href="{{ route('admin.badgesbonus.index') }}">
                <i class="fa-solid fa-trophy fa-2xl menu-icon"></i>
                <span>{{__('label.badges_&_bonus')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.panelsetting*') ? 'active' : '' }}">
            <a href="{{ route('admin.panelsetting.index') }}">
                <i class="fa-solid fa-palette fa-2xl menu-icon"></i>
                <span>{{__('label.panel_settings')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.adssetting*') ? 'active' : '' }}">
            <a href="{{ route('admin.adssetting.index') }}">
                <i class="fa-solid fa-tools fa-2xl menu-icon"></i>
                <span>{{__('label.ads_settings')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.systemsetting*') ? 'active' : '' }}">
            <a href="{{ route('admin.systemsetting.index') }}">
                <i class="fa-solid fa-gears fa-2xl menu-icon"></i>
                <span>{{__('label.system_settings')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->routeIs('admin.storagesetting*') ? 'active' : '' }}">
            <a href="{{ route('admin.storagesetting.index') }}">
                <i class="fa-solid fa-database fa-2xl menu-icon"></i>
                <span>{{__('label.storage_settings')}}</span>
            </a>
        </li>

        <p class="partition"><span>{{__('label.logout')}}</span></p>
        <li>
            <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-arrow-right-from-bracket fa-2xl menu-icon"></i>
                <span>{{__('label.logout')}}</span>
            </a>

            <form id="logout-form" action="{{ route('admin.logout') }}" method="GET" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</div>