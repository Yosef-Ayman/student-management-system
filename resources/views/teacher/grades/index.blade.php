@extends('layouts.app')
@section('title', 'Grades')
@section('page-title', 'Grade Management')

@section('content')

    {{-- Class & Exam selector --}}
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('teacher.grades.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" style="font-size:.83rem;font-weight:500;">Class / Subject</label>
                    <select name="class_subject_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($classSubjects as $cs)
                            <option value="{{ $cs->id }}"
                                {{ $selectedCs?->id == $cs->id ? 'selected' : '' }}>
                                {{ $cs->classroom->name }} — {{ $cs->subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:.83rem;font-weight:500;">Exam</label>
                    <select name="exam_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Select exam</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}"
                                {{ $selectedExam?->id == $exam->id ? 'selected' : '' }}>
                                {{ $exam->title }} ({{ $exam->total_marks }} marks · {{ $exam->exam_date?->format('d M Y') ?? '—' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($selectedCs)
                    <div class="col-md-auto">
                        <a href="{{ route('teacher.grades.report', $selectedCs) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-chart-bar me-1"></i>Class Report
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if($selectedExam && count($students))
        <div class="card">
            <div class="card-header-clean">
                <h6>
                    <i class="fas fa-clipboard-list me-2 text-success"></i>
                    {{ $selectedExam->title }}
                    <span class="text-muted fw-400" style="font-size:.83rem;">
                — {{ $selectedCs->classroom->name }} · {{ $selectedCs->subject->name }}
            </span>
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-muted">Total: {{ $selectedExam->total_marks }} marks</span>
                    <span class="badge bg-light text-muted">Pass: {{ $selectedExam->pass_marks }} marks</span>
                </div>
            </div>

            <form method="POST" action="{{ route('teacher.grades.store') }}">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $selectedExam->id }}">
                <input type="hidden" name="class_subject_id" value="{{ $selectedCs->id }}">

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th style="width:160px;">Marks (/ {{ $selectedExam->total_marks }})</th>
                            <th style="width:100px;">Absent</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($students as $i => $s)
                            @php $existing = $s['marks_obtained']; @endphp
                            <tr>
                                <td style="color:#9ca3af;font-size:.83rem;">{{ $i + 1 }}</td>
                                <td>
                                    <input type="hidden" name="grades[{{ $i }}][student_id]" value="{{ $s['student']->id }}">
                                    <div style="font-weight:500;font-size:.875rem;">{{ $s['student']->name }}</div>
                                    <div style="font-size:.75rem;color:#9ca3af;">{{ $s['student']->email }}</div>
                                </td>
                                <td>
                                    <input type="number"
                                           name="grades[{{ $i }}][marks_obtained]"
                                           value="{{ $existing ?? '' }}"
                                           min="0" max="{{ $selectedExam->total_marks }}"
                                           step="0.5"
                                           class="form-control form-control-sm grade-input"
                                           data-max="{{ $selectedExam->total_marks }}"
                                           style="width:100px;">
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox"
                                               name="grades[{{ $i }}][is_absent]"
                                               value="1"
                                               class="form-check-input absent-cb"
                                            {{ $s['grade']?->is_absent ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    @if($s['percentage'] !== null)
                                        @php
                                            $p = $s['percentage'];
                                            $bg = $p>=85?'#d1fae5':($p>=70?'#dbeafe':($p>=60?'#fef3c7':'#fee2e2'));
                                            $fg = $p>=85?'#065f46':($p>=70?'#1e40af':($p>=60?'#92400e':'#991b1b'));
                                        @endphp
                                        <span class="badge fw-600"
                                              style="background:{{ $bg }};color:{{ $fg }};">
                                {{ $s['letter'] }} ({{ $p }}%)
                            </span>
                                    @else
                                        <span class="text-muted" style="font-size:.8rem;">Not graded</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="text"
                                           name="grades[{{ $i }}][remarks]"
                                           value="{{ $s['grade']?->remarks ?? '' }}"
                                           class="form-control form-control-sm"
                                           placeholder="Optional note"
                                           style="width:140px;">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <div style="font-size:.83rem;color:#6b7280;">
                        {{ count($students) }} students · {{ collect($students)->whereNotNull('marks_obtained')->count() }} graded
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Save All Grades
                    </button>
                </div>
            </form>
        </div>

    @elseif($selectedCs && $exams->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>No exams created for this class yet.
        </div>
    @elseif(!$selectedCs)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle me-2"></i>You have no classes assigned yet.
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Select an exam above to enter grades.
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        // Live grade percentage preview
        document.querySelectorAll('.grade-input').forEach(input => {
            input.addEventListener('input', function () {
                const max  = parseFloat(this.dataset.max);
                const val  = parseFloat(this.value);
                const row  = this.closest('tr');
                const cell = row.querySelector('td:nth-child(5)');
                if (!isNaN(val) && max > 0) {
                    const pct = Math.round(val / max * 100 * 10) / 10;
                    let letter = 'Failure', bg = '#fee2e2', fg = '#991b1b';
                    if (pct >= 95) { letter = 'Excellent'; bg = '#d1fae5'; fg = '#065f46'; }
                    else if (pct >= 90) { letter = 'Very Good';  bg = '#d1fae5'; fg = '#065f46'; }
                    else if (pct >= 80) { letter = 'Good'; bg = '#dbeafe'; fg = '#1e40af'; }
                    else if (pct >= 70) { letter = 'Average Fair'; bg = '#fef3c7'; fg = '#92400e'; }
                    else if (pct >= 60) { letter = 'Pass';  bg = '#f3f4f6'; fg = '#374151'; }
                    cell.innerHTML = `<span class="badge fw-600" style="background:${bg};color:${fg};">${letter} (${pct}%)</span>`;
                }
            });
        });
        // Disable marks input when absent is checked
        document.querySelectorAll('.absent-cb').forEach(cb => {
            cb.addEventListener('change', function () {
                const input = this.closest('tr').querySelector('.grade-input');
                input.disabled = this.checked;
                if (this.checked) input.value = '';
            });
            if (cb.checked) cb.closest('tr').querySelector('.grade-input').disabled = true;
        });
    </script>
@endpush
