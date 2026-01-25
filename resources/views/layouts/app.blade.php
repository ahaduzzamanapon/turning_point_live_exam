<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TurningPoint') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
        }

        .bg-primary {
            background-color: #e53935 !important;
        }

        .text-primary {
            color: #e53935 !important;
        }

        .btn-primary {
            background-color: #e53935;
            border-color: #e53935;
        }

        .btn-primary:hover {
            background-color: #d32f2f;
            border-color: #d32f2f;
        }

        .btn-outline-primary {
            color: #e53935;
            border-color: #e53935;
        }

        .btn-outline-primary:hover {
            background-color: #e53935;
            color: #fff;
        }

        /* Layout */
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #fff;
            color: #333;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            background: #e53935;
            color: #fff;
            text-align: center;
        }

        .sidebar-header h3 {
            font-size: 22px;
            margin: 0;
            font-weight: 600;
        }

        #sidebar ul.components {
            padding: 20px 0;
            list-style: none;
        }

        #sidebar ul li a {
            padding: 15px 20px;
            font-size: 16px;
            display: flex;
            align-items: center;
            color: #555;
            text-decoration: none;
            transition: all 0.3s;
        }

        #sidebar ul li a:hover {
            color: #e53935;
            background: #f4f7f6;
        }

        #sidebar ul li.active>a {
            color: #e53935;
            background: #fce4ec;
            border-right: 4px solid #e53935;
        }

        #sidebar ul li a i {
            margin-right: 15px;
            min-width: 24px;
            text-align: center;
        }

        #content {
            width: 100%;
            padding: 20px 40px;
            /* margin-bottom: 60px; For mobile nav */
        }

        /* Mobile Bottom Nav */
        .bottom-navbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #fff;
            display: none;
            /* Hidden on desktop */
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .bottom-navbar .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #555;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .bottom-navbar .nav-item.active,
        .bottom-navbar .nav-item:hover {
            color: #e53935;
        }

        .bottom-navbar .nav-item i {
            font-size: 20px;
            margin-bottom: 2px;
        }

        .bottom-navbar .nav-item span {
            font-size: 10px;
        }

        @media (max-width: 768px) {
            #sidebar {
                display: none;
            }

            .bottom-navbar {
                display: flex;
            }

            #content {
                padding: 20px;
                margin-bottom: 70px;
            }
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f4f7f6;
            padding: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Desktop Sidebar -->
        @auth
            <nav id="sidebar">
                <div class="sidebar-header">
                    <i class="fas fa-graduation-cap fa-3x mb-2"></i>
                </div>
                <ul class="components">
                    <li
                        class="{{ request()->routeIs('student.exams.*') || request()->is('student/exams') ? 'active' : '' }}">
                        <a href="{{ route('student.exams.index') }}">
                            <i class="fas fa-file-alt"></i> Exams
                        </a>
                    </li>
                    <!-- Add more links here later -->
                    <li>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        @endauth

        <!-- Page Content -->
        <div id="content">
            <!-- Mobile Header (Simple) -->
            <div class="d-md-none d-flex justify-content-between align-items-center mb-4">
                <div class="text-primary">
                    <i class="fas fa-graduation-cap fa-2x"></i>
                </div>
                @auth
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="text-secondary">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                @endauth
            </div>

            @yield('content')
        </div>
    </div>

    <!-- Mobile Bottom Nav -->
    @auth
        <div class="bottom-navbar">
            <a href="{{ route('student.exams.index') }}"
                class="nav-item {{ request()->routeIs('student.exams.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i>
                <span>Exams</span>
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>