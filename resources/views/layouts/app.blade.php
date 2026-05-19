<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ClientPulse') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --sidebar-bg: #000000;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 72px;
            --primary: #1d4ed8;
            --primary-hover: #1e40af;
            --content-bg: #f0f2f5;
            --card-radius: 12px;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06);
            --border-color: #e5e7eb;
            --text-muted: #6b7280;
            --font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        * { box-sizing: border-box; }
        body {
            font-family: var(--font-family);
            background-color: var(--content-bg);
            overflow-x: hidden;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: width 0.25s ease;
        }
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        .sidebar .logo {
            padding: 18px 20px;
            color: #fff;
            font-weight: 700;
            font-size: 17px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
            white-space: nowrap;
            overflow: hidden;
        }
        .sidebar .logo i { font-size: 20px; flex-shrink: 0; }

        .sidebar .nav-section {
            color: #8a9ab5;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 20px 20px 6px;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar .nav-link-custom {
            padding: 10px 20px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.15s;
            border-left: 3px solid transparent;
            white-space: nowrap;
            overflow: hidden;
        }
        .sidebar .nav-link-custom:hover {
            background: rgba(37,99,235,0.15);
            color: #fff;
            border-left-color: #2563eb;
        }
        .sidebar .nav-link-custom.active {
            background: rgba(37,99,235,0.2);
            color: #fff;
            border-left-color: #2563eb;
        }
        .sidebar .nav-link-custom i {
            font-size: 18px;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
        }
        .sidebar .nav-link-custom .badge-count {
            margin-left: auto;
            background: var(--primary);
            color: #fff;
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 10px;
            flex-shrink: 0;
        }
        .sidebar .nav-link-custom .badge-count.orange { background: #f97316; }
        .sidebar .nav-link-custom .badge-count.red { background: #ef4444; }

        .sidebar .sidebar-user {
            margin-top: auto;
            padding: 14px 20px;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar .nav-label-text {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.25s ease;
        }
        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* ===== TOP HEADER ===== */
        .top-header {
            background: #fff;
            border-bottom: 1px solid var(--border-color);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            gap: 16px;
        }
        .top-header .page-info { min-width: 0; }
        .top-header .workspace-label {
            font-size: 10px;
            color: var(--primary);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .top-header h4 {
            margin: 2px 0 0;
            font-weight: 700;
            font-size: 20px;
            color: #111827;
        }
        .top-header .breadcrumb {
            margin: 2px 0 0;
            font-size: 12px;
        }
        .top-header .breadcrumb .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
        }
        .top-header .breadcrumb .breadcrumb-item.active {
            color: var(--text-muted);
        }
        .top-header .header-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }
        .top-header .header-user {
            text-align: right;
            line-height: 1.3;
        }
        .top-header .header-user .name {
            font-weight: 600;
            font-size: 13px;
            color: #111827;
        }

        /* ===== USER AVATAR ===== */
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 13px;
            flex-shrink: 0;
        }
        .user-avatar.admin { background: var(--primary); }
        .user-avatar.manager { background: #0d9488; }
        .user-avatar.sales { background: #059669; }

        /* ===== CONTENT AREA ===== */
        .content-area {
            padding: 24px 28px;
            flex: 1;
        }

        /* ===== CARDS ===== */
        .card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 20px;
            font-weight: 600;
        }
        .card-body { padding: 20px; }
        .card-body.p-0 .table { margin-bottom: 0; }
        .card-body.p-0 .table th,
        .card-body.p-0 .table td {
            padding-left: 20px;
            padding-right: 20px;
        }

        /* ===== STAT CARDS ===== */
        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .stat-card .stat-value {
            font-size: 26px;
            font-weight: 700;
            line-height: 1.2;
        }
        .stat-card .stat-label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* ===== TABLES ===== */
        .table th {
            text-transform: uppercase;
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 700;
            letter-spacing: 0.4px;
            border-bottom-width: 2px;
            padding: 12px 16px;
            white-space: nowrap;
        }
        .table td {
            padding: 12px 16px;
            vertical-align: middle;
            border-bottom-color: #f3f4f6;
        }
        .table-hover tbody tr:hover {
            background-color: #f9fafb;
        }

        /* ===== BADGES / STATUS ===== */
        .status-badge {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }
        .status-active, .status-approved, .status-completed, .status-won { background: #dcfce7; color: #166534; }
        .status-inactive, .status-rejected, .status-lost { background: #fee2e2; color: #991b1b; }
        .status-pending, .status-new { background: #fef3c7; color: #92400e; }
        .status-contacted { background: #ccfbf1; color: #0f766e; }
        .status-qualified { background: #d1fae5; color: #065f46; }
        .status-proposal_sent { background: #ffedd5; color: #9a3412; }
        .status-negotiation { background: #ede9fe; color: #6d28d9; }
        .status-call { background: #dcfce7; color: #166534; }
        .status-email { background: #dbeafe; color: #1e40af; }
        .status-meeting { background: #ede9fe; color: #6d28d9; }
        .status-note { background: #fef3c7; color: #92400e; }

        .priority-low { background: #f3f4f6; color: #6b7280; }
        .priority-medium { background: #fef3c7; color: #92400e; }
        .priority-high { background: #ffedd5; color: #9a3412; }
        .priority-critical { background: #fee2e2; color: #991b1b; }

        .role-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .role-admin { background: #dbeafe; color: #1e40af; }
        .role-manager { background: #ccfbf1; color: #0f766e; }
        .role-sales { background: #dcfce7; color: #166534; }

        /* ===== BUTTONS ===== */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        .btn-sm { font-size: 12px; padding: 4px 12px; border-radius: 6px; }
        .btn { border-radius: 8px; font-weight: 500; padding: 8px 18px; font-size: 13px; }

        /* ===== FORMS ===== */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 9px 14px;
            border-color: #d1d5db;
            font-size: 13px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(29,78,216,0.1);
        }
        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        /* ===== ALERTS ===== */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 13px;
        }

        /* ===== PAGINATION ===== */
        .pagination { margin-bottom: 0; }
        .page-link {
            border-radius: 6px !important;
            margin: 0 2px;
            font-size: 13px;
            color: #374151;
            border-color: #e5e7eb;
            padding: 6px 12px;
        }
        .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
        }

        /* ===== KANBAN ===== */
        .kanban-board { display: flex; gap: 16px; overflow-x: auto; padding-bottom: 12px; min-height: 70vh; }
        .kanban-column { flex: 0 0 270px; }
        .kanban-column .card-header {
            border-top: 3px solid;
            padding: 12px 16px;
        }
        .kanban-card { cursor: default; transition: box-shadow 0.15s; }
        .kanban-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

        /* ===== ACTIVITY TIMELINE ===== */
        .timeline-icon {
            width: 42px; height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar:not(.collapsed) { width: var(--sidebar-width); }
            .sidebar.collapsed { width: 0; overflow: hidden; }
            .main-content { margin-left: 0; }
            .main-content.expanded { margin-left: 0; }
            .content-area { padding: 16px; }
            .top-header { padding: 12px 16px; }
        }

        /* ===== UTILITY ===== */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .gap-2 { gap: 8px !important; }
        .gap-3 { gap: 12px !important; }
    </style>
    @yield('styles')
</head>
<body>
    @auth
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <i class="bi bi-people-fill"></i>
            <span class="nav-label-text">ClientPulse</span>
        </div>

        <div class="nav-section">Main</div>
        <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid"></i>
            <span class="nav-label-text">Dashboard</span>
        </a>

        <div class="nav-section">Performance</div>
        @if(auth()->user()->hasRole(['admin', 'manager']))
        <a href="{{ route('reports.index') }}" class="nav-link-custom {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i>
            <span class="nav-label-text">Reports</span>
        </a>
        @endif

        <div class="nav-section">Pipeline</div>
        <a href="{{ route('customers.index') }}" class="nav-link-custom {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            <span class="nav-label-text">Customers</span>
        </a>
        <a href="{{ route('leads.index') }}" class="nav-link-custom {{ request()->routeIs('leads.*') ? 'active' : '' }}">
            <i class="bi bi-send"></i>
            <span class="nav-label-text">Leads</span>
            @php $leadCount = \App\Models\Lead::whereIn('status', ['new','contacted','qualified','proposal_sent','negotiation'])->count(); @endphp
            @if($leadCount > 0) <span class="badge-count">{{ $leadCount }}</span> @endif
        </a>
        <a href="{{ route('activities.index') }}" class="nav-link-custom {{ request()->routeIs('activities.*') ? 'active' : '' }}">
            <i class="bi bi-activity"></i>
            <span class="nav-label-text">Activities</span>
            @php $activityCount = \App\Models\Activity::whereDate('activity_date', '>=', now()->subDays(7))->count(); @endphp
            @if($activityCount > 0) <span class="badge-count orange">{{ $activityCount }}</span> @endif
        </a>
        <a href="{{ route('follow-ups.index') }}" class="nav-link-custom {{ request()->routeIs('follow-ups.*') ? 'active' : '' }}">
            <i class="bi bi-check2-square"></i>
            <span class="nav-label-text">Follow Ups</span>
            @php $overdueCount = \App\Models\FollowUp::where('status', 'pending')->where('due_date', '<', now()->toDateString())->count(); @endphp
            @if($overdueCount > 0) <span class="badge-count red">{{ $overdueCount }}</span> @endif
        </a>

        @if(auth()->user()->isAdmin())
        <div class="nav-section">Admin</div>
        <a href="{{ route('users.index') }}" class="nav-link-custom {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i>
            <span class="nav-label-text">Users</span>
        </a>
        <a href="{{ route('system-config.index') }}" class="nav-link-custom {{ request()->routeIs('system-config.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            <span class="nav-label-text">System Config</span>
        </a>
        @endif

        <div class="sidebar-user">
            <div class="user-avatar {{ auth()->user()->role }}">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div class="nav-label-text">
                <div style="font-size:13px;font-weight:600;color:#fff;">{{ auth()->user()->name }}</div>
                <span class="role-badge role-{{ auth()->user()->role }}">{{ auth()->user()->role }}</span>
            </div>
        </div>
    </div>

    <div class="main-content" id="mainContent">
        <div class="top-header">
            <div class="page-info">
                <div class="workspace-label">Workspace · {{ auth()->user()->role }}</div>
                <h4>@yield('page-title', 'Dashboard')</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
            <div class="header-actions">
                <div class="header-user d-none d-md-block">
                    <div class="name">{{ auth()->user()->name }}</div>
                    <span class="role-badge role-{{ auth()->user()->role }}">{{ auth()->user()->role }}</span>
                </div>
                <div class="user-avatar {{ auth()->user()->role }} d-none d-md-flex">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-secondary p-1" title="Logout">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="content-area">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @yield('content')
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    document.getElementById('sidebar')?.classList.toggle('collapsed');
                    document.getElementById('mainContent')?.classList.toggle('expanded');
                });
            }
        });
    </script>
    @else
    @yield('guest-content')
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
