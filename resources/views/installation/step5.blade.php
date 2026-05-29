@extends('installation.layout.page-app')

@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-12 col-xl-6 d-flex flex-column justify-content-center">
            <div class="install-card">

                <!-- Alert MSG -->
                @if(session()->has('error'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">X</button>
                        <strong>{{ Session::get('error') }}</strong>
                    </div>
                @elseif(session()->has('success'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">X</button>
                        <strong>{{ Session::get('success') }}</strong>
                    </div>
                @endif

                <h1 class="primary-color install-title">{{__('label.admin_account_settings')}} <i class="fa fa-cogs"></i></h1>
                <h1 class="install_sub_title">{{__('label.provide_your_information')}}</h1>

                <div class="mt-3">
                    <form method="POST" action="{{ route('system_settings',['token'=>bcrypt('step_5')]) }}" onsubmit="showLoder()">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.admin_email')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="email" class="form-control" placeholder="{{__('label.admin@admin.com')}}" autofocus>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('label.password')}}<span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" placeholder="{{__('label.admin')}}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-install">{{__('label.continue')}}<i class="fa-solid fa-angles-right ml-2"></i></button>
                    </form>
                </div>

                <!-- Footer -->
                @include('installation.layout.footer')

            </div>
        </div>
        <div class="col-lg-6 install-bg-img d-none d-lg-block">
            <img src="{{ asset('assets/imgs/install_bg.png') }}">
        </div>
    </div>
@endsection