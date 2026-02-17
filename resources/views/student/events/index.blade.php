@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Upcoming Events</h1>
            <a href="{{ route('student.wallet.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-wallet"></i> Balance: ৳ {{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            @forelse($events as $event)
                <div class="col-md-6 col-lg-4 mb-4">
                    <!-- Poster Card -->
                    <div class="card h-100 border-0 shadow-lg position-relative overflow-hidden event-poster-card">

                        <!-- Background: Image or Gradient -->
                        <div class="poster-bg position-absolute w-100 h-100" style="
                                                @if($event->poster_image)
                                                    background-image: url('{{ asset('storage/' . $event->poster_image) }}');
                                                @else
                                                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                                @endif
                                                background-size: cover; 
                                                background-position: center;
                                                transition: transform 0.5s;
                                             ">
                            <!-- Overlay for readability -->
                            <div class="position-absolute w-100 h-100" style="background: rgba(0,0,0,0.6);"></div>
                        </div>

                        <div class="card-body position-relative text-white p-4 d-flex flex-column h-100">
                            <!-- Date Badge -->
                            <div class="mb-3">
                                <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill shadow-sm">
                                    <i class="far fa-calendar-alt me-1"></i> {{ $event->start_time->format('d M, Y') }}
                                </span>
                                <span class="badge bg-light text-dark fw-bold px-3 py-2 rounded-pill shadow-sm ms-1">
                                    <i class="far fa-clock me-1"></i> {{ $event->start_time->format('h:i A') }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h3 class="card-title fw-bold mb-2 text-uppercase"
                                style="letter-spacing: 1px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                {{ $event->title }}
                            </h3>

                            <!-- Description Snippet -->
                            <p class="card-text mb-4 text-white-50" style="font-size: 0.95rem;">
                                {{ Str::limit($event->description, 100) }}
                            </p>

                            <!-- Key Details Grid -->
                            <div class="row g-2 mb-4 mt-auto">
                                <div class="col-6">
                                    <div
                                        class="p-2 rounded border border-light border-opacity-25 text-center bg-black bg-opacity-25">
                                        <small class="text-uppercase text-white-50 d-block" style="font-size: 0.7rem;">Entry
                                            Fee</small>
                                        <span class="fw-bold fs-5">৳ {{ number_format($event->registration_fee, 0) }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div
                                        class="p-2 rounded border border-light border-opacity-25 text-center bg-black bg-opacity-25">
                                        <small class="text-uppercase text-white-50 d-block" style="font-size: 0.7rem;">Prize
                                            Pool</small>
                                        <span class="fw-bold fs-5 text-warning">৳
                                            {{ number_format(collect($event->prize_pool_config)->sum(), 0) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            @if(auth()->user()->events->contains($event->id))
                                <button type="button" class="btn btn-success fw-bold w-100 py-2 shadow-lg" disabled>
                                    <i class="fas fa-check-circle me-2"></i> REGISTERED
                                </button>
                            @else
                                <button type="button" class="btn btn-light fw-bold w-100 py-2 shadow-lg hover-scale"
                                    data-bs-toggle="modal" data-bs-target="#joinEventModal{{ $event->id }}">
                                    REGISTER NOW <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Join Modal (Unchanged Logic, mostly) -->
                @if(!auth()->user()->events->contains($event->id))
                    <div class="modal fade" id="joinEventModal{{ $event->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title fw-bold">
                                        <i class="fas fa-ticket-alt me-2"></i> Event Registration
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <h5 class="text-center mb-4 fw-bold text-dark">{{ $event->title }}</h5>

                                    <div class="card bg-light border-0 mb-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Entry Fee:</span>
                                                <span class="fw-bold">৳ {{ number_format($event->registration_fee, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Your Wallet Balance:</span>
                                                <span
                                                    class="fw-bold {{ (auth()->user()->wallet->balance ?? 0) < $event->registration_fee ? 'text-danger' : 'text-success' }}">
                                                    ৳ {{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    @if((auth()->user()->wallet->balance ?? 0) < $event->registration_fee)
                                        <div class="alert alert-danger d-flex align-items-center">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <div>Insufficient Funds. <a href="{{ route('student.wallet.index') }}"
                                                    class="alert-link">Add Money</a></div>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-flex align-items-center">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <div>Fee will be deducted from your wallet immediately.</div>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                    @if((auth()->user()->wallet->balance ?? 0) >= $event->registration_fee)
                                        <form action="{{ route('student.events.join', $event->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary px-4 fw-bold">Confirm Payment</button>
                                        </form>
                                    @else
                                        <a href="{{ route('student.wallet.index') }}" class="btn btn-success px-4 fw-bold">
                                            <i class="fas fa-plus-circle me-1"></i> Recharge Wallet
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-12 text-center py-5">
                    <div class="text-muted opacity-50">
                        <i class="fas fa-calendar-times fa-4x mb-3"></i>
                        <h4>No Upcoming Events</h4>
                        <p>Stay tuned! New contests are coming soon.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <style>
            .hover-scale {
                transition: transform 0.2s ease;
            }

            .hover-scale:hover {
                transform: scale(1.02);
            }

            .event-poster-card:hover .poster-bg {
                transform: scale(1.05);
            }
        </style>

        <div class="d-flex justify-content-center mt-4">
            {{ $events->links() }}
        </div>
    </div>
@endsection