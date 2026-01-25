@extends('layouts.app')

@section('content')
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4" style="border-radius: 15px 15px 0 0;">
                    <h3 class="font-weight-light my-1"><i class="fas fa-user-plus me-2"></i>Create Student Account</h3>
                </div>
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input class="form-control @error('name') is-invalid @enderror" id="inputName" type="text"
                                name="name" value="{{ old('name') }}" placeholder="Enter your name" required autofocus />
                            <label for="inputName">Full Name</label>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input class="form-control @error('email') is-invalid @enderror" id="inputEmail" type="email"
                                name="email" value="{{ old('email') }}" placeholder="name@example.com" required />
                            <label for="inputEmail">Email address</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <input class="form-control @error('password') is-invalid @enderror" id="inputPassword"
                                        type="password" name="password" placeholder="Create a password" required />
                                    <label for="inputPassword">Password</label>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <input class="form-control" id="inputPasswordConfirm" type="password"
                                        name="password_confirmation" placeholder="Confirm password" required />
                                    <label for="inputPasswordConfirm">Confirm Password</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button class="btn btn-primary btn-lg rounded-pill" type="submit">Create Account</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small"><a href="{{ route('login') }}">Have an account? Go to login</a></div>
                </div>
            </div>
        </div>
    </div>
@endsection