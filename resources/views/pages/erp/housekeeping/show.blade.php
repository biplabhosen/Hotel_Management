@extends('layout.erp.app')

@section('content')
<div class="row mb-3">
    <div class="col-sm-12 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Room {{ $room->room_number }} Housekeeping</h4>
        <a href="{{ route('housekeeping.index') }}" class="btn btn-sm btn-light">Back</a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Booking</th>
                        <th>Assigned Staff</th>
                        <th>Status</th>
                        <th style="width: 300px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <td>#{{ $task->id }} ({{ $task->task_type }})</td>
                            <td>{{ $task->booking_id ? '#'.$task->booking_id : '-' }}</td>
                            <td>{{ $task->staff->name ?? 'Unassigned' }}</td>
                            <td><span class="badge badge-secondary">{{ $task->status }}</span></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('housekeeping.assign', $task) }}" method="POST" class="mr-2">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <select name="staff_id" class="form-control" required>
                                                <option value="">Select staff</option>
                                                @foreach ($staff as $member)
                                                    <option value="{{ $member->id }}" @selected($task->staff_id == $member->id)>
                                                        {{ $member->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">Assign</button>
                                            </div>
                                        </div>
                                    </form>

                                    <form action="{{ route('housekeeping.complete', $task) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Mark Complete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No pending/in-progress tasks for this room.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
