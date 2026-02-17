@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Manage Questions for: {{ $event->title }}</h1>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary shadow-sm">Back of Events</a>
        </div>

        <div class="row">
            <!-- List of Added Questions -->
            <div class="col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Paper Questions ({{ $event->questions->count() }})
                        </h6>
                        <span class="badge bg-info text-white">Total Marks: {{ $event->total_marks }}</span>
                    </div>
                    <div class="card-body">
                        @if($event->questions->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-file-alt fa-3x mb-3"></i>
                                <p>No questions added yet.</p>
                            </div>
                        @else
                            <div class="d-flex justify-content-end mb-2">
                                @if($event->status == 'UPCOMING')
                                    <button class="btn btn-primary btn-sm" id="saveOrderBtn" style="display: none;" onclick="saveOrder()">
                                        <i class="fas fa-save"></i> Save Order
                                    </button>
                                @endif
                            </div>
                            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                <table class="table table-bordered table-hover">
                                    <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>Question</th>
                                            <th style="width: 80px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="questions-list">
                                        @foreach($event->questions as $index => $question)
                                            <tr data-id="{{ $question->id }}">
                                                <td class="drag-handle" style="cursor: move;">{{ $index + 1 }} <i class="fas fa-bars text-muted ms-1"></i></td>
                                                <td>
                                                    <div class="fw-bold">
                                                        {!! Str::limit(strip_tags($question->question_text), 100) !!}</div>
                                                    <small class="text-muted">
                                                        {{ $question->subject->name ?? 'N/A' }} | {{ $question->difficulty }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if($event->status == 'UPCOMING')
                                                    <form
                                                        action="{{ route('admin.events.paper.destroy', ['event' => $event->id, 'question' => $question->id]) }}"
                                                        method="POST" onsubmit="return confirm('Remove this question?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm rounded-circle">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    @else
                                                        <span class="text-muted"><i class="fas fa-lock"></i></span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Add / Generate Questions -->
            <div class="col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Add / Generate Questions</h6>
                    </div>
                    <div class="card-body">
                        @if($event->status == 'UPCOMING')
                            <!-- Auto Generate -->
                            <div class="mb-4 text-center">
                                <form action="{{ route('admin.events.paper.auto', $event->id) }}" method="POST" onsubmit="return confirm('Check: This will REMOVE all existing questions and regenerate based on total marks. Continue?');">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100 mb-2">
                                        <i class="fas fa-magic"></i> Auto Generate Paper
                                    </button>
                                    <small class="text-muted">Generates {{ $event->total_marks }} questions randomly.</small>
                                </form>
                            </div>
                            <hr>

                            <!-- Manual Add -->
                            <div class="mb-3">
                                <label>Filter by Subject</label>
                                <select id="subjectFilter" class="form-control">
                                    <option value="">All Subjects</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Difficulty</label>
                                <select id="difficultyFilter" class="form-control">
                                    <option value="">Any</option>
                                    <option value="EASY">Easy</option>
                                    <option value="MEDIUM">Medium</option>
                                    <option value="HARD">Hard</option>
                                </select>
                            </div>
                            
                            <button class="btn btn-primary w-100 mb-3" onclick="searchQuestions()">
                                <i class="fas fa-search me-2"></i> Search Questions
                            </button>

                            <hr>

                            <form action="{{ route('admin.events.paper.store', $event->id) }}" method="POST">
                                @csrf
                                <div id="searchResults" class="mb-3" style="max-height: 400px; overflow-y: auto;">
                                    <div class="text-muted text-center small">Search to see available questions.</div>
                                </div>
                                <button type="submit" class="btn btn-success w-100" id="addBtn" disabled>
                                    Add Selected to Paper
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-lock fa-2x mb-3"></i><br>
                                Paper is <strong>LOCKED</strong>.<br>
                                Event status is {{ $event->status }}.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SortableJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

    <script>
        // Sortable Implementation
        const list = document.getElementById('questions-list');
        if (list) {
            new Sortable(list, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: function () {
                    // Show save button when order changes
                    document.getElementById('saveOrderBtn').style.display = 'inline-block';
                }
            });
        }

        function saveOrder() {
            let order = [];
            document.querySelectorAll('#questions-list tr').forEach((row) => {
                order.push(row.getAttribute('data-id'));
            });

            fetch('{{ route("admin.events.paper.reorder", $event->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Order saved successfully!');
                    document.getElementById('saveOrderBtn').style.display = 'none';
                    // Optional: reload to refresh indices
                    location.reload(); 
                } else {
                    alert('Failed to save order.');
                }
            });
        }

        function searchQuestions() {
            let subject_id = document.getElementById('subjectFilter').value;
            let difficulty = document.getElementById('difficultyFilter').value;
            let resultsDiv = document.getElementById('searchResults');

            resultsDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';

            fetch(`{{ route('admin.events.paper.search') }}?event_id={{ $event->id }}&subject_id=${subject_id}&difficulty=${difficulty}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        resultsDiv.innerHTML = '<div class="text-center text-muted">No questions found.</div>';
                        return;
                    }

                    let html = '<ul class="list-group">';
                    data.forEach(q => {
                        html += `
                        <li class="list-group-item">
                            <div class="form-check">
                                <input class="form-check-input question-checkbox" type="checkbox" name="question_ids[]" value="${q.id}" id="q${q.id}" onchange="toggleAddBtn()">
                                <label class="form-check-label" for="q${q.id}">
                                    ${q.question_text.replace(/<[^>]*>?/gm, '').substring(0, 60)}...
                                    <br>
                                    <span class="badge bg-secondary" style="font-size: 0.6rem;">${q.difficulty}</span>
                                </label>
                            </div>
                        </li>
                    `;
                    });
                    html += '</ul>';
                    resultsDiv.innerHTML = html;
                    toggleAddBtn();
                });
        }

        function toggleAddBtn() {
            let checked = document.querySelectorAll('.question-checkbox:checked').length;
            document.getElementById('addBtn').disabled = checked === 0;
            document.getElementById('addBtn').innerHTML = `Add Selected (${checked})`;
        }
    </script>
@endsection