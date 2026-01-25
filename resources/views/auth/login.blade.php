@extends('layouts.app')

@section('content')
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4" style="border-radius: 15px 15px 0 0;">
                    <h3 class="font-weight-light my-1"><i class="fas fa-user-graduate me-2"></i>Student Login</h3>
                </div>
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-floating mb-3">
                            <input class="form-control @error('email') is-invalid @enderror" id="inputEmail" type="email"
                                name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus />
                            <label for="inputEmail">Email address</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control @error('password') is-invalid @enderror" id="inputPassword"
                                type="password" name="password" placeholder="Password" required />
                            <label for="inputPassword">Password</label>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small text-decoration-none" href="{{ route('password.request') }}">Forgot
                                Password?</a>
                            <button class="btn btn-primary btn-lg rounded-pill px-5" type="submit">Login</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3 text-muted">
                <small>Need an account? <a href="{{ route('register') }}" class="text-decoration-none fw-bold">Sign
                        up!</a></small><br>
                <small>Admin Login? <a href="{{ route('admin.login') }}" class="text-decoration-none">Click Here</a></small>
            </div>
        </div>
    </div>
@endsection