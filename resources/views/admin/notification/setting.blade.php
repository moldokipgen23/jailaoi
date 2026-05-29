@extends('admin.layout.page-app')
@section('page_title', __('Label.Notification_Setting'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('Label.Notification_Setting')}}</h1>

            <div class="border-bottom row">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('Label.Notification_Setting')}}</li>
                    </ol>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <form id="notification-setting" autocomplete="off" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.ONESIGNAL APP ID')}}</label>
                                <input name="onesignal_apid" type="text" class="form-control" value="@if($result){{$result['onesignal_apid']}}@endif" placeholder="ENTER ONESIGNAL APP ID">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.ONESIGNAL REST KEY')}}</label>
                                <input name="onesignal_rest_key" type="text" class="form-control" value="@if($result){{$result['onesignal_rest_key']}}@endif" placeholder="ENTER ONESIGNAL REST KEY">
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="notification_setting()">{{__('Label.SAVE')}}</button>
                        <a href="{{route('notification.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('Label.CANCEL')}}</a>
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
		sidebar_down($(document).height());

        function notification_setting() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if (Check_Admin == 1) {

                $("#dvloader").show();
                var formData = new FormData($("#notification-setting")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("notification.settingsave") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'notification-setting', '{{ route("notification.index") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                toastr.error('You have no right to add, edit, and delete.');
            }
        }
    </script>
@endsection