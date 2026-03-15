<div class="nav-section-label">Main</div>
<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

<div class="nav-section-label">Management</div>
<a href="{{ route('admin.users.index', ['role'=>'teacher']) }}" class="nav-link {{ request()->routeIs('admin.users.*') && request('role')=='teacher' ? 'active' : '' }}">
    <i class="fas fa-chalkboard-teacher"></i> Teachers
</a>
<a href="{{ route('admin.users.index', ['role'=>'student']) }}" class="nav-link {{ request()->routeIs('admin.users.*') && request('role')=='student' ? 'active' : '' }}">
    <i class="fas fa-user-graduate"></i> Students
</a>
<a href="{{ route('admin.users.index', ['role'=>'parent']) }}" class="nav-link {{ request()->routeIs('admin.users.*') && request('role')=='parent' ? 'active' : '' }}">
    <i class="fas fa-users"></i> Parents
</a>
<a href="{{ route('admin.classrooms.index') }}" class="nav-link {{ request()->routeIs('admin.classrooms.*') ? 'active' : '' }}">
    <i class="fas fa-school"></i> Classrooms
</a>
<a href="{{ route('admin.class-subjects.create') }}" class="nav-link {{ request()->routeIs('admin.class-subjects.*') ? 'active' : '' }}">
    <i class="fas fa-book"></i> Assign Subject
</a>
<a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') && !request('role') ? 'active' : '' }}">
    <i class="fas fa-user-cog"></i> All Users
</a>

<div class="nav-section-label">Analytics</div>
<a href="{{ route('admin.analytics') }}" class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
    <i class="fas fa-chart-bar"></i> Analytics
</a>
