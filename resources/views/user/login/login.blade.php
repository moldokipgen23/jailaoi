@extends('user.layout.page-app')
@section('page_title', __('label.artist_portal'))
@section('tab_title', __('label.artist_portal'))

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #1a0a1f 0%, #2d0a1f 50%, #1a0a1f 100%);
        min-height: 100vh;
        margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .login-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(224, 30, 117, 0.25);
        width: 100%;
        max-width: 420px;
        padding: 40px 35px;
    }
    .login-logo {
        text-align: center;
        margin-bottom: 30px;
    }
    .login-logo h2 {
        color: #E01E75;
        font-weight: 700;
        font-size: 32px;
        margin: 0 0 6px 0;
        letter-spacing: -0.5px;
    }
    .login-logo p {
        color: #6c757d;
        font-size: 14px;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 500;
    }
    .login-form .form-group {
        margin-bottom: 20px;
    }
    .login-form label {
        color: #333;
        font-weight: 500;
        font-size: 14px;
        margin-bottom: 8px;
        display: block;
    }
    .login-form .form-control {
        width: 100%;
        padding: 12px 16px;
        font-size: 14px;
        border: 1.5px solid #e9ecef;
        border-radius: 10px;
        background: #f8f9fa;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }
    .login-form .form-control:focus {
        border-color: #E01E75;
        background: #ffffff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(224, 30, 117, 0.1);
    }
    .login-form .btn {
        width: 100%;
        padding: 13px 20px;
        background: #E01E75;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
        transition: all 0.2s ease;
        letter-spacing: 0.3px;
    }
    .login-form .btn:hover {
        background: #c41a66;
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(224, 30, 117, 0.3);
    }
    .login-footer {
        margin-top: 30px;
        text-align: center;
        font-size: 12px;
        color: #adb5bd;
    }
    .text-danger { color: #E01E75; }
</style>

<div class="login-page">
    <div class="login-card">
        <div class="login-logo">
            <h2>{{ App_Name() }}</h2>
            <p>{{__('label.artist_portal')}}</p>
        </div>

        <form class="login-form" id="login_form">
            <div class="form-group">
                <label>{{__('label.email')}} <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="{{__('label.email_here')}}" autofocus>
            </div>

            <div class="form-group">
                <label>{{__('label.password')}} <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="{{__('label.password_here')}}">
            </div>

            <div style="text-align:right;margin:-10px 0 12px;">
                <a href="{{ route('user.password.forgot') }}" style="color:#E01E75;font-size:13px;font-weight:500;text-decoration:none;">{{__('label.forgot_password')}}?</a>
            </div>

            <button type="button" class="btn" onclick="save_login()">{{__('label.login')}}</button>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>

        <div style="text-align:center;margin-top:18px;font-size:13px;color:#6c757d;">
            {{__('label.new_artist_question')}} <a href="{{ route('user.register') }}" style="color:#E01E75;font-weight:600;text-decoration:none;">{{__('label.apply_as_artist')}}</a>
        </div>

        <div class="login-footer">
            &copy; {{ date('Y') }} {{ App_Name() }} &middot; {{__('label.all_right_reserved')}}
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    function save_login() {
        var formData = new FormData($("#login_form")[0]);
        $("#dvloader").show();
        $.ajax({
            type: 'POST',
            url: "{{ route('user.save.login') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                $("#dvloader").hide();
                if (resp.status == 200) {
                    window.location.href = "{{ route('user.dashboard') }}";
                } else {
                    toastr.error(resp.errors);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("#dvloader").hide();
                toastr.error(errorThrown, textStatus);
            }
        });
    }

    $(document).keypress(function(e) {
        if (e.which == 13) {
            save_login();
        }
    });
</script>
@endsection
