@extends('admin.layout.page-app')
@section('page_title', __('label.notification_configuration'))
@section('tab_title', __('label.notification_configuration'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">

        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.notification_configuration')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.notification_configuration')}}</li>
                </ol>
            </div>
        </div>
        <!-- main checkbox  -->
        <div class="col-8 mb-2">
            <div class="custom-control custom-checkbox mr-sm-2">
                <input type="checkbox" class="custom-control-input" id="notificationToggle" {{ $main_status == 1 ? 'checked' : ''}} autofocus>
                <label class="custom-control-label h5 font-weight-bold" for="notificationToggle">{{__('label.do_you_want_to_disable_all_notifications')}}</label>
            </div>
        </div>
        <!-- table  -->
        <div class="table-responsive table" id="dataTable-container">
            <table class="table table-striped text-center table-bordered" id="datatable">
                <thead>
                    <tr class="bg-table">
                        <th> {{ __('label.type') }} </th>
                        <th> {{ __('label.send_notification') }} </th>
                        <th> {{ __('label.send_mail') }} </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        @if($main_status ==1)
        <div>
            <button class="btn btn-default mw-120" id="save_btn">{{__('label.save')}}</button>
        </div>
        @endif
    </div>
</div>
@endsection

@section('pagescript')
<script>
    // Sidebar Scroll Down
    let sidebarHeight = $('.sidebar')[0].scrollHeight;
    sidebar_down(sidebarHeight);

    $(document).ready(function() {

        var table = $('#datatable').DataTable({
            ...datatabledefault,
            ajax: {
                url: "{{route('notification_configuration.index')}}",
            },
            columns: [{
                    data: 'type',
                    name: 'type',
                    render: function(data, type, row, meta) {
                        return data ? '<div class="primary-color text-left h5">' + data + '</div>' : "";
                    }
                },
                {
                    data: 'send_notification',
                    name: 'send_notification',
                    render: function(data, type, row, meta) {
                        let disable = (row.type == 'login' || row.type == 'register') ? 'disabled' : '';
                        return '<input class="notification checkbox" ' + disable + ' data-type="' + row.type + '" type="checkbox" ' + (data == 1 ? "checked" : "") + '>';
                    }
                },
                {
                    data: 'send_mail',
                    name: 'send_mail',
                    render: function(data, type, row, meta) {
                        let disable = (row.type == 'add-radio-station' || row.type == 'add-podcast' || row.type == 'add-live-event' || row.type == 'package-expired' || row.type == 'add-music') ? 'disabled' : '';
                        return '<input class="mail checkbox" ' + disable + ' data-type="' + row.type + '" type="checkbox" ' + (data == 1 ? "checked" : "") + '>';
                    },
                },
            ]
        });

        let main_status = "{{$main_status}}";
        if (main_status == 1) {
            $('#datatable').show();
        } else {
            $('#datatable').hide();

        }

        function save(key) {

            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if (Check_Admin == 1) {

                $("#dvloader").show();
                let data = [];

                $('#datatable tbody tr').each(function() {
                    let notification_type = $(this).find('.notification').data('type');
                    let notification = $(this).find('.notification').is(':checked') ? 1 : 0;
                    let mail = $(this).find('.mail').is(':checked') ? 1 : 0;

                    data.push({
                        type: notification_type,
                        notification: notification,
                        mail: mail
                    });
                })

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('notification_configuration.store')}}",
                    type: "POST",
                    data: {
                        key: key,
                        data: data,
                    },
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, '', "{{route('notification_configuration.index')}}");
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                })

            } else {
                toastr.error("{{__('label.you_have_no_right_to_add_edit_and_delete')}}");
            }
        }

        $('#notificationToggle').change(function() {

            let checkAdmin = <?php echo Check_Admin_Access() ?>;

            if (checkAdmin == 1) {
                if (this.checked) {
                    save(1);
                } else {
                    save(0);
                }
            } else {
                return toastr.error("{{__('label.you_have_no_right_to_add_edit_and_delete')}}")
            }
        })
        $('#save_btn').click(function() {
            save(1);
        })

    });
</script>
@endsection