@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-5 animate-fade-in">
            <div>
                <h1 class="h3 fw-bold text-gray-800 mb-1">Student Dashboard</h1>
                <p class="mb-0 text-muted">Overview of your activity</p>
            </div>
            <div class="text-end">
                <p class="mb-0 small text-uppercase text-muted fw-bold ls-1">Welcome back</p>
                <h4 class="fw-bold text-gradient-primary">{{ auth()->user()->name }}</h4>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-5">
            <!-- Wallet Balance -->
            <div class="col-md-4 mb-3 animate-slide-up delay-100">
                <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white overflow-hidden">
                    <div class="card-body position-relative p-4">
                        <div class="d-flex justify-content-between align-items-start z-1 position-relative">
                            <div>
                                <h6 class="text-uppercase mb-1 text-white-50 small fw-bold ls-1">Wallet Balance</h6>
                                <h2 class="fw-bold mb-0">৳ {{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}</h2>
                            </div>
                            <div class="p-2 bg-white bg-opacity-25 rounded-circle">
                                <i class="fas fa-wallet fa-2x text-white stat-card-icon"></i>
                            </div>
                        </div>
                        <a href="{{ route('student.wallet.index') }}"
                            class="btn btn-sm btn-light text-primary mt-4 fw-bold rounded-pill px-4 shadow-sm">
                            <i class="fas fa-plus-circle me-1"></i> Top Up
                        </a>
                        <!-- Decorative Circle -->
                        <div class="position-absolute top-0 end-0 p-5 rounded-circle bg-white opacity-10" style="margin-top: -50px; margin-right: -50px;"></div>
                    </div>
                </div>
            </div>

            <!-- Active Exams -->
            <div class="col-md-4 mb-3 animate-slide-up delay-200">
                <div class="card border-0 shadow-sm h-100 bg-white">
                    <div class="card-body p-4 position-relative">
                         <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-uppercase mb-1 text-muted small fw-bold ls-1">Available Exams</h6>
                                <h2 class="fw-bold mb-0 text-dark">{{ $availableExamsCount }}</h2>
                            </div>
                            <div class="p-2 bg-primary bg-opacity-10 rounded-circle text-primary">
                                <i class="fas fa-file-alt fa-2x stat-card-icon"></i>
                            </div>
                        </div>
                         <a href="{{ route('student.exams.index') }}" class="btn btn-sm btn-outline-primary mt-4 rounded-pill px-4">
                            View Exams <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="col-md-4 mb-3 animate-slide-up delay-300">
                <div class="card border-0 shadow-sm h-100 bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-uppercase mb-1 text-muted small fw-bold ls-1">Upcoming Events</h6>
                                <h2 class="fw-bold mb-0 text-dark">{{ $upcomingEventsCount }}</h2>
                            </div>
                            <div class="p-2 bg-warning bg-opacity-10 rounded-circle text-warning">
                                <i class="fas fa-trophy fa-2x stat-card-icon"></i>
                            </div>
                        </div>
                        <a href="{{ route('student.events.index') }}"
                            class="btn btn-sm btn-outline-warning mt-4 text-dark rounded-pill px-4">
                            View Events <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row animate-slide-up delay-300">
            <!-- Recent Activity / History Placeholder -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-gray-800"><i class="fas fa-history me-2 text-primary"></i> Recent Results</h6>
                        <a href="{{ route('student.exams.index') }}" class="small text-decoration-none fw-bold">View All</a>
                    </div>
                    <div class="card-body p-0">
                        @if($recentResults->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3">Exam</th>
                                            <th class="py-3">Date</th>
                                            <th class="py-3">Score</th>
                                            <th class="py-3">Status</th>
                                            <th class="pe-4 py-3 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentResults as $result)
                                            <tr>
                                                <td class="ps-4 fw-bold">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-light rounded p-2 me-3 text-primary">
                                                            <i class="fas fa-file-contract"></i>
                                                        </div>
                                                        {{ $result->exam->title ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td class="text-muted small">{{ $result->created_at->format('d M, Y') }}<br>{{ $result->created_at->format('h:i A') }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 6px; width: 60px;">
                                                            <div class="progress-bar {{ $result->percentage >= 40 ? 'bg-success' : 'bg-danger' }}" 
                                                                 role="progressbar" 
                                                                 style="width: {{ $result->percentage }}%"></div>
                                                        </div>
                                                        <span class="small fw-bold">{{ $result->score }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($result->percentage >= 40)
                                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Passed</span>
                                                    @else
                                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Failed</span>
                                                    @endif
                                                </td>
                                                <td class="pe-4 text-end">
                                                    <a href="{{ route('student.exams.result', $result->id) }}"
                                                        class="btn btn-sm btn-light text-primary rounded-circle shadow-sm" data-bs-toggle="tooltip" title="View Result">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" alt="No Data" class="opacity-50 grayscale">
                                </div>
                                <h6 class="text-muted mb-3">No exams taken yet</h6>
                                <a href="{{ route('student.exams.index') }}" class="btn btn-primary rounded-pill px-4 shadow">
                                    <i class="fas fa-play me-2"></i> Start Your First Exam
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions / Notifications -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 mb-4 h-100">
                    <div class="card-header bg-transparent py-3">
                        <h6 class="m-0 fw-bold text-gray-800"><i class="fas fa-bolt me-2 text-warning"></i> Quick Actions</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('student.exams.index') }}"
                                class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center hover-bg-light transition">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                                    <i class="fas fa-play-circle fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Take a Live Exam</h6>
                                    <small class="text-muted">Test your knowledge now</small>
                                </div>
                                <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                            </a>
                            <a href="{{ route('student.wallet.index') }}"
                                class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center hover-bg-light transition">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                                    <i class="fas fa-wallet fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Recharge Wallet</h6>
                                    <small class="text-muted">Add funds securely</small>
                                </div>
                                <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                            </a>
                            <a href="{{ route('student.events.index') }}"
                                class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center hover-bg-light transition">
                                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                                    <i class="fas fa-trophy fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Join Contests</h6>
                                    <small class="text-muted">Win exciting prizes</small>
                                </div>
                                <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection