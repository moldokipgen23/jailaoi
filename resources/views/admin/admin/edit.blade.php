@extends('admin.layout.page-app')
@section('page_title', 'Edit Admin')

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <h1 class="page-title-sm">Edit Admin</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Manage Admins</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Admin</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('admin.index') }}" class="btn btn-default mw-120 mb-3">Admin List</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form action="{{ route('admin.update', $admin->id) }}" method="POST">
                @csrf
                @method('PUT')

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
                                    <input type="text" name="user_name" class="form-control" value="{{ old('user_name', $admin->user_name) }}" required autofocus>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Password <span class="text-muted">(leave blank to keep current)</span></label>
                                    <input type="password" name="password" class="form-control" minlength="6">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Role <span class="text-danger">*</span></label>
                                    <select name="role" class="form-control" {{ $admin->isSuperAdmin() && $admin->id !== auth('admin')->id() ? 'disabled' : '' }}>
                                        <option value="">-- Select Role --</option>
                                        @foreach($roles as $key => $label)
                                            <option value="{{ $key }}" {{ old('role', $admin->role) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @if($admin->isSuperAdmin() && $admin->id !== auth('admin')->id())
                                        <small class="text-muted">Only the Super Admin can change their own role.</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="custom-control custom-checkbox pt-2">
                                        <input type="checkbox" class="custom-control-input" id="status" name="status" {{ old('status', $admin->status) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <button type="submit" class="btn btn-default mw-120">Update</button>
                    <a href="{{ route('admin.index') }}" class="btn btn-cancel mw-120">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
