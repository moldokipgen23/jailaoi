@extends('admin.layout.page-app')

@section('content')
<div class="h-100">
    <div class="h-100 no-gutters row">
        <div class="d-none d-lg-block h-100 col-lg-8 col-xl-7">
            <div class="left-caption">
                <img src="{{login_image()}}" class="bg-img" />
                <div class="caption">
                    <div>
                        <!-- logo -->
                        <h1 class="logo logo-color">{{App_Name()}}</h1>

                        <?php $setting = Setting_Data(); ?>
                        <p class="text">
                            {{$setting['app_desripation']}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-100 d-flex login-bg justify-content-center align-items-lg-center col-md-12 col-lg-4 col-xl-5">
            <div class="mx-auto col-sm-12 col-md-10 col-xl-8">
                <div class="py-5">
                    <div class="app-logo mb-4">
                        <h1 class="primary-color mb-4 d-block d-lg-none">{{ App_Name() }}</h1>
                        <h3 class="primary-color mb-0 font-weight-bold text-center">{{__('label.login')}}</h3>
                    </div>

                    <h4 class="mb-0 font-weight-bold text-center mb-4">
                        <span class="d-block mb-2">{{__('label.welcome_back_admin')}}</span>
                        <span>{{__('label.please_sign_in_to_your_account')}}</span>
                    </h4>

                    @php
                    $emailValue = Check_Admin_Access() == 0 ? 'admin@admin.com' : '';
                    $passwordValue = Check_Admin_Access() == 0 ? 'admin' : '';
                    @endphp

                    <form method="POST" id="login_form">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>{{__('label.email')}}</label>
                                <input name="email" placeholder="{{__('label.email_here')}}" type="email" class="form-control" value="{{ $emailValue }}" required autofocus>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>{{__('label.password')}}</label>
                                <input name="password" placeholder="{{__('label.password_here')}}" type="password" class="form-control" value="{{ $passwordValue }}" required>
                            </div>
                        </div>
                        <div class="col-12 text-center text-sm-left">
                            <button class="btn btn-default w-100" onclick="save_login()" type="button">Login</button>
                        </div>
                        @if(Check_Admin_Access() == 0)
                        <hr>
                        <h6>
                            If you cannot login, then
                            <a href="{{ route('admin.login') }}" class="btn-link" target="_blank">{{ __('label.click_here') }}</a>
                        </h6>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    function save_login() {

        $("#dvloader").show();
        var formData = new FormData($("#login_form")[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{ route("admin.save.login") }}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(resp) {
                $("#dvloader").hide();
                get_responce_message(resp, 'login_form', '{{ route("admin.dashboard") }}');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("#dvloader").hide();
                toastr.error(errorThrown, textStatus);
            }
        });
    }

    // Press Enter Key & Save Form
    $('#login_form').keypress((e) => {
        // Enter key corresponds to number 13 
        if (e.which === 13) {
            save_login();
        }
    })
</script>
@endsection