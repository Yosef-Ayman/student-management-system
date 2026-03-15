@extends('layouts.app')
@section('title', 'Manage Users')
@section('page-title', 'Manage Users')

@section('content')

{{-- Role filter tabs --}}
<div class="d-flex gap-2 mb-4 flex-wrap align-items-center">
    @foreach([
        ['all',     'All Users',  $roleCounts['all']],
        ['admin',   'Admins',     $roleCounts['admin']],
        ['teacher', 'Teachers',   $roleCounts['teacher']],
        ['student', 'Students',   $roleCounts['student']],
        ['parent',  'Parents',    $roleCounts['parent']],
    ] as [$roleKey, $label, $count])
    <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => $roleKey === 'all' ? null : $roleKey])) }}"
       class="btn btn-sm {{ request('role', 'all') === $roleKey ? 'btn-primary' : 'btn-outline-secondary' }}">
        {{ $label }}
        <span class="badge {{ request('role', 'all') === $roleKey ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">
            {{ number_format($count) }}
        </span>
    </a>
    @endforeach

    <div class="ms-auto d-flex gap-2">
        {{-- Search --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control form-control-sm" placeholder="Search name or email..." style="width:220px;">
            <button class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>New User
        </a>
    </div>
</div>

{{-- Users table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="color:#9ca3af;font-size:.83rem;">{{ $user->id }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="topbar-avatar" style="width:34px;height:34px;font-size:.75rem;flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:500;font-size:.875rem;">{{ $user->name }}</div>
                                <div style="font-size:.75rem;color:#9ca3af;">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge role-{{ $user->role }}" style="font-size:.78rem;padding:4px 10px;">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td style="font-size:.83rem;color:#6b7280;">{{ $user->phone ?? '—' }}</td>
                    <td style="font-size:.8rem;color:#6b7280;">
                        {{ $user->created_at?->format('d M Y') ?? '—' }}
                    </td>
                    <td>
                        @if($user->trashed())
                            <span class="badge bg-secondary-subtle text-secondary">Deleted</span>
                        @elseif($user->is_active)
                            <span class="badge bg-success-subtle text-success">Active</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="btn btn-sm btn-outline-secondary" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->trashed())
                                <form method="POST" action="{{ route('admin.users.restore', $user->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success" title="Restore">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Delete this user?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-users d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center py-2 px-3">
        <div style="font-size:.83rem;color:#6b7280;">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
        </div>
        {{ $users->links() }}
    </div>
    @endif
</div>

@endsection
