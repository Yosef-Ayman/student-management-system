<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error') — Centarica</title>
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
    <style>
        body {
            background: #f4f6f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .error-topbar {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #e8eaf0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
            flex-shrink: 0;
        }
        .error-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .error-brand-icon {
            width: 34px; height: 34px;
            background: #4f46e5;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: .85rem;
        }
        .error-brand-name {
            font-size: 1rem;
            font-weight: 700;
            color: #1a1d2e;
        }
        .error-topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .error-home-btn {
            font-size: .83rem;
            color: #6b7280;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #e8eaf0;
            background: #fff;
            transition: all .2s;
        }
        .error-home-btn:hover {
            background: #f9fafb;
            color: #1a1d2e;
            border-color: #d1d5db;
        }
        @if(Auth::check())
        .error-user-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .83rem;
            color: #6b7280;
        }
        .error-avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: #4f46e5;
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: .72rem; font-weight: 700;
        }
        @endif

        .error-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .error-card-wrap {
            width: 100%;
            max-width: 540px;
            text-align: center;
        }

        .error-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e8eaf0;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,.06);
        }
        .error-accent-bar {
            height: 4px;
            background: var(--error-color, #4f46e5);
        }
        .error-card-body {
            padding: 48px 40px 40px;
            background: var(--error-bg, #f9fafb);
        }
        .error-icon-wrap {
            width: 84px; height: 84px;
            border-radius: 50%;
            background: var(--error-icon-bg, rgba(79,70,229,.1));
            border: 2px solid var(--error-border, #e8eaf0);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
        }
        .error-icon-wrap i {
            font-size: 1.9rem;
            color: var(--error-color, #4f46e5);
        }
        .error-code-badge {
            display: inline-block;
            background: var(--error-icon-bg, rgba(79,70,229,.1));
            color: var(--error-badge, #3730a3);
            font-size: .7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px;
            padding: 4px 14px; border-radius: 20px;
            margin-bottom: 16px;
        }
        .error-heading {
            font-size: 1.65rem;
            font-weight: 700;
            color: #1a1d2e;
            margin-bottom: 12px;
            line-height: 1.25;
        }
        .error-desc {
            font-size: .925rem;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .error-debug {
            background: #1a1d2e;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 28px;
            text-align: left;
        }
        .error-debug-label {
            font-size: .68rem; color: #6b7280; font-weight: 700;
            text-transform: uppercase; letter-spacing: .6px;
            margin-bottom: 6px;
        }
        .error-debug code {
            font-size: .8rem; color: #a5b4fc;
            word-break: break-word;
            font-family: 'Courier New', monospace;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .error-btn-primary {
            background: var(--error-color, #4f46e5);
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-size: .9rem; font-weight: 600;
            display: inline-flex; align-items: center; gap: 8px;
            border: none; cursor: pointer;
            transition: opacity .2s;
        }
        .error-btn-primary:hover { color: #fff; opacity: .85; }
        .error-btn-secondary {
            background: #fff;
            color: #374151;
            text-decoration: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-size: .9rem; font-weight: 600;
            border: 1.5px solid #e5e7eb;
            display: inline-flex; align-items: center; gap: 8px;
            cursor: pointer;
            transition: background .2s;
        }
        .error-btn-secondary:hover { background: #f9fafb; color: #374151; }

        .error-footer {
            margin-top: 20px;
            font-size: .78rem;
            color: #9ca3af;
        }
        .error-footer a {
            color: #9ca3af;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        .error-footer a:hover { color: #6b7280; }

        .error-footer-bar {
            background: #fff;
            border-top: 1px solid #e8eaf0;
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .error-footer-bar span { font-size: .78rem; color: #9ca3af; }

        @media (max-width: 576px) {
            .error-topbar { padding: 0 16px; }
            .error-card-body { padding: 36px 24px 32px; }
            .error-heading { font-size: 1.4rem; }
            .error-footer-bar { flex-direction: column; gap: 4px; text-align: center; }
            .error-topbar-right .error-home-btn span { display: none; }
        }
    </style>
</head>
<body>

<header class="error-topbar">
    <a class="error-brand" href="{{ url('/') }}">
        <div class="error-brand-icon">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <span class="error-brand-name">Centarica</span>
    </a>

    <div class="error-topbar-right">
        @auth
        <div class="error-user-badge">
            <div class="error-avatar"
                 style="background:{{ match(auth()->user()->role) {
                     'admin'   => '#4f46e5',
                     'teacher' => '#059669',
                     'student' => '#2563eb',
                     'parent'  => '#d97706',
                     default   => '#6b7280',
                 } }};">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
        </div>
        @endauth

        <a href="{{ url('/') }}" class="error-home-btn">
            <i class="fas fa-home" style="font-size:.8rem;"></i>
            <span>Home</span>
        </a>

        @auth
        <a href="{{ match(auth()->user()->role) {
            'admin'   => route('admin.dashboard'),
            'teacher' => route('teacher.dashboard'),
            'student' => route('student.dashboard'),
            'parent'  => route('parent.dashboard'),
            default   => url('/'),
        } }}" class="error-home-btn"
           style="background:#4f46e5;color:#fff;border-color:#4f46e5;">
            <i class="fas fa-tachometer-alt" style="font-size:.8rem;"></i>
            <span>Dashboard</span>
        </a>
        @endauth
    </div>
</header>

<main class="error-body">
    <div class="error-card-wrap">

        <div class="error-card">
            <div class="error-accent-bar"></div>
            <div class="error-card-body">

                {{-- Icon --}}
                <div class="error-icon-wrap">
                    <i class="fas @yield('icon', 'fa-exclamation-circle')"></i>
                </div>

                {{-- Badge --}}
                <div class="error-code-badge">
                    Error @yield('code', '000')
                </div>

                {{-- Heading --}}
                <h1 class="error-heading">@yield('heading', 'Something went wrong')</h1>

                {{-- Description --}}
                <p class="error-desc">@yield('desc', 'An unexpected error occurred. Please try again.')</p>

                {{-- Debug block --}}
                @if(config('app.debug') && isset($exception) && $exception->getMessage())
                <div class="error-debug">
                    <div class="error-debug-label">Debug info</div>
                    <code>{{ $exception->getMessage() }}</code>
                </div>
                @endif

                {{-- Buttons --}}
                <div class="error-actions">
                    @yield('actions')
                </div>

            </div>
        </div>

        {{-- Footer hint --}}
        <div class="error-footer">
            HTTP @yield('code', '000') &nbsp;·&nbsp;
            <a href="{{ url('/') }}">Centarica</a> Student Management
            @if(config('app.debug') && isset($exception))
                &nbsp;·&nbsp; {{ class_basename($exception) }}
            @endif
        </div>

    </div>
</main>

<footer class="error-footer-bar">
    <span>© {{ date('Y') }} Centarica — Student Management System</span>
    <span>
    </span>
</footer>

<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
