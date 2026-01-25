@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
        </div>

        <!-- Content Row -->
        <div class="row">

            <!-- Total Exams Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-5 border-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                    Total Exams</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['total_exams'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Exams Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-5 border-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                    Active Exams</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['active_exams'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Students Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-5 border-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">Total Students
                                </div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['total_students'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Attempts Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-5 border-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                    Total Attempts</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['total_attempts'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <!-- Recent Activity -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-primary">Recent Exam Attempts</h6>
                        <a href="{{ route('admin.results.index') }}" class="btn btn-sm btn-outline-primary shadow-sm">View
                            All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Exam</th>
                                        <th>Score</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recent_attempts as $attempt)
                                        <tr>
                                            <td>{{ $attempt->user->name ?? 'Unknown' }}</td>
                                            <td>{{ $attempt->exam->title ?? 'N/A' }}</td>
                                            <td>
                                                @if($attempt->status == 'COMPLETED')
                                                    <span class="badge bg-success">{{ $attempt->score }} /
                                                        {{ $attempt->exam->total_marks ?? 0 }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $attempt->updated_at->diffForHumans() }}</td>
                                            <td>
                                                @if($attempt->status == 'COMPLETED')
                                                    <span class="badge bg-success">Completed</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">In Progress</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No recent activity.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection