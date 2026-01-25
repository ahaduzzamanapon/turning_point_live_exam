@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Result Details</h1>
            <a href="{{ route('admin.results.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="row">
            <!-- Student & Exam Info -->
            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Candidate Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase">Student Name</label>
                            <div class="fw-bold fs-5">{{ $attempt->user->name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase">Email</label>
                            <div>{{ $attempt->user->email }}</div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase">Exam Title</label>
                            <div class="fw-bold">{{ $attempt->exam->title }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase">Submission Time</label>
                            <div>{{ $attempt->updated_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Score Info -->
            <div class="col-md-8 mb-4">
                <div class="card shadow h-100 border-start border-5 border-primary">
                    <div class="card-body d-flex align-items-center justify-content-center text-center p-5">
                        <div>
                            <div class="text-uppercase text-muted letter-spacing-2 mb-2">Total Score</div>
                            <h1 class="display-1 fw-bold text-primary mb-0">{{ $attempt->score }}</h1>
                            <div class="fs-4 text-muted">out of {{ $attempt->exam->total_marks }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Analysis -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Answer Analysis</h6>
            </div>
            <div class="card-body">
                @foreach($attempt->answers as $index => $answer)
                    <div class="border rounded p-3 mb-3 {{ $answer->is_correct ? 'border-success bg-success-subtle' : 'border-danger bg-danger-subtle' }}"
                        style="--bs-bg-opacity: .05;">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold">Q{{ $index + 1 }}. {{ $answer->question->question_text }}</h6>
                            <span class="badge {{ $answer->is_correct ? 'bg-success' : 'bg-danger' }}">
                                {{ $answer->is_correct ? 'Correct' : 'Incorrect' }}
                            </span>
                        </div>

                        <div class="mt-2 ms-3">
                            @foreach($answer->question->options as $option)
                                @php
                                    $isSelected = is_array($answer->selected_options) && in_array($option->id, $answer->selected_options);
                                    $isCorrect = $option->is_correct;

                                    $class = "text-muted";
                                    $icon = "bi bi-circle";

                                    if ($isCorrect) {
                                        $class = "text-success fw-bold";
                                        $icon = "bi bi-check-circle-fill";
                                    } elseif ($isSelected) {
                                        $class = "text-danger fw-bold";
                                        $icon = "bi bi-x-circle-fill";
                                    }
                                @endphp
                                <div class="{{ $class }} mb-1">
                                    <i class="{{ $icon }} me-2"></i> {{ $option->option_text }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection