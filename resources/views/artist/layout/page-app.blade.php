<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{Tab_Icon()}}">

    <title>{{ App_Name() }} - Artist Dashboard</title>

    <link href="{{asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{asset('assets/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{asset('assets/css/toastr.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.css">

    <input type="hidden" value="{{URL('')}}" id="base_url">

    <style>
        .select2-container .select2-selection--single {
            border: 1px solid #f5f5f5;
            background: #fdfdfd;
            border-radius: 8px;
            padding: 8px;
            font-size: 14px;
            height: auto !important;
        }
    </style>

    <script>
        var globalSiteUrl = '<?php echo url('/'); ?>'
        var currentRouteName = '<?php echo request()->route()->getName(); ?>'
    </script>
</head>

<body>

    @yield('content')

    <div style="display:none" id="dvloader"><img src="{{ asset('assets/imgs/loading.gif')}}" /></div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/js.js')}}"></script>
    <script src="{{ asset('assets/js/toastr.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        function get_responce_message(resp, form_name = "", url = "") {
            if (resp.status == '200') {
                toastr.success(resp.success);
                if (form_name != "") {
                    document.getElementById(form_name).reset();
                }
                if (url != "") {
                    setTimeout(function() {
                        window.location.replace(url);
                    }, 500);
                }
            } else {
                var obj = resp.errors;
                if (typeof obj === 'string') {
                    toastr.error(obj);
                } else {
                    $.each(obj, function(i, e) {
                        toastr.error(e);
                    });
                }
            }
        }

        @if(Session::has('error'))
        toastr.error('{{ Session::get("error") }}');
        @elseif(Session::has('success'))
        toastr.success('{{ Session::get("success") }}');
        @endif

        $('#imageUpload').change(function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr("src", e.target.result);
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>

    @yield('pagescript')
</body>

</html>
