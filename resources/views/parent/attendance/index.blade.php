@extends('layouts.app')
@section('title', 'Child Attendance')
@section('page-title', "Child's Attendance")

@section('content')

@if($children->count() > 1)
<div class="d-flex gap-2 mb-4 flex-wrap">
    @foreach($children as $ch)
    <a href="{{ route('parent.attendance.index', ['child_id' => $ch->id]) }}"
       class="btn btn-sm {{ isset($child) && $child->id === $ch->id ? 'btn-warning' : 'btn-outline-secondary' }}">
        <i class="fas fa-user-graduate me-1"></i>{{ $ch->name }}
    </a>
    @endforeach
</div>
@endif

@if(!$child)
<div class="alert alert-info">No children linked to your account.</div>
@else

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1fae5;"><i class="fas fa-calendar-check" style="color:#059669;"></i></div>
            <div>
                <div class="stat-value" style="color:#059669;">{{ $overall->rate ?? 0 }}%</div>
                <div class="stat-label">Attendance Rate</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;"><i class="fas fa-check" style="color:#2563eb;"></i></div>
            <div>
                <div class="stat-value">{{ $overall->present ?? 0 }}</div>
                <div class="stat-label">Present</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2;"><i class="fas fa-times" style="color:#dc2626;"></i></div>
            <div>
                <div class="stat-value" style="color:#dc2626;">{{ $overall->absent ?? 0 }}</div>
                <div class="stat-label">Absent</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;"><i class="fas fa-clock" style="color:#d97706;"></i></div>
            <div>
                <div class="stat-value" style="color:#d97706;">{{ $overall->late ?? 0 }}</div>
                <div class="stat-label">Late</div>
            </div>
        </div>
    </div>
</div>

@if($recentAbsences->count())
<div class="alert alert-warning d-flex gap-3 mb-4" style="border-radius:12px;">
    <i class="fas fa-exclamation-triangle fa-lg mt-1" style="color:#d97706;flex-shrink:0;"></i>
    <div>
        <strong>{{ $child->name }}</strong> has been absent
        <strong>{{ $recentAbsences->count() }}</strong> time(s) recently.
        Most recent: {{ $recentAbsences->first()?->session?->session_date?->format('l, d M Y') ?? '—' }}
    </div>
</div>
@endif

<div class="row g-3 mb-4">
    {{-- Monthly trend --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-chart-line me-2 text-warning"></i>Monthly Trend</h6>
            </div>
            <div class="card-body">
                @if($monthlyTrend->count())
                <canvas id="trendChart" height="120"></canvas>
                @else
                <div class="text-center text-muted py-3" style="font-size:.85rem;">No data available.</div>
                @endif
            </div>
        </div>
    </div>
    {{-- By subject --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-book me-2 text-warning"></i>By Subject</h6>
            </div>
            <div class="card-body p-0">
                @forelse($bySubject as $s)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <div style="font-size:.875rem;font-weight:500;">{{ $s['subject']->name }}</div>
                            <div style="font-size:.75rem;color:#9ca3af;">{{ $s['teacher']->name }}</div>
                        </div>
                        <span style="font-size:.875rem;font-weight:700;
                            color:{{ ($s['data']->rate??0)>=80?'#059669':(($s['data']->rate??0)>=60?'#d97706':'#dc2626') }};">
                            {{ $s['data']->rate ?? 0 }}%
                        </span>
                    </div>
                    <div style="background:#f1f5f9;border-radius:4px;height:5px;">
                        <div style="width:{{ $s['data']->rate ?? 0 }}%;height:100%;border-radius:4px;
                            background:{{ ($s['data']->rate??0)>=80?'#059669':(($s['data']->rate??0)>=60?'#d97706':'#dc2626') }};"></div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">No data.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Recent absences table --}}
@if($recentAbsences->count())
<div class="card">
    <div class="card-header-clean">
        <h6><i class="fas fa-list me-2 text-warning"></i>Recent Absences</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Subject</th><th>Date</th><th>Notified</th></tr>
            </thead>
            <tbody>
                @foreach($recentAbsences as $rec)
                <tr>
                    <td style="font-weight:500;font-size:.875rem;">{{ $rec->session?->classSubject?->subject?->name ?? '—' }}</td>
                    <td style="font-size:.83rem;color:#6b7280;">{{ $rec->session?->session_date?->format('l, d M Y') ?? '—' }}</td>
                    <td>
                        @if($rec->parent_notified)
                        <span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i>Yes</span>
                        @else
                        <span class="badge bg-secondary-subtle text-secondary">No</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endif
@endsection

@push('scripts')
<script src="{{ asset('ChartJS/chart.umd.min.js') }}"></script>
<script>
@if(isset($child) && $monthlyTrend->count())
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: @json($monthlyTrend->pluck('month_label')),
        datasets: [{
            label: 'Attendance %',
            data: @json($monthlyTrend->pluck('rate')),
            borderColor: '#d97706', backgroundColor: 'rgba(217,119,6,.08)',
            fill: true, tension: .4, borderWidth: 2,
            pointRadius: 4, pointBackgroundColor: '#d97706'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { min: 0, max: 100, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
    }
});
@endif
</script>
@endpush
