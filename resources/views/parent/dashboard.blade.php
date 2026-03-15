@extends('layouts.app')
@section('title', 'Parent Portal')
@section('page-title', 'Parent Portal')

@section('content')

{{-- Child selector tabs --}}
@if($children->count() > 1)
<div class="d-flex gap-2 mb-4 flex-wrap">
    @foreach($children as $ch)
    <a href="{{ route('parent.dashboard', ['child_id' => $ch->id]) }}"
       class="btn {{ isset($child) && $child->id === $ch->id ? 'btn-primary' : 'btn-outline-secondary' }} btn-sm">
        <i class="fas fa-user-graduate me-1"></i>
        {{ $ch->name }}
        <span class="ms-1 opacity-75" style="font-size:.75rem;">
            ({{ $ch->studentProfile?->classroom?->name ?? 'No class' }})
        </span>
    </a>
    @endforeach
</div>
@endif

@if(!isset($child))
<div class="alert alert-info">No children linked to your account. Please contact the administrator.</div>
@else

{{-- Alert: upcoming exam or recent absence --}}
@if($recentAbsences->count())
<div class="alert alert-warning d-flex align-items-center gap-3 mb-4" style="border-radius:12px;">
    <i class="fas fa-exclamation-triangle fa-lg" style="color:#d97706;flex-shrink:0;"></i>
    <div>
        <strong>Absence alert:</strong>
        {{ $child->name }} was absent on {{ $recentAbsences->first()->session?->session_date?->format('l, d M Y') ?? 'a recent session' }}.
        <span style="font-size:.83rem;color:#92400e;">Total absences this term: {{ $recentAbsences->count() }}</span>
    </div>
</div>
@endif

@if($upcomingExams->count())
<div class="alert alert-info d-flex align-items-center gap-3 mb-4" style="border-radius:12px;background:#eff6ff;border-color:#bfdbfe;">
    <i class="fas fa-bell fa-lg" style="color:#2563eb;flex-shrink:0;"></i>
    <div>
        <strong>Upcoming exam:</strong> {{ $upcomingExams->first()->classSubject->subject->name }}
        — {{ $upcomingExams->first()->exam_date->format('l, d M Y') }}
        ({{ $upcomingExams->first()->exam_date->diffForHumans() }})
    </div>
</div>
@endif

