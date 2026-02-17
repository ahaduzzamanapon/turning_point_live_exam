@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h5 class="text-muted">Current Balance</h5>
                        <h1 class="display-4 fw-bold text-success">৳ {{ number_format($wallet->balance ?? 0, 2) }}</h1>
                        <p class="text-muted small">Available for Event Registration</p>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Transaction History</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $txn)
                                        <tr>
                                            <td>{{ $txn->created_at->format('d M Y, h:i A') }}</td>
                                            <td>{{ $txn->description }}</td>
                                            <td>
                                                @if($txn->type == 'CREDIT')
                                                    <span class="badge bg-success">Credit</span>
                                                @else
                                                    <span class="badge bg-danger">Debit</span>
                                                @endif
                                            </td>
                                            <td class="{{ $txn->type == 'CREDIT' ? 'text-success' : 'text-danger' }}">
                                                {{ $txn->type == 'CREDIT' ? '+' : '-' }} ৳ {{ number_format($txn->amount, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No transactions found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection