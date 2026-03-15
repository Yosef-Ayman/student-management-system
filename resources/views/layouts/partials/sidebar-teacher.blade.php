<div class="nav-section-label">Main</div>
<a href="{{ route('teacher.dashboard') }}" class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

<div class="nav-section-label">Teaching</div>
<a href="{{ route('teacher.grades.index') }}" class="nav-link {{ request()->routeIs('teacher.grades.*') ? 'active' : '' }}">
    <i class="fas fa-clipboard-list"></i> Grades
</a>
<a href="{{ route('teacher.attendance.index') }}" class="nav-link {{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}">
    <i class="fas fa-calendar-check"></i> Attendance
</a>
<a href="{{ route('teacher.attendance.summary') }}" class="nav-link">
    <i class="fas fa-chart-line"></i> Analysis
</a>
