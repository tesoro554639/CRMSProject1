@extends('layouts.app')

@php $pageTitle = auth()->user()->isAdmin() ? 'Admin Dashboard' : 'Manager Dashboard'; @endphp

@section('page-title', $pageTitle)

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10">
                    <i class="bi bi-people text-primary" style="font-size: 22px;"></i>
                </div>
                <div>
                    <div class="stat-label">Total Customers</div>
                    <div class="stat-value text-primary">{{ $totalCustomers }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-secondary bg-opacity-10">
                    <i class="bi bi-funnel text-secondary" style="font-size: 22px;"></i>
                </div>
                <div>
                    <div class="stat-label">Active Leads</div>
                    <div class="stat-value" style="color: #6b7280;">{{ $activeLeads }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-check-circle text-success" style="font-size: 22px;"></i>
                </div>
                <div>
                    <div class="stat-label">Completed Follow-ups</div>
                    <div class="stat-value text-success">{{ $completedFollowUps }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 22px;"></i>
                </div>
                <div>
                    <div class="stat-label">Overdue Follow-ups</div>
                    <div class="stat-value text-danger">{{ $overdueFollowUps }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0">Lead Pipeline</h6></div>
            <div class="card-body"><canvas id="leadPipelineChart" height="220"></canvas></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0">Follow-up Health</h6></div>
            <div class="card-body"><canvas id="followUpHealthChart" height="220"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Lead Status Breakdown</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $statusLabels = ['new'=>'New','contacted'=>'Contacted','qualified'=>'Qualified','proposal_sent'=>'Proposal Sent','negotiation'=>'Negotiation','won'=>'Won','lost'=>'Lost']; @endphp
                            @foreach($statusLabels as $key => $label)
                            <tr>
                                <td><span class="status-badge status-{{ $key }}">{{ $label }}</span></td>
                                <td class="fw-bold">{{ $leadStatusCounts[$key] ?? 0 }}</td>
                            </tr>
                            @endforeach
                            <tr class="fw-bold border-top border-2">
                                <td>Total Leads</td>
                                <td>{{ array_sum($leadStatusCounts) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Upcoming Follow-ups</h6>
                <span class="badge bg-warning">{{ $pendingFollowUps }} Pending</span>
            </div>
            <div class="card-body p-0">
                @forelse($upcomingFollowUps as $followUp)
                <div class="px-4 py-3 border-bottom last:border-0">
                    <div class="fw-semibold">{{ $followUp->title }}</div>
                    <small class="text-muted">{{ $followUp->due_date->format('M d, Y') }} · {{ $followUp->user?->name ?? 'Deleted User' }}</small>
                    @if($followUp->lead)<br><small class="text-primary">{{ $followUp->lead->name }}</small>@endif
                </div>
                @empty
                <div class="p-4 text-muted text-center">No upcoming follow-ups</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0">Recent Activities</h6></div>
            <div class="card-body p-0">
                @forelse($recentActivities as $activity)
                <div class="px-4 py-3 border-bottom last:border-0">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <span class="status-badge status-{{ $activity->activity_type }} me-1">{{ ucfirst($activity->activity_type) }}</span>
                            <span class="small text-muted">{{ $activity->user?->name ?? 'Deleted User' }}</span>
                            @if($activity->lead)<small class="text-primary"> · {{ $activity->lead->name }}</small>
                            @elseif($activity->customer)<small class="text-success"> · {{ $activity->customer->first_name }}</small>@endif
                        </div>
                        <small class="text-muted text-nowrap">{{ $activity->activity_date->diffForHumans() }}</small>
                    </div>
                    <p class="mb-0 mt-1 small text-muted">{{ Str::limit($activity->description, 80) }}</p>
                </div>
                @empty
                <div class="p-4 text-muted text-center">No recent activities</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const leadPipelineCtx = document.getElementById('leadPipelineChart').getContext('2d');
new Chart(leadPipelineCtx, {
    type: 'doughnut',
    data: {
        labels: ['New','Contacted','Qualified','Proposal Sent','Negotiation','Won','Lost'],
        datasets: [{
            data: [{{ $leadStatusCounts['new'] ?? 0 }},{{ $leadStatusCounts['contacted'] ?? 0 }},{{ $leadStatusCounts['qualified'] ?? 0 }},{{ $leadStatusCounts['proposal_sent'] ?? 0 }},{{ $leadStatusCounts['negotiation'] ?? 0 }},{{ $leadStatusCounts['won'] ?? 0 }},{{ $leadStatusCounts['lost'] ?? 0 }}],
            backgroundColor: ['#3b82f6','#14b8a6','#22c55e','#f97316','#a855f7','#10b981','#ef4444']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});

const followUpHealthCtx = document.getElementById('followUpHealthChart').getContext('2d');
new Chart(followUpHealthCtx, {
    type: 'bar',
    data: {
        labels: ['Completed','Pending','Overdue'],
        datasets: [{
            label: 'Follow-ups',
            data: [{{ $completedFollowUpsCount }},{{ $pendingFollowUps }},{{ $overdueFollowUpsCount }}],
            backgroundColor: ['#166534','#0891b2','#dc2626']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
});
</script>
@endsection
