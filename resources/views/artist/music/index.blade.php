@extends('artist.layout.page-app')
@section('tab_title', 'My Music')
@section('page_title', 'My Music')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        @if(count($music) > 0)
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Views</th>
                        <th>Likes</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($music as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $item->portrait_img ?? asset('assets/imgs/default.png') }}" width="40" height="40" style="object-fit:cover;border-radius:8px" class="me-2">
                                <span>{{ $item->title ?? 'Untitled' }}</span>
                            </div>
                        </td>
                        <td>{{ $item->total_view ?? 0 }}</td>
                        <td>{{ $item->total_like ?? 0 }}</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="deleteItem({{ $item->id }})"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted text-center">No music uploaded yet</p>
        @endif
    </div>
</div>
@endsection

@section('pagescript')
<script>
    function deleteItem(id) {
        if(!confirm('Delete this item?')) return;
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: '{{ url("artist/music/delete") }}/' + id,
            success: function(resp) {
                get_responce_message(resp, '', window.location.href);
            }
        });
    }
</script>
@endsection
