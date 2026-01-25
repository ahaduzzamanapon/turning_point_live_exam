@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Theme Settings</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.theme.update') }}" method="POST">
        @csrf
        
        {{-- Presets Section --}}
        <div class="card shadow mb-4">
                    <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Theme Presets</h6>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#savePresetModal">
                    <i class="bi bi-save me-1"></i> Save Current as Preset
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($presets as $preset)
                        @php
                            $isActive = isset($activePresetId) && (int)$activePresetId === (int)$preset->id;
                        @endphp
                        <div class="col-md-4 mb-3">
                            <!-- Debug: Active={{ $activePresetId }} Preset={{ $preset->id }} -->
                            <div class="card h-100 {{ $isActive ? 'border-success shadow' : ($preset->is_default ? 'border-primary' : 'border-secondary') }}">
                                <div class="card-header {{ $isActive ? 'bg-success text-white' : '' }}">
                                    <h5 class="card-title mb-0">
                                        {{ $preset->name }}
                                        @if($isActive)
                                            <i class="bi bi-check-circle-fill ms-2"></i>
                                        @endif
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($preset->is_default)
                                        <span class="badge {{ $isActive ? 'bg-light text-success' : 'bg-primary' }} mb-2">Default</span>
                                    @else
                                        <span class="badge {{ $isActive ? 'bg-light text-success' : 'bg-secondary' }} mb-2">Custom</span>
                                    @endif
                                    <div class="d-grid gap-2">
                                        <form action="{{ route('admin.theme.apply') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="preset_id" value="{{ $preset->id }}">
                                            <button type="submit" class="btn {{ $isActive ? 'btn-light text-success fw-bold' : 'btn-outline-primary' }} btn-sm w-100" {{ $isActive ? 'disabled' : '' }}>
                                                {{ $isActive ? 'Active' : 'Apply' }}
                                            </button>
                                        </form>
                                        @if(!$preset->is_default)
                                            <div class="btn-group w-100">
                                                <a href="{{ route('admin.theme.preset.edit', $preset->id) }}" class="btn btn-outline-warning btn-sm w-50">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.theme.preset.destroy', $preset->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="w-50">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row">
            {{-- General Colors --}}
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
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
                    <div class="card-header py-3">
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
                    <div class="card-header py-3">
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
                    <div class="card-header py-3">
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
                    <div class="card-header py-3">
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
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i>Save Settings</button>
        </div>
    </form>
</div>

<!-- Save Preset Modal -->
<div class="modal fade" id="savePresetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.theme.preset.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Save Current Theme as Preset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="preset_name" class="form-label">Preset Name</label>
                        <input type="text" class="form-control" id="preset_name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Preset</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
