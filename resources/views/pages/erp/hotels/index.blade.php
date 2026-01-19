@extends('layout.erp.app')

@section('title', 'Hotels')

@section('css')
    <style>
        /* Shared dropdown base */
        .dropdown-menu-actions .dropdown-item {
            position: relative;
            padding: 8px 14px 8px 18px;
            font-size: 14px;
            transition: background-color .15s ease, color .15s ease;
        }

        /* Left accent bar (hidden by default) */
        .dropdown-menu-actions .dropdown-item::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 3px;
            height: 100%;
            opacity: 0;
            transition: opacity .15s ease;
        }

        /* EDIT hover */
        .dropdown-menu-actions .action-edit:hover {
            background-color: #eef4ff;
            color: #0d6efd;
        }

        .dropdown-menu-actions .action-edit:hover::before {
            background-color: #0d6efd;
            opacity: 1;
        }

        /* ARCHIVE hover (destructive but soft) */
        .dropdown-menu-actions .action-archive {
            color: #856404;
        }

        .dropdown-menu-actions .action-archive:hover {
            background-color: #fff3cd;
            color: #856404;
        }

        .dropdown-menu-actions .action-archive:hover::before {
            background-color: #ffc107;
            opacity: 1;
        }
    </style>

@endsection

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">Hotels</h4>
                <small class="text-muted">Manage all registered properties</small>
            </div>

            <a href="{{ route('hotels.create') }}" class="btn btn-primary">
                + Add Hotel
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
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Table --}}
        <div class="card">
            <div class="card-body p-0">

                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Hotel</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($hotels as $index => $hotel)
                            <tr>
                                <td>
                                    {{ $hotels->firstItem() + $index }}
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $hotel->name }}</div>
                                    <small class="text-muted">
                                        Created {{ $hotel->created_at->format('d M Y') }}
                                    </small>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $hotel->email }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $hotel->phone }}
                                    </small>
                                </td>


                                <td>{{ $hotel->address }}</td>

                                <td>
                                    @if ($hotel->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="dropdown">

                                        <button class="btn btn-sm btn-kebab" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false" title="Actions">
                                            &#x22EE;
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-actions">

                                            {{-- Edit --}}
                                            <li>
                                                <a class="dropdown-item action-edit"
                                                    href="{{ route('hotels.edit', $hotel->id) }}">
                                                    Edit
                                                </a>
                                            </li>

                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>

                                            {{-- Archive --}}
                                            <li>
                                                <form action="{{ route('hotels.destroy', $hotel->id) }}" method="POST"
                                                    onsubmit="return confirm('Archive this hotel?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="dropdown-item action-archive">
                                                        Archive
                                                    </button>
                                                </form>
                                            </li>

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No hotels found.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>

            </div>

            {{-- Pagination --}}
            @if ($hotels->hasPages())
                <div class="card-footer">
                    {{ $hotels->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
