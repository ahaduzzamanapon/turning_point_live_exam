@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-gray-800 fw-bold"><i class="bi bi-gear me-2"></i>General Settings</h5>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- App Name --}}
                            <div class="mb-4">
                                <label for="app_name" class="form-label fw-bold">App Name</label>
                                <input type="text" class="form-control @error('app_name') is-invalid @enderror"
                                    id="app_name" name="app_name"
                                    value="{{ \App\Models\Setting::get('app_name', config('app.name')) }}" required>
                                @error('app_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">This name will appear in the sidebar.</div>
                            </div>

                            {{-- Browser Title --}}
                            <div class="mb-4">
                                <label for="browser_title" class="form-label fw-bold">Browser Title</label>
                                <input type="text" class="form-control @error('browser_title') is-invalid @enderror"
                                    id="browser_title" name="browser_title"
                                    value="{{ \App\Models\Setting::get('browser_title', \App\Models\Setting::get('app_name', config('app.name'))) }}">
                                @error('browser_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">This title will appear in the browser tab.</div>
                            </div>

                            {{-- App Logo --}}
                            <div class="mb-4">
                                <label for="app_logo" class="form-label fw-bold">App Logo</label>
                                <input type="file" class="form-control @error('app_logo') is-invalid @enderror"
                                    id="app_logo" name="app_logo" accept="image/*">
                                @error('app_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if(\App\Models\Setting::get('app_logo'))
                                    <div class="mt-3">
                                        <p class="mb-1 text-muted small">Current Logo:</p>
                                        <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" alt="App Logo"
                                            class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>

                            {{-- Favicon --}}
                            <div class="mb-4">
                                <label for="favicon" class="form-label fw-bold">Favicon</label>
                                <input type="file" class="form-control @error('favicon') is-invalid @enderror" id="favicon"
                                    name="favicon" accept="image/x-icon,image/png,image/jpeg,image/gif,image/svg+xml">
                                @error('favicon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Upload a .ico, .png, or .svg file (max 512KB).</div>

                                @if(\App\Models\Setting::get('favicon'))
                                    <div class="mt-3">
                                        <p class="mb-1 text-muted small">Current Favicon:</p>
                                        <img src="{{ asset('storage/' . \App\Models\Setting::get('favicon')) }}" alt="Favicon"
                                            class="img-thumbnail" style="max-height: 32px;">
                                    </div>
                                @endif
                            </div>

                            <hr class="my-5">

                            <h5 class="mb-4 text-gray-800 fw-bold"><i class="bi bi-envelope me-2"></i>Mail Configuration
                                (SMTP)</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Mailer</label>
                                    <input type="text" class="form-control" name="mail_mailer"
                                        value="{{ \App\Models\Setting::get('mail_mailer', 'smtp') }}" placeholder="smtp">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Host</label>
                                    <input type="text" class="form-control" name="mail_host"
                                        value="{{ \App\Models\Setting::get('mail_host') }}" placeholder="smtp.gmail.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Port</label>
                                    <input type="text" class="form-control" name="mail_port"
                                        value="{{ \App\Models\Setting::get('mail_port') }}" placeholder="587">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Encryption</label>
                                    <input type="text" class="form-control" name="mail_encryption"
                                        value="{{ \App\Models\Setting::get('mail_encryption') }}" placeholder="tls">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Username</label>
                                    <input type="text" class="form-control" name="mail_username"
                                        value="{{ \App\Models\Setting::get('mail_username') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Password</label>
                                    <input type="password" class="form-control" name="mail_password"
                                        value="{{ \App\Models\Setting::get('mail_password') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">From Address</label>
                                    <input type="email" class="form-control" name="mail_from_address"
                                        value="{{ \App\Models\Setting::get('mail_from_address') }}"
                                        placeholder="admin@example.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">From Name</label>
                                    <input type="text" class="form-control" name="mail_from_name"
                                        value="{{ \App\Models\Setting::get('mail_from_name') }}" placeholder="App Name">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-2"></i>Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection