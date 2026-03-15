@extends('layouts.app')
@section('title', 'Class Grade Report')
@section('page-title', 'Class Grade Report')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('teacher.grades.index', ['class_subject_id' => $classSubject->id]) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Grades
        </a>
        <div style="font-size:.875rem;color:#6b7280;">
            {{ $classSubject->classroom->name }} · {{ $classSubject->subject->name }}
        </div>
    </div>

    <div class="card">
        <div class="card-header-clean">
            <h6><i class="fas fa-chart-bar me-2 text-success"></i>Student Performance Report</h6>
            <span class="badge bg-light text-muted">{{ $reportData->count() }} students</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Rank</th>
                    <th>Student</th>
                    @foreach($classSubject->exams as $exam)
                        <th style="font-size:.75rem;">{{ Str::limit($exam->title, 12) }}</th>
                    @endforeach
                    <th>Average</th>
                    <th>Grade</th>
                </tr>
                </thead>
                <tbody>
                @foreach($reportData->values() as $i => $row)
                    <tr>
                        <td style="color:#9ca3af;font-size:.83rem;">{{ $i + 1 }}</td>
                        <td>
                            <div style="font-weight:500;font-size:.875rem;">{{ $row['student']->name }}</div>
                        </td>
                        @foreach($classSubject->exams as $exam)
                            @php
                                $g = $row['grades']->firstWhere('exam_id', $exam->id);
                                $p = $g ? round($g->marks_obtained / $g->total_marks * 100, 1) : null;
                            @endphp
                            <td style="font-size:.83rem;">
                                @if($p !== null)
                                    <span style="color:{{ $p>=70?'#059669':($p>=60?'#d97706':'#dc2626') }};font-weight:500;">
                                {{ $g->marks_obtained }}
                            </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        @endforeach
                        <td>
                            @if($row['average'] !== null)
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:60px;background:#f1f5f9;border-radius:4px;height:6px;">
                                        <div style="width:{{ $row['average'] }}%;background:{{ $row['average']>=85?'#059669':($row['average']>=70?'#2563eb':($row['average']>=60?'#d97706':'#dc2626')) }};border-radius:4px;height:100%;"></div>
                                    </div>
                                    <span style="font-size:.83rem;font-weight:600;">{{ $row['average'] }}%</span>
                                </div>
                            @else
                                <span class="text-muted" style="font-size:.83rem;">No data</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $l = $row['letter'];
                                $bg = in_array($l,['Excellent','Very Good'])?'#d1fae5':(in_array($l,['Good','Average Fair'])?'#dbeafe':($l=='Pass'?'#fef3c7':('#fee2e2')));
                                $fg = in_array($l,['Excellent','Very Good'])?'#065f46':(in_array($l,['Good','Average Fair'])?'#1e40af':($l=='Pass'?'#92400e':('#991b1b')));
                            @endphp
                            <span class="badge fw-700" style="background:{{ $bg }};color:{{ $fg }};">{{ $l }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
