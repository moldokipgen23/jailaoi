@extends('admin.layout.page-app')
@section('page_title', __('label.notification'))
@section('tab_title', __('label.notification'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.notification')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.notification')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.notification.setting') }}" class="btn btn-default mw-120" style="margin-top: -14px;">{{__('label.notification_setting')}}</a>
                </div>
            </div>

            <!-- Add Notification -->
            <div class="card custom-border-card mt-3">
                <h5 class="card-header">{{__('label.add_notification')}}</h5>
                <div class="card-body">
                    <form id="notification" enctype="multipart/form-data">
					    <input type="hidden" name="id" value="">
					    <div class="form-row">
                            <div class="col-md-8">
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('label.title')}}<span class="text-danger">*</span></label>
                                            <input name="title" type="text" class="form-control" placeholder="{{__('label.title_here')}}" autofocus>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('label.message')}}<span class="text-danger">*</span></label>
                                            <textarea class="form-control" rows="2" name="message" placeholder="{{__('label.message_here')}}"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ml-5">
                                    <label class="ml-5">{{__('label.image')}}</label>
                                    <div class="avatar-upload ml-5">
                                        <div class="avatar-edit">
                                            <input type='file' name="image" id="imageUpload1" accept=".png, .jpg, .jpeg" />
                                            <label for="imageUpload1" title="{{__('label.upload_file')}}"></label>
                                        </div>
                                        <div class="avatar-preview">
                                            <img src="{{ asset('assets/imgs/upload_img.png') }}" id="imagePreview1">
                                        </div>
                                    </div>
                                    <label class="mt-3 ml-5 text-gray">{{__('label.max_size_5mb')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="save_notification()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search && Table -->
            <div class="card custom-border-card mt-3">
                <div class="page-search mb-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass fa-xl"></i></span>
                        </div>
                        <input type="text" id="input_search" class="form-control" placeholder="{{__('label.search')}}" aria-label="Search" aria-describedby="basic-addon1">
                    </div>
                </div>

                <div class="table-responsive table">
                    <table class="table table-striped text-center table-bordered" id="datatable">
                        <thead>
                            <tr>
                                <th>{{__('label.#')}}</th>
                                <th>{{__('label.image')}}</th>
                                <th>{{__('label.title')}}</th>
                                <th>{{__('label.message')}}</th>
                                <th>{{__('label.action')}}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        // Sidebar Scroll Down
		sidebar_down(850);

        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                ...dataTableDefaults,
                ajax: {
                    url: "{{ route('admin.notification.index') }}",
                    data: function(d) {
                        d.input_search = $('#input_search').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {
						data: 'image',
						name: 'image',
						orderable: false,
						searchable: false,
						render: function(data, type, full, meta) {
                            return `<a href='${data}' target='_blank'>
                                        <img src='${data}' class='img-thumbnail' style='height:55px; width:55px'>
                                    </a>`;
						},
					},
                    {
                        data: 'title',
                        name: 'title',
                        render: function(data) {
                            return data ? '<div style="text-align: left; font-size: 14px;">' + data + '</div>' : "-";
                        }
                    },
                    {
                        data: 'message',
                        name: 'message',
                        render: function(data) {
                            return data ? '<div style="text-align: left; font-size: 14px;">' + data + '</div>' : "-";
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
            });

            $('#input_search').keyup(function() {
                table.draw();
            });
        });
        function save_notification() {

			$("#dvloader").show();
			var formData = new FormData($("#notification")[0]);
			$.ajax({
				type: 'POST',
				url: '{{ route("admin.notification.store") }}',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				success: function(resp) {
					$("#dvloader").hide();
					get_responce_message(resp, 'notification', '{{ route("admin.notification.index") }}');
				},
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
			});
		}
    </script>
@endsection