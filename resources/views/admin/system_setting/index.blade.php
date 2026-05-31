@extends('admin.layout.page-app')
@section('page_title', __('label.system_settings'))
@section('tab_title', __('label.system_settings'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">

            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.system_settings')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.system_settings')}}</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="card custom-border-card">
                        <a data-bs-toggle="collapse" data-bs-target="#clear_data">
                            <h5 class="card-header"><i class="fa-solid fa-chevron-down float-right"></i>{{__('label.clear_cache')}}</h5>
                        </a>

                        <div id="clear_data" class="collapse">
                            <div class="card-body">
                                <p>{{__('label.clear_cache_notes')}}</p>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-default mw-120" onclick="clear_data()">{{__('label.clear_cache')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card custom-border-card">
                        <a data-bs-toggle="collapse" data-bs-target="#clear_interest">
                            <h5 class="card-header"><i class="fa-solid fa-chevron-down float-right"></i>{{__('label.clear_low_interests_activity')}}</h5>
                        </a>

                        <div id="clear_interest" class="collapse">
                            <div class="card-body">
                                <p>{{__('label.clear_interests_notes')}}</p>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-default mw-120" onclick="clear_interests()">{{__('label.clear_interests')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="card custom-border-card">
                        <a data-bs-toggle="collapse" data-bs-target="#download_database">
                            <h5 class="card-header"><i class="fa-solid fa-chevron-down float-right"></i>{{__('label.backup_database')}}</h5>
                        </a>
    
                        <div id="download_database" class="collapse">
                            <div class="card-body">
                                <p>{{__('label.backup_data_notes')}}</p>
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.systemsetting.downloadsqlfile') }}" onclick="event.preventDefault(); confirmLink(this.href, '{{__('label.download')}}', '{{__('label.you_want_to_download_this_sql_file')}}', '{{__('label.download')}}', 'btn-primary')" class="btn btn-default mw-120">{{__('label.download')}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card custom-border-card">
                        <a data-bs-toggle="collapse" data-bs-target="#clean_database">
                            <h5 class="card-header"><i class="fa-solid fa-chevron-down float-right"></i>{{__('label.clean_database')}}</h5>
                        </a>

                        <div id="clean_database" class="collapse">
                            <div class="card-body">
                                <p>{{__('label.clean_database_notes')}}</p>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-default mw-120" onclick="clean_database()">{{__('label.clean_database')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        // Sidebar Scroll Down
        let sidebarHeight = $('.sidebar')[0].scrollHeight;
        sidebar_down(sidebarHeight);

        function clear_data() {
            confirmAction({
                title: '{{__("label.clear_data")}}',
                message: '{{__("label.do_you_confirm_clear_the_data")}}',
                btnText: '{{__("label.clear")}}',
                btnClass: 'btn-warning',
                onConfirm: function() {
                    $("#dvloader").show();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        enctype: 'multipart/form-data',
                        type: 'POST',
                        url: '{{ route("admin.systemsetting.cleardata") }}',
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(resp) {
                            $("#dvloader").hide();
                            get_responce_message(resp, '', '{{ route("admin.systemsetting.index") }}');
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            $("#dvloader").hide();
                            toastr.error(errorThrown, textStatus);
                        }
                    });
                }
            });
        }
        function clean_database() {
			var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                confirmAction({
                    title: '{{__("label.clean_database")}}',
                    message: '{{__("label.do_you_confirm_clean_the_database")}}',
                    btnText: '{{__("label.clean")}}',
                    btnClass: 'btn-warning',
                    onConfirm: function() {
                        $("#dvloader").show();
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            enctype: 'multipart/form-data',
                            type: 'POST',
                            url: '{{ route("admin.systemsetting.cleandatabase") }}',
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function(resp) {
                                $("#dvloader").hide();
                                get_responce_message(resp, '', '{{ route("admin.systemsetting.index") }}');
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                $("#dvloader").hide();
                                toastr.error(errorThrown, textStatus);
                            }
                        });
                    }
                });
			} else {
                showError();
            }
        }
        function clear_interests() {
            confirmAction({
                title: '{{__("label.clear")}}',
                message: '{{__("label.do_you_confirm_clear_the_data")}}',
                btnText: '{{__("label.clear")}}',
                btnClass: 'btn-warning',
                onConfirm: function() {
                    $("#dvloader").show();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        enctype: 'multipart/form-data',
                        type: 'POST',
                        url: '{{ route("admin.systemsetting.clearinterests") }}',
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(resp) {
                            $("#dvloader").hide();
                            get_responce_message(resp, '', '{{ route("admin.systemsetting.index") }}');
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            $("#dvloader").hide();
                            toastr.error(errorThrown, textStatus);
                        }
                    });
                }
            });
        }
    </script>
@endsection