@extends('admin.layout.page-app')
@section('page_title', __('label.feed_comments'))
@section('tab_title', __('label.feed_comments'))

@section('content')
    @include('admin.layout.sidebar')

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.feed_comments')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.feed_comments')}}</li>
                    </ol>
                </div>
            </div>

            <!-- Search -->
            <div class="page-search mb-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fa-solid fa-magnifying-glass fa-xl"></i>
                        </span>
                    </div>
                    <input type="text" name="input_search" id="input_search" value="{{ $_GET['input_search'] ?? '' }}" class="form-control" placeholder="{{__('label.search')}}" aria-label="Search" aria-describedby="basic-addon1">
                </div>
                <div class="sorting mr-2 w-100">
                    <label>{{__('label.sort_by')}}</label>
                    <select class="form-control" name="input_feed" id="input_feed">
                        <option value="0" selected>{{__('label.all_feeds')}}</option>
                        @for ($i = 0; $i < count($feed); $i++)
                            <option value="{{ $feed[$i]['id'] }}" @if(isset($_GET['input_feed'])){{ $_GET['input_feed'] == $feed[$i]['id'] ? 'selected' : ''}} @endif>
                                {{ $feed[$i]['description'] }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="sorting w-75">
                    <select class="form-control" name="input_user" id="input_user">
                        <option value="0" selected>{{__('label.all_users')}}</option>
                        @for ($i = 0; $i < count($user); $i++) 
                            <option value="{{ $user[$i]['id'] }}" @if(isset($_GET['input_user'])){{ $_GET['input_user'] == $user[$i]['id'] ? 'selected' : ''}} @endif>
                                {{ $user[$i]['channel_name'] }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="table-responsive table">
                <table class="table table-striped text-center table-bordered" id="datatable">
                    <thead>
                        <tr>
                            <th>{{__('label.#')}}</th>
                            <th>{{__('label.user')}}</th>
                            <th style="width: 500px;">{{__('label.feed')}}</th>
                            <th>{{__('label.comments')}}</th>
                            <th>{{__('label.date')}}</th>
                            <th>{{__('label.action')}} </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        // Sidebar Scroll Down
		sidebar_down(850);

        $("#input_user").select2();
        $("#input_feed").select2();

        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                ...dataTableDefaults,
                ajax: {
                    url: "{{ route('admin.feedcomment.index') }}",
                    data: function(d) {
                        d.input_search = $('#input_search').val();
                        d.input_user = $('#input_user').val();
                        d.input_feed = $('#input_feed').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {
                        data: 'user',
                        name: 'user',
                        render: function(data, type, row) {
                            return `<div style="text-align: left;">${row.user.channel_name || ''}<br><span style="font-size: 14px; font-weight: 600;">${row.user.full_name || ''}</span>`;
                        }
                    },
                    {
                        data: 'feed',
                        name: 'feed',
                        render: function(data) {
                            return data ? '<div style="text-align: left; font-size: 14px;">' + data.description + '</div>' : "-";
                        }
                    },
                    {
                        data: 'comment',
                        name: 'comment',
                        orderable: false,
                        render: function(data) {
                            return data ? data : "-";
                        }
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            $('#input_user, #input_feed').change(function() {
                table.draw();
            });
            $('#input_search').keyup(function() {
                table.draw();
            });
        });

        function change_status(id) {
            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var url = `{{ route('admin.feedcomment.show', '') }}/${id}`;

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