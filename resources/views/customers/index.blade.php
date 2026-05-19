@extends('layouts.app')

@section('page-title', 'Customers')

@section('breadcrumb')
<li class="breadcrumb-item active">Customers</li>
@endsection

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10"><i class="bi bi-people text-primary" style="font-size: 22px;"></i></div>
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
                <div class="stat-icon bg-success bg-opacity-10"><i class="bi bi-check-circle text-success" style="font-size: 22px;"></i></div>
                <div>
                    <div class="stat-label">Active</div>
                    <div class="stat-value text-success">{{ $activeCustomers }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10"><i class="bi bi-x-circle text-danger" style="font-size: 22px;"></i></div>
                <div>
                    <div class="stat-label">Inactive</div>
                    <div class="stat-value text-danger">{{ $inactiveCustomers }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10"><i class="bi bi-plus-circle text-info" style="font-size: 22px;"></i></div>
                <div>
                    <div class="stat-label">New This Month</div>
                    <div class="stat-value text-info">{{ $newThisMonth }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div><small class="text-muted">Viewing all customer records</small></div>
    @can('create', \App\Models\Customer::class)
    <a href="{{ route('customers.create') }}" class="btn btn-primary"><i class="bi bi-plus me-1"></i> Add Customer</a>
    @endcan
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 g-md-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search name, email, phone, company..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="assigned_user_id" class="form-select">
                    <option value="">All Assignees</option>
                    @foreach($salesUsers as $user)
                    <option value="{{ $user->id }}" {{ request('assigned_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="assignment_status" class="form-select">
                    <option value="">All Assignment</option>
                    <option value="pending" {{ request('assignment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('assignment_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('assignment_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i> Filter</button>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Assignment</th>
                        <th>Assigned To</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td class="fw-semibold">#{{ $customer->id }}</td>
                        <td>{{ $customer->first_name }}</td>
                        <td>{{ $customer->last_name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->company ?? '-' }}</td>
                        <td><span class="status-badge status-{{ $customer->status }}">{{ ucfirst($customer->status) }}</span></td>
                        <td><span class="status-badge status-{{ $customer->assignment_status }}">{{ ucfirst($customer->assignment_status) }}</span></td>
                        <td>{{ $customer->assignedUser?->name ?? '-' }}</td>
                        <td class="text-end"><a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-4 text-muted">No customers found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $customers->appends(request()->query())->links() }}</div>
@endsection
