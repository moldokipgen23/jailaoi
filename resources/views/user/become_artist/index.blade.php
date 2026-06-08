@extends('user.layout.page-app')
@section('page_title', __('label.become_an_artist'))
@section('tab_title', __('label.become_an_artist'))

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #1a0a1f 0%, #2d0a1f 50%, #1a0a1f 100%);
        min-height: 100vh;
        margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    .ba-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }
    .ba-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(224, 30, 117, 0.25);
        width: 100%;
        max-width: 520px;
        padding: 40px 35px;
    }
    .ba-logo { text-align: center; margin-bottom: 25px; }
    .ba-logo h2 {
        color: #E01E75;
        font-weight: 700;
        font-size: 30px;
        margin: 0 0 6px 0;
        letter-spacing: -0.5px;
    }
    .ba-logo p {
        color: #6c757d;
        font-size: 13px;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 500;
    }
    .ba-intro {
        background: #fff5f9;
        border-left: 3px solid #E01E75;
        padding: 12px 14px;
        border-radius: 8px;
        font-size: 13px;
        color: #4a3a44;
        margin-bottom: 22px;
        line-height: 1.5;
    }
    .ba-form .form-group { margin-bottom: 16px; }
    .ba-form label {
        color: #333;
        font-weight: 500;
        font-size: 13px;
        margin-bottom: 6px;
        display: block;
    }
    .ba-form .form-control,
    .ba-form textarea {
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
    .ba-form .form-control:focus,
    .ba-form textarea:focus {
        border-color: #E01E75;
        background: #ffffff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(224, 30, 117, 0.1);
    }
    .ba-form .btn {
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
    .ba-form .btn:hover {
        background: #c41a66;
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(224, 30, 117, 0.3);
    }
    .ba-form .btn:disabled {
        background: #c9aebb;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .text-danger { color: #E01E75; }
    .ba-status {
        text-align: center;
        padding: 20px 10px;
    }
    .ba-status .status-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
    .ba-status .status-icon i { color: #fff; font-size: 32px; }
    .ba-status .status-icon.pending { background: #ffc107; }
    .ba-status .status-icon.approved { background: #28a745; }
    .ba-status .status-icon.rejected { background: #dc3545; }
    .ba-status h3 { color: #333; margin: 0 0 10px 0; }
    .ba-status p { color: #6c757d; font-size: 14px; line-height: 1.6; }
    .ba-footer {
        margin-top: 22px;
        text-align: center;
        font-size: 13px;
        color: #6c757d;
    }
    .ba-footer a {
        color: #E01E75;
        font-weight: 600;
        text-decoration: none;
    }
    .ba-footer a:hover { text-decoration: underline; }
    .photo-upload {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .photo-upload .photo-preview {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: #f8f9fa;
        border: 2px dashed #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }
    .photo-upload .photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .photo-upload .photo-preview i {
        font-size: 24px;
        color: #adb5bd;
    }
    .photo-upload .photo-btn {
        padding: 8px 16px;
        background: #f8f9fa;
        border: 1.5px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        color: #333;
        font-weight: 500;
    }
</style>

<div class="ba-page">
    <div class="ba-card">
        @if ($step === 'login')
            {{-- JAILAOI: Login step --}}
            <div id="loginWrap">
                <div class="ba-logo">
                    <h2>{{ App_Name() }}</h2>
                    <p>{{__('label.become_an_artist')}}</p>
                </div>
                <div class="ba-intro">
                    Already have a JailaOi account? Log in below to apply as an artist.
                </div>
                <form class="ba-form" id="loginForm">
                    <div class="form-group">
                        <label>{{__('label.email')}} <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{{__('label.password')}} <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="button" class="btn" id="loginBtn" onclick="submitLogin()">{{__('label.login')}}</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
                <div class="ba-footer">
                    New to JailaOi? <a href="{{ route('user.register') }}">Register as Artist</a>
                </div>
            </div>
        @elseif ($step === 'form')
            {{-- JAILAOI: Application form step --}}
            <div id="formWrap">
                <div class="ba-logo">
                    <h2>{{ App_Name() }}</h2>
                    <p>{{__('label.become_an_artist')}}</p>
                </div>
                <div class="ba-intro">
                    {{__('label.artist_application_intro')}}
                </div>
                <form class="ba-form" id="applyForm">
                    <div class="form-group">
                        <label>{{__('label.artist_name')}} <span class="text-danger">*</span></label>
                        <input type="text" name="artist_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{{__('label.bio')}} <span class="text-danger">*</span></label>
                        <textarea name="bio" rows="4" placeholder="{{__('label.bio_placeholder')}}" required minlength="20"></textarea>
                    </div>
                    <div class="form-group">
                        <label>{{__('label.what_will_you_create')}} <span class="text-danger">*</span></label>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:6px;">
                            <label style="display:flex;align-items:center;gap:8px;padding:10px 14px;border:1.5px solid #e9ecef;border-radius:10px;background:#f8f9fa;cursor:pointer;flex:1;min-width:180px;font-weight:500;">
                                <input type="checkbox" name="artist_types[]" value="music" checked style="accent-color:#E01E75;width:18px;height:18px;">
                                <span>&#x1F3B5; {{__('label.music')}}</span>
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;padding:10px 14px;border:1.5px solid #e9ecef;border-radius:10px;background:#f8f9fa;cursor:pointer;flex:1;min-width:180px;font-weight:500;">
                                <input type="checkbox" name="artist_types[]" value="podcast" style="accent-color:#E01E75;width:18px;height:18px;">
                                <span>&#x1F399;&#xFE0F; {{__('label.podcast')}}</span>
                            </label>
                        </div>
                        <small style="color:#6c757d;font-size:12px;display:block;margin-top:6px;">{{__('label.artist_types_help')}}</small>
                    </div>
                    <button type="button" class="btn" id="applyBtn" onclick="submitApplication()">{{__('label.submit_application')}}</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
        @elseif ($step === 'status')
            {{-- JAILAOI: Application status step --}}
            <div id="statusWrap">
                <div class="ba-logo">
                    <h2>{{ App_Name() }}</h2>
                    <p>{{__('label.artist_application')}}</p>
                </div>
                <div class="ba-status">
                    @php
                        $status = $request->status ?? 'pending';
                        $statusIcon = $status === 'approved' ? 'fa-check' : ($status === 'rejected' ? 'fa-xmark' : 'fa-clock');
                        $statusTitle = $status === 'approved' ? 'Application Approved!' : ($status === 'rejected' ? 'Application Rejected' : 'Application Under Review');
                        $statusDesc = $status === 'approved'
                            ? 'Congratulations! Your artist application has been approved. You can now log in to the artist portal.'
                            : ($status === 'rejected'
                                ? 'Your application was not approved at this time. ' . ($request->admin_note ? 'Reason: ' . $request->admin_note : '')
                                : 'Thank you for applying! We are reviewing your application. You will receive an email once a decision is made.');
                    @endphp
                    <div class="status-icon {{ $status }}">
                        <i class="fa-solid {{ $statusIcon }}"></i>
                    </div>
                    <h3>{{ $statusTitle }}</h3>
                    <p>{{ $statusDesc }}</p>
                    @if ($status === 'approved')
                        <a href="{{ route('user.login') }}" class="btn" style="display:inline-block;text-decoration:none;margin-top:18px;padding:11px 28px;width:auto;">{{__('label.login')}}</a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('pagescript')
@if ($step === 'login')
<script>
function submitLogin() {
    var btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerText = "Logging in...";

    var formData = new FormData(document.getElementById('loginForm'));
    $.ajax({
        type: 'POST',
        url: "{{ route('user.become.artist.login') }}",
        data: formData,
        processData: false,
        contentType: false,
        success: function(resp) {
            btn.disabled = false;
            btn.innerText = "{{__('label.login')}}";
            if (resp.status == 200) {
                window.location.reload();
            } else {
                var err = Array.isArray(resp.errors) ? resp.errors.join('\n') : resp.errors;
                toastr.error(err);
            }
        },
        error: function() {
            btn.disabled = false;
            btn.innerText = "{{__('label.login')}}";
            toastr.error('Something went wrong');
        }
    });
}

$(document).keypress(function(e) {
    if (e.which == 13) {
        submitLogin();
    }
});
</script>
@elseif ($step === 'form')
<script>
function submitApplication() {
    var btn = document.getElementById('applyBtn');
    btn.disabled = true;
    btn.innerText = "Submitting...";

    var formData = new FormData(document.getElementById('applyForm'));
    $.ajax({
        type: 'POST',
        url: "{{ route('user.become.artist.store') }}",
        data: formData,
        processData: false,
        contentType: false,
        success: function(resp) {
            btn.disabled = false;
            btn.innerText = "{{__('label.submit_application')}}";
            if (resp.status == 200) {
                toastr.success(resp.success);
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
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
</script>
@endif
@endsection
