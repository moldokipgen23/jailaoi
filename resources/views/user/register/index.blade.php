@extends('user.layout.page-app')
@section('page_title', __('label.artist_application'))
@section('tab_title', __('label.artist_application'))

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #1a0a1f 0%, #2d0a1f 50%, #1a0a1f 100%);
        min-height: 100vh;
        margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    .reg-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }
    .reg-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(224, 30, 117, 0.25);
        width: 100%;
        max-width: 560px;
        padding: 40px 35px;
    }
    .reg-logo { text-align: center; margin-bottom: 25px; }
    .reg-logo h2 {
        color: #E01E75;
        font-weight: 700;
        font-size: 30px;
        margin: 0 0 6px 0;
        letter-spacing: -0.5px;
    }
    .reg-logo p {
        color: #6c757d;
        font-size: 13px;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 500;
    }
    .reg-intro {
        background: #fff5f9;
        border-left: 3px solid #E01E75;
        padding: 12px 14px;
        border-radius: 8px;
        font-size: 13px;
        color: #4a3a44;
        margin-bottom: 22px;
        line-height: 1.5;
    }
    .reg-form .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    @media (max-width: 540px) {
        .reg-form .form-row { grid-template-columns: 1fr; }
    }
    .reg-form .form-group { margin-bottom: 16px; }
    .reg-form label {
        color: #333;
        font-weight: 500;
        font-size: 13px;
        margin-bottom: 6px;
        display: block;
    }
    .reg-form .form-control,
    .reg-form textarea {
        width: 100%;
        padding: 11px 14px;
        font-size: 14px;
        border: 1.5px solid #e9ecef;
        border-radius: 10px;
        background: #f8f9fa;
        transition: all 0.2s ease;
        box-sizing: border-box;
        font-family: inherit;
        resize: vertical;
    }
    .reg-form .form-control:focus,
    .reg-form textarea:focus {
        border-color: #E01E75;
        background: #ffffff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(224, 30, 117, 0.1);
    }
    .reg-form .btn {
        width: 100%;
        padding: 13px 20px;
        background: #E01E75;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 8px;
        transition: all 0.2s ease;
    }
    .reg-form .btn:hover {
        background: #c41a66;
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(224, 30, 117, 0.3);
    }
    .reg-form .btn:disabled {
        background: #c9aebb;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .reg-footer {
        margin-top: 22px;
        text-align: center;
        font-size: 13px;
        color: #6c757d;
    }
    .reg-footer a {
        color: #E01E75;
        font-weight: 600;
        text-decoration: none;
    }
    .reg-footer a:hover { text-decoration: underline; }
    .text-danger { color: #E01E75; }
    .reg-success {
        text-align: center;
        padding: 30px 20px;
    }
    .reg-success .icon {
        width: 70px;
        height: 70px;
        background: #E01E75;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
    .reg-success .icon i { color: #fff; font-size: 32px; }
    .reg-success h3 { color: #333; margin: 0 0 10px 0; }
    .reg-success p { color: #6c757d; font-size: 14px; line-height: 1.6; }
</style>

<div class="reg-page">
    <div class="reg-card">
        <div id="regFormWrap">
            <div class="reg-logo">
                <h2>{{ App_Name() }}</h2>
                <p>{{__('label.artist_application')}}</p>
            </div>

            <div class="reg-intro">
                {{__('label.artist_application_intro')}}
            </div>

            <form class="reg-form" id="register_form">
                <div class="form-row">
                    <div class="form-group">
                        <label>{{__('label.full_name')}} <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{{__('label.artist_name')}} <span class="text-danger">*</span></label>
                        <input type="text" name="artist_name" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{__('label.email')}} <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>{{__('label.country_code')}}</label>
                        <input type="text" name="country_code" class="form-control" placeholder="+1">
                    </div>
                    <div class="form-group">
                        <label>{{__('label.mobile_number')}}</label>
                        <input type="text" name="mobile_number" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>{{__('label.password')}} <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label>{{__('label.confirm_password')}} <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" minlength="6" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{__('label.what_will_you_create')}} <span class="text-danger">*</span></label>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:6px;">
                        <label style="display:flex;align-items:center;gap:8px;padding:10px 14px;border:1.5px solid #e9ecef;border-radius:10px;background:#f8f9fa;cursor:pointer;flex:1;min-width:180px;font-weight:500;">
                            <input type="checkbox" name="artist_types[]" value="music" checked style="accent-color:#E01E75;width:18px;height:18px;">
                            <span>🎵 {{__('label.music')}}</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;padding:10px 14px;border:1.5px solid #e9ecef;border-radius:10px;background:#f8f9fa;cursor:pointer;flex:1;min-width:180px;font-weight:500;">
                            <input type="checkbox" name="artist_types[]" value="podcast" style="accent-color:#E01E75;width:18px;height:18px;">
                            <span>🎙️ {{__('label.podcast')}}</span>
                        </label>
                    </div>
                    <small style="color:#6c757d;font-size:12px;display:block;margin-top:6px;">{{__('label.artist_types_help')}}</small>
                </div>

                <div class="form-group">
                    <label>{{__('label.bio')}} <span class="text-danger">*</span></label>
                    <textarea name="bio" rows="4" placeholder="{{__('label.bio_placeholder')}}" required minlength="20"></textarea>
                </div>

                <button type="button" class="btn" id="submitBtn" onclick="submitRegister()">{{__('label.submit_application')}}</button>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>

            <div class="reg-footer">
                {{__('label.already_have_account')}} <a href="{{ route('user.login') }}">{{__('label.login')}}</a>
            </div>
        </div>

        <div id="regSuccessWrap" style="display:none;">
            <div class="reg-success">
                <div class="icon"><i class="fa-solid fa-check"></i></div>
                <h3>{{__('label.application_received')}}</h3>
                <p>{{__('label.application_received_desc')}}</p>
                <a href="{{ route('user.login') }}" class="btn" style="display:inline-block;text-decoration:none;margin-top:18px;padding:11px 28px;width:auto;">{{__('label.back_to_login')}}</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
function submitRegister() {
    var btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerText = "Submitting...";

    var formData = new FormData(document.getElementById('register_form'));
    $.ajax({
        type: 'POST',
        url: "{{ route('user.register.store') }}",
        data: formData,
        processData: false,
        contentType: false,
        success: function(resp) {
            btn.disabled = false;
            btn.innerText = "{{__('label.submit_application')}}";
            if (resp.status == 200) {
                document.getElementById('regFormWrap').style.display = 'none';
                document.getElementById('regSuccessWrap').style.display = 'block';
            } else {
                var err = Array.isArray(resp.errors) ? resp.errors.join('\n') : resp.errors;
                toastr.error(err);
            }
        },
        error: function() {
            btn.disabled = false;
            btn.innerText = "{{__('label.submit_application')}}";
            toastr.error('Something went wrong');
        }
    });
}

$(document).keypress(function(e) {
    if (e.which == 13 && e.target.tagName !== 'TEXTAREA') {
        submitRegister();
    }
});
</script>
@endsection
