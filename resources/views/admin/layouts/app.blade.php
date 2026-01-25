<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::get('browser_title', \App\Models\Setting::get('app_name', config('app.name'))) }}</title>
    @if(\App\Models\Setting::get('favicon'))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . \App\Models\Setting::get('favicon')) }}">
    @endif
    @include('admin.layouts.styles')
</head>
<body class="bg-light">
<div id="global-loader" class="position-fixed top-0 start-0 w-100 h-100 bg-white d-flex justify-content-center align-items-center" style="z-index: 9999;">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<script>
    function hideLoader() {
        const loader = document.getElementById('global-loader');
        if (loader) {
            loader.style.transition = 'opacity 0.5s ease';
            loader.style.opacity = '0';
            loader.style.pointerEvents = 'none';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }
    }

    window.addEventListener('load', hideLoader);
    
    // Fallback if load event doesn't fire or takes too long
    setTimeout(hideLoader, 3000); 
</script>
<div class="d-flex" id="wrapper">
    @include('admin.layouts.sidebar')
    
    <div id="page-content-wrapper" class="w-100">
        @include('admin.layouts.navbar')
        <main class="p-4">
            @yield('content')
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function (event) {
                event.preventDefault();
                document.getElementById('wrapper').classList.toggle('toggled');
            });
        }
    });
</script>
@stack('scripts')
</body>
</html>
