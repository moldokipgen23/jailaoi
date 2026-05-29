@extends('admin.layout.page-app')
@section('page_title', __('label.payment'))
@section('tab_title', __('label.payment'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.payment')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.payment')}}</li>
                    </ol>
                </div>
            </div>

            <!-- Search -->
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
                            <th> {{__('label.#')}} </th>
                            <th> {{__('label.name')}} </th>
                            <th> {{__('label.status')}} </th>
                            <th> {{__('label.payment_environment')}} </th>
                            <th> {{__('label.action')}} </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
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
                ...dataTableDefaults,
				ajax:
					{
					url: "{{ route('admin.payment.index') }}",
					data: function(d){
						d.input_search = $('#input_search').val();
					},
				},
				columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, visible: false},
					{
						data: 'name',
						name: 'name',
						render: function(data) {
                            return data ? "<h5 class='text-dark'><strong>"+data+"</strong></h5>" : "-";
                        }
					},
                    {
                        data: 'visibility',
                        name: 'visibility',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            if (data == 1) {
                                return "<h5 class='text-success'><strong>{{__('label.active')}}</strong></h5>";
                            } else {
                                return "<h5 class='text-danger'><strong>{{__('label.inactive')}}</strong></h5>";
                            }
                        }
                    },
                    {
                        data: 'is_live',
                        name: 'is_live',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            if (data == 1) {
                                return "<h5 class='primary-color'><strong>{{__('label.live')}}</strong></h5>";
                            } else {
                                return "<h5 class='primary-color'><strong>{{__('label.sandbox')}}</strong></h5>";
                            }
                        }
                    },
					{ data: 'action', name: 'action', orderable: false, searchable: false }
				],
			});
			$('#input_search').keyup(function() {
				table.draw();
			});
        });
    </script>
@endsection