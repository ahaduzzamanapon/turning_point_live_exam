@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Create New Event</h1>

        <div class="card shadow mb-4">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control"
                                    rows="4">{{ old('description') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="start_time" class="form-control"
                                        value="{{ old('start_time') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="end_time" class="form-control"
                                        value="{{ old('end_time') }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Duration (Minutes) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration_minutes" class="form-control"
                                        value="{{ old('duration_minutes', 60) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                                    <input type="number" name="total_marks" class="form-control"
                                        value="{{ old('total_marks', 100) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Negative Marking (Per Wrong) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.25" name="negative_marking" class="form-control"
                                        value="{{ old('negative_marking', 0) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Poster Image</label>
                                <input type="file" name="poster_image" class="form-control" accept="image/*">
                                <small class="text-muted">Max 2MB. Recommended: 800x400px</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Registration Fee (BDT) <span class="text-danger">*</span></label>
                                <input type="number" name="registration_fee" class="form-control"
                                    value="{{ old('registration_fee', 0) }}" required min="0">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Prize Pool Config (JSON)</label>
                                <textarea name="prize_pool_json" class="form-control" rows="5"
                                    placeholder='{"1": 1000, "2": 500, "3": 200}'>{{ old('prize_pool_json') }}</textarea>
                                <small class="text-muted">Format: {"Rank": Amount}</small>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3 text-end">
                        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection