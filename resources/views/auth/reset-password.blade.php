@extends('layouts.auth')

@section('guest-content')
<div class="text-center mb-4">
    <i class="bi bi-shield-lock text-primary" style="font-size: 40px;"></i>
    <h5 class="mt-2 fw-bold">Reset Your Password</h5>
    <p class="text-muted small mb-0">Enter your new password below</p>
</div>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $request->email }}" required autofocus>
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">New Password</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
        @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-4">
        <label class="form-label">Confirm New Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2">Reset Password</button>
</form>

<div class="text-center mt-4">
    <a href="{{ route('login') }}" class="text-decoration-none small auth-footer">
        <i class="bi bi-arrow-left me-1"></i> Back to Login
    </a>
</div>
@endsection
