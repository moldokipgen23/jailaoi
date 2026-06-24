@extends('admin.layout.page-app')
@section('page_title', 'Add Admin')

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <h1 class="page-title-sm">Add Admin</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Manage Admins</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Admin</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('admin.index') }}" class="btn btn-default mw-120 mb-3">Admin List</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form action="{{ route('admin.store') }}" method="POST">
                @csrf

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <div class="form-row">
                    <div class="col-md-8">
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Username <span class="text-danger">*</span></label>
                                    <input type="text" name="user_name" class="form-control" value="{{ old('user_name') }}" required autofocus>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" required minlength="6">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Role <span class="text-danger">*</span></label>
                                    <select name="role" id="role-select" class="form-control" required>
                                        <option value="">-- Select Role --</option>
                                        @foreach($roles as $key => $label)
                                            <option value="{{ $key }}" {{ old('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="custom-control custom-checkbox pt-2">
                                        <input type="checkbox" class="custom-control-input" id="status" name="status" checked>
                                        <label class="custom-control-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3 mt-3">
                    <h5 class="mb-3">Permissions <small class="text-muted">(auto-loaded from role; uncheck to restrict, check to grant extra)</small></h5>
                    <div class="row" id="permissions-container">
                        @foreach($permissionGroups as $section => $items)
                        <div class="col-md-6 mb-3">
                            <div class="card" style="background:#f8f9fa;border:1px solid #e9ecef;">
                                <div class="card-header py-2 px-3" style="background:#e9ecef;font-weight:600;font-size:13px;">{{ $section }}</div>
                                <div class="card-body py-2 px-3">
                                    @foreach($items as $item)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input perm-checkbox" id="perm-{{ Str::slug($item['label']) }}" name="permissions[]" value="{{ $item['label'] }}">
                                        <label class="custom-control-label" for="perm-{{ Str::slug($item['label']) }}">{{ $item['label'] }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-top pt-3">
                    <button type="submit" class="btn btn-default mw-120">Save</button>
                    <a href="{{ route('admin.index') }}" class="btn btn-cancel mw-120">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var rolePermissionsMap = @json($rolePermissionsMap);

function checkPermissionsForRole(role) {
    var perms = rolePermissionsMap[role] || [];
    document.querySelectorAll('.perm-checkbox').forEach(function(cb) {
        cb.checked = false;
    });
    perms.forEach(function(p) {
        var cb = document.querySelector('.perm-checkbox[value="' + p.label.replace(/"/g, '\\"') + '"]');
        if (cb) cb.checked = p.checked;
    });
}

document.getElementById('role-select').addEventListener('change', function() {
    checkPermissionsForRole(this.value);
});

(function() {
    var preselected = '{{ old('role') }}';
    if (preselected) {
        checkPermissionsForRole(preselected);
    }
})();
</script>
@endpush
