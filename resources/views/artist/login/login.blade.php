@extends('artist.layout.page-app')
@section('tab_title', __('label.artist_login'))

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height:100vh">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold" style="color:#667eea">{{ App_Name() }}</h2>
                        <p class="text-muted">Artist Portal Login</p>
                    </div>
                    <form id="login_form">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input name="email" type="email" class="form-control form-control-lg" placeholder="Enter email" autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input name="password" type="password" class="form-control form-control-lg" placeholder="Enter password">
                        </div>
                        <button type="button" class="btn btn-primary w-100 btn-lg" onclick="save_login()" style="background:linear-gradient(135deg,#667eea,#764ba2);border:none">Login</button>
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
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: '{{ route("artist.save.login") }}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(resp) {
                $("#dvloader").hide();
                get_responce_message(resp, 'login_form', '{{ route("artist.dashboard") }}');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("#dvloader").hide();
                toastr.error(errorThrown, textStatus);
            }
        });
    }
    $('#login_form').keypress((e) => { if (e.which === 13) save_login(); })
</script>
@endsection
