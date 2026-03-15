<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Centarica — Student Management')</title>
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

<nav class="sidebar" id="sidebar">
    <a class="sidebar-brand" href="./dashboard">
        <div class="brand-icon"><i class="fas fa-graduation-cap text-white" style="font-size:.9rem;"></i></div>
        <span>Centarica</span>
    </a>
    <button class="sidebar-close-btn" onclick="closeSidebar()" title="Close menu">
        <i class="fas fa-times"></i>
    </button>
    <div class="sidebar-nav">
        @auth
            @if(auth()->user()->isAdmin())
                @include('layouts.partials.sidebar-admin')
            @elseif(auth()->user()->isTeacher())
                @include('layouts.partials.sidebar-teacher')
            @elseif(auth()->user()->isStudent())
                @include('layouts.partials.sidebar-student')
            @elseif(auth()->user()->isParent())
                @include('layouts.partials.sidebar-parent')
            @endif
        @endauth
    </div>
    <div class="sidebar-footer">
        @auth
        <div class="d-flex align-items-center gap-2">
            <div class="topbar-avatar" style="width:32px;height:32px;font-size:.72rem;
                background:{{ auth()->user()->isAdmin()?'#4f46e5':(auth()->user()->isTeacher()?'#059669':(auth()->user()->isStudent()?'#2563eb':'#d97706')) }};">
                {{ strtoupper(substr(auth()->user()->name,0,2)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:.8rem;color:#fff;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ auth()->user()->name }}
                </div>
                <div style="font-size:.7rem;color:rgba(255,255,255,.4);">
                    {{ ucfirst(auth()->user()->role) }}
                </div>
            </div>
        </div>
        @endauth
    </div>
</nav>

{{-- ── Sidebar overlay (mobile tap-to-close) ──────────────────── --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- ── Topbar ────────────────────────────────────────────────────── --}}
<div class="topbar">
    <div class="topbar-left">
        <button class="btn btn-sm d-md-none border-0" onclick="openSidebar()">
            <i class="fas fa-bars text-muted"></i>
        </button>
        {{-- Role-aware dashboard link --}}
        @auth
        @php
            $dashRoute = match(auth()->user()->role) {
                'admin'   => 'admin.dashboard',
                'teacher' => 'teacher.dashboard',
                'student' => 'student.dashboard',
                'parent'  => 'parent.dashboard',
                default   => null,
            };
            $roleColor = match(auth()->user()->role) {
                'admin'   => '#4f46e5',
                'teacher' => '#059669',
                'student' => '#2563eb',
                'parent'  => '#d97706',
                default   => '#6b7280',
            };
            $roleLabel = ucfirst(auth()->user()->role).' Dashboard';
        @endphp
        @if($dashRoute)
        <a href="{{ route($dashRoute) }}"
           class="d-none d-md-flex align-items-center gap-2 text-decoration-none"
           style="font-size:.83rem;color:{{ $roleColor }};font-weight:600;
                  background:{{ $roleColor }}18;padding:5px 12px;border-radius:8px;">
            <i class="fas fa-{{ auth()->user()->isAdmin()?'shield-alt':(auth()->user()->isTeacher()?'chalkboard-teacher':(auth()->user()->isStudent()?'user-graduate':'users')) }}"
               style="font-size:.8rem;"></i>
            {{ $roleLabel }}
        </a>
        @endif
        @endauth
        <span class="topbar-title d-none d-lg-block" style="color:#9ca3af;font-size:.875rem;">
            @yield('page-title', 'Dashboard')
        </span>
    </div>

    <div class="topbar-right">
        {{-- Notification Bell --}}
        @auth
        <div class="notif-wrap" id="notifWrap">
            <button class="notif-bell-btn" id="notifBell"
                    onclick="toggleNotifDropdown(event)"
                    aria-label="Notifications">
                <i class="fas fa-bell"></i>
                <span class="notif-badge {{ auth()->user()->unreadNotifications->count() === 0 ? 'hidden' : '' }}"
                      id="notifBadge">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
            </button>

            <div class="notif-dropdown" id="notifDropdown" role="dialog" aria-label="Notifications panel">

                {{-- Header --}}
                <div class="notif-drop-head">
                    <h6>
                        <i class="fas fa-bell" style="color:var(--color-brand);font-size:.85rem;"></i>
                        Notifications
                        <span id="notifUnreadLabel"
                              class="badge bg-primary ms-1"
                              style="font-size:.68rem;display:{{ auth()->user()->unreadNotifications->count() > 0 ? 'inline-flex' : 'none' }}">
                            {{ auth()->user()->unreadNotifications->count() }} new
                        </span>
                    </h6>
                    <div class="notif-drop-actions">
                        <button class="notif-action-btn" onclick="markAllRead()" title="Mark all as read">
                            <i class="fas fa-check-double me-1"></i>
                            <span class="d-none d-sm-inline">Mark all read</span>
                        </button>
                        <button class="notif-action-btn text-danger" onclick="deleteAll()" title="Clear all">
                            <i class="fas fa-trash me-1"></i>
                            <span class="d-none d-sm-inline">Clear all</span>
                        </button>
                    </div>
                </div>

                {{-- List --}}
                <div class="notif-list" id="notifList">
                    <div class="notif-empty">
                        <i class="fas fa-bell-slash"></i>
                        No notifications yet
                    </div>
                </div>

                {{-- Footer --}}
                <div class="notif-drop-footer">
                    <span style="font-size:.75rem;color:var(--color-text-muted);">
                        Showing latest 20 notifications
                    </span>
                </div>

            </div>
        </div>
        @endauth

        {{-- User dropdown --}}
        <div class="dropdown">
            <div class="topbar-avatar dropdown-toggle" data-bs-toggle="dropdown"
                 style="background:{{ isset($roleColor) ? $roleColor : '#4f46e5' }};">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width:200px;border-radius:10px;margin-top:6px;">
                <li>
                    <div class="px-3 py-2 border-bottom">
                        <div style="font-weight:600;font-size:.875rem;color:#1a1d2e;">{{ auth()->user()->name ?? '' }}</div>
                        <div style="font-size:.75rem;color:#9ca3af;">{{ auth()->user()->email ?? '' }}</div>
                        <span class="badge role-{{ auth()->user()->role ?? '' }} mt-1" style="font-size:.7rem;">
                            {{ ucfirst(auth()->user()->role ?? '') }}
                        </span>
                    </div>
                </li>
                <li><a class="dropdown-item py-2" href="
                @auth
                    @if(auth()->user()->isAdmin())
                        {{ route('admin.dashboard') }}
                    @elseif(auth()->user()->isTeacher())
                        {{ route('teacher.dashboard') }}
                    @elseif(auth()->user()->isStudent())
                        {{ route('student.dashboard') }}
                    @elseif(auth()->user()->isParent())
                        {{ route('parent.dashboard') }}
                    @endif
                @endauth
                " style="font-size:.875rem;"><i class="fas fa-user me-2 text-muted"></i>My Profile</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger" style="font-size:.875rem;">
                            <i class="fas fa-sign-out-alt me-2"></i>Sign Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- ── Main content ──────────────────────────────────────────────── --}}
<div class="main-content">
    <div class="page-content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible alert-flash fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible alert-flash fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
// ── Sidebar open / close (mobile) ─────────────────────────────────
function openSidebar() {
    document.getElementById('sidebar').classList.add('show');
    document.getElementById('sidebarOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('show');
    document.getElementById('sidebarOverlay').classList.remove('show');
    document.body.style.overflow = '';
}

// ── Flash auto-dismiss ──────────────────────────────────────────────
setTimeout(() => {
    document.querySelectorAll('.alert-flash').forEach(el => {
        try { bootstrap.Alert.getOrCreateInstance(el).close(); } catch(e){}
    });
}, 4000);

// ── Notification dropdown ───────────────────────────────────────────
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function toggleNotifDropdown(e) {
    e.stopPropagation();
    const dd  = document.getElementById('notifDropdown');
    const isOpen = dd.classList.contains('open');
    if (!isOpen) {
        dd.classList.add('open');
        loadNotifications();
        // On mobile add a backdrop
        if (window.innerWidth < 576) {
            let bd = document.getElementById('notifBackdrop');
            if (!bd) {
                bd = document.createElement('div');
                bd.id = 'notifBackdrop';
                bd.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.4);';
                bd.addEventListener('click', closeNotifDropdown);
                document.body.appendChild(bd);
            }
            document.body.style.overflow = 'hidden';
        }
    } else {
        closeNotifDropdown();
    }
}

function closeNotifDropdown() {
    document.getElementById('notifDropdown')?.classList.remove('open');
    const bd = document.getElementById('notifBackdrop');
    if (bd) { bd.remove(); }
    document.body.style.overflow = '';
}

// Close on outside click (desktop)
document.addEventListener('click', (e) => {
    const wrap = document.getElementById('notifWrap');
    if (wrap && !wrap.contains(e.target)) closeNotifDropdown();
});

// Close on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeNotifDropdown();
});

