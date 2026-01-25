@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Add New Question</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.questions.store') }}" method="POST" id="questionForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="subject_id" class="form-label">Subject</label>
                            <select class="form-control" id="subject_id" name="subject_id" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="topic_id" class="form-label">Topic</label>
                            <select class="form-control" id="topic_id" name="topic_id">
                                <option value="">Select Topic</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}">{{ $topic->name }} ({{ $topic->subject->name }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="question_text" class="form-label">Question Text</label>
                        <textarea class="form-control" id="question_text" name="question_text" rows="3"
                            required>{{ old('question_text') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="question_type" class="form-label">Type</label>
                            <select class="form-control" id="question_type" name="question_type" required>
                                <option value="SINGLE">Single Choice</option>
                                <option value="MULTIPLE">Multiple Choice</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="difficulty" class="form-label">Difficulty</label>
                            <select class="form-control" id="difficulty" name="difficulty" required>
                                <option value="EASY">Easy</option>
                                <option value="MEDIUM" selected>Medium</option>
                                <option value="HARD">Hard</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="negative_mark" class="form-label">Negative Mark</label>
                            <input type="number" step="0.01" class="form-control" id="negative_mark" name="negative_mark"
                                value="0.25">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="answer_explanation" class="form-label">Answer Explanation (Optional)</label>
                        <textarea class="form-control" id="answer_explanation" name="answer_explanation"
                            rows="3">{{ old('answer_explanation') }}</textarea>
                        <div class="form-text">Explain why the correct answer is correct. This will be shown to students
                            after the exam.</div>
                    </div>

                    <hr>
                    <h5>Options</h5>
                    <div id="options-container">
                        @for($i = 0; $i < 4; $i++)
                            <div class="input-group mb-2 option-row">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0 correct-answer-selector" type="radio"
                                        name="correct_option" value="{{ $i }}"
                                        aria-label="Radio button for following text input">
                                    <input class="form-check-input mt-0 d-none correct-answer-checkbox" type="checkbox"
                                        name="correct_options[]" value="{{ $i }}"
                                        aria-label="Checkbox for following text input">
                                </div>
                                <input type="text" class="form-control" name="options[{{ $i }}][text]"
                                    placeholder="Option {{ $i + 1 }}" required>
                                <button class="btn btn-outline-danger remove-option" type="button">X</button>
                            </div>
                        @endfor
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm mb-3" id="add-option">Add Option</button>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Create Question</button>
                        <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.getElementById('options-container');
                const addButton = document.getElementById('add-option');
                const typeSelect = document.getElementById('question_type');

                let optionCount = 4;

                // Toggle between Radio and Checkbox based on Question Type
                function updateSelectors() {
                    const isMultiple = typeSelect.value === 'MULTIPLE';
                    const radios = document.querySelectorAll('.correct-answer-selector');
                    const checkboxes = document.querySelectorAll('.correct-answer-checkbox');

                    if (isMultiple) {
                        radios.forEach(el => el.classList.add('d-none'));
                        radios.forEach(el => el.disabled = true);
                        checkboxes.forEach(el => el.classList.remove('d-none'));
                        checkboxes.forEach(el => el.disabled = false);
                    } else {
                        checkboxes.forEach(el => el.classList.add('d-none'));
                        checkboxes.forEach(el => el.disabled = true);
                        radios.forEach(el => el.classList.remove('d-none'));
                        radios.forEach(el => el.disabled = false);
                    }
                }

                typeSelect.addEventListener('change', updateSelectors);

                // Add Option
                addButton.addEventListener('click', function () {
                    const index = optionCount++;
                    const div = document.createElement('div');
                    div.className = 'input-group mb-2 option-row';
                    div.innerHTML = `
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0 correct-answer-selector" type="radio" name="correct_option" value="${index}" ${typeSelect.value === 'MULTIPLE' ? 'disabled class="d-none"' : ''}>
                                    <input class="form-check-input mt-0 correct-answer-checkbox" type="checkbox" name="correct_options[]" value="${index}" ${typeSelect.value === 'SINGLE' ? 'disabled class="d-none"' : ''}>
                                </div>
                                <input type="text" class="form-control" name="options[${index}][text]" placeholder="Option ${index + 1}" required>
                                <button class="btn btn-outline-danger remove-option" type="button">X</button>
                            `;
                    container.appendChild(div);
                    updateSelectors(); // Ensure correct input type visibility
                });

                // Remove Option
                container.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-option')) {
                        if (container.children.length > 2) {
                            e.target.closest('.option-row').remove();
                        } else {
                            alert('Minimum 2 options required.');
                        }
                    }
                });

                updateSelectors();
            });
        </script>
    @endpush
@endsection