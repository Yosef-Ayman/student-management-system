@extends('layouts.app')
@section('title', 'Edit Classroom')
@section('page-title', 'Edit Classroom')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.classrooms.show', $classroom) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="card" style="max-width:620px;">
        <div class="card-header-clean">
            <h6><i class="fas fa-edit me-2 text-primary"></i>Edit — {{ $classroom->name }}</h6>
            <span class="badge {{ $classroom->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
            {{ $classroom->is_active ? 'Active' : 'Inactive' }}
        </span>
        </div>
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0" style="font-size:.875rem;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.classrooms.update', $classroom) }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Classroom Name *</label>
                        <input type="text" name="name" value="{{ old('name', $classroom->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Room Number</label>
                        <input type="text" name="room_number" value="{{ old('room_number', $classroom->room_number) }}"
                               class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Homeroom Teacher</label>
                        <select name="homeroom_teacher_id" class="form-select">
                            <option value="">None</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ old('homeroom_teacher_id', $classroom->homeroom_teacher_id) == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Capacity *</label>
                        <input type="number" name="capacity" value="{{ old('capacity', $classroom->capacity) }}"
                               class="form-control" min="1" max="100" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500" style="font-size:.85rem;">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ $classroom->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$classroom->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <a href="{{ route('admin.classrooms.show', $classroom) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

@endsection
