@extends('layouts.app')
@section('title', 'Edit Class Subject')
@section('page-title', 'Edit Class Subject')

@section('content')

    <div class="mb-4">
        <a href="{{ route('admin.classrooms.show', $classSubject->classroom_id) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Classroom
        </a>
    </div>

    <div class="card" style="max-width:520px;">
        <div class="card-header-clean">
            <h6><i class="fas fa-edit me-2 text-primary"></i>
                Edit: {{ $classSubject->subject->name }} — {{ $classSubject->classroom->name }}
            </h6>
        </div>
        <div class="card-body p-4">

            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0" style="font-size:.875rem;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.class-subjects.update', $classSubject) }}">
                @csrf @method('PUT')
                <div class="row g-3">

                    {{-- Read-only info --}}
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:.83rem;color:#6b7280;">Subject</label>
                        <div class="form-control bg-light" style="font-size:.875rem;">
                            {{ $classSubject->subject->name }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:.83rem;color:#6b7280;">Classroom</label>
                        <div class="form-control bg-light" style="font-size:.875rem;">
                            {{ $classSubject->classroom->name }}
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-500" style="font-size:.85rem;">Teacher *</label>
                        <select name="teacher_id" class="form-select" required>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}"
                                    {{ old('teacher_id', $classSubject->teacher_id) == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Schedule</label>
                        <input type="text" name="schedule"
                               value="{{ old('schedule', $classSubject->schedule) }}"
                               class="form-control" placeholder="e.g. Sun,Tue,Thu 08:00-09:00">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Room Number</label>
                        <input type="text" name="room_number"
                               value="{{ old('room_number', $classSubject->room_number) }}"
                               class="form-control" placeholder="e.g. 204">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $classSubject->is_active) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !old('is_active', $classSubject->is_active) ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <a href="{{ route('admin.classrooms.show', $classSubject->classroom_id) }}"
                       class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>

            {{-- Delete --}}
            <div class="mt-4 pt-4 border-top">
                <form method="POST" action="{{ route('admin.class-subjects.destroy', $classSubject) }}"
                      onsubmit="return confirm('Remove this subject from the classroom?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-unlink me-2"></i>Remove Subject from Classroom
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection
