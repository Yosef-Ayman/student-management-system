@extends('layouts.app')
@section('title', 'Assign Subject to Classroom')
@section('page-title', 'Assign Subject')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ $preselected ? route('admin.classrooms.show', $preselected) : route('admin.classrooms.index') }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="card" style="max-width:620px;">
        <div class="card-header-clean">
            <h6><i class="fas fa-book me-2 text-success"></i>Assign Subject to Classroom</h6>
        </div>
        <div class="card-body p-4">

            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0" style="font-size:.875rem;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.class-subjects.store') }}">
                @csrf
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label fw-500" style="font-size:.85rem;">Classroom *</label>
                        <select name="classroom_id" class="form-select @error('classroom_id') is-invalid @enderror" required>
                            <option value="">Select classroom</option>
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}"
                                    {{ (old('classroom_id', $preselected) == $c->id) ? 'selected' : '' }}>
                                    {{ $c->name }} — {{ $c->gradeLevel->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Subject *</label>
                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                            <option value="">Select subject</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} ({{ $s->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Teacher *</label>
                        <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                            <option value="">Select teacher</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ old('teacher_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Academic Year *</label>
                        <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                            <option value="">Select year</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ old('academic_year_id', $ay->is_current ? $ay->id : '') == $ay->id ? 'selected' : '' }}>
                                    {{ $ay->name }} {{ $ay->is_current ? '(Current)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Room Number</label>
                        <input type="text" name="room_number" value="{{ old('room_number') }}"
                               class="form-control" placeholder="e.g. 204">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-500" style="font-size:.85rem;">Schedule</label>
                        <input type="text" name="schedule" value="{{ old('schedule') }}"
                               class="form-control" placeholder="e.g. Sun,Tue,Thu 08:00-09:00">
                        <div class="form-text">Days and time when this subject is taught.</div>
                    </div>

                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-link me-2"></i>Assign Subject
                    </button>
                    <a href="{{ $preselected ? route('admin.classrooms.show', $preselected) : route('admin.classrooms.index') }}"
                       class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

@endsection
