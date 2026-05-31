@extends('admin.layout.page-app')
@section('page_title', 'Albums')
@section('tab_title', 'Albums')

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">Albums</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Albums</li>
                    </ol>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <div class="card-body">
                    <table id="datatable" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cover</th>
                                <th>Name</th>
                                <th>Artist</th>
                                <th>Songs</th>
                                <th>Status</th>
                                <th>Action</th>
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
    $(document).ready(function() {
        var table = $('#datatable').DataTable({
            dom: "<'top'f>rt<'row'<'col-2'i><'col-1'l><'col-9'p>>",
            responsive: true,
            autoWidth: false,
            searching: false,
            processing: true,
            serverSide: true,
            language: {
                paginate: {
                    previous: "<i class='fa-solid fa-chevron-left'></i>",
                    next: "<i class='fa-solid fa-chevron-right'></i>"
                }
            },
            ajax: {
                url: "{{ route('admin.album.index') }}",
                data: function(d) {
                    d.input_search = $('#input_search').val();
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'cover_image', name: 'cover_image', orderable: false, searchable: false,
                    render: function(data) {
                        return `<img src='${data}' class='img-thumbnail' style='height:55px; width:55px'>`;
                    },
                },
                { data: 'name', name: 'name' },
                { data: 'artist_name', name: 'artist_name' },
                { data: 'song_count', name: 'song_count' },
                { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        $('#input_search').on('keyup', function() {
            table.draw();
        });
    });

    function delete_album(id) {
        confirmAction({
            title: '{{__("label.delete")}}',
            message: 'Delete this album? Songs will be unlinked.',
            btnText: '{{__("label.delete")}}',
            btnClass: 'btn-danger',
            onConfirm: function() {
                $.ajax({
                    type: 'DELETE',
                    url: "{{ route('admin.album.index') }}/" + id,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() { location.reload(); },
                });
            }
        });
    }
</script>
@endsection
