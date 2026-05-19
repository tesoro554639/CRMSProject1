@extends('layouts.auth')

@section('guest-content')
<div class="text-center mb-4">
    <i class="bi bi-key text-primary" style="font-size: 40px;"></i>
    <h5 class="mt-2 fw-bold">Reset Password</h5>
    <p class="text-muted small mb-0">Enter your email and we'll send you a reset link</p>
</div>

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2">Send Reset Link</button>
</form>

<div class="text-center mt-4">
    <a href="{{ route('login') }}" class="text-decoration-none small auth-footer">
        <i class="bi bi-arrow-left me-1"></i> Back to Login
    </a>
</div>
@endsection
