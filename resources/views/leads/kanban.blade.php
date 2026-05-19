@extends('layouts.app')

@section('page-title', 'Leads - Kanban')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
<li class="breadcrumb-item active">Kanban</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h5 class="mb-0">Lead Pipeline</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary"><i class="bi bi-table me-1"></i> Table View</a>
        <a href="{{ route('leads.create') }}" class="btn btn-primary"><i class="bi bi-plus me-1"></i> Add Lead</a>
    </div>
</div>

<div class="kanban-board">
    @php
    $statuses = [
        'new' => ['label' => 'New', 'color' => '#3b82f6'],
        'contacted' => ['label' => 'Contacted', 'color' => '#14b8a6'],
        'qualified' => ['label' => 'Qualified', 'color' => '#22c55e'],
        'proposal_sent' => ['label' => 'Proposal Sent', 'color' => '#f97316'],
        'negotiation' => ['label' => 'Negotiation', 'color' => '#a855f7'],
        'won' => ['label' => 'Won', 'color' => '#10b981'],
        'lost' => ['label' => 'Lost', 'color' => '#ef4444'],
    ];
    @endphp

    @foreach($statuses as $status => $config)
    @php $statusLeads = $leads->where('status', $status); @endphp
    <div class="kanban-column">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center" style="border-top-color: {{ $config['color'] }};">
                <h6 class="mb-0" style="color: {{ $config['color'] }};">{{ $config['label'] }}</h6>
                <span class="badge bg-secondary rounded-pill">{{ $statusLeads->count() }}</span>
            </div>
            <div class="card-body p-2" style="min-height: 400px;">
                @forelse($statusLeads as $lead)
                <div class="card kanban-card mb-2">
                    <div class="card-body p-3">
                        <div class="fw-semibold small">{{ $lead->name }}</div>
                        @if($lead->expected_value)
                        <div class="text-success small fw-semibold">${{ number_format($lead->expected_value, 0) }}</div>
                        @endif
                        <div class="mt-2"><span class="status-badge priority-{{ $lead->priority }}">{{ ucfirst($lead->priority) }}</span></div>
                        <small class="text-muted d-block mt-1">{{ $lead->assignedUser?->name ?? 'Unassigned' }}</small>
                        <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">View</a>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4"><small>No leads</small></div>
                @endforelse
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
