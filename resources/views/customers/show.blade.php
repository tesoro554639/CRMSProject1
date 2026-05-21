@extends('layouts.app')

@section('page-title', 'Customer Details')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
<li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0">Customer Information</h5>
                <div class="d-flex gap-2">
                    @if(auth()->user()->hasRole(['admin', 'sales']))
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    @endif
                    @if(auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small text-uppercase" style="font-size: 10px;letter-spacing:0.4px;font-weight:600;">Name</div>
                        <p class="mb-0 fw-semibold">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small text-uppercase" style="font-size: 10px;letter-spacing:0.4px;font-weight:600;">Email</div>
                        <p class="mb-0">{{ $customer->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small text-uppercase" style="font-size: 10px;letter-spacing:0.4px;font-weight:600;">Phone</div>
                        <p class="mb-0">{{ $customer->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small text-uppercase" style="font-size: 10px;letter-spacing:0.4px;font-weight:600;">Company</div>
                        <p class="mb-0">{{ $customer->company ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small text-uppercase" style="font-size: 10px;letter-spacing:0.4px;font-weight:600;">Status</div>
                        <span class="status-badge status-{{ $customer->status }}">{{ ucfirst($customer->status) }}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small text-uppercase" style="font-size: 10px;letter-spacing:0.4px;font-weight:600;">Assignment Status</div>
                        <span class="status-badge status-{{ $customer->assignment_status }}">{{ ucfirst($customer->assignment_status) }}</span>
                        @if($customer->assignment_status == 'pending' && auth()->user()->hasRole(['admin', 'manager']))
                        <div class="mt-2 d-flex gap-1">
                            <form method="POST" action="{{ route('customers.approve', $customer) }}" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-success">Approve</button></form>
                            <form method="POST" action="{{ route('customers.reject', $customer) }}" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-danger">Reject</button></form>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small text-uppercase" style="font-size: 10px;letter-spacing:0.4px;font-weight:600;">Assigned To</div>
                        <p class="mb-0">{{ $customer->assignedUser?->name ?? '-' }}</p>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small text-uppercase" style="font-size: 10px;letter-spacing:0.4px;font-weight:600;">Address</div>
                        <p class="mb-0">{{ $customer->address ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header"><h5 class="mb-0">Activities</h5></div>
            <div class="card-body">
                @forelse($customer->activities as $activity)
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
                @forelse($customer->followUps as $followUp)
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
            <div class="card-header"><h5 class="mb-0">Leads from this Customer</h5></div>
            <div class="card-body p-0">
                @forelse($customer->leads as $lead)
                <a href="{{ route('leads.show', $lead) }}" class="d-block px-4 py-3 border-bottom text-decoration-none text-dark">
                    <div class="fw-semibold">{{ $lead->name }}</div>
                    <span class="status-badge status-{{ $lead->status }}">{{ ucwords(str_replace('_', ' ', $lead->status)) }}</span>
                </a>
                @empty
                <p class="p-3 text-muted mb-0 text-center">No leads</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
