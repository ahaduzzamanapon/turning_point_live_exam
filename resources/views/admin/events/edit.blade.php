@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Edit Event</h1>

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

                <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ old('title', $event->title) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control"
                                    rows="4">{{ old('description', $event->description) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="start_time" class="form-control"
                                        value="{{ old('start_time', $event->start_time->format('Y-m-d\TH:i')) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="end_time" class="form-control"
                                        value="{{ old('end_time', $event->end_time->format('Y-m-d\TH:i')) }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Duration (Minutes) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration_minutes" class="form-control"
                                        value="{{ old('duration_minutes', $event->duration_minutes) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                                    <input type="number" name="total_marks" class="form-control"
                                        value="{{ old('total_marks', $event->total_marks) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Negative Marking <span class="text-danger">*</span></label>
                                    <input type="number" step="0.25" name="negative_marking" class="form-control"
                                        value="{{ old('negative_marking', $event->negative_marking) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select">
                                    <option value="UPCOMING" {{ $event->status == 'UPCOMING' ? 'selected' : '' }}>UPCOMING
                                    </option>
                                    <option value="LIVE" {{ $event->status == 'LIVE' ? 'selected' : '' }}>LIVE</option>
                                    <option value="COMPLETED" {{ $event->status == 'COMPLETED' ? 'selected' : '' }}>COMPLETED
                                    </option>
                                    <option value="CANCELLED" {{ $event->status == 'CANCELLED' ? 'selected' : '' }}>CANCELLED
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Poster Image</label>
                                @if($event->poster_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $event->poster_image) }}" class="img-fluid rounded"
                                            style="max-height: 150px;">
                                    </div>
                                @endif
                                <input type="file" name="poster_image" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Registration Fee (BDT) <span class="text-danger">*</span></label>
                                <input type="number" name="registration_fee" class="form-control"
                                    value="{{ old('registration_fee', $event->registration_fee) }}" required min="0">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Prize Pool Config (JSON)</label>
                                <textarea name="prize_pool_json" class="form-control"
                                    rows="5">{{ old('prize_pool_json', json_encode($event->prize_pool_config)) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3 text-end">
                        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection