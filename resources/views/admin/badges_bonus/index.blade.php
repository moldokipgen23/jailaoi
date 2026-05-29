@extends('admin.layout.page-app')
@section('page_title', __('label.badges_&_bonus'))
@section('tab_title', __('label.badges_&_bonus'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.badges_&_bonus')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.badges_&_bonus')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.badgesbonus.create') }}" class="btn btn-default mw-120" style="margin-top: -14px;">{{__('label.add_badges_&_bonus')}}</a>
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
                <div class="sorting mr-2 w-50">
                    <label>{{__('label.sort_by')}}</label>
                    <select class="form-control" id="input_type">
                        <option value="all">{{__('label.all_type')}}</option>
                        <option value="1">{{__('label.badges')}}</option>
                        <option value="2">{{__('label.bonus')}}</option>
                        <option value="0">{{__('label.badges_&_bonus')}}</option>
                    </select>
                </div>
                <div class="sorting w-50">
                    <select class="form-control" id="input_condition_type">
                        <option value="all">{{__('label.all_condition_type')}}</option>
                        <option value="subscriber_count">{{__('label.x_number_of_subscriber')}}</option>
                        <option value="refer_user">{{__('label.x_number_of_refer_user')}}</option>
                        <option value="content_views">{{__('label.x_number_of_views_on_x_content')}}</option>
                        <option value="content_likes">{{__('label.x_number_of_likes_on_x_content')}}</option>
                        <option value="video_upload">{{__('label.x_number_of_video_upload')}}</option>
                        <option value="music_upload">{{__('label.x_number_of_music_upload')}}</option>
                        <option value="reels_upload">{{__('label.x_number_of_reels_upload')}}</option>
                        <option value="podcasts_upload">{{__('label.x_number_of_podcasts_upload')}}</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive table">
                <table class="table table-striped text-center table-bordered" id="datatable">
                    <thead>
                        <tr>
                            <th>{{__('label.#')}}</th>
                            <th>{{__('label.image')}}</th>
                            <th>{{__('label.name')}}</th>
                            <th>{{__('label.type')}}</th>
                            <th>{{__('label.condition_type')}}</th>
                            <th>{{__('label.bonus_coin')}}</th>
                            <th>{{__('label.x_number')}}</th>
                            <th>{{__('label.x_content')}}</th>
                            <th>{{__('label.status')}}</th>
                            <th>{{__('label.action')}}</th>
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
                    url: "{{ route('admin.badgesbonus.index') }}",
                    data: function(d){
                        d.input_search = $('#input_search').val();
                        d.input_type = $('#input_type').val();
                        d.input_condition_type = $('#input_condition_type').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
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
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        render: function(data) {
                            return data ? data : "-";
                        }
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if (data == 1) {
                                return "{{__('label.badges')}}";
                            } else if (data == 2) {
                                return "{{__('label.bonus')}}";
                            } else if (data == 0) {
                                return "{{__('label.badges_&_bonus')}}";
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'condition_type',
                        name: 'condition_type',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if (data == 'subscriber_count') {
                                return "{{__('label.x_number_of_subscriber')}}";
                            } else if (data == 'refer_user') {
                                return "{{__('label.x_number_of_refer_user')}}";
                            } else if (data == 'content_views') {
                                return "{{__('label.x_number_of_views_on_x_content')}}";
                            } else if (data == 'content_likes') {
                                return "{{__('label.x_number_of_likes_on_x_content')}}";
                            } else if (data == 'video_upload') {
                                return "{{__('label.x_number_of_video_upload')}}";
                            } else if (data == 'music_upload') {
                                return "{{__('label.x_number_of_music_upload')}}";
                            } else if (data == 'reels_upload') {
                                return "{{__('label.x_number_of_reels_upload')}}";
                            } else if (data == 'podcasts_upload') {
                                return "{{__('label.x_number_of_podcasts_upload')}}";
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'bonus_coin',
                        name: 'bonus_coin',
                        orderable: false,
                        render: function(data) {
                            return `<span style="font-size: 18px; font-weight: 600;" class="primary-color">${data || 0}</span>`;
                        }
                    },
                    {
                        data: 'x_number',
                        name: 'x_number',
                        orderable: false,
                        render: function(data) {
                            return `<span style="font-size: 18px; font-weight: 600;" class="primary-color">${data || 0}</span>`;
                        }
                    },
                    {
                        data: 'x_content',
                        name: 'x_content',
                        orderable: false,
                        render: function(data) {
                            return `<span style="font-size: 18px; font-weight: 600;" class="primary-color">${data || 0}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            $('#input_type, #input_condition_type').change(function(){
                table.draw();
            });
            $('#input_search').keyup(function(){
                table.draw();
            });
        });

        function change_status(id) {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var url = `{{ route('admin.badgesbonus.show', '') }}/${id}`;

                $.ajax({
                    type: "GET",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(resp) {
                        $("#dvloader").hide();

                        if (resp.status == 200) {
                            if (resp.status_code == 1) {
                                $('#' + id).text('{{__("label.show")}}').removeClass('hide-btn').addClass('show-btn');
                            } else {
                                $('#' + id).text('{{__("label.hide")}}').removeClass('show-btn').addClass('hide-btn');
                            }
                            toastr.success(resp.success);
                        } else {
                            toastr.error(resp.errors);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                showError();
            }
        };
    </script>
@endsection