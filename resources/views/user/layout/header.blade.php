<header class="header">
    <div class="title-control">
        <button class="btn side-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <a href="{{ route('user.dashboard') }}" class="side-logo primary-color">
            <h3>{{ App_Name() }}</h3>
        </a>

        <h1 class="page-title">@yield('page_title')</h1>
    </div>

    <div class="head-control">

        <!-- Demo Mode  -->
        @if( env('DEMO_MODE') == 'ON')
        <div class="demo-mode-box">
            <span>{{__('label.demo_mode')}}</span>
        </div>
        @endif

        <!-- Profile -->
        <div class="dropdown dropright">
            <a href="#" class="btn head-btn bg-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-user fa-2xl primary-color" class="avatar-img"></i>
            </a>

            <div class="dropdown-menu p-2 mt-2" aria-labelledby="dropdownMenuLink">
                <a class="dropdown-item primary-color" href="{{ route('user.profile.index') }}">
                    <span><i class="fa-solid fa-user fa-xl mr-2"></i></span>
                    {{__('label.profile')}}
                </a>
                <a class="dropdown-item primary-color" href="{{ route('user.password.index') }}">
                    <span><i class="fa-solid fa-lock fa-xl mr-2"></i></span>
                    {{__('label.change_password')}}
                </a>
                <a class="dropdown-item primary-color" href="{{ route('user.logout') }}">
                    <span><i class="fa-solid fa-arrow-right-from-bracket fa-xl mr-2"></i></span>
                    {{__('label.logout')}}
                </a>
            </div>
        </div>
    </div>
</header>