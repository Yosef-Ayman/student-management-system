@extends('layouts.app')
@section('title', 'Create User')
@section('page-title', 'Create New User')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-header-clean">
        <h6><i class="fas fa-user-plus me-2 text-primary"></i>New User Details</h6>
    </div>
    <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0" style="font-size:.875rem;">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-500" style="font-size:.85rem;">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500" style="font-size:.85rem;">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-500" style="font-size:.85rem;">Password *</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500" style="font-size:.85rem;">Confirm Password *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-500" style="font-size:.85rem;">Role *</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror"
                            id="roleSelect" required>
                        <option value="">Select role</option>
                        @foreach(['admin','teacher','student','parent'] as $r)
                        <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>
                            {{ ucfirst($r) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-500" style="font-size:.85rem;">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-500" style="font-size:.85rem;">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">Select</option>
                        <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            {{-- Teacher extra fields --}}
            <div id="teacherFields" style="display:none;" class="border rounded p-3 mb-3 bg-light">
                <div class="fw-600 mb-3" style="font-size:.83rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;">Teacher Details</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:.85rem;">Employee Code</label>
                        <input type="text" name="employee_code" value="{{ old('employee_code') }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:.85rem;">Specialization</label>
                        <input type="text" name="specialization" value="{{ old('specialization') }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:.85rem;">Qualification</label>
                        <input type="text" name="qualification" value="{{ old('qualification') }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:.85rem;">Hire Date</label>
                        <input type="date" name="hire_date" value="{{ old('hire_date') }}" class="form-control">
                    </div>
                </div>
            </div>

            {{-- Student extra fields --}}
            <div id="studentFields" style="display:none;" class="border rounded p-3 mb-3 bg-light">
                <div class="fw-600 mb-3" style="font-size:.83rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;">Student Details</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:.85rem;">Student Code</label>
                        <input type="text" name="student_code" value="{{ old('student_code') }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:.85rem;">Classroom</label>
                        <select name="classroom_id" class="form-select">
                            <option value="">Select classroom</option>
                            @foreach($classrooms as $c)
                            <option value="{{ $c->id }}" {{ old('classroom_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->gradeLevel->name }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:.85rem;">Enrollment Date</label>
                        <input type="date" name="enrollment_date" value="{{ old('enrollment_date', now()->toDateString()) }}" class="form-control">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Create User
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('roleSelect').addEventListener('change', function () {
    document.getElementById('teacherFields').style.display = this.value === 'teacher' ? 'block' : 'none';
    document.getElementById('studentFields').style.display = this.value === 'student' ? 'block' : 'none';
});
// Trigger on page load if old() value exists
document.getElementById('roleSelect').dispatchEvent(new Event('change'));
</script>
@endpush
