<div class="nav-section-label">My Portal</div>
<a href="{{ route('parent.dashboard') }}" class="nav-link {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home"></i> Overview
</a>
<a href="{{ route('parent.grades.index') }}" class="nav-link {{ request()->routeIs('parent.grades.*') ? 'active' : '' }}">
    <i class="fas fa-star"></i> Grades
</a>
<a href="{{ route('parent.attendance.index') }}" class="nav-link {{ request()->routeIs('parent.attendance.*') ? 'active' : '' }}">
    <i class="fas fa-calendar-check"></i> Attendance
</a>
<a href="{{ route('parent.messages.index') }}" class="nav-link {{ request()->routeIs('parent.messages.*') ? 'active' : '' }}">
    <i class="fas fa-envelope"></i> Messages
</a>
<a href="#" class="nav-link"><i class="fas fa-bullhorn"></i> Notices</a>
