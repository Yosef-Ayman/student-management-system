@extends('layouts.app')
@section('title', 'Attendance Summary')
@section('page-title', 'Attendance Summary')

@section('content')

    <div class="card">
        <div class="card-header-clean">
            <h6><i class="fas fa-chart-line me-2 text-success"></i>Student Attendance Summary — My Classes</h6>
            <span class="badge bg-light text-muted">{{ $summary->total() }} students</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Total Sessions</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Late</th>
                    <th>Rate</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($summary as $i => $row)
                    <tr>
                        <td style="color:#9ca3af;font-size:.83rem;">{{ $summary->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:500;font-size:.875rem;">{{ $row->student_name ?? '—' }}</div>
                            <div style="font-size:.75rem;color:#9ca3af;">{{ $row->student_email ?? '' }}</div>
                        </td>
                        <td style="font-size:.83rem;">{{ $row->total }}</td>
                        <td>
                            <span class="badge bg-success-subtle text-success">{{ $row->present }}</span>
                        </td>
                        <td>
                            <span class="badge bg-danger-subtle text-danger">{{ $row->absent }}</span>
                        </td>
                        <td>
                            <span class="badge bg-warning-subtle text-warning">{{ $row->late }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:70px;background:#f1f5f9;border-radius:4px;height:6px;">
                                    <div style="width:{{ $row->rate }}%;background:{{ $row->rate>=80?'#059669':($row->rate>=60?'#d97706':'#dc2626') }};border-radius:4px;height:100%;"></div>
                                </div>
                                <span style="font-size:.83rem;font-weight:600;
                                color:{{ $row->rate>=80?'#059669':($row->rate>=60?'#d97706':'#dc2626') }};">
                                {{ $row->rate }}%
                            </span>
                            </div>
                        </td>
                        <td>
                            @if($row->rate >= 80)
                                <span class="badge bg-success-subtle text-success">Excellent</span>
                            @elseif($row->rate >= 60)
                                <span class="badge bg-warning-subtle text-warning">Average</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">At Risk</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-calendar-times d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>
                            No attendance data found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($summary->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2 px-3">
                <div style="font-size:.83rem;color:#6b7280;">
                    Showing {{ $summary->firstItem() }}–{{ $summary->lastItem() }} of {{ $summary->total() }}
                </div>
                {{ $summary->links() }}
            </div>
        @endif
    </div>
@endsection
