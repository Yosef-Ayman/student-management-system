@extends('layouts.app')
@section('title', 'Student Portal')
@section('page-title', 'Student Portal')

@section('content')

{{-- Welcome banner --}}
<div class="card mb-4" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border:none;">
    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h5 class="text-white fw-700 mb-1">Good day, {{ explode(' ', $student->name)[0] }}! 👋</h5>
            <div class="text-white opacity-75" style="font-size:.875rem;">
                {{ $classroom?->name ?? 'No class assigned' }}
                @if($classroom) · {{ $classroom->gradeLevel->name }} @endif
                @if($upcomingExams->count())
                · <i class="fas fa-bell me-1"></i>{{ $upcomingExams->count() }} upcoming exam(s)
                @endif
            </div>
        </div>
        @if($rank)
        <div class="text-center" style="background:rgba(255,255,255,.15);border-radius:12px;padding:12px 20px;">
            <div class="text-white" style="font-size:1.8rem;font-weight:700;">#{{ $rank }}</div>
            <div class="text-white opacity-75" style="font-size:.78rem;">Class Rank</div>
        </div>
        @endif
    </div>
</div>

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
                <div class="stat-label">Attendance</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;">
                <i class="fas fa-book-open" style="color:#2563eb;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $classSubjects->count() }}</div>
                <div class="stat-label">Subjects</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;">
                <i class="fas fa-calendar-alt" style="color:#d97706;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $upcomingExams->count() }}</div>
                <div class="stat-label">Upcoming Exams</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Subject grades --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-graduation-cap me-2 text-primary"></i>My Subject Grades</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Subject</th><th>Teacher</th><th>Avg Score</th><th>Grade</th><th>Exams</th></tr>
                    </thead>
                    <tbody>
                        @forelse($subjectAverages as $s)
                        <tr>
                            <td>
                                <div style="font-weight:500;font-size:.875rem;">{{ $s['subject']->name }}</div>
                            </td>
                            <td style="font-size:.83rem;color:#6b7280;">{{ $s['teacher']->name }}</td>
                            <td>
                                @if($s['average'] !== null)
                                <div class="d-flex align-items-center gap-2">
                                    <div style="flex:1;background:#f1f5f9;border-radius:4px;height:6px;">
                                        <div style="width:{{ $s['average'] }}%;background:{{ $s['average']>=85?'#059669':($s['average']>=70?'#2563eb':($s['average']>=60?'#d97706':'#dc2626')) }};border-radius:4px;height:100%;"></div>
                                    </div>
                                    <span style="font-size:.83rem;font-weight:600;min-width:36px;">{{ $s['average'] }}%</span>
                                </div>
                                @else
                                <span class="text-muted" style="font-size:.83rem;">No grades yet</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge fw-700"
                                    style="font-size:.8rem;background:{{ in_array($s['letter'],['Excellent','Very Good'])?'#d1fae5':(in_array($s['letter'],['Good','Average Fair'])?'#dbeafe':($s['letter']==='Pass'?'#fef3c7':'#fee2e2')) }};
                                           color:{{ in_array($s['letter'],['Excellent','Very Good'])?'#065f46':(in_array($s['letter'],['Good','Average Fair'])?'#1e40af':($s['letter']==='Pass'?'#92400e':'#991b1b')) }};">
                                    {{ $s['letter'] }}
                                </span>
                            </td>
                            <td style="font-size:.83rem;color:#6b7280;">{{ $s['grades']->count() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No subjects found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Attendance + Semester progress --}}
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header-clean">
                <h6><i class="fas fa-chart-pie me-2 text-primary"></i>Attendance Summary</h6>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" height="140"></canvas>
                <div class="mt-3">
                    @foreach([
                        ['Present', $attendance->present ?? 0, '#059669'],
                        ['Absent',  $attendance->absent  ?? 0, '#dc2626'],
                        ['Late',    $attendance->late    ?? 0, '#d97706'],
                    ] as [$label,$val,$color])
                    <div class="d-flex justify-content-between py-1" style="font-size:.83rem;">
                        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{$color}};margin-right:6px;"></span>{{$label}}</span>
                        <span class="fw-600">{{$val}} days</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Today's schedule --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-calendar-day me-2 text-primary"></i>My Schedule</h6>
            </div>
            <div class="card-body p-0">
                @php
                    $subjectColors = ['#4f46e5','#059669','#d97706','#dc2626','#7c3aed','#0891b2'];
                @endphp
                @forelse($classSubjects as $i => $cs)
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <div style="width:4px;height:40px;border-radius:2px;background:{{ $subjectColors[$i % count($subjectColors)] }};flex-shrink:0;"></div>
                    <div class="flex-grow-1">
                        <div style="font-weight:500;font-size:.875rem;">{{ $cs->subject->name }}</div>
                        <div style="font-size:.78rem;color:#6b7280;">
                            {{ $cs->teacher->name }} · {{ $cs->schedule ?? 'See timetable' }}
                        </div>
                    </div>
                    <span style="font-size:.75rem;color:#9ca3af;">{{ $cs->classroom->room_number ?? '' }}</span>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:.85rem;">No schedule found.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Upcoming exams --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-file-alt me-2 text-primary"></i>Upcoming Exams</h6>
            </div>
            <div class="card-body p-0">
                @forelse($upcomingExams as $exam)
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <div class="text-center" style="background:#ede9fe;border-radius:10px;padding:8px 12px;min-width:50px;">
                        <div style="font-size:1rem;font-weight:700;color:#7c3aed;">{{ $exam->exam_date->format('d') }}</div>
                        <div style="font-size:.7rem;color:#8b5cf6;text-transform:uppercase;">{{ $exam->exam_date->format('M') }}</div>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-weight:500;font-size:.875rem;">{{ $exam->classSubject->subject->name }}</div>
                        <div style="font-size:.78rem;color:#6b7280;">
                            {{ ucfirst($exam->type) }} · {{ $exam->total_marks }} marks
                        </div>
                    </div>
                    <span class="badge bg-warning-subtle text-warning">
                        {{ $exam->exam_date->diffForHumans() }}
                    </span>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:.85rem;">
                    <i class="fas fa-check-circle d-block mb-2" style="font-size:1.5rem;color:#059669;"></i>
                    No upcoming exams
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('ChartJS/chart.umd.min.js') }}"></script>
<script>
new Chart(document.getElementById('attendanceChart'), {
    type: 'doughnut',
    data: {
        labels: ['Present','Absent','Late'],
        datasets: [{
            data: [{{ $attendance->present ?? 0 }}, {{ $attendance->absent ?? 0 }}, {{ $attendance->late ?? 0 }}],
            backgroundColor: ['#059669','#dc2626','#d97706'],
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: {
        responsive: true, cutout: '70%',
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
