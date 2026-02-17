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
        /* Modern Styling & Animations */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            overflow-x: hidden;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes pulse-soft {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        .animate-slide-up {
            animation: slideUp 0.6s ease-out forwards;
            opacity: 0;
            /* Star hidden */
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        /* Glassmorphism & Cards */
        .card {
            border: none;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            /* Deeper shadow */
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            /* Bouncy transition */
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }

        /* Sidebar Styling */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #ffffff;
            color: #555;
            transition: all 0.3s;
            box-shadow: 5px 0 25px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar-header {
            padding: 30px 20px;
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            /* Gradient Red */
            color: #fff;
            text-align: center;
            border-bottom-right-radius: 50px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(255, 75, 43, 0.3);
        }

        .sidebar-header i {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        #sidebar ul.components {
            padding: 0 15px;
            list-style: none;
            padding-left: 0;
        }

        #sidebar ul li {
            margin-bottom: 8px;
            list-style: none;
        }

        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            color: #666;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        #sidebar ul li.active>a {
            color: #fff;
            background: linear-gradient(90deg, #ff4b2b, #ff416c);
            box-shadow: 0 4px 12px rgba(255, 75, 43, 0.3);
            border-right: none;
            /* Removed sidebar border */
        }

        #sidebar ul li a:hover:not(.active) {
            color: #ff4b2b;
            background: rgba(255, 75, 43, 0.05);
            transform: translateX(5px);
        }

        #sidebar ul li a i {
            margin-right: 15px;
            min-width: 24px;
            text-align: center;
            font-size: 18px;
        }

        #content {
            flex: 1;
            padding: 30px;
            width: 100%;
            /* Ensure it takes full width */
            max-width: 100%;
            transition: all 0.3s;
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
                padding-bottom: 100px;
                /* Space for bottom nav */
                width: 100%;
            }
        }

        /* Layout */
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
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
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
            border-top: 1px solid rgba(0, 0, 0, 0.05);
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

        /* Gradients */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #2af598 0%, #009efd 100%) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #fce38a 0%, #f38181 100%) !important;
        }

        .stat-card-icon {
            opacity: 0.8;
            transform: scale(0.8);
            transition: all 0.3s;
        }

        .card:hover .stat-card-icon {
            transform: scale(1.1) rotate(10deg);
            opacity: 1;
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
                    <li class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('student.dashboard') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li
                        class="{{ request()->routeIs('student.exams.*') || request()->is('student/exams') ? 'active' : '' }}">
                        <a href="{{ route('student.exams.index') }}">
                            <i class="fas fa-file-alt"></i> Exams
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('student.wallet.index') ? 'active' : '' }}">
                        <a href="{{ route('student.wallet.index') }}">
                            <i class="fas fa-wallet"></i> Wallet
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('student.events.*') ? 'active' : '' }}">
                        <a href="{{ route('student.events.index') }}">
                            <i class="fas fa-calendar-alt"></i> Events
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
            <a href="{{ route('student.dashboard') }}"
                class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('student.exams.index') }}"
                class="nav-item {{ request()->routeIs('student.exams.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i>
                <span>Exams</span>
            </a>
            <a href="{{ route('student.events.index') }}"
                class="nav-item {{ request()->routeIs('student.events.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span>Events</span>
            </a>
            <a href="{{ route('student.wallet.index') }}"
                class="nav-item {{ request()->routeIs('student.wallet.index') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i>
                <span>Wallet</span>
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