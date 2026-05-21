@extends('layouts.app')

@section('page-title', 'Activity Log')

@section('breadcrumb')
<li class="breadcrumb-item active">Activities</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h5 class="mb-0">Team Timeline</h5>
        <small class="text-muted">{{ $totalActivities }} total activities recorded</small>
    </div>
    @if(auth()->user()->hasRole(['admin', 'sales']))
    <a href="{{ route('activities.create') }}" class="btn btn-primary"><i class="bi bi-plus me-1"></i> Log Activity</a>
    @endif
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 g-md-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search description..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="activity_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="call" {{ request('activity_type') == 'call' ? 'selected' : '' }}>Call</option>
                    <option value="email" {{ request('activity_type') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="meeting" {{ request('activity_type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                    <option value="note" {{ request('activity_type') == 'note' ? 'selected' : '' }}>Note</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="user_id" class="form-select">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i> Filter</button>
                <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @forelse($activities as $activity)
        <div class="d-flex gap-3 px-4 py-3 border-bottom">
            <div class="flex-shrink-0">
                <div class="timeline-icon {{ $activity->activity_type == 'email' ? 'bg-primary bg-opacity-10' : ($activity->activity_type == 'call' ? 'bg-success bg-opacity-10' : ($activity->activity_type == 'meeting' ? 'bg-purple' : 'bg-warning bg-opacity-10')) }}" style="{{ $activity->activity_type == 'meeting' ? 'background-color: #ede9fe;' : '' }}">
                    <i class="bi {{ $activity->activity_type == 'email' ? 'bi-envelope' : ($activity->activity_type == 'call' ? 'bi-telephone' : ($activity->activity_type == 'meeting' ? 'bi-calendar' : 'bi-sticky')) }}" style="font-size: 18px;{{ $activity->activity_type == 'email' ? 'color:#1e40af;' : ($activity->activity_type == 'call' ? 'color:#166534;' : ($activity->activity_type == 'meeting' ? 'color:#6d28d9;' : 'color:#92400e;')) }}"></i>
                </div>
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-1">
                    <div>
                        <span class="status-badge status-{{ $activity->activity_type }} me-1">{{ ucfirst($activity->activity_type) }}</span>
                        <span class="fw-semibold">{{ $activity->user->name }}</span>
                        @if($activity->lead)<span class="text-primary small"> · Lead: {{ $activity->lead->name }}</span>
                        @elseif($activity->customer)<span class="text-success small"> · Customer: {{ $activity->customer->first_name }} {{ $activity->customer->last_name }}</span>@endif
                    </div>
                    <small class="text-muted text-nowrap">{{ $activity->activity_date->diffForHumans() }}</small>
                </div>
                <p class="mb-0 mt-1 text-dark">{{ $activity->description }}</p>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">{{ $activity->activity_date->format('M d, Y') }}</small>
                    @if(auth()->user()->hasRole(['admin', 'sales']))
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('activities.edit', $activity) }}" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil me-1"></i>Edit</a>
                        <form action="{{ route('activities.destroy', $activity) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash me-1"></i>Delete</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">No activities found</div>
        @endforelse
    </div>
</div>

<div class="mt-3">{{ $activities->appends(request()->query())->links() }}</div>
@endsection
