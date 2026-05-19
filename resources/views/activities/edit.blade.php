@extends('layouts.app')

@section('page-title', 'Edit Activity')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('activities.index') }}">Activities</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Edit Activity</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('activities.update', $activity) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Activity Type <span class="text-danger">*</span></label>
                            <select name="activity_type" class="form-select @error('activity_type') is-invalid @enderror" required>
                                <option value="call" {{ old('activity_type', $activity->activity_type) == 'call' ? 'selected' : '' }}>Call</option>
                                <option value="email" {{ old('activity_type', $activity->activity_type) == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="meeting" {{ old('activity_type', $activity->activity_type) == 'meeting' ? 'selected' : '' }}>Meeting</option>
                                <option value="note" {{ old('activity_type', $activity->activity_type) == 'note' ? 'selected' : '' }}>Note</option>
                            </select>
                            @error('activity_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="activity_date" class="form-control @error('activity_date') is-invalid @enderror" value="{{ old('activity_date', $activity->activity_date->format('Y-m-d')) }}" required>
                            @error('activity_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description', $activity->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Link to</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <select name="lead_id" class="form-select">
                                        <option value="">-- Select Lead --</option>
                                        @foreach($leads as $lead)
                                        <option value="{{ $lead->id }}" {{ old('lead_id', $activity->lead_id) == $lead->id ? 'selected' : '' }}>{{ $lead->name }} ({{ $lead->lead_id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select name="customer_id" class="form-select">
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id', $activity->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->first_name }} {{ $customer->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <form method="POST" action="{{ route('activities.destroy', $activity) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                        <div class="d-flex gap-2">
                            <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Activity</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
