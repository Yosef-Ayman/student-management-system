@extends('layouts.app')
@section('title', 'My Grades')
@section('page-title', 'My Grades')

@section('content')

{{-- Overall average banner --}}
<div class="card mb-4" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border:none;">
    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="text-white opacity-75" style="font-size:.85rem;">Overall Average — {{ $year?->name ?? 'Current Year' }}</div>
            <div class="text-white fw-700" style="font-size:2rem;line-height:1.1;">
                {{ $overallAvg ? round($overallAvg, 1).'%' : '—' }}
            </div>
        </div>
        <div class="d-flex gap-3">
            @foreach($subjectSummary as $s)
            @if($s['average'] !== null)
            <div class="text-center" style="background:rgba(255,255,255,.15);border-radius:10px;padding:10px 16px;">
                <div class="text-white fw-700" style="font-size:1.1rem;">{{ $s['letter'] }}</div>
                <div class="text-white opacity-75" style="font-size:.72rem;">{{ Str::limit($s['subject']->name,8) }}</div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>

{{-- Subject tabs --}}
@foreach($subjectSummary as $s)
<div class="card mb-3">
    <div class="card-header-clean">
        <div class="d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#ede9fe;width:40px;height:40px;">
                <i class="fas fa-book" style="color:#7c3aed;font-size:.85rem;"></i>
            </div>
            <div>
                <div style="font-weight:600;font-size:.9rem;">{{ $s['subject']->name }}</div>
                <div style="font-size:.78rem;color:#6b7280;">{{ $s['teacher']->name }}</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            @if($s['average'] !== null)
            <div style="text-align:right;">
                <div style="font-size:1.3rem;font-weight:700;
                    color:{{ $s['average']>=85?'#059669':($s['average']>=70?'#2563eb':($s['average']>=60?'#d97706':'#dc2626')) }};">
                    {{ $s['average'] }}%
                </div>
                <div style="font-size:.75rem;color:#6b7280;">average</div>
            </div>
            @php
                $l=$s['letter'];
                $bg=in_array($l,['Excellent','Very Good'])?'#d1fae5':(in_array($l,['Good','Average Fair'])?'#dbeafe':($l=='Pass'?'#fef3c7':('#fee2e2')));
                $fg=in_array($l,['Excellent','Very Good'])?'#065f46':(in_array($l,['Good','Average Fair'])?'#1e40af':($l=='Pass'?'#92400e':('#991b1b')));
            @endphp
            <span class="badge fw-700" style="font-size:.9rem;padding:6px 14px;background:{{ $bg }};color:{{ $fg }};">
                {{ $l }}
            </span>
            @endif
        </div>
    </div>

    @if($s['grades']->count())
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Exam</th><th>Type</th><th>Score</th><th>Out of</th><th>%</th><th>Grade</th><th>Date</th></tr>
            </thead>
            <tbody>
                @foreach($s['grades'] as $g)
                @php $pct = $g->total_marks > 0 ? round($g->marks_obtained / $g->total_marks * 100, 1) : 0; @endphp
                <tr>
                    <td style="font-size:.875rem;font-weight:500;">{{ $g->exam?->title ?? '—' }}</td>
                    <td>
                        <span class="badge bg-light text-muted" style="font-size:.75rem;">
                            {{ ucfirst($g->exam?->type ?? '') }}
                        </span>
                    </td>
                    <td style="font-weight:600;font-size:.875rem;">{{ $g->marks_obtained }}</td>
                    <td style="font-size:.83rem;color:#6b7280;">{{ $g->total_marks }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:50px;background:#f1f5f9;border-radius:4px;height:5px;">
                                <div style="width:{{ $pct }}%;background:{{ $pct>=85?'#059669':($pct>=70?'#2563eb':($pct>=60?'#d97706':'#dc2626')) }};border-radius:4px;height:100%;"></div>
                            </div>
                            <span style="font-size:.83rem;font-weight:500;">{{ $pct }}%</span>
                        </div>
                    </td>
                    <td>
                        @php $bg2=$pct>=85?'#d1fae5':($pct>=70?'#dbeafe':($pct>=60?'#fef3c7':'#fee2e2')); $fg2=$pct>=85?'#065f46':($pct>=70?'#1e40af':($pct>=60?'#92400e':'#991b1b')); @endphp
                        <span class="badge fw-600" style="background:{{ $bg2 }};color:{{ $fg2 }};">
                            {{ $g->grade_role }}
                        </span>
                    </td>
                    <td style="font-size:.78rem;color:#9ca3af;">
                        {{ $g->graded_at?->format('d M Y') ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center text-muted py-3" style="font-size:.85rem;">No grades recorded yet.</div>
    @endif
</div>
@endforeach

@if($subjectSummary->isEmpty())
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>No subjects found for the current academic year.
</div>
@endif

@endsection
