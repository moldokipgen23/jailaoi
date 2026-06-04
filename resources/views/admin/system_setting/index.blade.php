@extends('admin.layout.page-app')
@section('page_title', __('label.system_settings'))

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
            <!-- clear cache  -->
            <div class="col-6">
                <div class="card custom-border-card">
                    <a data-bs-toggle="collapse" data-bs-target="#clear_data">
                        <h5 class="card-header"><i class="fa-solid fa-chevron-down float-right"></i>{{__('label.clear_cache')}}</h5>
                    </a>

                    <div id="clear_data" class="collapse">
                        <div class="card-body">
                            <p>{{__('label.clear_cache_msg')}}</p>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-default mw-120" onclick="clear_data()">{{__('label.clear_cache')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- backup database  -->
            <div class="col-6">
                <div class="card custom-border-card">
                    <a data-bs-toggle="collapse" data-bs-target="#download_database">
                        <h5 class="card-header"><i class="fa-solid fa-chevron-down float-right"></i>{{__('label.backup_database')}}</h5>
                    </a>

                    <div id="download_database" class="collapse">
                        <div class="card-body">
                            <p>{{__('label.backup_database_msg')}}</p>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('system.setting.downloadsqlfile') }}" onclick="return confirm('{{__('label.download_sql')}}')" class="btn btn-default mw-120">{{__('label.download')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- clean database  -->
        <div class="row">
            <!-- <div class="col-6">
                    <div class="card custom-border-card">
                        <a data-bs-toggle="collapse" data-bs-target="#dummy_data">
                            <h5 class="card-header"><i class="fa-solid fa-chevron-down float-right"></i>Dummy Data</h5>
                        </a>

                        <div id="dummy_data" class="collapse">
                            <div class="card-body">
                                <p>Add the dummy data in this database.</p>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-default mw-120" onclick="dummy_data()">Insert Data</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            <div class="col-6">
                <div class="card custom-border-card">
                    <a data-bs-toggle="collapse" data-bs-target="#clean_database">
                        <h5 class="card-header"><i class="fa-solid fa-chevron-down float-right"></i>{{__('label.clean_database')}}</h5>
                    </a>

                    <div id="clean_database" class="collapse">
                        <div class="card-body">
                            <p>{{__('label.clean_database_msg')}}</p>
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
    sidebar_down($(document).height());

    function clear_data() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            if (confirm('Do you confirm Clear the Data !!!')) {

                $("#dvloader").show();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    enctype: 'multipart/form-data',
                    type: 'POST',
                    url: '{{ route("system.setting.cleardata") }}',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, '', '{{ route("system.setting.index") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            }
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }

    function dummy_data() {
        
        if (confirm('Do you confirm Insert the Dummy Data !!!')) {

            $("#dvloader").show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                enctype: 'multipart/form-data',
                type: 'POST',
                url: '{{ route("system.setting.dummydata") }}',
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, '', '{{ route("system.setting.index") }}');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        }
    }

    function clean_database() {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            if (confirm('Do you confirm Clean the Database !!!')) {

                $("#dvloader").show();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    enctype: 'multipart/form-data',
                    type: 'POST',
                    url: '{{ route("system.setting.cleandatabase") }}',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, '', '{{ route("system.setting.index") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            }
        } else {
            toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
        }
    }
</script>
@endsection