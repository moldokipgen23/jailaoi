@extends('user.layout.page-app')
@section('page_title', __('label.forgot_password'))
@section('tab_title', __('label.forgot_password'))

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #1a0a1f 0%, #2d0a1f 50%, #1a0a1f 100%);
        min-height: 100vh; margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    .login-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .login-card { background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(224,30,117,0.25); width: 100%; max-width: 420px; padding: 40px 35px; }
    .login-logo { text-align: center; margin-bottom: 30px; }
    .login-logo h2 { color: #E01E75; font-weight: 700; font-size: 32px; margin: 0 0 6px 0; }
    .login-logo p { color: #6c757d; font-size: 14px; margin: 0; text-transform: uppercase; letter-spacing: 1.5px; }
    .login-form .form-group { margin-bottom: 20px; }
    .login-form label { color: #333; font-weight: 500; font-size: 14px; margin-bottom: 8px; display: block; }
    .login-form .form-control { width: 100%; padding: 12px 16px; font-size: 14px; border: 1.5px solid #e9ecef; border-radius: 10px; background: #f8f9fa; box-sizing: border-box; }
    .login-form .form-control:focus { border-color: #E01E75; background: #fff; outline: none; box-shadow: 0 0 0 3px rgba(224,30,117,0.1); }
    .login-form .btn { width: 100%; padding: 13px 20px; background: #E01E75; color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 10px; }
    .login-form .btn:hover { background: #c41a66; }
    .login-form .btn:disabled { background: #c9aebb; cursor: not-allowed; }
    .login-footer { margin-top: 22px; text-align: center; font-size: 13px; color: #6c757d; }
    .login-footer a { color: #E01E75; font-weight: 600; text-decoration: none; }
    .text-danger { color: #E01E75; }
    .intro { color: #6c757d; font-size: 13px; text-align: center; margin-bottom: 20px; line-height: 1.5; }
</style>

<div class="login-page">
    <div class="login-card">
        <div class="login-logo">
            <h2>{{ App_Name() }}</h2>
            <p>{{__('label.reset_password')}}</p>
        </div>

        <p class="intro">{{__('label.forgot_password_intro')}}</p>

        <form class="login-form" id="forgot_form">
            <div class="form-group">
                <label>{{__('label.email')}} <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" autofocus required>
            </div>
            <button type="button" class="btn" id="submitBtn" onclick="submitForgot()">{{__('label.send_reset_link')}}</button>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div id="msg" style="margin-top:14px;"></div>
        </form>

        <div class="login-footer">
            <a href="{{ route('user.login') }}">← {{__('label.back_to_login')}}</a>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
function submitForgot() {
    var btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerText = "Sending...";
    var formData = new FormData(document.getElementById('forgot_form'));
    $.ajax({
        type: 'POST',
        url: "{{ route('user.password.email') }}",
        data: formData,
        processData: false,
        contentType: false,
        success: function(resp) {
            btn.disabled = false;
            btn.innerText = "{{__('label.send_reset_link')}}";
            var msg = document.getElementById('msg');
            if (resp.status == 200) {
                msg.innerHTML = '<div style="background:#d4edda;color:#155724;padding:12px;border-radius:8px;font-size:13px;text-align:center;">' + resp.success + '</div>';
            } else {
                var err = Array.isArray(resp.errors) ? resp.errors.join('<br>') : resp.errors;
                msg.innerHTML = '<div style="background:#f8d7da;color:#721c24;padding:12px;border-radius:8px;font-size:13px;text-align:center;">' + err + '</div>';
            }
        },
        error: function() {
            btn.disabled = false;
            btn.innerText = "{{__('label.send_reset_link')}}";
            document.getElementById('msg').innerHTML = '<div style="color:#721c24;font-size:13px;">Something went wrong</div>';
        }
    });
}
$(document).keypress(function(e) { if (e.which == 13) submitForgot(); });
</script>
@endsection
