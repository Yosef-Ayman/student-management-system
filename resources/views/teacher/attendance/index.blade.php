@extends('layouts.app')
@section('title', 'Attendance')
@section('page-title', 'Attendance')

@section('content')

    {{-- Class selector --}}
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('teacher.attendance.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label" style="font-size:.83rem;font-weight:500;">Class / Subject</label>
                    <select name="class_subject_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($classSubjects as $cs)
                            <option value="{{ $cs->id }}" {{ $selectedCs?->id == $cs->id ? 'selected' : '' }}>
                                {{ $cs->classroom->name }} — {{ $cs->subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        {{-- Take today's attendance --}}
        <div class="col-md-7">
            <div class="card">
                <div class="card-header-clean">
                    <h6><i class="fas fa-calendar-check me-2 text-success"></i>
                        Take Attendance
                        <span class="text-muted fw-400" style="font-size:.83rem;">— {{ now()->format('l, d M Y') }}</span>
                    </h6>
                </div>

                @if(count($students))
                    <form method="POST" action="{{ route('teacher.attendance.store') }}">
                        @csrf
                        <input type="hidden" name="class_subject_id" value="{{ $selectedCs->id }}">
                        <input type="hidden" name="session_date" value="{{ now()->toDateString() }}">

                        <div class="p-3 border-bottom">
                            <input type="text" name="topic" class="form-control form-control-sm"
                                   placeholder="Session topic (optional)">
                        </div>

                        {{-- Quick mark all --}}
                        <div class="p-2 border-bottom d-flex gap-2" style="background:#f9fafb;">
                            <span style="font-size:.8rem;color:#6b7280;line-height:2;">Mark all:</span>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="markAll('present')">
                                <i class="fas fa-check me-1"></i>Present
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('absent')">
                                <i class="fas fa-times me-1"></i>Absent
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr><th>#</th><th>Student</th><th>Status</th><th>Late (min)</th></tr>
                                </thead>
                                <tbody>
                                @foreach($students as $i => $s)
                                    <tr>
                                        <td style="color:#9ca3af;font-size:.83rem;">{{ $i + 1 }}</td>
                                        <td>
                                            <input type="hidden" name="records[{{ $i }}][student_id]"
                                                   value="{{ $s['student']->id }}">
                                            <div style="font-weight:500;font-size:.875rem;">{{ $s['student']->name }}</div>
                                        </td>
                                        <td>
                                            <select name="records[{{ $i }}][status]"
                                                    class="form-select form-select-sm status-select"
                                                    style="width:120px;">
                                                @foreach(['present','absent','late','excused'] as $st)
                                                    <option value="{{ $st }}"
                                                        {{ $s['status'] === $st ? 'selected' : '' }}>
                                                        {{ ucfirst($st) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="records[{{ $i }}][minutes_late]"
                                                   value="0" min="0" max="60"
                                                   class="form-control form-control-sm"
                                                   style="width:80px;">
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-3 border-top">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Save Attendance
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center text-muted py-4" style="font-size:.85rem;">
                        <i class="fas fa-users d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>
                        No students enrolled in this class.
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent sessions --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-header-clean">
                    <h6><i class="fas fa-history me-2 text-success"></i>Recent Sessions</h6>
                    <a href="{{ route('teacher.attendance.summary') }}" class="btn btn-sm btn-outline-success">
                        Full Summary
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr><th>Date</th><th>Present</th><th>Absent</th><th>Rate</th></tr>
                        </thead>
                        <tbody>
                        @forelse($sessions as $session)
                            <tr>
                                <td>
                                    <a href="{{ route('teacher.attendance.session', $session) }}"
                                       style="font-size:.875rem;font-weight:500;">
                                        {{ $session->session_date->format('d M Y') }}
                                    </a>
                                    @if($session->topic)
                                        <div style="font-size:.75rem;color:#9ca3af;">{{ $session->topic }}</div>
                                    @endif
                                </td>
                                <td>
                                <span class="badge bg-success-subtle text-success">
                                    {{ $session->present_count }}
                                </span>
                                </td>
                                <td>
                                <span class="badge bg-danger-subtle text-danger">
                                    {{ $session->absent_count }}
                                </span>
                                </td>
                                <td style="font-size:.83rem;">
                                    @php
                                        $total = $session->records_count;
                                        $rate  = $total > 0 ? round(($session->present_count + $session->late_count) / $total * 100) : 0;
                                    @endphp
                                    <span style="color:{{ $rate>=80?'#059669':($rate>=60?'#d97706':'#dc2626') }};font-weight:600;">
                                    {{ $rate }}%
                                </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3" style="font-size:.85rem;">
                                    No sessions recorded yet.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($sessions, 'hasPages') && $sessions->hasPages())
                    <div class="p-2">{{ $sessions->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function markAll(status) {
            document.querySelectorAll('.status-select').forEach(sel => sel.value = status);
        }
    </script>
@endpush
