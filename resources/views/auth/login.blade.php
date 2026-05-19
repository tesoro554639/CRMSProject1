@extends('layouts.auth')

@section('guest-content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="text-center mb-4">
        <i class="bi bi-shield-lock text-primary" style="font-size: 40px;"></i>
        <h5 class="mt-2 fw-bold">Sign In</h5>
        <p class="text-muted small mb-0">Access your CRM workspace</p>
    </div>
    <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
        @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label small" for="remember">Remember me</label>
        </div>
        @if(Route::has('password.request'))
        <a href="{{ route('password.request') }}" class="text-decoration-none small auth-footer">Forgot password?</a>
        @endif
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2">Sign In</button>
</form>

    <div class="text-center mt-4 pt-3 border-top">
        <p class="text-muted small mb-1">Demo Credentials</p>
        <div class="d-flex flex-wrap justify-content-center gap-2 small">
            <span class="badge bg-light text-dark px-3 py-2"><strong>Admin:</strong> admin@clientpulse.io</span>
            <span class="badge bg-light text-dark px-3 py-2"><strong>Manager:</strong> manager@clientpulse.io</span>
            <span class="badge bg-light text-dark px-3 py-2"><strong>Sales:</strong> sales1@clientpulse.io</span>
            <span class="badge bg-secondary text-white px-3 py-2"><strong>Pass:</strong> password</span>
        </div>
    </div>
@endsection
