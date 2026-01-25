@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <!-- Score Card -->
                <div class="card shadow border-0 rounded-4 overflow-hidden mb-5">
                    <div class="card-header bg-gradient bg-primary text-white text-center py-5">
                        <h5 class="text-uppercase letter-spacing-2 opacity-75 mb-3">{{ $attempt->exam->title }}</h5>
                        <h1 class="display-1 fw-bold mb-0 text-white">{{ $attempt->score }}</h1>
                        <p class="fs-5 opacity-75">Total Score out of {{ $attempt->exam->total_marks }}</p>

                        <div class="mt-4">
                            <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm fs-6">
                                <i class="fas fa-calendar-check me-2"></i>
                                {{ $attempt->updated_at->format('M d, Y h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Answer Review -->
                <h4 class="mb-4 fw-bold text-secondary"><i class="fas fa-list-check me-2"></i> Detailed Review</h4>

                <div class="review-list">
                    @foreach($attempt->answers as $index => $answer)
                        <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start">
                                    <span
                                        class="badge {{ $answer->is_correct ? 'bg-success' : 'bg-danger' }} rounded-circle p-3 fs-5 flex-shrink-0"
                                        style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas {{ $answer->is_correct ? 'fa-check' : 'fa-times' }}"></i>
                                    </span>

                                    <div class="ms-3 flex-grow-1">
                                        <h5 class="fw-bold text-dark mb-3">Q{{ $index + 1 }}.
                                            {{ $answer->question->question_text }}</h5>

                                        <div
                                            class="options border-start border-3 ps-3 {{ $answer->is_correct ? 'border-success' : 'border-danger' }}">
                                            @foreach($answer->question->options as $option)
                                                @php
                                                    $isSelected = is_array($answer->selected_options) && in_array($option->id, $answer->selected_options);
                                                    $isCorrect = $option->is_correct;

                                                    $rowClass = "";
                                                    if ($isCorrect)
                                                        $rowClass = "bg-success-subtle border-success text-success fw-bold";
                                                    else if ($isSelected && !$isCorrect)
                                                        $rowClass = "bg-danger-subtle border-danger text-danger text-decoration-line-through";
                                                    else
                                                        $rowClass = "text-muted border-light";
                                                @endphp

                                                <div
                                                    class="p-2 mb-2 rounded border {{ $rowClass }} d-flex justify-content-between align-items-center">
                                                    <span>{{ $option->option_text }}</span>
                                                    @if($isCorrect) <i class="fas fa-check-circle"></i>
                                                    @elseif($isSelected) <i class="fas fa-times-circle"></i> @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        @if($answer->question->answer_explanation)
                                            <div class="mt-3 p-3 bg-light rounded text-dark">
                                                <p class="mb-1 fw-bold text-secondary text-uppercase small"><i
                                                        class="fas fa-lightbulb text-warning me-1"></i> Explanation</p>
                                                <p class="mb-0 text-secondary">{{ $answer->question->answer_explanation }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-5">
                    <a href="{{ route('student.exams.index') }}"
                        class="btn btn-outline-secondary btn-lg rounded-pill px-5 shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i> Back to All Exams
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient {
            background: linear-gradient(45deg, #e53935, #d32f2f);
        }

        .letter-spacing-2 {
            letter-spacing: 2px;
        }

        .bg-success-subtle {
            background-color: #d1e7dd;
        }

        .bg-danger-subtle {
            background-color: #f8d7da;
        }
    </style>
@endsection