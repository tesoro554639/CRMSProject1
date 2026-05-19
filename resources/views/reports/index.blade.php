@extends('layouts.app')

@section('page-title', 'Reports')

@section('breadcrumb')
<li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div><small class="text-muted">Team and pipeline reporting for admin and managers.</small></div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.export-csv') }}" class="btn btn-outline-secondary"><i class="bi bi-download me-1"></i> Export CSV</a>
        <a href="{{ route('reports.export-pdf') }}" class="btn btn-primary"><i class="bi bi-file-earmark-pdf me-1"></i> Export PDF</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 g-md-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="from" class="form-control" value="{{ $fromDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="to" class="form-control" value="{{ $toDate }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i> Apply Filter</button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10"><i class="bi bi-people text-primary" style="font-size: 22px;"></i></div>
                <div>
                    <div class="stat-label">Total Customers</div>
                    <div class="stat-value text-primary">{{ $totalCustomers }}</div>
                    <span class="status-badge status-new mt-1 d-inline-block">All Customers</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10"><i class="bi bi-funnel text-warning" style="font-size: 22px;"></i></div>
                <div>
                    <div class="stat-label">Pipeline Leads</div>
                    <div class="stat-value text-warning">{{ $pipelineLeads }}</div>
                    <span class="status-badge status-pending mt-1 d-inline-block">Active</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10"><i class="bi bi-trophy text-success" style="font-size: 22px;"></i></div>
                <div>
                    <div class="stat-label">Won Leads</div>
                    <div class="stat-value text-success">{{ $wonLeads }}</div>
                    <span class="status-badge status-won mt-1 d-inline-block">Closed Won</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10"><i class="bi bi-pie-chart text-info" style="font-size: 22px;"></i></div>
                <div>
                    <div class="stat-label">Follow-up Completion</div>
                    <div class="stat-value text-info">{{ $completionRate }}%</div>
                    <span class="status-badge" style="background:#ccfbf1;color:#0f766e;" class="mt-1 d-inline-block">Completion Rate</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0">Lead Status Report</h6></div>
            <div class="card-body">
                <canvas id="leadStatusChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0">Sales Pipeline Summary</h6></div>
            <div class="card-body">
                <canvas id="pipelineSummaryChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const leadStatusCtx = document.getElementById('leadStatusChart').getContext('2d');
new Chart(leadStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['New','Contacted','Qualified','Proposal Sent','Negotiation','Won','Lost'],
        datasets: [{ data: [{{ $leadStatusCounts['new'] ?? 0 }},{{ $leadStatusCounts['contacted'] ?? 0 }},{{ $leadStatusCounts['qualified'] ?? 0 }},{{ $leadStatusCounts['proposal_sent'] ?? 0 }},{{ $leadStatusCounts['negotiation'] ?? 0 }},{{ $leadStatusCounts['won'] ?? 0 }},{{ $leadStatusCounts['lost'] ?? 0 }}], backgroundColor: ['#3b82f6','#14b8a6','#22c55e','#f97316','#a855f7','#10b981','#ef4444'] }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});
const pipelineCtx = document.getElementById('pipelineSummaryChart').getContext('2d');
new Chart(pipelineCtx, {
    type: 'bar',
    data: {
        labels: ['Active','Won','Lost'],
        datasets: [{ label: 'Leads', data: [{{ $activeLeads }},{{ $wonLeads }},{{ $lostLeads }}], backgroundColor: ['#0891b2','#22c55e','#ef4444'] }]
    },
    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
});
</script>
@endsection
