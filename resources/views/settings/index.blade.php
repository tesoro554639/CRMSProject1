@extends('layouts.app')

@section('page-title', 'System Configuration')

@section('breadcrumb')
<li class="breadcrumb-item active">System Config</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Business Identity</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('system-config.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Application Name</label>
                            <input type="text" name="app_name" class="form-control" value="{{ old('app_name', $config->app_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Currency Code</label>
                            <input type="text" name="currency_code" class="form-control" value="{{ old('currency_code', $config->currency_code) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Company Email</label>
                            <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $config->company_email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company Phone</label>
                            <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $config->company_phone) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Company Address</label>
                            <textarea name="company_address" class="form-control" rows="2">{{ old('company_address', $config->company_address) }}</textarea>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3 fw-bold" style="font-size:13px;">Pipeline Defaults</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Default Lead Status</label>
                            <select name="default_lead_status" class="form-select">
                                <option value="new" {{ old('default_lead_status', $config->default_lead_status) == 'new' ? 'selected' : '' }}>New</option>
                                <option value="contacted" {{ old('default_lead_status', $config->default_lead_status) == 'contacted' ? 'selected' : '' }}>Contacted</option>
                                <option value="qualified" {{ old('default_lead_status', $config->default_lead_status) == 'qualified' ? 'selected' : '' }}>Qualified</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Lead Priority</label>
                            <select name="default_lead_priority" class="form-select">
                                <option value="low" {{ old('default_lead_priority', $config->default_lead_priority) == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('default_lead_priority', $config->default_lead_priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('default_lead_priority', $config->default_lead_priority) == 'high' ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ old('default_lead_priority', $config->default_lead_priority) == 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3 fw-bold" style="font-size:13px;">Password Reset</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Reset Link Expiry (minutes)</label>
                            <input type="number" name="reset_link_expiry" class="form-control" value="{{ old('reset_link_expiry', $config->reset_link_expiry) }}" min="1" max="10080">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4"><i class="bi bi-check2 me-1"></i> Save Configuration</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Current Snapshot</h6></div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block">App Name</small>
                        <strong>{{ $config->app_name }}</strong>
                    </li>
                    <li class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block">Currency</small>
                        <strong>{{ $config->currency_code }}</strong>
                    </li>
                    <li class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block">Default Lead Status</small>
                        <strong>{{ ucfirst($config->default_lead_status) }}</strong>
                    </li>
                    <li class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block">Default Priority</small>
                        <strong>{{ ucfirst($config->default_lead_priority) }}</strong>
                    </li>
                    <li class="mb-0">
                        <small class="text-muted d-block">Reset Link Expiry</small>
                        <strong>{{ $config->reset_link_expiry }} minutes</strong>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <p class="text-muted small mb-0">Password reset links currently expire after <strong>{{ $config->reset_link_expiry }} minutes</strong>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
