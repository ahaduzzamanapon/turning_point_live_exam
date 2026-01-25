@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Exams</h1>
            <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">Create New Exam</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Duration (min)</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                                <tr>
                                    <td>{{ $exam->id }}</td>
                                    <td>{{ $exam->title }}</td>
                                    <td>{{ $exam->duration_minutes }}</td>
                                    <td>
                                        Starts: {{ $exam->start_time->format('M d, H:i') }} <br>
                                        Ends: {{ $exam->end_time->format('M d, H:i') }}
                                    </td>
                                    <td>
                                        @if($exam->status == 'DRAFT')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($exam->status == 'PUBLISHED')
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-info">Completed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.exams.edit', $exam->id) }}"
                                            class="btn btn-primary btn-sm rounded-circle me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.exams.paper.index', $exam->id) }}"
                                            class="btn btn-warning btn-sm rounded-circle me-1" title="Manage Question Paper">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                        <button type="button" class="btn btn-info btn-sm rounded-circle me-1" title="Assign"
                                            onclick="openAssignModal({{ $exam->id }}, '{{ $exam->title }}', '{{ $exam->access_mode }}')">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <form action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-circle"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No exams found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $exams->links() }}
            </div>
        </div>
    </div>
@endsection