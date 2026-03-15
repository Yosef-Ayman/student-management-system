@extends('layouts.app')
@section('title', 'Child Grades')
@section('page-title', "Child's Grades")

@section('content')

{{-- Child selector --}}
@if($children->count() > 1)
<div class="d-flex gap-2 mb-4 flex-wrap">
    @foreach($children as $ch)
    <a href="{{ route('parent.grades.index', ['child_id' => $ch->id]) }}"
       class="btn btn-sm {{ isset($child) && $child->id === $ch->id ? 'btn-primary' : 'btn-outline-secondary' }}">
        <i class="fas fa-user-graduate me-1"></i>{{ $ch->name }}
    </a>
    @endforeach
</div>
@endif

@if(!$child)
<div class="alert alert-info">No children linked to your account.</div>
@else

{{-- Overall banner --}}
<div class="card mb-4" style="background:linear-gradient(135deg,#d97706,#f59e0b);border:none;">
    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="text-white opacity-75" style="font-size:.85rem;">{{ $child->name }} — Overall Average</div>
            <div class="text-white fw-700" style="font-size:2rem;line-height:1.1;">
                {{ $overallAvg ? round($overallAvg, 1).'%' : '—' }}
            </div>
        </div>
        <div class="d-flex gap-3 flex-wrap">
            @foreach($subjectSummary as $s)
            @if($s['average'] !== null)
            @php $l=$s['letter']; $bg=in_array($l,['Excellent','Very Good'])?'#d1fae5':(in_array($l,['Good','Average Fair'])?'#dbeafe':'#fef3c7'); $fg=in_array($l,['Excellent','Very Good'])?'#065f46':(in_array($l,['Good','Average Fair'])?'#1e40af':'#92400e'); @endphp
            <div class="text-center" style="background:rgba(255,255,255,.2);border-radius:10px;padding:8px 14px;">
                <div class="text-white fw-700" style="font-size:1.1rem;">{{ $l }}</div>
                <div class="text-white opacity-75" style="font-size:.7rem;">{{ Str::limit($s['subject']->name,8) }}</div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>

{{-- Subject cards --}}
@foreach($subjectSummary as $s)
<div class="card mb-3">
    <div class="card-header-clean">
        <div class="d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#fef3c7;width:40px;height:40px;">
                <i class="fas fa-book" style="color:#d97706;font-size:.85rem;"></i>
            </div>
            <div>
                <div style="font-weight:600;font-size:.9rem;">{{ $s['subject']->name }}</div>
                <div style="font-size:.78rem;color:#6b7280;">{{ $s['teacher']->name }}</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            @if($s['average'] !== null)
            @php $l=$s['letter']; $bg=in_array($l,['Excellent','Very Good'])?'#d1fae5':(in_array($l,['Good','Average Fair'])?'#dbeafe':($l=='Pass'?'#fef3c7':('#fee2e2'))); $fg=in_array($l,['Excellent','Very Good'])?'#065f46':(in_array($l,['Good','Average Fair'])?'#1e40af':($l=='Pass'?'#92400e':('#991b1b'))); @endphp
            <span style="font-size:1.2rem;font-weight:700;color:{{ $s['average']>=80?'#059669':($s['average']>=70?'#2563eb':($s['average']>=60?'#d97706':'#dc2626')) }};">
                {{ $s['average'] }}%
            </span>
            <span class="badge fw-700" style="font-size:.85rem;padding:5px 12px;background:{{ $bg }};color:{{ $fg }};">
                {{ $l }}
            </span>
            @else
            <span class="text-muted" style="font-size:.85rem;">No grades yet</span>
            @endif
        </div>
    </div>

    @if($s['grades']->count())
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Exam</th><th>Type</th><th>Score</th><th>%</th><th>Grade</th><th>Date</th></tr>
            </thead>
            <tbody>
                @foreach($s['grades'] as $g)
                @php $pct = $g->total_marks > 0 ? round($g->marks_obtained / $g->total_marks * 100, 1) : 0; @endphp
                <tr>
                    <td style="font-size:.875rem;font-weight:500;">{{ $g->exam?->title ?? '—' }}</td>
                    <td><span class="badge bg-light text-muted" style="font-size:.75rem;">{{ ucfirst($g->exam?->type ?? '') }}</span></td>
                    <td style="font-weight:600;">{{ $g->marks_obtained }} / {{ $g->total_marks }}</td>
                    <td style="font-size:.83rem;">{{ $pct }}%</td>
                    <td>
                        @php $bg2=$pct>=85?'#d1fae5':($pct>=70?'#dbeafe':($pct>=60?'#fef3c7':'#fee2e2')); $fg2=$pct>=85?'#065f46':($pct>=70?'#1e40af':($pct>=60?'#92400e':'#991b1b')); @endphp
                        <span class="badge fw-600" style="background:{{ $bg2 }};color:{{ $fg2 }};">{{ $g->grade_role }}</span>
                    </td>
                    <td style="font-size:.78rem;color:#9ca3af;">{{ $g->graded_at?->format('d M Y') ?? '—' }}</td>
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
<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No subjects found for the current year.</div>
@endif

@endif
@endsection
