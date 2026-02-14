@extends('layout.erp.app')

@section('content')
<div class="row mb-3">
    <div class="col-sm-12 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Housekeeping Dashboard</h4>
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
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Pending Tasks</th>
                        <th>Assign Staff</th>
                        <th style="width: 150px;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasksByRoom as $roomId => $tasks)
                        @php $firstTask = $tasks->first(); @endphp
                        <tr>
                            <td>Room {{ $firstTask->room->room_number ?? $roomId }}</td>
                            <td>
                                @foreach ($tasks as $task)
                                    <div class="mb-2">
                                        <strong>Task #{{ $task->id }}</strong>
                                        ({{ $task->task_type }}) -
                                        <span class="badge badge-secondary">{{ $task->status }}</span>
                                        @if ($task->booking)
                                            <span class="text-muted">Booking #{{ $task->booking_id }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($tasks as $task)
                                    <form action="{{ route('housekeeping.assign', $task) }}" method="POST" class="mb-2">
                                        @csrf
                                        <div class="input-group">
                                            <select name="staff_id" class="form-control form-control-sm" required>
                                                <option value="">Select staff</option>
                                                @foreach ($staff as $member)
                                                    <option value="{{ $member->id }}" @selected($task->staff_id == $member->id)>
                                                        {{ $member->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-primary" type="submit">Assign</button>
                                            </div>
                                        </div>
                                    </form>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('housekeeping.show', $firstTask->room_id) }}" class="btn btn-sm btn-outline-primary w-100">
                                    Open Room
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No pending housekeeping tasks.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
