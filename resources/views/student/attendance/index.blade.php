@extends('layouts.app')
@section('title', 'My Attendance')
@section('page-title', 'My Attendance')

@section('content')

{{-- Overall summary cards --}}
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

<div class="row g-3 mb-4">
    {{-- Monthly trend --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-chart-line me-2 text-primary"></i>Monthly Attendance Trend</h6>
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

    {{-- Per-subject breakdown --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-book me-2 text-primary"></i>By Subject</h6>
            </div>
            <div class="card-body p-0">
                @forelse($bySubject as $s)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span style="font-size:.875rem;font-weight:500;">{{ $s['subject']->name }}</span>
                        <span style="font-size:.83rem;font-weight:600;
                            color:{{ ($s['data']->rate??0)>=80?'#059669':(($s['data']->rate??0)>=60?'#d97706':'#dc2626') }};">
                            {{ $s['data']->rate ?? 0 }}%
                        </span>
                    </div>
                    <div style="background:#f1f5f9;border-radius:4px;height:5px;">
                        <div style="width:{{ $s['data']->rate ?? 0 }}%;height:100%;border-radius:4px;
                            background:{{ ($s['data']->rate??0)>=80?'#059669':(($s['data']->rate??0)>=60?'#d97706':'#dc2626') }};"></div>
                    </div>
                    <div style="font-size:.75rem;color:#9ca3af;margin-top:3px;">
                        {{ $s['data']->present ?? 0 }} present · {{ $s['data']->absent ?? 0 }} absent · {{ $s['data']->late ?? 0 }} late
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:.85rem;">No data.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Recent absences --}}
@if($recentAbsences->count())
<div class="card">
    <div class="card-header-clean">
        <h6><i class="fas fa-exclamation-circle me-2 text-danger"></i>Recent Absences</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Subject</th><th>Date</th><th>Parent Notified</th></tr>
            </thead>
            <tbody>
                @foreach($recentAbsences as $rec)
                <tr>
                    <td style="font-weight:500;font-size:.875rem;">
                        {{ $rec->session?->classSubject?->subject?->name ?? '—' }}
                    </td>
                    <td style="font-size:.83rem;color:#6b7280;">
                        {{ $rec->session?->session_date?->format('l, d M Y') ?? '—' }}
                    </td>
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

@endsection

@push('scripts')
<script src="{{ asset('ChartJS/chart.umd.min.js') }}"></script>
<script>
@if($monthlyTrend->count())
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: @json($monthlyTrend->pluck('month_label')),
        datasets: [{
            label: 'Attendance %',
            data: @json($monthlyTrend->pluck('rate')),
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79,70,229,.08)',
            fill: true, tension: .4, borderWidth: 2,
            pointRadius: 4, pointBackgroundColor: '#4f46e5'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { min: 0, max: 100, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});
@endif
</script>
@endpush
