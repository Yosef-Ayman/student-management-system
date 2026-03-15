@extends('layouts.app')
@section('title', 'Classrooms')
@section('page-title', 'Classrooms')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-600 mb-1" style="color:#1a1d2e;">All Classrooms</h5>
            <div style="font-size:.83rem;color:#6b7280;">
                {{ $classrooms->total() }} classrooms across all grades
            </div>
        </div>
        <a href="{{ route('admin.classrooms.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-2"></i>New Classroom
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Classroom</th>
                    <th>Grade Level</th>
                    <th>Academic Year</th>
                    <th>Homeroom Teacher</th>
                    <th>Students</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($classrooms as $classroom)
                    <tr>
                        <td style="color:#9ca3af;font-size:.83rem;">{{ $classroom->id }}</td>
                        <td>
                            <div style="font-weight:600;font-size:.875rem;">{{ $classroom->name }}</div>
                            @if($classroom->room_number)
                                <div style="font-size:.75rem;color:#9ca3af;">Room {{ $classroom->room_number }}</div>
                            @endif
                        </td>
                        <td style="font-size:.875rem;">{{ $classroom->gradeLevel->name }}</td>
                        <td style="font-size:.83rem;color:#6b7280;">{{ $classroom->academicYear->name }}</td>
                        <td style="font-size:.875rem;">
                            {{ $classroom->homeroomTeacher?->name ?? '—' }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:60px;background:#f1f5f9;border-radius:4px;height:6px;">
                                    @php $pct = $classroom->capacity > 0 ? min(100, round($classroom->student_count / $classroom->capacity * 100)) : 0; @endphp
                                    <div style="width:{{ $pct }}%;background:{{ $pct>=90?'#ef4444':($pct>=70?'#f59e0b':'#059669') }};border-radius:4px;height:100%;"></div>
                                </div>
                                <span style="font-size:.83rem;font-weight:600;">{{ $classroom->student_count }}</span>
                            </div>
                        </td>
                        <td style="font-size:.83rem;color:#6b7280;">{{ $classroom->capacity }}</td>
                        <td>
                            @if($classroom->is_active)
                                <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.classrooms.show', $classroom) }}"
                                   class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.classrooms.edit', $classroom) }}"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.classrooms.destroy', $classroom) }}"
                                      onsubmit="return confirm('Deactivate this classroom?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-warning" title="Deactivate">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-school d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>
                            No classrooms found. <a href="{{ route('admin.classrooms.create') }}">Create one</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($classrooms->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2 px-3">
                <div style="font-size:.83rem;color:#6b7280;">
                    Showing {{ $classrooms->firstItem() }}–{{ $classrooms->lastItem() }} of {{ $classrooms->total() }}
                </div>
                {{ $classrooms->links() }}
            </div>
        @endif
    </div>

@endsection
