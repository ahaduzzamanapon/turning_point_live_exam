<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<style>
    @php
        $themeSettings = \App\Models\ThemeSetting::pluck('value', 'key')->toArray();
    @endphp

    :root {
        --primary-color: {{ $themeSettings['primary_color'] ?? '#4f46e5' }};
        --danger-color: {{ $themeSettings['danger_color'] ?? '#dc3545' }};
        --body-text-color: {{ $themeSettings['body_text_color'] ?? '#212529' }};
        --body-bg-color: {{ $themeSettings['body_bg_color'] ?? '#f8f9fa' }};
        --sidebar-bg: {{ $themeSettings['sidebar_bg'] ?? '#111827' }};
        --sidebar-text-color: {{ $themeSettings['sidebar_text_color'] ?? '#9ca3af' }};
        --active-menu-bg: {{ $themeSettings['active_menu_bg'] ?? 'rgba(255,255,255,0.1)' }};
        --hover-menu-bg: {{ $themeSettings['hover_menu_bg'] ?? 'rgba(255,255,255,0.1)' }};
        --active-menu-text-color: {{ $themeSettings['active_menu_text_color'] ?? '#ffffff' }};
        --card-bg: {{ $themeSettings['card_bg'] ?? '#ffffff' }};
        --table-header-bg: {{ $themeSettings['table_header_bg'] ?? '#f8f9fa' }};
        --table-header-text-color: {{ $themeSettings['table_header_text_color'] ?? '#212529' }};
        --table-font-size: {{ $themeSettings['table_font_size'] ?? '16' }}px;
        --navbar-bg: {{ $themeSettings['navbar_bg'] ?? '#ffffff' }};
        --navbar-text-color: {{ $themeSettings['navbar_text_color'] ?? '#212529' }};
    }

    /* --- Global & Typography --- */
    body {
        font-family: 'Inter', sans-serif;
        color: var(--body-text-color);
        background-color: var(--body-bg-color);
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    h1, h2, h3, h4, h5, h6 {
        font-weight: 600;
        letter-spacing: -0.025em;
    }

    /* --- Buttons --- */
    .btn {
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        letter-spacing: 0.01em;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2), 0 2px 4px -1px rgba(79, 70, 229, 0.1);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3), 0 4px 6px -2px rgba(79, 70, 229, 0.15);
    }

    .btn-danger {
        background-color: var(--danger-color);
        border-color: var(--danger-color);
        box-shadow: 0 4px 6px -1px rgba(220, 53, 69, 0.2);
    }

    /* --- Cards --- */
    .card {
        background-color: var(--card-bg);
        border: none;
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        margin-bottom: 1.5rem;
    }

    .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.25rem 1.5rem;
        border-top-left-radius: 1rem !important;
        border-top-right-radius: 1rem !important;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* --- Forms & Inputs --- */
    .form-control, .form-select {
        border-radius: 0.5rem;
        padding: 0.625rem 1rem;
        border: 1px solid #e2e8f0;
        font-size: 0.95rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    .form-label {
        font-weight: 500;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        color: #4b5563;
    }

    /* --- Tables --- */
    .table-responsive {
        border-radius: 0 0 1rem 1rem;
    }

    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background-color: var(--table-header-bg);
        color: var(--table-header-text-color);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        font-size: 0.95rem;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        transition: background-color 0.15s ease;
    }

    .table-hover tbody tr:hover td {
        background-color: #f9fafb;
    }

    /* --- Layout & Sidebar --- */
    #wrapper {
        display: flex;
        width: 100%;
        align-items: stretch;
        height: 100vh;
        overflow: hidden;
    }

    .sidebar {
        min-width: 280px;
        max-width: 280px;
        background-color: var(--sidebar-bg);
        color: var(--sidebar-text-color);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 4px 0 24px rgba(0,0,0,0.05);
        z-index: 1000;
        display: flex;
        flex-direction: column;
    }

    @media (max-width: 768px) {
        .sidebar {
            margin-left: -280px;
        }
        #wrapper.toggled .sidebar {
            margin-left: 0;
        }
    }

    @media (min-width: 769px) {
        #wrapper.toggled .sidebar {
            margin-left: -280px;
        }
    }

    .sidebar .nav-link {
        color: var(--sidebar-text-color);
        padding: 0.5rem 0.75rem;
        margin: 0.15rem 0.5rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .sidebar .nav-link i {
        margin-right: 0.75rem;
        font-size: 1.2rem;
        opacity: 0.75;
        transition: opacity 0.2s;
    }

    .sidebar .nav-link:hover {
        color: var(--active-menu-text-color);
        background-color: var(--hover-menu-bg);
        transform: translateX(4px);
    }
    
    .sidebar .nav-link:hover i {
        opacity: 1;
    }

    .sidebar .nav-link.active {
        color: var(--active-menu-text-color);
        background-color: var(--active-menu-bg);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* --- Navbar --- */
    .navbar {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        backdrop-filter: blur(12px);
        background-color: rgba(255, 255, 255, 0.9);
        border-bottom: 1px solid rgba(0,0,0,0.03);
        padding: 0.75rem 1.5rem;
    }

    /* --- Custom Scrollbar --- */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* --- Animations --- */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .container-fluid {
        animation: fadeIn 0.4s ease-out;
    }

    /* --- Fixed Layout Structure --- */
    #page-content-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background-color: #f3f4f6;
    }

    main {
        flex: 1;
        overflow: hidden;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .container-fluid {
        height: 100%;
        display: flex;
        flex-direction: column;
        padding-bottom: 1rem;
        overflow-y: auto;
        padding-right: 0.5rem; /* Prevent scrollbar overlap */
    }

    /* Full Height Cards for Tables */
    .container-fluid > .card.card-fixed {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        margin-bottom: 0 !important;
    }

    .container-fluid > .card.card-fixed > .card-body {
        flex: 1;
        overflow: hidden; /* Internal scroll handles table */
        padding: 0;
        display: flex;
        flex-direction: column;
    }

    .table-responsive {
        flex: 1;
        overflow-y: auto;
    }

    .table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    /* Dashboard Rows */
    .container-fluid > .row {
        flex-shrink: 0; /* Don't shrink rows */
    }

    /* Search Loader Overlay */
    .search-loader-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
        backdrop-filter: blur(2px);
    }
</style>
