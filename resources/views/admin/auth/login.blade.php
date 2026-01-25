<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TurningPoint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .custom-label {
            letter-spacing: 0.5px;
            font-size: 0.75rem;
        }

        .form-control:focus {
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }

        .transition-all:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        }
    </style>
</head>

<body>

    <div class="row g-0 min-vh-100">
        <!-- Left Side: Branding -->
        <div
            class="col-lg-6 d-none d-lg-flex flex-column align-items-center justify-content-center text-white text-center p-5 bg-gradient-primary">
            <div class="mb-4">
                <div class="bg-white rounded-circle p-3 d-inline-flex shadow-sm mb-3">
                    <i class="fas fa-user-shield fa-4x text-primary"></i>
                </div>
            </div>
            <h1 class="fw-bold display-5 mb-2">Admin Control Panel</h1>
            <p class="fs-5 opacity-75 mb-4">Manage exams, students, and results with ease.</p>
            <div class="small opacity-50">
                &copy; {{ date('Y') }} TurningPoint. All rights reserved.
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white p-4 p-md-5">
            <div class="w-100" style="max-width: 450px;">
                <div class="mb-5">
                    <h2 class="fw-bold text-dark mb-1">Admin Sign In</h2>
                    <p class="text-muted">Enter your credentials to access the dashboard.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.post') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label text-uppercase small fw-bold text-muted custom-label">Email
                            Address</label>
                        <input class="form-control form-control-lg bg-light border-0" type="email" name="email" required
                            autofocus placeholder="admin@example.com"
                            style="padding: 1rem 1.25rem; font-size: 0.95rem;">
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase small fw-bold text-muted custom-label">Password</label>
                        <input class="form-control form-control-lg bg-light border-0" type="password" name="password"
                            required placeholder="........" style="padding: 1rem 1.25rem; font-size: 0.95rem;">
                    </div>

                    <div class="d-grid mb-4">
                        <button class="btn btn-primary btn-lg fw-bold py-3 shadow-sm transition-all" type="submit">
                            Access Dashboard <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-5 pt-4 text-center border-top">
                    <small class="text-muted">Return to <a href="{{ route('login') }}"
                            class="text-decoration-none fw-bold">Student Login</a></small>
                </div>
            </div>
        </div>
    </div>

</body>

</html>