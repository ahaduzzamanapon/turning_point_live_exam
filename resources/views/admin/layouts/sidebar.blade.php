<div class="sidebar d-flex flex-column">
    <div class="sidebar-header d-flex align-items-center justify-content-center border-bottom border-secondary border-opacity-25"
        style="padding: 11px;">
        @if(\App\Models\Setting::get('app_logo'))
            <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" alt="Logo"
                style="max-height: 50px; max-width: 100%;">
        @else
            <i class="bi bi-hexagon-fill fs-1 text-primary"></i>
        @endif
    </div>

    <div class="sidebar-content flex-grow-1 overflow-auto py-3">
        <div class="px-3 mb-2 text-uppercase text-xs fw-bold opacity-50"
            style="font-size: 0.75rem; letter-spacing: 0.05em;">Main Menu</div>
        <ul class="nav flex-column">
            @include('admin.layouts.menu', ['menus' => $menus])
        </ul>
    </div>

    <div class="sidebar-footer p-3 border-top border-secondary border-opacity-25">
        <a href="{{ route('admin.logout') }}"
            class="nav-link text-danger d-flex align-items-center justify-content-center p-2 rounded hover-bg-danger-subtle">
            <i class="bi bi-box-arrow-right me-2"></i>
            <span class="fw-medium">Logout</span>
        </a>
    </div>
</div>