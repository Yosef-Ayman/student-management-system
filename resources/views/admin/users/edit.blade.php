@extends('layouts.app')
@section('title', 'Edit '.$user->name)
@section('page-title', 'Edit User')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-header-clean">
        <h6><i class="fas fa-edit me-2 text-primary"></i>Edit — {{ $user->name }}</h6>
        <span class="badge role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
    </div>
    <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0" style="font-size:.875rem;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-500" style="font-size:.85rem;">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500" style="font-size:.85rem;">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="form-control @error('email') is-invalid @enderror" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500" style="font-size:.85rem;">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500" style="font-size:.85rem;">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">Select</option>
                        <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500" style="font-size:.85rem;">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
