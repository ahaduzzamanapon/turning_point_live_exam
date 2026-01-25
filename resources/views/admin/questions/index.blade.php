@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Questions</h1>
            <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">Add New Question</a>
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
                                <th>Question</th>
                                <th>Subject / Topic</th>
                                <th>Type</th>
                                <th>Difficulty</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $question)
                                <tr>
                                    <td>{{ $question->id }}</td>
                                    <td>{{ Str::limit($question->question_text, 50) }}</td>
                                    <td>
                                        {{ $question->subject->name }} <br>
                                        <small class="text-muted">{{ $question->topic->name ?? 'No Topic' }}</small>
                                    </td>
                                    <td>{{ $question->question_type }}</td>
                                    <td>
                                        @if($question->difficulty == 'EASY')
                                            <span class="badge bg-success">Easy</span>
                                        @elseif($question->difficulty == 'MEDIUM')
                                            <span class="badge bg-warning text-dark">Medium</span>
                                        @else
                                            <span class="badge bg-danger">Hard</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.questions.edit', $question->id) }}"
                                            class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No questions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $questions->links() }}
            </div>
        </div>
    </div>
@endsection