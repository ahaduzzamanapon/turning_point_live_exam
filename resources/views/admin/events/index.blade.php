@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Event Management</h1>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Create New Event
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Events</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Poster</th>
                                <th>Title</th>
                                <th>Schedule</th>
                                <th>Fee</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr>
                                    <td>
                                        @if($event->poster_image)
                                            <img src="{{ asset('storage/' . $event->poster_image) }}" alt="Poster"
                                                style="height: 50px; width: auto rounded;">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $event->title }}</strong>
                                        <small class="d-block text-muted">{{ Str::limit($event->description, 50) }}</small>
                                    </td>
                                    <td>
                                        <div><i class="fas fa-play-circle text-success input-group-text"></i>
                                            {{ $event->start_time->format('d M, h:i A') }}</div>
                                        <div><i class="fas fa-stop-circle text-danger input-group-text"></i>
                                            {{ $event->end_time->format('d M, h:i A') }}</div>
                                    </td>
                                    <td>৳ {{ number_format($event->registration_fee, 0) }}</td>
                                    <td>
                                        @php
                                            $badges = [
                                                'UPCOMING' => 'bg-info',
                                                'LIVE' => 'bg-success',
                                                'COMPLETED' => 'bg-secondary',
                                                'CANCELLED' => 'bg-danger'
                                            ];
                                        @endphp
                                        <span class="badge {{ $badges[$event->status] ?? 'bg-secondary' }}">
                                            {{ $event->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this event?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No events found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $events->links() }}
            </div>
        </div>
    </div>
@endsection