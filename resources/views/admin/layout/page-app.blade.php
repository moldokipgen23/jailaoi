<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tag -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tab Icon -->
    <link rel="shortcut icon" href="{{ Tab_Icon() }}">

    <!-- Title Tag  -->
    <title>@yield('tab_title') | {{ App_Name() }}</title>

    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <!-- base_url -->
    <input type="hidden" value="{{URL('')}}" id="base_url">

    <!-- Custom Style -->
    <style>
        /* Select 2 DropDown */
        .select2-container .select2-selection--single {
            border: 1px solid #e9ecef;
            background: #fdfdfd;
            border-radius: 8px;
            padding: 8px;
            font-size: 14px;
            height: auto !important;
        }
        .select2-container .select2-selection--multiple {
            border: 1px solid #e9ecef;
            background: #fdfdfd;
            border-radius: 8px;
            padding: 8px;
            font-size: 14px;
            height: auto !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border: 1px solid #4e45b8 !important;
        }
    </style>

    <!--Custom Script-->
    <script>
        var globalSiteUrl = '<?php echo $path = url('/'); ?>'
        var serverEnvironment = '<?php echo env('APP_ENV'); ?>'
        var currentRouteName = '<?php echo request()->route()->getName(); ?>'
    </script>
</head>

<body>

    @yield('content')

    <div style="display:none" id="dvloader"><img src="{{ asset('assets/imgs/loading.gif')}}" /></div>

    <!-- Jquery -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Datatable -->
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/js.js')}}"></script>
    <!-- Toastr -->
    <script src="{{ asset('assets/js/toastr.min.js')}}"></script>

    <script>
        // Counter
        $('.counting').each(function() {
            var $this = $(this),
                countTo = $this.attr('data-count');

            countTo = getVal(countTo);

            $(this).prop('Counter', 0).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'swing',
                step: function(now) {
                    $(this).text(Math.ceil(now));
                },
                complete: function() {
                    $this.text($this.attr('data-count'));
                }
            });
        });
        function getVal(val) {

            multiplier = val.substr(-1).toLowerCase();

            if (multiplier == "k")
                return parseFloat(val) * 1000;
            else if (multiplier == "m")
                return parseFloat(val) * 1000000;
            else if (multiplier == "b")
                return parseFloat(val) * 1000000000;
            else if (multiplier == "t")
                return parseFloat(val) * 1000000000000;
            else
                return val;
        }

        function get_responce_message(resp, form_name="", url="") {
            if (resp.status == '200') {
                toastr.success(resp.success);
                if(form_name != ""){
                    document.getElementById(form_name).reset();
                }
                if(url != ""){  
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

        // Toastr MSG Show
        @if(Session::has('error'))
            toastr.error('{{ Session::get("error") }}');
        @elseif(Session::has('success'))
            toastr.success('{{ Session::get("success") }}');
        @endif

        // Image Upload Preview Add
        $('#imageUpload1').change(function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview1').attr("src", e.target.result);
                    $('#imagePreview1').hide();
                    $('#imagePreview1').fadeIn(650);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        $('#imageUpload2').change(function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview2').attr("src", e.target.result);
                    $('#imagePreview2').hide();
                    $('#imagePreview2').fadeIn(650);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        $('#imageUpload3').change(function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview3').attr("src", e.target.result);
                    $('#imagePreview3').hide();
                    $('#imagePreview3').fadeIn(650);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        $('#imageUpload4').change(function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview4').attr("src", e.target.result);
                    $('#imagePreview4').hide();
                    $('#imagePreview4').fadeIn(650);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Sidebar Scroll Down
        function sidebar_down(height) {
            $(".sidebar").animate({
                scrollTop: height
            });
        }

        // DataTable Defaults
        var dataTableDefaults = {
            dom: "<'top'f>rt<'row'<'col-2'i><'col-1'l><'col-9'p>>",
            searching: false,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            lengthMenu: [
                [10, 50, 100, 500, -1],
                [10, 50, 100, 500, "All"]
            ],
            language: {
                paginate: {
                    previous: "<i class='fa-solid fa-chevron-left'></i>",
                    next: "<i class='fa-solid fa-chevron-right'></i>"
                }
            }
        };

        // Demo Mode Ajex Error
        function showError() {
            toastr.error("{{__('label.access_denied_can_not_add_edit_delete')}}");
        }

        // Convert Date Time
        function msToHours(duration) {
            var hours = Math.floor((duration / (1000 * 60 * 60)) % 24);
                hours = (hours < 10) ? "0" + hours : hours;
                return hours;
        }
        function msToMinutes(duration) {
            var minutes = Math.floor((duration / (1000 * 60)) % 60),
                minutes = (minutes < 10) ? "0" + minutes : minutes;
                return minutes;
        }
        function msToSeconds(duration) {
            var seconds = Math.floor((duration / 1000) % 60),
                seconds = (seconds < 10) ? "0" + seconds : seconds;
                return seconds;
        }

        // Sidebar toggle (desktop)
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-collapsed');
        }

        // Mobile sidebar toggle
        function toggleMobileSidebar() {
            document.querySelector('.sidebar').classList.toggle('mobile-open');
        }

        // Close mobile sidebar on click outside
        document.addEventListener('click', function(e) {
            var sidebar = document.querySelector('.sidebar');
            var toggle = document.getElementById('mobileToggle');
            if (window.innerWidth <= 768 && sidebar.classList.contains('mobile-open') &&
                !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });

        // Remove sidebar-collapsed class on small screens
        if (window.innerWidth <= 768) {
            document.body.classList.remove('sidebar-collapsed');
        }
    </script>

    @yield('pagescript')
</body>

</html>