<div class="nav-section-label">My Portal</div>
<a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home"></i> Home
</a>
<a href="{{ route('student.grades.index') }}" class="nav-link {{ request()->routeIs('student.grades.*') ? 'active' : '' }}">
    <i class="fas fa-star"></i> My Grades
</a>
<a href="{{ route('student.attendance.index') }}" class="nav-link {{ request()->routeIs('student.attendance.*') ? 'active' : '' }}">
    <i class="fas fa-calendar-check"></i> Attendance
</a>
<a href="#" class="nav-link"><i class="fas fa-bullhorn"></i> Notices</a>