function loadNotifications() {
    fetch('/notifications', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
        .then(r => r.json())
        .then(data => {
            renderNotifications(data.notifications);
            updateBadge(data.unread);
        })
        .catch(() => {});
}

function renderNotifications(items) {
    const list = document.getElementById('notifList');
    if (!items || items.length === 0) {
        list.innerHTML = `<div class="notif-empty"><i class="fas fa-bell-slash"></i>No notifications yet</div>`;
        return;
    }
    list.innerHTML = items.map(n => `
        <div class="notif-item ${n.read ? '' : 'unread'}" id="notif-${n.id}">
            <div class="notif-dot ${n.read ? 'read' : ''}"></div>
            <div class="notif-text">
                <div class="notif-msg">${escHtml(n.message)}</div>
                <div class="notif-time">${escHtml(n.time)}</div>
            </div>
            <div class="notif-item-actions">
                ${!n.read ? `<button class="notif-item-btn" title="Mark as read" onclick="markOneRead('${n.id}')">
                    <i class="fas fa-check"></i></button>` : ''}
                <button class="notif-item-btn" title="Delete" onclick="deleteOne('${n.id}')"
                        style="color:#dc2626;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `).join('');
}

function updateBadge(count) {
    const badge = document.getElementById('notifBadge');
    if (!badge) return;
    badge.textContent = count;
    badge.classList.toggle('hidden', count === 0);
}

function markOneRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    }).then(() => loadNotifications());
}

function markAllRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    }).then(() => loadNotifications());
}

function deleteOne(id) {
    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    }).then(() => {
        document.getElementById(`notif-${id}`)?.remove();
        loadNotifications();
    });
}

function deleteAll() {
    if (!confirm('Clear all notifications?')) return;
    fetch('/notifications', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    }).then(() => loadNotifications());
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Load badge count on page load (without opening dropdown)
document.addEventListener('DOMContentLoaded', () => {
    fetch('/notifications', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
        .then(r => r.json())
        .then(data => updateBadge(data.unread))
        .catch(() => {});
});
</script>

@stack('scripts')
</body>
</html>
