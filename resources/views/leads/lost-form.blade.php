@extends('layouts.app')

@section('page-title', 'Mark Lead as Lost')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
<li class="breadcrumb-item"><a href="{{ route('leads.show', $lead) }}">{{ $lead->name }}</a></li>
<li class="breadcrumb-item active">Lost Form</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Mark Lead as Lost</h5></div>
            <div class="card-body">
                <p class="text-muted">You are marking <strong>{{ $lead->name }}</strong> as lost. Please provide the reason.</p>
                <form method="POST" action="{{ route('leads.mark-lost', $lead) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Lost Category <span class="text-danger">*</span></label>
                        <select name="lost_category" class="form-select @error('lost_category') is-invalid @enderror" required>
                            <option value="">-- Select Category --</option>
                            <option value="Price">Price</option>
                            <option value="Competitor">Competitor</option>
                            <option value="No Response">No Response</option>
                            <option value="Lost Interest">Lost Interest</option>
                            <option value="Timeline">Timeline</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('lost_category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lost Reason <span class="text-danger">*</span></label>
                        <textarea name="lost_reason" class="form-control @error('lost_reason') is-invalid @enderror" rows="4" required placeholder="Please provide more details..."></textarea>
                        @error('lost_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-danger">Mark as Lost</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
