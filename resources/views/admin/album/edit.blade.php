@extends('admin.layout.page-app')
@section('page_title', 'Edit Album')
@section('tab_title', 'Edit Album')

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">Edit Album</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.album.index') }}">Albums</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <div class="card-body">
                    <form id="albumForm" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="id" value="{{ $data['id'] }}">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $data['name'] }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Artist</label>
                                    <input type="text" class="form-control" value="{{ $data['user']['channel_name'] ?? '—' }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" rows="3">{{ $data['description'] }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cover Image</label>
                                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                                    @if($data['cover_image'])
                                        <img src="{{ $data['cover_image'] }}" class="mt-2" style="max-height:100px">
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ $data['status'] == 1 ? 'selected' : '' }}>Show</option>
                                        <option value="0" {{ $data['status'] == 0 ? 'selected' : '' }}>Hide</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-default mw-120">Update</button>
                        <a href="{{ route('admin.album.index') }}" class="btn btn-secondary mw-120">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
<script>
    $('#albumForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.album.update') }}",
            data: formData,
            contentType: false,
            processData: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(resp) {
                if (resp.status == 200) {
                    window.location.href = "{{ route('admin.album.index') }}";
                } else {
                    alert(resp.errors || 'Error updating album');
                }
            },
            error: function() {
                alert('Error updating album');
            }
        });
    });
</script>
@endsection
