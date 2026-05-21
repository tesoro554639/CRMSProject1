@extends('layouts.app')

@section('page-title', 'Follow-ups')

@section('breadcrumb')
<li class="breadcrumb-item active">Follow-ups</li>
@endsection

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10"><i class="bi bi-list-task text-primary" style="font-size: 22px;"></i></div>
                <div>
                    <div class="stat-label">Total Follow-ups</div>
                    <div class="stat-value text-primary">{{ $totalFollowUps }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10"><i class="bi bi-clock text-warning" style="font-size: 22px;"></i></div>
                <div>
                    <div class="stat-label">Pending</div>
                    <div class="stat-value text-warning">{{ $pendingFollowUps }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10"><i class="bi bi-check-all text-success" style="font-size: 22px;"></i></div>
                <div>
                    <div class="stat-label">Completed</div>
                    <div class="stat-value text-success">{{ $completedFollowUps }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h5 class="mb-0">Task Tracking</h5>
    @if(auth()->user()->hasRole(['admin', 'sales']))
    <a href="{{ route('follow-ups.create') }}" class="btn btn-primary"><i class="bi bi-plus me-1"></i> Add Follow-up</a>
    @endif
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 g-md-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search follow-up title..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i> Filter</button>
                <a href="{{ route('follow-ups.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Customer</th>
                        <th>Lead</th>
                        <th>Assigned To</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($followUps as $followUp)
                    <tr class="{{ $followUp->isOverdue() ? 'table-danger' : '' }}">
                        <td>
                            <div class="fw-semibold">{{ $followUp->title }}</div>
                            <small class="text-muted">{{ Str::limit($followUp->description, 40) }}</small>
                        </td>
                        <td>
                            @if($followUp->isOverdue())
                            <span class="text-danger fw-semibold">{{ $followUp->due_date->format('M d, Y') }}</span>
                            @else
                            {{ $followUp->due_date->format('M d, Y') }}
                            @endif
                        </td>
                        <td><span class="status-badge status-{{ $followUp->status }}">{{ ucfirst($followUp->status) }}</span></td>
                        <td>{{ $followUp->customer?->first_name ?? '-' }}</td>
                        <td>{{ $followUp->lead?->name ?? '-' }}</td>
                        <td>{{ $followUp->user->name }}</td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end flex-wrap">
                                @if($followUp->isPending() && auth()->user()->hasRole(['admin', 'sales']))
                                <form method="POST" action="{{ route('follow-ups.complete', $followUp) }}" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-check me-1"></i>Complete</button></form>
                                @elseif($followUp->isCompleted() && auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('follow-ups.reopen', $followUp) }}" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-outline-warning"><i class="bi bi-arrow-counterclockwise me-1"></i>Reopen</button></form>
                                @endif
                                @if(auth()->user()->hasRole(['admin', 'sales']) && (!$followUp->isCompleted() || auth()->user()->isAdmin()))
                                <a href="{{ route('follow-ups.edit', $followUp) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
                                @endif
                                @if(auth()->user()->hasRole(['admin', 'sales']))
                                <form action="{{ route('follow-ups.destroy', $followUp) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No follow-ups found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $followUps->appends(request()->query())->links() }}</div>
@endsection
