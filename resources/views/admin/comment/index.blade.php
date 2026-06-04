@extends('admin.layout.page-app')
@section('page_title', __('label.comment'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.comment')}}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('label.comment')}}</li>
                </ol>
            </div>
        </div>
        <!-- search && table -->
        <div class="page-search mb-3">
            <div class="input-group" title="Search">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass fa-xl light-gray"></i></span>
                </div>
                <input type="text" id="input_search" class="form-control" placeholder="Search Comment" aria-label="Search" aria-describedby="basic-addon1">
            </div>
            <div class="sorting mr-2" style="width: 40%;">
                <label>{{__('label.sort_by')}}</label>
                <select class="form-control" name="input_user" id="input_user">
                    <option value="0" selected>{{__('label.all_user')}}</option>
                    @foreach($user as $key=>$value)
                    <option value="{{$value['id']}}">{{$value['full_name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="sorting mr-2" style="width: 40%;">
                <select class="form-control" name="input_type" id="input_type">
                    <option value="0">{{__('label.all_content')}}</option>
                    <option value="1">{{__('label.radio_station')}}</option>
                    <option value="2">{{__('label.podcast')}}</option>
                </select>
            </div>
        </div>
        <div class="table-responsive table">
            <table class="table table-striped text-center table-bordered" id="datatable">
                <thead>
                    <tr class="bg-table">
                        <th>{{__('label.#')}}</th>
                        <th>{{__('label.user')}}</th>
                        <th>{{__('label.type')}}</th>
                        <th>{{__('label.content')}}</th>
                        <th>{{__('label.episode')}}</th>
                        <th>{{__('label.comment')}}</th>
                        <th>{{__('label.date')}}</th>
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
    sidebar_down($(document).height());

    $("#input_user").select2();

    $(document).ready(function() {

        var table = $('#datatable').DataTable({
            ...datatabledefault,
            ajax: {
                url: "{{ route('comment.index') }}",
                data: function(d) {
                    d.input_search = $('#input_search').val();
                    d.input_user = $('#input_user').val();
                    d.input_type = $('#input_type').val();
                },
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'user',
                    name: 'user',
                    orderable: false,
                },
                {
                    data: 'type',
                    name: 'type',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        if (data == 1) {
                            return "Radio Station";
                        } else if (data == 2) {
                            return "Podcasts";
                        } else {
                            return "-";
                        }
                    },
                },
                {
                    data: 'content',
                    name: 'content',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        if (full.type == 1) {
                            return full.song ? full.song.name : "-";
                        } else if (full.type == 2) {
                            return full.podcasts ? full.podcasts.title : "-";
                        } else {
                            return "-";
                        }
                    },
                },
                {
                    data: 'episode.name',
                    name: 'episode.name',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
                    },
                },
                {
                    data: 'comment',
                    name: 'comment',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        if (data) {
                            return data;
                        } else {
                            return "-";
                        }
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
        $('#input_user, #input_type').change(function() {
            table.draw();
        });
        $('#input_search').keyup(function() {
            table.draw();
        });
    });

    function change_status(id, status) {

        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if (Check_Admin == 1) {

            $("#dvloader").show();
            var url = "{{route('comment.show', '')}}" + "/" + id;
            $.ajax({
                type: "GET",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: id,
                success: function(resp) {
                    $("#dvloader").hide();
                    if (resp.status == 200) {
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
            toastr.error("{{__('label.you_have_no_right_to_add_edit_and_delete')}}");
        }
    };
    $(document).on('change', '.status-checkbox', function() {
        id = $(this).data('id');
        change_status(id);
    })
</script>
@endsection