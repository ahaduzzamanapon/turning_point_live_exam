@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Edit Preset: {{ $preset->name }}</h1>
        <a href="{{ route('admin.theme.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Settings
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.theme.preset.update', $preset->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            {{-- General Colors --}}
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">General Colors</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Primary Color</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['primary_color'] ?? '#4f46e5' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="primary_color" value="{{ $settings['primary_color'] ?? '#4f46e5' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Danger Color</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['danger_color'] ?? '#dc3545' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="danger_color" value="{{ $settings['danger_color'] ?? '#dc3545' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Body Text Color</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['body_text_color'] ?? '#212529' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="body_text_color" value="{{ $settings['body_text_color'] ?? '#212529' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Body Background Color</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['body_bg_color'] ?? '#f8f9fa' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="body_bg_color" value="{{ $settings['body_bg_color'] ?? '#f8f9fa' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Navbar Settings --}}
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Navbar Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Navbar Background</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['navbar_bg'] ?? '#ffffff' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="navbar_bg" value="{{ $settings['navbar_bg'] ?? '#ffffff' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Navbar Text Color</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['navbar_text_color'] ?? '#212529' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="navbar_text_color" value="{{ $settings['navbar_text_color'] ?? '#212529' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Colors --}}
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Sidebar Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Sidebar Background</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['sidebar_bg'] ?? '#111827' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="sidebar_bg" value="{{ $settings['sidebar_bg'] ?? '#111827' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sidebar Text Color</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['sidebar_text_color'] ?? '#9ca3af' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="sidebar_text_color" value="{{ $settings['sidebar_text_color'] ?? '#9ca3af' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Active Menu Background</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['active_menu_bg'] ?? '#ffffff' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="active_menu_bg" value="{{ $settings['active_menu_bg'] ?? 'rgba(255,255,255,0.1)' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hover Menu Background</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['hover_menu_bg'] ?? '#ffffff' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="hover_menu_bg" value="{{ $settings['hover_menu_bg'] ?? 'rgba(255,255,255,0.1)' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Active/Hover Text Color</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['active_menu_text_color'] ?? '#ffffff' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="active_menu_text_color" value="{{ $settings['active_menu_text_color'] ?? '#ffffff' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Component Colors --}}
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Component Colors</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Card Background</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['card_bg'] ?? '#ffffff' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="card_bg" value="{{ $settings['card_bg'] ?? '#ffffff' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Table Header Background</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['table_header_bg'] ?? '#f8f9fa' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="table_header_bg" value="{{ $settings['table_header_bg'] ?? '#f8f9fa' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Table Header Text Color</label>
                            <div class="input-group">
                                <span class="input-group-text p-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" value="{{ $settings['table_header_text_color'] ?? '#212529' }}" oninput="this.parentElement.nextElementSibling.value = this.value">
                                </span>
                                <input type="text" class="form-control" name="table_header_text_color" value="{{ $settings['table_header_text_color'] ?? '#212529' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Typography --}}
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Typography</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Table Font Size (px)</label>
                            <input type="number" class="form-control" name="table_font_size" value="{{ $settings['table_font_size'] ?? '16' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i>Save Changes</button>
        </div>
    </form>
</div>
@endsection
