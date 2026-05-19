<!DOCTYPE html>
<html>
<head>
    <title>CRM Report - {{ now()->format('Y-m-d') }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #1a2035; }
        .stats { display: flex; gap: 20px; margin: 20px 0; }
        .stat-box { background: #f3f4f6; padding: 15px; border-radius: 8px; flex: 1; }
        .stat-value { font-size: 24px; font-weight: bold; color: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #e5e7eb; text-align: left; }
        th { background: #f9fafb; }
    </style>
</head>
<body>
    <h1>ClientPulse Report</h1>
    <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    
    <div class="stats">
        <div class="stat-box">
            <div>Total Customers</div>
            <div class="stat-value">{{ $totalCustomers }}</div>
        </div>
        <div class="stat-box">
            <div>Pipeline Leads</div>
            <div class="stat-value">{{ $pipelineLeads }}</div>
        </div>
        <div class="stat-box">
            <div>Won Leads</div>
            <div class="stat-value">{{ $wonLeads }}</div>
        </div>
    </div>

    <h2>Lead Status Breakdown</h2>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation', 'won', 'lost'] as $status)
            <tr>
                <td>{{ ucwords(str_replace('_', ' ', $status)) }}</td>
                <td>{{ $leadStatusCounts[$status] ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($fromDate || $toDate)
    <p style="margin-top: 20px; color: #6b7280;">
        Report Period: {{ $fromDate ? $fromDate : 'Start' }} to {{ $toDate ? $toDate : 'Now' }}
    </p>
    @endif
</body>
</html>