<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - TurningPoint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .custom-label {
            letter-spacing: 0.5px;
            font-size: 0.75rem;
        }

        .form-control:focus {
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(229, 57, 53, 0.25);
            border-color: #e53935;
        }

        .transition-all:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .bg-red-gradient {
            background: linear-gradient(135deg, #e53935 0%, #b71c1c 100%);
        }

        .text-red {
            color: #e53935 !important;
        }

        .btn-red {
            background-color: #e53935;
            border-color: #e53935;
            color: white;
        }

        .btn-red:hover {
            background-color: #c62828;
            border-color: #c62828;
            color: white;
        }
    </style>
</head>

<body>

    <div class="row g-0 min-vh-100">
        <!-- Left Side: Branding -->
        <div
            class="col-lg-6 d-none d-lg-flex flex-column align-items-center justify-content-center text-white text-center p-5 bg-red-gradient">
            <div class="mb-4">
                <div class="bg-white rounded-circle p-3 d-inline-flex shadow-sm mb-3">
                    <i class="fas fa-graduation-cap fa-4x text-red"></i>
                </div>
            </div>
            <h1 class="fw-bold display-5 mb-2">TurningPoint Exam System</h1>
            <p class="fs-5 opacity-75 mb-4">Empowering students to achieve their academic goals.</p>
            <div class="small opacity-50">
                &copy; {{ date('Y') }} TurningPoint. All rights reserved.
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white p-4 p-md-5">
            <div class="w-100" style="max-width: 450px;">
                <div class="mb-5">
                    <h2 class="fw-bold text-dark mb-1">Welcome Back!</h2>
                    <p class="text-muted">Please sign in to continue accessing your exam panel.</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="inputEmail"
                            class="form-label text-uppercase small fw-bold text-muted custom-label">Email
                            Address</label>
                        <input
                            class="form-control form-control-lg bg-light border-0 @error('email') is-invalid @enderror"
                            id="inputEmail" type="email" name="email" value="{{ old('email') }}"
                            placeholder="name@example.com" required autofocus
                            style="padding: 1rem 1.25rem; font-size: 0.95rem;">
                        @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="inputPassword"
                            class="form-label text-uppercase small fw-bold text-muted custom-label">Password</label>
                        <input
                            class="form-control form-control-lg bg-light border-0 @error('password') is-invalid @enderror"
                            id="inputPassword" type="password" name="password" placeholder="........" required
                            style="padding: 1rem 1.25rem; font-size: 0.95rem;">
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="d-grid mb-4">
                        <button class="btn btn-red btn-lg fw-bold py-3 shadow-sm transition-all" type="submit">
                            Sign In <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>

                    <div class="text-center d-flex justify-content-between align-items-center">
                        <a class="small text-decoration-none fw-bold text-secondary"
                            href="{{ route('password.request') }}">Forgot
                            Password?</a>
                        <a class="small text-decoration-none fw-bold text-red" href="{{ route('register') }}">Create
                            Account</a>
                    </div>
                </form>

                <div class="mt-5 pt-4 text-center border-top">
                    <small class="text-muted">Are you an Admin? <a href="{{ route('admin.login') }}"
                            class="text-decoration-none fw-bold">Login Here</a></small>
                </div>
            </div>
        </div>
    </div>

</body>

</html>