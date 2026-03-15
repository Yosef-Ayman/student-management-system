@extends('layouts.app')
@section('title', 'Create Classroom')
@section('page-title', 'Create Classroom')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.classrooms.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="card" style="max-width:620px;">
        <div class="card-header-clean">
            <h6><i class="fas fa-school me-2 text-primary"></i>New Classroom</h6>
        </div>
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0" style="font-size:.875rem;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.classrooms.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Classroom Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Grade 10-A" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Room Number</label>
                        <input type="text" name="room_number" value="{{ old('room_number') }}"
                               class="form-control" placeholder="e.g. 204">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Grade Level *</label>
                        <select name="grade_level_id" class="form-select @error('grade_level_id') is-invalid @enderror" required>
                            <option value="">Select grade level</option>
                            @foreach($gradeLevels as $gl)
                                <option value="{{ $gl->id }}" {{ old('grade_level_id') == $gl->id ? 'selected' : '' }}>
                                    {{ $gl->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Academic Year *</label>
                        <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                            <option value="">Select year</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ old('academic_year_id') == $ay->id ? 'selected' : '' }}>
                                    {{ $ay->name }} {{ $ay->is_current ? '(Current)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Homeroom Teacher</label>
                        <select name="homeroom_teacher_id" class="form-select">
                            <option value="">None</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ old('homeroom_teacher_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Capacity *</label>
                        <input type="number" name="capacity" value="{{ old('capacity', 30) }}"
                               class="form-control" min="1" max="100" required>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Classroom
                    </button>
                    <a href="{{ route('admin.classrooms.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

@endsection
