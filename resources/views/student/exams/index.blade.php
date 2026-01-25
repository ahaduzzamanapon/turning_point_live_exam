@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4 fw-bold text-dark">Available Exams</h2>

        @if(session('error'))
            <div class="alert alert-danger shadow-sm border-0">{{ session('error') }}</div>
        @endif

        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-secondary">Title</th>
                                <th class="text-secondary">Duration</th>
                                <th class="text-secondary">Marks</th>
                                <th class="text-secondary">Schedule</th>
                                <th class="text-end pe-5 text-secondary">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                                <tr id="exam-row-{{ $exam->id }}" data-start="{{ $exam->start_time->timestamp * 1000 }}"
                                    data-end="{{ $exam->end_time->timestamp * 1000 }}">

                                    <td class="ps-4 fw-semibold text-dark">
                                        <span class="d-block">{{ $exam->title }}</span>
                                    </td>
                                    <td><span class="badge bg-light text-dark border rounded-pill px-3"><i
                                                class="fas fa-clock me-1 text-primary"></i> {{ $exam->duration_minutes }}
                                            mins</span></td>
                                    <td><span
                                            class="badge bg-light text-dark border rounded-pill px-3 fw-bold">{{ $exam->total_marks }}
                                            Pts</span></td>
                                    <td>
                                        <div class="small text-muted">
                                            <div class="mb-1"><i class="fas fa-calendar-alt me-1 text-success"></i> Start:
                                                {{ $exam->start_time->format('M d, Y h:i A') }}</div>
                                            <div><i class="fas fa-calendar-times me-1 text-danger"></i> End:
                                                {{ $exam->end_time->format('M d, Y h:i A') }}</div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-5">
                                        <div class="action-wrapper">
                                            <!-- Countdown Timer -->
                                            <div class="countdown-timer text-primary fw-bold mb-1" style="display: none;">
                                                Starts in: <span class="timer-display">00:00:00</span>
                                            </div>

                                            @php
                                                $attempt = $exam->attempts->first();
                                                $buttonClass = "btn-primary"; // Default
                                            @endphp

                                            @if($attempt)
                                                @if($attempt->status == 'COMPLETED')
                                                    <!-- View Result Button -->
                                                    <a href="{{ route('student.exams.result', $attempt->id) }}" class="btn btn-success btn-sm rounded-pill px-4 shadow-sm fw-bold">
                                                        View Result <i class="fas fa-poll ms-1"></i>
                                                    </a>
                                                    @php $buttonClass = "d-none"; @endphp
                                                @else
                                                    <!-- Resume Exam Button -->
                                                    <a href="{{ route('student.exams.live', $attempt->id) }}" class="btn btn-warning text-dark btn-sm rounded-pill px-4 shadow-sm fw-bold">
                                                        Resume Exam <i class="fas fa-play ms-1"></i>
                                                    </a>
                                                     @php $buttonClass = "d-none"; @endphp
                                                @endif
                                            @endif

                                            <!-- Start Button -->
                                            <form action="{{ route('student.exams.start', $exam->id) }}" method="POST" 
                                                  class="start-form {{ isset($buttonClass) && $buttonClass == 'd-none' ? 'd-none-important' : '' }}" 
                                                  style="display: none;">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold">
                                                    Start Exam <i class="fas fa-play ms-1"></i>
                                                </button>
                                            </form>

                                            <!-- Status Badges -->
                                            <button class="btn btn-secondary btn-sm rounded-pill px-3 expired-badge"
                                                style="display: none;" disabled>Ended</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-clipboard-list fa-3x mb-3 opacity-25"></i>
                                            <p class="mb-0">No exams available at the moment.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Update timers every second
                setInterval(updateExamStatus, 1000);
                updateExamStatus(); // Initial call
            });

            function updateExamStatus() {
                const now = new Date().getTime();

                document.querySelectorAll('tr[id^="exam-row-"]').forEach(row => {
                    const start = parseInt(row.getAttribute('data-start'));
                    const end = parseInt(row.getAttribute('data-end'));

                    const countdownEl = row.querySelector('.countdown-timer');
                    const timerDisplay = row.querySelector('.timer-display');
                    const startForm = row.querySelector('.start-form');
                    const expiredBadge = row.querySelector('.expired-badge');

                    if (now < start) {
                        // Future Exam
                        const diff = start - now;
                        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                        timerDisplay.textContent = `${hours}h ${minutes}m ${seconds}s`;

                        countdownEl.style.display = 'block';
                        if(startForm && !startForm.classList.contains('d-none-important')) startForm.style.display = 'none';
                        if(expiredBadge) expiredBadge.style.display = 'none';
                    } else if (now >= start && now <= end) {
                        // Active Exam
                        countdownEl.style.display = 'none';
                         // Only show start button if not already attempted (marked by d-none-important)
                        if(startForm && !startForm.classList.contains('d-none-important')) {
                            startForm.style.display = 'inline-block';
                        }
                        if(expiredBadge) expiredBadge.style.display = 'none';
                    } else {
                        // Expired Exam
                        countdownEl.style.display = 'none';
                        if(startForm) startForm.style.display = 'none';
                         // Show expired badge only if no result button is there? 
                         // For simplicity, showing expired badge if exam is over, but if user finished they see 'View Result' alongside?
                         // Actually the TD layout might get cluttered.
                         // If user completed, View Result is visible always. Expired badge might be redundant or confusing.
                         // Let's just show Expired Badge if NO attempt exists and time is up.
                        if(expiredBadge) {
                             if(startForm && !startForm.classList.contains('d-none-important')) {
                                 expiredBadge.style.display = 'inline-block';
                             } else {
                                 expiredBadge.style.display = 'none'; // Don't show expired if viewing result
                             }
                        }
                    }
                });
            }
        </script>
        <style>
            .d-none-important { display: none !important; }
        </style>
    @endpush
@endsection