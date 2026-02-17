@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4" style="background: #f8f9fa;">
        <div class="row g-4">
            <!-- Sidebar / Navigation -->
            <div class="col-lg-3 order-lg-last">
                <div class="card shadow border-0 fixed-sidebar rounded-3 overflow-hidden">
                    <div class="card-header bg-warning text-dark text-center py-4">
                        <h6 class="mb-2 text-uppercase letter-spacing-2 opacity-75" style="font-size: 0.8rem;">Time
                            Remaining</h6>
                        <h1 id="timer" class="fw-bold mb-0 display-4 timer-text">--:--</h1>
                    </div>
                    <div class="card-body bg-white">
                        <p class="text-secondary fw-bold small text-uppercase text-center mb-4 letter-spacing-1">Question
                            Navigator</p>
                        <div class="d-flex flex-wrap gap-2 justify-content-center px-2">
                            @foreach($questionsWithState as $index => $question)
                                <button type="button"
                                    class="btn btn-outline-secondary btn-sm question-nav-btn position-relative rounded-3 shadow-sm"
                                    style="width: 40px; height: 40px; font-weight: 600;" id="nav-btn-{{ $question->id }}"
                                    data-qid="{{ $question->id }}"
                                    onclick="scrollToQuestion({{ $question->id }})">
                                    {{ $index + 1 }}
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle d-none"
                                        id="status-dot-{{ $question->id }}"></span>
                                </button>
                            @endforeach
                        </div>

                        <div class="mt-5">
                            <div class="d-flex align-items-center justify-content-center gap-4 text-muted small">
                                <div class="d-flex align-items-center"><span class="bg-success rounded-circle me-2"
                                        style="width: 10px; height: 10px;"></span> Answered</div>
                                <div class="d-flex align-items-center"><span
                                        class="bg-secondary opacity-50 rounded-circle me-2"
                                        style="width: 10px; height: 10px;"></span> Pending</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 p-3">
                        <button type="button"
                            class="btn btn-warning w-100 py-3 fw-bold text-uppercase letter-spacing-2 shadow hover-lift"
                            onclick="confirmFinish()">
                            Submit Event
                        </button>
                        <form action="{{ route('student.events.finish', $participant->id) }}" method="POST" id="finishForm"
                            class="d-none">@csrf</form>
                    </div>
                </div>
            </div>

            <!-- Question Area -->
            <div class="col-lg-9">
                <!-- Header Card -->
                <div class="card shadow-sm border-0 mb-4 rounded-3 bg-white">
                    <div class="card-body p-4 border-start border-5 border-warning">
                        <h3 class="fw-bold text-dark mb-1">{{ $event->title }}</h3>
                        <p class="text-muted mb-0"><i class="fas fa-info-circle me-1"></i> Answer all questions. The test
                            will auto-submit when the timer ends.</p>
                    </div>
                </div>

                @foreach($questionsWithState as $index => $question)
                    <div class="card shadow-sm border-0 mb-4 question-block rounded-3 hover-shadow transition-all"
                        id="question-{{ $question->id }}">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-warning text-dark rounded-circle fs-5 shadow-sm"
                                        style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">{{ $index + 1 }}</span>
                                </div>
                                <div class="flex-grow-1 ms-3 pt-1">
                                    <h5 class="fw-bold text-dark mb-2" style="font-size: 1.15rem; line-height: 1.6;">
                                        {{ $question->question_text }}
                                    </h5>
                                    <div>
                                        <span
                                            class="badge bg-light text-secondary border rounded-pill px-3 py-1">{{ $question->question_type == 'SINGLE' ? 'Single Choice' : 'Multiple Choice' }}</span>
                                        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 ms-1">1
                                            Mark</span>
                                    </div>
                                </div>
                            </div>

                            <div class="options-list ms-md-5 ps-md-2">
                                @foreach($question->options as $option)
                                    <div
                                        class="form-check custom-option mb-3 p-3 rounded-3 border-2 position-relative d-flex align-items-center">
                                        <input class="form-check-input flex-shrink-0 me-3 shadow-none"
                                            style="width: 1.3em; height: 1.3em; margin-top: 0; border-color: #dee2e6;"
                                            type="{{ $question->question_type === 'SINGLE' ? 'radio' : 'checkbox' }}"
                                            name="q_{{ $question->id }}{{ $question->question_type === 'MULTIPLE' ? '[]' : '' }}"
                                            value="{{ $option->id }}" id="opt-{{ $option->id }}"
                                            onchange="saveAnswer({{ $question->id }}, '{{ $question->question_type }}')"
                                            {{ (is_array($question->selected_options) && in_array($option->id, $question->selected_options)) ? 'checked' : '' }}>

                                        <label class="form-check-label w-100 cursor-pointer fw-medium text-secondary stretched-link"
                                            for="opt-{{ $option->id }}">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        body {
            background-color: #f8f9fa;
        }

        .fixed-sidebar {
            position: sticky;
            top: 20px;
            z-index: 100;
        }

        .timer-text {
            font-family: 'Poppins', monospace;
            font-variant-numeric: tabular-nums;
            letter-spacing: -2px;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .custom-option {
            border: 1px solid #e9ecef;
            background-color: #fff;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-option:hover {
            background-color: #f8f9fa;
            border-color: #ced4da;
            transform: translateY(-1px);
        }

        .custom-option:has(.form-check-input:checked) {
            background-color: #FEF2F2;
            /* Red-50 */
            border-color: #EF4444;
            /* Red-500 */
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1), 0 2px 4px -1px rgba(239, 68, 68, 0.06);
        }

        .custom-option:has(.form-check-input:checked) label {
            color: #B91C1C;
            /* Red-700 */
            font-weight: 600 !important;
        }

        .form-check-input:checked {
            background-color: #EF4444;
            border-color: #EF4444;
        }

        .question-nav-btn {
            color: #495057;
            border-color: #e9ecef;
        }

        .question-nav-btn:hover {
            background-color: #e9ecef;
            color: #212529;
        }

        .question-nav-btn.active-q {
            background-color: #EF4444 !important;
            color: white !important;
            border-color: #EF4444 !important;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        }

        .letter-spacing-1 {
            letter-spacing: 1px;
        }

        .letter-spacing-2 {
            letter-spacing: 2px;
        }

        .hover-lift {
            transition: transform 0.2s;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
        }

        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important;
        }

        .transition-all {
            transition: all 0.3s ease;
        }
    </style>

    <script>
        let remainingSeconds = {{ $remainingSeconds }};
        const participantId = {{ $participant->id }};
        const csrfToken = '{{ csrf_token() }}';
        const timerElement = document.getElementById('timer');

        function updateTimer() {
            if (remainingSeconds <= 0) {
                timerElement.innerText = "00:00";
                timerElement.classList.add('text-white-50'); // Dim it
                finishExam();
                return;
            }

            let minutes = Math.floor(remainingSeconds / 60);
            let seconds = Math.floor(remainingSeconds % 60);

            // Pad with leading zeros
            let minStr = minutes < 10 ? "0" + minutes : minutes;
            let secStr = seconds < 10 ? "0" + seconds : seconds;

            timerElement.innerText = `${minStr}:${secStr}`;

            if (remainingSeconds < 60) {
                timerElement.classList.add('animate-pulse'); // Add urgency animation if desired
            }

            remainingSeconds--;
        }

        // Update immediately then interval
        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);

        function scrollToQuestion(qid) {
            document.getElementById('question-' + qid).scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function saveAnswer(questionId, type) {
            let selected = [];
            if (type === 'SINGLE') {
                const el = document.querySelector(`input[name="q_${questionId}"]:checked`);
                if (el) selected.push(el.value);
            } else {
                document.querySelectorAll(`input[name="q_${questionId}[]"]:checked`).forEach((el) => {
                    selected.push(el.value);
                });
            }

            // UI Feedback
            const navBtn = document.getElementById('nav-btn-' + questionId);
            const dot = document.getElementById('status-dot-' + questionId);

            if (selected.length > 0) {
                navBtn.classList.add('active-q');
                navBtn.classList.remove('btn-outline-secondary');
                dot.classList.remove('d-none');
            } else {
                navBtn.classList.remove('active-q');
                navBtn.classList.add('btn-outline-secondary');
                dot.classList.add('d-none');
            }

            // AJAX Save
            fetch(`{{ url('/student/events/exam') }}/${participantId}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    question_id: questionId,
                    selected_options: selected
                })
            }).catch(err => console.error('Save failed', err));
        }

        function confirmFinish() {
            if (confirm('Are you sure you want to finish and submit your exam?')) {
                finishExam();
            }
        }

        function finishExam() {
            clearInterval(timerInterval);
            document.getElementById('finishForm').submit();
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Init state
            @foreach($questionsWithState as $question)
                @if(!empty($question->selected_options))
                    const btn = document.getElementById('nav-btn-{{ $question->id }}');
                    btn.classList.add('active-q');
                    btn.classList.remove('btn-outline-secondary');
                    document.getElementById('status-dot-{{ $question->id }}').classList.remove('d-none');
                @endif
            @endforeach
            });
    </script>
@endsection
