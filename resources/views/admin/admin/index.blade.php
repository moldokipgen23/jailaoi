@extends('admin.layout.page-app')
@section('page_title', 'Manage Admins')

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">Manage Admins</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Admins</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end mb-3">
                <a href="{{ route('admin.create') }}" class="btn btn-default mw-120">+ Add Admin</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive table">
            <table class="table table-striped text-center table-bordered" id="datatable">
                <thead>
                    <tr class="bg-table">
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $i => $admin)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $admin->user_name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>
                            @php
                                $roleLabels = \App\Http\Middleware\RoleMiddleware::getRoleLabels();
                            @endphp
                            <span class="badge
                                @if($admin->role === 'super_admin') badge-danger
                                @elseif($admin->role === 'staff') badge-primary
                                @elseif($admin->role === 'finance') badge-success
                                @else badge-secondary
                                @endif">
                                {{ $roleLabels[$admin->role] ?? ucfirst(str_replace('_', ' ', $admin->role)) }}
                            </span>
                        </td>
                        <td>
                            @if($admin->status == 1)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.edit', $admin->id) }}" class="btn btn-sm btn-edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            @if(!$admin->isSuperAdmin())
                            <form action="{{ route('admin.destroy', $admin->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this admin?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-delete"><i class="fa-solid fa-trash"></i></button>
                            </form>
                            <form action="{{ route('admin.status', $admin->id) }}" method="POST" style="display:inline" onsubmit="return confirm('{{ $admin->status ? 'Deactivate' : 'Activate' }} this admin?')">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $admin->status ? 'btn-warning' : 'btn-success' }}"><i class="fa-solid {{ $admin->status ? 'fa-ban' : 'fa-check' }}"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
