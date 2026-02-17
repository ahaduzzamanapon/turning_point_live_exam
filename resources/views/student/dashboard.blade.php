@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Student Dashboard</h1>
            <div class="text-end">
                <p class="mb-0 text-muted">Welcome back,</p>
                <h5 class="fw-bold text-primary">{{ auth()->user()->name }}</h5>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <!-- Wallet Balance -->
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white"
                    style="background: linear-gradient(45deg, #4e73df, #224abe);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-2 text-white-50">Wallet Balance</h6>
                                <h3 class="fw-bold mb-0">৳ {{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}</h3>
                            </div>
                            <i class="fas fa-wallet fa-2x text-white-50"></i>
                        </div>
                        <a href="{{ route('student.wallet.index') }}"
                            class="btn btn-sm btn-light text-primary mt-3 fw-bold">Top Up</a>
                    </div>
                </div>
            </div>

            <!-- Active Exams -->
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100 bg-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-2 text-muted">Available Exams</h6>
                                <h3 class="fw-bold mb-0 text-dark">{{ $availableExamsCount }}</h3>
                            </div>
                            <i class="fas fa-file-alt fa-2x text-primary"></i>
                        </div>
                        <a href="{{ route('student.exams.index') }}" class="btn btn-sm btn-outline-primary mt-3">View
                            Exams</a>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100 bg-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-2 text-muted">Upcoming Events</h6>
                                <h3 class="fw-bold mb-0 text-dark">{{ $upcomingEventsCount }}</h3>
                            </div>
                            <i class="fas fa-calendar-alt fa-2x text-warning"></i>
                        </div>
                        <a href="{{ route('student.events.index') }}"
                            class="btn btn-sm btn-outline-warning mt-3 text-dark">View Events</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Activity / History Placeholder -->
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Results</h6>
                    </div>
                    <div class="card-body">
                        @if($recentResults->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Exam</th>
                                            <th>Date</th>
                                            <th>Score</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentResults as $result)
                                            <tr>
                                                <td>{{ $result->exam->title ?? 'N/A' }}</td>
                                                <td>{{ $result->created_at->format('d M, Y') }}</td>
                                                <td class="fw-bold">{{ $result->score }} / {{ $result->total_marks }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $result->percentage >= 40 ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $result->percentage >= 40 ? 'Passed' : 'Failed' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('student.exams.result', $result->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                <p>No exams taken yet.</p>
                                <a href="{{ route('student.exams.index') }}" class="btn btn-primary">Start an Exam</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions / Notifications -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('student.exams.index') }}"
                                class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-play-circle text-success me-3"></i> Take a Live Exam
                            </a>
                            <a href="{{ route('student.wallet.index') }}"
                                class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-plus-circle text-primary me-3"></i> Add Money to Wallet
                            </a>
                            <a href="{{ route('student.events.index') }}"
                                class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-trophy text-warning me-3"></i> Join a Contest
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection