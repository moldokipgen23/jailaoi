@extends('user.layout.page-app')
@section('page_title', __('label.artist_portal'))
@section('tab_title', __('label.artist_portal'))

@section('content')
<div class="login-page">
    <div class="login-card">
        <div class="login-logo">
            <h2>{{ App_Name() }}</h2>
            <p>{{__('label.welcome_back_artist')}}</p>
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

            <button type="button" class="btn btn-default" onclick="save_login()">{{__('label.login')}}</button>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>

        <div class="login-footer">
            &copy; {{ date('Y') }} {{ App_Name() }}. {{__('label.all_right_reserved')}}.
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
