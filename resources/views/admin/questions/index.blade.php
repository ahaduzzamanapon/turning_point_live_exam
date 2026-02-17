@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import"></i> Import JSON
                </button>
                <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">Add New Question</a>
            </div>
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
                                <th>SL</th>
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
                                    <td>{{ $questions->firstItem() + $loop->index }}</td>
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

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.questions.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Questions from JSON</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="json_file" class="form-label">Select JSON File</label>
                            <input type="file" class="form-control" id="json_file" name="json_file" accept=".json" required>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                File must be a valid JSON with structure:
                                <pre>[
          {
            "category": "Subject Name",
            "question": "Question Text",
            "options": ["Opt1", "Opt2", ...],
            "answer": "Correct Opt"
          }
        ]</pre>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection