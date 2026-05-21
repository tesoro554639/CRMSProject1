@extends('layouts.app')

@section('page-title', 'Lead Details')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
<li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-1">{{ $lead->name }}</h5>
                    <span class="status-badge status-{{ $lead->status }}">{{ ucwords(str_replace('_', ' ', $lead->status)) }}</span>
                    <span class="status-badge priority-{{ $lead->priority }}">{{ ucfirst($lead->priority) }}</span>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if($lead->status == 'won' && $lead->canBeConverted() && auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('leads.convert', $lead) }}" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-success">Convert to Customer</button></form>
                    @endif
                    @if($lead->status == 'lost' && auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('leads.reopen', $lead) }}" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-warning">Reopen Lead</button></form>
                    @endif
                    @if($lead->status != 'lost' && $lead->status != 'won' && auth()->user()->hasRole(['admin', 'sales']))
                    <a href="{{ route('leads.lost-form', $lead) }}" class="btn btn-sm btn-outline-danger">Mark as Lost</a>
                    @endif
                    @if(auth()->user()->hasRole(['admin', 'sales']))
                    <a href="{{ route('leads.edit', $lead) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    @endif
                    @if(auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('leads.destroy', $lead) }}" class="d-inline" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><div class="text-muted small text-uppercase" style="font-size:10px;font-weight:600;letter-spacing:0.4px;">Email</div><p class="mb-0">{{ $lead->email ?? '-' }}</p></div>
                    <div class="col-md-6"><div class="text-muted small text-uppercase" style="font-size:10px;font-weight:600;letter-spacing:0.4px;">Phone</div><p class="mb-0">{{ $lead->phone ?? '-' }}</p></div>
                    <div class="col-md-6"><div class="text-muted small text-uppercase" style="font-size:10px;font-weight:600;letter-spacing:0.4px;">Expected Value</div><p class="mb-0">${{ number_format($lead->expected_value ?? 0, 2) }}</p></div>
                    <div class="col-md-6"><div class="text-muted small text-uppercase" style="font-size:10px;font-weight:600;letter-spacing:0.4px;">Source</div><p class="mb-0">{{ $lead->source ?? '-' }}</p></div>
                    <div class="col-md-6"><div class="text-muted small text-uppercase" style="font-size:10px;font-weight:600;letter-spacing:0.4px;">Assigned To</div><p class="mb-0">{{ $lead->assignedUser?->name ?? '-' }}</p></div>
                    <div class="col-md-6"><div class="text-muted small text-uppercase" style="font-size:10px;font-weight:600;letter-spacing:0.4px;">Lead ID</div><p class="mb-0 fw-semibold">{{ $lead->lead_id }}</p></div>
                    <div class="col-12"><div class="text-muted small text-uppercase" style="font-size:10px;font-weight:600;letter-spacing:0.4px;">Notes</div><p class="mb-0">{{ $lead->notes ?? '-' }}</p></div>
                    @if($lead->isLost())
                    <div class="col-md-6"><div class="text-muted small text-uppercase" style="font-size:10px;font-weight:600;letter-spacing:0.4px;">Lost Category</div><p class="mb-0">{{ $lead->lost_category }}</p></div>
                    <div class="col-md-6"><div class="text-muted small text-uppercase" style="font-size:10px;font-weight:600;letter-spacing:0.4px;">Lost Reason</div><p class="mb-0">{{ $lead->lost_reason }}</p></div>
                    <div class="col-md-6"><div class="text-muted small text-uppercase" style="font-size:10px;font-weight:600;letter-spacing:0.4px;">Lost At</div><p class="mb-0">{{ $lead->lost_at?->format('M d, Y H:i') }}</p></div>
                    @endif
                    @if($lead->converted_to_customer_id)
                    <div class="col-12"><div class="text-muted small text-uppercase" style="font-size:10px;font-weight:600;letter-spacing:0.4px;">Converted to Customer</div><p class="mb-0"><a href="{{ route('customers.show', $lead->convertedToCustomer) }}">{{ $lead->convertedToCustomer->first_name }} {{ $lead->convertedToCustomer->last_name }}</a></p></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header"><h5 class="mb-0">Activities</h5></div>
            <div class="card-body">
                @forelse($lead->activities as $activity)
                <div class="border-bottom pb-2 mb-2">
                    <span class="status-badge status-{{ $activity->activity_type }} me-1">{{ ucfirst($activity->activity_type) }}</span>
                    <small class="text-muted">{{ $activity->activity_date->format('M d, Y') }}</small>
                    <p class="mb-0 small mt-1">{{ $activity->description }}</p>
                </div>
                @empty
                <p class="text-muted mb-0">No activities recorded</p>
                @endforelse
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header"><h5 class="mb-0">Follow-ups</h5></div>
            <div class="card-body">
                @forelse($lead->followUps as $followUp)
                <div class="border-bottom pb-2 mb-2">
                    <span class="status-badge status-{{ $followUp->status }} me-1">{{ ucfirst($followUp->status) }}</span>
                    <small class="text-muted">Due: {{ $followUp->due_date->format('M d, Y') }}</small>
                    <p class="mb-0 small fw-semibold mt-1">{{ $followUp->title }}</p>
                </div>
                @empty
                <p class="text-muted mb-0">No follow-ups</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Actions</h5></div>
            <div class="card-body">
                <a href="{{ route('activities.create') }}?lead_id={{ $lead->id }}" class="btn btn-outline-primary w-100 mb-2">Log Activity</a>
                <a href="{{ route('follow-ups.create') }}?lead_id={{ $lead->id }}" class="btn btn-outline-primary w-100">Create Follow-up</a>
                @if(auth()->user()->isAdmin())
                <hr>
                <form method="POST" action="{{ route('leads.destroy', $lead) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure?')">Delete Lead</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
