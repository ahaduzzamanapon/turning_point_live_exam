@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Assign Exam: {{ $exam->title }}</h1>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.exams.storeAssignment', $exam->id) }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">Access Mode</label>
                        <select name="access_mode" id="access_mode" class="form-select" onchange="toggleStudentList()">
                            <option value="PUBLIC" {{ $exam->access_mode == 'PUBLIC' ? 'selected' : '' }}>Public (Open to All)
                            </option>
                            <option value="PRIVATE" {{ $exam->access_mode == 'PRIVATE' ? 'selected' : '' }}>Private (Assigned
                                Students Only)</option>
                        </select>
                        <div class="form-text">Public exams are visible to all students. Private exams are only visible to
                            selected students.</div>
                    </div>

                    <div id="student-list" class="{{ $exam->access_mode == 'PUBLIC' ? 'd-none' : '' }}">
                        <h5 class="mb-3">Select Students</h5>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th width="50"><input type="checkbox" id="select-all"></th>
                                        <th>Name</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="user_ids[]" value="{{ $student->id }}"
                                                    class="student-checkbox" {{ in_array($student->id, $assignedUserIds) ? 'checked' : '' }}>
                                            </td>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->email }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleStudentList() {
                const mode = document.getElementById('access_mode').value;
                const list = document.getElementById('student-list');
                if (mode === 'PRIVATE') {
                    list.classList.remove('d-none');
                } else {
                    list.classList.add('d-none');
                }
            }

            document.getElementById('select-all').addEventListener('change', function (e) {
                document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = e.target.checked);
            });
        </script>
    @endpush
@endsection