{{-- KPI row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede9fe;">
                <i class="fas fa-star" style="color:#7c3aed;"></i>
            </div>
            <div>
                <div class="stat-value">{{ round($overallAvg ?? 0, 1) }}%</div>
                <div class="stat-label">Overall Average</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1fae5;">
                <i class="fas fa-calendar-check" style="color:#059669;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $attendance->rate ?? 0 }}%</div>
                <div class="stat-label">Attendance Rate</div>
                <div class="stat-change {{ ($attendance->rate ?? 0) >= 80 ? 'up' : 'down' }}">
                    {{ $attendance->present ?? 0 }} present · {{ $attendance->absent ?? 0 }} absent
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;">
                <i class="fas fa-trophy" style="color:#2563eb;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $rank ? '#'.$rank : '—' }}</div>
                <div class="stat-label">Class Rank</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;">
                <i class="fas fa-envelope" style="color:#d97706;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $unreadCount }}</div>
                <div class="stat-label">Unread Messages</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Subject performance table --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header-clean">
                <h6>
                    <i class="fas fa-graduation-cap me-2 text-warning"></i>
                    {{ $child->name }}'s Subject Performance
                </h6>
                <span class="badge bg-light text-muted">{{ $classroom?->name ?? '—' }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Subject</th><th>Teacher</th><th>Average</th><th>Grade</th><th>Trend</th></tr>
                    </thead>
                    <tbody>
                        @forelse($subjectSummary as $s)
                        <tr>
                            <td style="font-weight:500;font-size:.875rem;">{{ $s['subject']->name }}</td>
                            <td style="font-size:.83rem;color:#6b7280;">{{ $s['teacher']->name }}</td>
                            <td>
                                @if($s['average'] !== null)
                                <div class="d-flex align-items-center gap-2">
                                    <div style="flex:1;background:#f1f5f9;border-radius:4px;height:6px;min-width:60px;">
                                        <div style="width:{{ $s['average'] }}%;height:100%;border-radius:4px;
                                            background:{{ $s['average']>=85?'#059669':($s['average']>=70?'#2563eb':($s['average']>=60?'#d97706':'#dc2626')) }};"></div>
                                    </div>
                                    <span style="font-size:.83rem;font-weight:600;">{{ $s['average'] }}%</span>
                                </div>
                                @else
                                <span class="text-muted" style="font-size:.8rem;">No data</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $l = $s['letter'];
                                    $bg = in_array($l,['Excellent','Very Good'])?'#d1fae5':(in_array($l,['Good','Average Fair'])?'#dbeafe':($l=='Pass'?'#fef3c7':('#fee2e2')));
                                    $fg = in_array($l,['Excellent','Very Good'])?'#065f46':(in_array($l,['Good','Average Fair'])?'#1e40af':($l=='Pass'?'#92400e':('#991b1b')));
                                @endphp
                                <span class="badge fw-700" style="background:{{ $bg }};color:{{ $fg }};font-size:.8rem;">{{ $l }}</span>
                            </td>
                            <td>
                                @if($s['trend'] === 'up')
                                    <span style="color:#059669;font-size:.9rem;"><i class="fas fa-arrow-trend-up"></i></span>
                                @elseif($s['trend'] === 'down')
                                    <span style="color:#dc2626;font-size:.9rem;"><i class="fas fa-arrow-trend-down"></i></span>
                                @else
                                    <span style="color:#9ca3af;font-size:.9rem;"><i class="fas fa-minus"></i></span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No subjects found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Attendance donut --}}
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header-clean">
                <h6><i class="fas fa-chart-pie me-2 text-warning"></i>Attendance</h6>
            </div>
            <div class="card-body text-center">
                <canvas id="parentAttChart" height="160"></canvas>
                <div class="mt-2" style="font-size:1.4rem;font-weight:700;color:#1a1d2e;">
                    {{ $attendance->rate ?? 0 }}%
                </div>
                <div style="font-size:.8rem;color:#6b7280;">Overall attendance rate</div>
                <div class="mt-3 text-start">
                    @foreach([['Present',$attendance->present??0,'#059669'],['Absent',$attendance->absent??0,'#dc2626'],['Late',$attendance->late??0,'#d97706']] as [$l,$v,$c])
                    <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:.83rem;">
                        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{$c}};margin-right:6px;"></span>{{$l}}</span>
                        <span class="fw-600">{{$v}}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Messages from teachers --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-comments me-2 text-warning"></i>Messages
                    @if($unreadCount) <span class="badge bg-danger">{{ $unreadCount }}</span> @endif
                </h6>
                <button class="btn btn-sm btn-outline-warning">New Message</button>
            </div>
            <div class="card-body p-0">
                @forelse($messages as $msg)
                <div class="d-flex align-items-start gap-3 p-3 border-bottom {{ !$msg->is_read ? 'bg-light' : '' }}">
                    <div class="topbar-avatar" style="width:34px;height:34px;font-size:.75rem;flex-shrink:0;background:#059669;">
                        {{ strtoupper(substr($msg->sender->name,0,2)) }}
                    </div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <div class="d-flex justify-content-between">
                            <span style="font-size:.85rem;font-weight:{{ !$msg->is_read?'700':'500' }};">{{ $msg->sender->name }}</span>
                            <span style="font-size:.72rem;color:#9ca3af;">{{ $msg->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="font-size:.8rem;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $msg->subject ?? Str::limit($msg->body, 55) }}
                        </div>
                        @if(!$msg->is_read)
                        <span style="display:inline-block;width:6px;height:6px;background:#4f46e5;border-radius:50;margin-top:4px;"></span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:.85rem;">
                    <i class="fas fa-inbox d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>No messages yet
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Upcoming events & announcements --}}
    <div class="col-md-7">
        <div class="card mb-3">
            <div class="card-header-clean">
                <h6><i class="fas fa-calendar-alt me-2 text-warning"></i>Upcoming for {{ explode(' ', $child->name)[0] }}</h6>
            </div>
            <div class="card-body p-0">
                @forelse($upcomingExams as $exam)
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <div class="text-center" style="background:#fef3c7;border-radius:10px;padding:8px 12px;min-width:50px;">
                        <div style="font-size:1rem;font-weight:700;color:#d97706;">{{ $exam->exam_date->format('d') }}</div>
                        <div style="font-size:.7rem;color:#d97706;text-transform:uppercase;">{{ $exam->exam_date->format('M') }}</div>
                    </div>
                    <div>
                        <div style="font-weight:500;font-size:.875rem;">{{ $exam->classSubject->subject->name }} — {{ ucfirst($exam->type) }}</div>
                        <div style="font-size:.78rem;color:#6b7280;">
                            {{ $exam->total_marks }} marks · {{ $exam->exam_date->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3" style="font-size:.85rem;">No upcoming exams.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-bullhorn me-2 text-warning"></i>Student Announcements</h6>
            </div>
            <div class="card-body p-0">
                @forelse($announcements as $ann)
                <div class="p-3 border-bottom">
                    <div style="font-size:.85rem;font-weight:500;">{{ $ann->title }}</div>
                    <div style="font-size:.78rem;color:#6b7280;margin-top:3px;">
                        {{ $ann->published_at?->diffForHumans() }}
                        <span class="badge bg-light text-muted ms-1">{{ ucfirst($ann->audience) }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3" style="font-size:.85rem;">No announcements.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endif {{-- end child check --}}

@endsection

@push('scripts')
<script src="{{ asset('ChartJS/chart.umd.min.js') }}"></script>
<script>
@if(isset($child))
new Chart(document.getElementById('parentAttChart'), {
    type: 'doughnut',
    data: {
        labels: ['Present','Absent','Late'],
        datasets: [{
            data: [{{ $attendance->present ?? 0 }}, {{ $attendance->absent ?? 0 }}, {{ $attendance->late ?? 0 }}],
            backgroundColor: ['#059669','#dc2626','#d97706'],
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: { responsive:true, cutout:'72%', plugins:{ legend:{ display:false } } }
});
@endif
</script>
@endpush
