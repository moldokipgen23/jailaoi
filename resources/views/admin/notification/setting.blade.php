@extends('admin.layout.page-app')
@section('page_title', __('label.notification_setting'))
@section('tab_title', __('label.notification_setting'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.notification_setting')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.notification.index') }}">{{__('label.notification')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.notification_setting')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.notification.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('label.notification_list')}}</a>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <form id="setting" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('label.onesignal_app_id')}}<span class="text-danger">*</span></label>
                                <input name="onesignal_app_id" type="text" class="form-control" value="{{ $result['onesignal_app_id'] }}" placeholder="{{__('label.id_here')}}" autofocus>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('label.onesignal_rest_key')}}<span class="text-danger">*</span></label>
                                <input name="onesignal_rest_key" type="text" class="form-control" value="{{ $result['onesignal_rest_key'] }}" placeholder="{{__('label.key_here')}}">
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="notification_setting()">{{__('label.save')}}</button>
                        <a href="{{route('admin.notification.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        // Sidebar Scroll Down
		sidebar_down(850);

        function notification_setting() {

            var Check_Admin = '<?php echo Demo_Mode(); ?>';
            if(Check_Admin == 1){

                $("#dvloader").show();
                var formData = new FormData($("#setting")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.notification.settingsave") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'setting', '{{ route("admin.notification.index") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                showError();
            }
        }
    </script>
@endsection