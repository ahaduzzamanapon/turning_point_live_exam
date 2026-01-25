@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Question</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.questions.update', $question->id) }}" method="POST" id="questionForm">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="subject_id" class="form-label">Subject</label>
                            <select class="form-control" id="subject_id" name="subject_id" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ $question->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="topic_id" class="form-label">Topic</label>
                            <select class="form-control" id="topic_id" name="topic_id">
                                <option value="">Select Topic</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}" {{ $question->topic_id == $topic->id ? 'selected' : '' }}>
                                        {{ $topic->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="question_text" class="form-label">Question Text</label>
                        <textarea class="form-control" id="question_text" name="question_text" rows="3"
                            required>{{ old('question_text', $question->question_text) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="question_type" class="form-label">Type</label>
                            <select class="form-control" id="question_type" name="question_type" required>
                                <option value="SINGLE" {{ $question->question_type == 'SINGLE' ? 'selected' : '' }}>Single
                                    Choice</option>
                                <option value="MULTIPLE" {{ $question->question_type == 'MULTIPLE' ? 'selected' : '' }}>
                                    Multiple Choice</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="difficulty" class="form-label">Difficulty</label>
                            <select class="form-control" id="difficulty" name="difficulty" required>
                                <option value="EASY" {{ $question->difficulty == 'EASY' ? 'selected' : '' }}>Easy</option>
                                <option value="MEDIUM" {{ $question->difficulty == 'MEDIUM' ? 'selected' : '' }}>Medium
                                </option>
                                <option value="HARD" {{ $question->difficulty == 'HARD' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="negative_mark" class="form-label">Negative Mark</label>
                            <input type="number" step="0.01" class="form-control" id="negative_mark" name="negative_mark"
                                value="{{ old('negative_mark', $question->negative_mark) }}">
                        </div>

                        <div class="mb-3">
                            <label for="answer_explanation" class="form-label">Answer Explanation (Optional)</label>
                            <textarea class="form-control" id="answer_explanation" name="answer_explanation"
                                rows="3">{{ old('answer_explanation', $question->answer_explanation) }}</textarea>
                            <div class="form-text">Explain why the correct answer is correct. This will be shown to students
                                after the exam.</div>
                        </div>
                    </div>

                    <hr>
                    <h5>Options</h5>
                    <div id="options-container">
                        @foreach($question->options as $index => $option)
                            <div class="input-group mb-2 option-row">
                                <div class="input-group-text">
                                    <input
                                        class="form-check-input mt-0 correct-answer-selector {{ $question->question_type == 'MULTIPLE' ? 'd-none' : '' }}"
                                        type="radio" name="correct_option" value="{{ $index }}" {{ $option->is_correct ? 'checked' : '' }}>

                                    <input
                                        class="form-check-input mt-0 correct-answer-checkbox {{ $question->question_type == 'SINGLE' ? 'd-none' : '' }}"
                                        type="checkbox" name="correct_options[]" value="{{ $index }}" {{ $option->is_correct ? 'checked' : '' }}>
                                </div>
                                <input type="text" class="form-control" name="options[{{ $index }}][text]"
                                    value="{{ $option->option_text }}" required>
                                <button class="btn btn-outline-danger remove-option" type="button">X</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm mb-3" id="add-option">Add Option</button>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Question</button>
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

                // Initial count based on existing options
                let optionCount = {{ $question->options->count() }};
                if (optionCount < 1) optionCount = 0;

                // Toggle between Radio and Checkbox based on Question Type
                function updateSelectors() {
                    const isMultiple = typeSelect.value === 'MULTIPLE';
                    const radios = document.querySelectorAll('.correct-answer-selector');
                    const checkboxes = document.querySelectorAll('.correct-answer-checkbox');

                    if (isMultiple) {
                        radios.forEach(el => el.classList.add('d-none'));
                        // radios.forEach(el => el.disabled = true); // Don't disable here in edit as it might mess up submission if not careful, but visually hiding is key
                        checkboxes.forEach(el => el.classList.remove('d-none'));
                        // checkboxes.forEach(el => el.disabled = false);
                    } else {
                        checkboxes.forEach(el => el.classList.add('d-none'));
                        radios.forEach(el => el.classList.remove('d-none'));
                    }
                }

                typeSelect.addEventListener('change', updateSelectors);

                // Add Option
                addButton.addEventListener('click', function () {
                    const index = optionCount++; // This ensures unique index for new items even if some deleted
                    // Actually for array handling locally it might be better to just append.
                    // But we need unique indices if we want radio buttons to work if we used ID.
                    // Here we use value="@{{ index }}" so we need to match the array index.
                    // Limitation: if we delete an option in the middle, indices might get messy.
                    // Best approach for dynamic forms is usually to re-index or use a large random ID.
                    // For simple prototype: We just append and risk index mismatch if user deletes middle one?
                    // Yes, deleting middle one will shift indices in PHP array if we submit as options[],
                    // but the radio value refers to the index KEY.
                    // If we submit options[0], options[2], the keys are 0 and 2.
                    // If radio value is 2, it matches options[2]. So it should work fine given PHP associative array handling.

                    // To be safe, we'll use a timestamp or counter for index to avoid collision 
                    // but here we just need to ensure the value matches the input name index.

                    const effectiveIndex = new Date().getTime(); // Safe unique index

                    const div = document.createElement('div');
                    div.className = 'input-group mb-2 option-row';
                    div.innerHTML = `
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0 correct-answer-selector" type="radio" name="correct_option" value="${effectiveIndex}" ${typeSelect.value === 'MULTIPLE' ? 'class="d-none"' : ''}>
                                            <input class="form-check-input mt-0 correct-answer-checkbox" type="checkbox" name="correct_options[]" value="${effectiveIndex}" ${typeSelect.value === 'SINGLE' ? 'class="d-none"' : ''}>
                                        </div>
                                        <input type="text" class="form-control" name="options[${effectiveIndex}][text]" placeholder="New Option" required>
                                        <button class="btn btn-outline-danger remove-option" type="button">X</button>
                                    `;
                    container.appendChild(div);
                    updateSelectors();
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