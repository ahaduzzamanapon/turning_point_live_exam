@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Topics</h1>
            <a href="{{ route('admin.topics.create') }}" class="btn btn-primary">Add New Topic</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Subject</th>
                                <th>Topic Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topics as $topic)
                                <tr>
                                    <td>{{ $topic->id }}</td>
                                    <td>{{ $topic->subject->name }}</td>
                                    <td>{{ $topic->name }}</td>
                                    <td>
                                        <a href="{{ route('admin.topics.edit', $topic->id) }}"
                                            class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('admin.topics.destroy', $topic->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No topics found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $topics->links() }}
            </div>
        </div>
    </div>
@endsection