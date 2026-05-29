@extends('artist.layout.page-app')
@section('tab_title', __('label.profile'))
@section('page_title', 'Artist Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form id="profile_form">
                    <div class="text-center mb-4">
                        <img src="{{ $user->image ?? asset('assets/imgs/default-user.png') }}" class="rounded-circle" width="100" height="100" style="object-fit:cover">
                        <h4 class="mt-3">{{ $user->channel_name ?? $user->email }}</h4>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Artist Name</label>
                        <input name="name" class="form-control" value="{{ $artist->name ?? $user->channel_name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" class="form-control" rows="4">{{ $artist->bio ?? $user->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="avatar" class="form-control">
                    </div>
                    <button type="button" class="btn btn-primary" onclick="update_profile()" style="background:linear-gradient(135deg,#667eea,#764ba2);border:none">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    function update_profile() {
        $("#dvloader").show();
        var formData = new FormData($("#profile_form")[0]);
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: '{{ route("artist.profile.update") }}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(resp) {
                $("#dvloader").hide();
                get_responce_message(resp, 'profile_form');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("#dvloader").hide();
                toastr.error(errorThrown, textStatus);
            }
        });
    }
</script>
@endsection
