@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Manage Question Paper</h1>
                <p class="text-muted small mb-0">Exam: <strong>{{ $exam->title }}</strong></p>
            </div>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Exams
            </a>
        </div>

        <div class="row">
            <!-- Current Paper Section -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold">Current Questions ({{ $exam->questions->count() }})</h6>
                        <form action="{{ route('admin.exams.paper.generate', $exam->id) }}" method="POST"
                            onsubmit="return confirm('This will replace ALL existing questions with a new set based on your rules. Continue?');">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm fw-bold">
                                <i class="bi bi-magic me-1"></i> Auto-Generate from Rules
                            </button>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Question</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($exam->questions as $index => $question)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ Str::limit($question->question_text, 60) }}</td>
                                            <td><span class="badge bg-info text-dark">{{ $question->subject->name }}</span></td>
                                            <td><small>{{ $question->question_type }}</small></td>
                                            <td class="text-end">
                                                <form
                                                    action="{{ route('admin.exams.paper.destroy', [$exam->id, $question->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm rounded-circle"
                                                        style="width: 32px; height: 32px; padding: 0;">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="bi bi-file-earmark-x display-4"></i>
                                                <p class="mt-2">No questions assigned to this paper yet.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Manual Questions Section -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 fw-bold text-primary">Add Questions Manually</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <select id="filter-subject" class="form-control mb-2">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            <select id="filter-difficulty" class="form-control mb-2">
                                <option value="">All Diffculties</option>
                                <option value="EASY">Easy</option>
                                <option value="MEDIUM">Medium</option>
                                <option value="HARD">Hard</option>
                            </select>
                            <button class="btn btn-secondary w-100" id="btn-search">Search Questions</button>
                        </div>

                        <form action="{{ route('admin.exams.paper.store', $exam->id) }}" method="POST">
                            @csrf
                            <div id="search-results" class="border rounded p-2 mb-3 bg-light"
                                style="max-height: 400px; overflow-y: auto;">
                                <p class="text-center text-muted small mt-3">Search to see available questions</p>
                            </div>
                            <button type="submit" class="btn btn-success w-100" id="btn-add-selected" disabled>
                                <i class="bi bi-plus-circle me-1"></i> Add Selected
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('btn-search').addEventListener('click', function () {
                const subject = document.getElementById('filter-subject').value;
                const difficulty = document.getElementById('filter-difficulty').value;
                const container = document.getElementById('search-results');

                container.innerHTML = '<p class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Loading...</p>';

                fetch(`{{ route('admin.exams.paper.search') }}?exam_id={{ $exam->id }}&subject_id=${subject}&difficulty=${difficulty}`)
                    .then(res => res.json())
                    .then(data => {
                        container.innerHTML = '';
                        if (data.length === 0) {
                            container.innerHTML = '<p class="text-center text-muted small mt-3">No matching questions found.</p>';
                        } else {
                            data.forEach(q => {
                                const div = document.createElement('div');
                                div.className = 'form-check border-bottom py-2';
                                div.innerHTML = `
                                    <input class="form-check-input question-checkbox" type="checkbox" name="question_ids[]" value="${q.id}" id="q-${q.id}">
                                    <label class="form-check-label w-100 small" for="q-${q.id}" style="cursor:pointer;">
                                        <div class="fw-bold text-truncate">${q.question_text}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">${q.subject.name} | ${q.difficulty}</div>
                                    </label>
                                `;
                                container.appendChild(div);
                            });

                            // Re-attach listeners for checkboxes to enable button
                            document.querySelectorAll('.question-checkbox').forEach(cb => {
                                cb.addEventListener('change', updateAddButton);
                            });
                        }
                        updateAddButton();
                    });
            });

            function updateAddButton() {
                const count = document.querySelectorAll('.question-checkbox:checked').length;
                const btn = document.getElementById('btn-add-selected');
                btn.disabled = count === 0;
                btn.innerHTML = `<i class="bi bi-plus-circle me-1"></i> Add Selected (${count})`;
            }
        </script>
    @endpush
@endsection