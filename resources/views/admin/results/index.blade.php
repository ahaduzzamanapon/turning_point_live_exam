@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Exam Results</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Exam Title</th>
                                <th>Score</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attempts as $attempt)
                                <tr>
                                    <td>{{ $attempt->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $attempt->user->name }}</div>
                                        <small class="text-muted">{{ $attempt->user->email }}</small>
                                    </td>
                                    <td>{{ $attempt->exam->title }}</td>
                                    <td>
                                        <span class="badge bg-danger fs-6">{{ $attempt->score }} /
                                            {{ $attempt->exam->total_marks }}</span>
                                    </td>
                                    <td>{{ $attempt->updated_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('admin.results.show', $attempt->id) }}"
                                            class="btn btn-sm btn-outline-danger shadow-sm">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No completed exams found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $attempts->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection