<nav class="navbar navbar-expand-lg border-bottom px-3 py-2" style="background-color: var(--navbar-bg); color: var(--navbar-text-color);">
    <div class="d-flex align-items-center">
        <button class="btn btn-primary me-3" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <h5 class="mb-0 d-md-none">{{ config('app.name', 'Admin Panel') }}</h5>
    </div>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mt-2 mt-lg-0 align-items-center">
            <!-- Notifications -->
            <li class="nav-item dropdown me-3">
                <a class="nav-link position-relative" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        3
                        <span class="visually-hidden">unread messages</span>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdownMenuLink">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    <li><a class="dropdown-item" href="#"><small class="text-muted">New user registered</small></a></li>
                    <li><a class="dropdown-item" href="#"><small class="text-muted">Order #1234 placed</small></a></li>
                    <li><a class="dropdown-item" href="#"><small class="text-muted">System update available</small></a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center small text-primary" href="#">View all</a></li>
                </ul>
            </li>

            <!-- Profile -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownProfile" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ auth()->guard('admin')->user()->name ?? 'Admin' }}&background=random" alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                    <span class="d-none d-lg-inline">{{ auth()->guard('admin')->user()->name ?? 'Admin' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdownProfile">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.theme.index') }}"><i class="bi bi-palette me-2"></i>Theme Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="{{ route('admin.logout') }}"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
