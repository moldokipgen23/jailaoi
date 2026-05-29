@extends('artist.layout.page-app')

@section('content')
    <div class="h-100">
        <div class="h-100 no-gutters row">
            <div class="d-none d-lg-block h-100 col-lg-5 col-xl-4">
                <div class="left-caption">
                    <img src="{{asset('assets/imgs/login.jpg')}}" class="bg-img"/>
                    <div class="caption">
                        <div>
                            <h1 style="font-size: 60px; font-weight: bold;">{{App_Name()}}</h1>
                            <?php $setting = Setting_Data();?>
                            <p class="text">
                                {{String_Cut($setting['app_desripation'], 200)}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-100 d-flex login-bg justify-content-center align-items-lg-center col-md-12 col-lg-7 col-xl-8">
                <div class="mx-auto col-sm-12 col-md-10 col-xl-8">
                    <div class="py-5">

                        <div class="app-logo mb-4">
                            <h1 class="primary-color mb-4 d-block d-lg-none">{{ App_Name() }}</h1>
                            <h3 class="primary-color mb-0 font-weight-bold">Artist Login</h3>
                        </div>

                        <h4 class="mb-0 font-weight-bold">
                            <span class="d-block mb-2">Welcome back, Artist</span>
                            <span>Please sign in to your account.</span>
                        </h4>

                        <form method="POST" id="login_form">
                            <div class="form-row mt-4">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label>Email</label>
                                        <input name="email" placeholder="Email here..." type="email" class="form-control" required autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label>Password</label>
                                        <input name="password" placeholder="Password here..." type="password" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-sm-6 text-center text-sm-left">
                                    <button class="btn btn-default mw-120" onclick="save_login()" type="button">Login</button>
                                </div>
                            </div>
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

        $('#login_form').keypress((e) => {
            if (e.which === 13) {
                save_login();
            }
        })
    </script>
@endsection
