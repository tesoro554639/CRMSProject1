@extends('layouts.app')

@section('page-title', 'Leads')

@section('breadcrumb')
<li class="breadcrumb-item active">Leads</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h5 class="mb-0">{{ $totalLeads }} total leads in pipeline</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('leads.kanban') }}" class="btn btn-outline-secondary"><i class="bi bi-kanban me-1"></i> Kanban View</a>
        <a href="{{ route('leads.create') }}" class="btn btn-primary"><i class="bi bi-plus me-1"></i> Add Lead</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 g-md-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search name, email, phone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="qualified" {{ request('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                    <option value="proposal_sent" {{ request('status') == 'proposal_sent' ? 'selected' : '' }}>Proposal Sent</option>
                    <option value="negotiation" {{ request('status') == 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                    <option value="won" {{ request('status') == 'won' ? 'selected' : '' }}>Won</option>
                    <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="assigned_user_id" class="form-select">
                    <option value="">All Users</option>
                    @foreach($salesUsers as $user)
                    <option value="{{ $user->id }}" {{ request('assigned_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i> Filter</button>
                <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>Lead ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Value</th>
                        <th>Assigned</th>
                        <th>Source</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td class="fw-semibold">{{ $lead->lead_id }}</td>
                        <td>{{ $lead->name }}</td>
                        <td>{{ $lead->email ?? '-' }}</td>
                        <td>{{ $lead->phone ?? '-' }}</td>
                        <td><span class="status-badge status-{{ $lead->status }}">{{ ucwords(str_replace('_', ' ', $lead->status)) }}</span></td>
                        <td><span class="status-badge priority-{{ $lead->priority }}">{{ ucfirst($lead->priority) }}</span></td>
                        <td>${{ number_format($lead->expected_value ?? 0, 2) }}</td>
                        <td>{{ $lead->assignedUser?->name ?? '-' }}</td>
                        <td>{{ $lead->source ?? '-' }}</td>
                        <td>{{ $lead->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('leads.edit', $lead) }}" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('leads.destroy', $lead) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="text-center py-4 text-muted">No leads found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $leads->appends(request()->query())->links() }}</div>
@endsection
