@extends('layout.erp.app')

@section('title', 'Users')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">Users</h4>
                <small class="text-muted">Manage system users and roles</small>
            </div>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                + Add User
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter/Search --}}
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="Search name, email, phone">
            </div>

            <div class="col-md-3">
                <select name="role_id" class="form-select">
                    <option value="">All Roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        {{-- Users Table --}}
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email / Phone</th>
                    <th>Role</th>
                    <th>Hotel</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $index => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <small class="text-muted">ID: {{ $user->id }}</small>
                        </td>
                        <td>
                            <div>{{ $user->email }}</div>
                            <small class="text-muted">{{ $user->phone ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $user->role?->name ?? 'Unassigned' }}</span>
                        </td>
                        <td>
                            <span class="text-muted">{{ $user->hotel?->name ?? '-' }}</span>
                        </td>
                        <td>
                            @if ($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-warning text-dark">Archived</span>
                            @endif
                        </td>
                        {{-- Actions dropdown (Archive/Restore/Edit) --}}
                        <td class="text-end">
                            @include('pages.erp.partials.table-actions-user', ['user' => $user])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="mt-2">
                {{ $users->links() }}
            </div>
        @endif

    </div>
@endsection
