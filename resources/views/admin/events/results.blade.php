@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Event Results: {{ $event->title }}</h1>
            <div>
                @if($totalPrizeDistributed == 0)
                <form action="{{ route('admin.events.distribute', $event->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Are you sure you want to distribute prizes? This will credit user wallets.');">
                    @csrf
                    <button type="submit" class="btn btn-success shadow-sm">
                        <i class="fas fa-gift fa-sm text-white-50"></i> Distribute Prizes
                    </button>
                </form>
                @else
                <button class="btn btn-secondary shadow-sm" disabled>
                    <i class="fas fa-check-circle fa-sm text-white-50"></i> Prizes Distributed
                </button>
                @endif
                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary shadow-sm ml-2">Back</a>
            </div>
        </div>

        <!-- Financial Stats -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Participants
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalParticipants }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Collection
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">৳
                                    {{ number_format($totalCollection, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Prize Distributed
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">৳
                                    {{ number_format($totalPrizeDistributed, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-trophy fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-{{ $profit >= 0 ? 'info' : 'danger' }} shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div
                                    class="text-xs font-weight-bold text-{{ $profit >= 0 ? 'info' : 'danger' }} text-uppercase mb-1">
                                    Net Profit/Loss</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">৳ {{ number_format($profit, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboard -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Leaderboard & Results</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Student</th>
                                <th>Score</th>
                                <th>Status</th>
                                <th>Prize Won</th>
                                <th>Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($participants as $index => $participant)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $participant->name }} <br>
                                        <small class="text-muted">{{ $participant->email }}</small>
                                    </td>
                                    <td>{{ $participant->pivot->score ?? 0 }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $participant->pivot->status == 'COMPLETED' ? 'success' : 'secondary' }}">
                                            {{ $participant->pivot->status ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($participant->pivot->prize_won > 0)
                                            <span class="text-success font-weight-bold">৳
                                                {{ number_format($participant->pivot->prize_won, 2) }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $participant->pivot->updated_at->format('d M, h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No participants found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection