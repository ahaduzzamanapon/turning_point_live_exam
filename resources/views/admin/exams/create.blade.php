@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Create New Exam</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.exams.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Exam Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}"
                                required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                            <input type="number" class="form-control" id="duration_minutes" name="duration_minutes"
                                value="{{ old('duration_minutes', 60) }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="total_marks" class="form-label">Total Marks</label>
                            <input type="number" class="form-control" id="total_marks" name="total_marks"
                                value="{{ old('total_marks', 100) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time"
                                value="{{ old('start_time') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="datetime-local" class="form-control" id="end_time" name="end_time"
                                value="{{ old('end_time') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="DRAFT">Draft</option>
                                <option value="PUBLISHED">Published</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="negative_marking" name="negative_marking"
                                value="1" {{ old('negative_marking') ? 'checked' : '' }}>
                            <label class="form-check-label" for="negative_marking">
                                Enable Negative Marking
                            </label>
                        </div>
                    </div>

                    <hr>
                    <h5>Question Generation Rules</h5>
                    <p class="text-muted small">Specify how many questions of each difficulty to pull from the question bank
                        for each subject.</p>

                    <div id="rules-container">
                        <div class="row g-2 mb-2 rule-row">
                            <div class="col-md-4">
                                <select class="form-control" name="rules[0][subject_id]" required>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="rules[0][easy]" placeholder="Easy Qty"
                                    min="0" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="rules[0][medium]" placeholder="Med Qty"
                                    min="0" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="rules[0][hard]" placeholder="Hard Qty"
                                    min="0" required>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-danger w-100 remove-rule" type="button">Remove</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm mb-3" id="add-rule">Add Rule</button>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Create Exam</button>
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.getElementById('rules-container');
                const addButton = document.getElementById('add-rule');
                let ruleCount = 1;

                addButton.addEventListener('click', function () {
                    const index = ruleCount++;
                    const div = document.createElement('div');
                    div.className = 'row g-2 mb-2 rule-row';
                    div.innerHTML = `
                        <div class="col-md-4">
                            <select class="form-control" name="rules[${index}][subject_id]" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="rules[${index}][easy]" placeholder="Easy Qty" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="rules[${index}][medium]" placeholder="Med Qty" min="0" required>
                        </div>
                        <div class="col-md-2">
                                <input type="number" class="form-control" name="rules[${index}][hard]" placeholder="Hard Qty" min="0" required>
                        </div>
                            <div class="col-md-2">
                            <button class="btn btn-outline-danger w-100 remove-rule" type="button">Remove</button>
                        </div>
                    `;
                    container.appendChild(div);
                });

                container.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-rule')) {
                        if (container.children.length > 1) {
                            e.target.closest('.rule-row').remove();
                        } else {
                            alert('At least one rule is required.');
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection