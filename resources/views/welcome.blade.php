<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centarica — Student Management System</title>
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

{{-- ── Nav ──────────────────────────────────────────────────────── --}}
<nav class="welcome-nav">
    <a class="welcome-brand" href="/">
        <div class="brand-icon"><i class="fas fa-graduation-cap"></i></div>
        <span class="brand-name">Centarica</span>
    </a>
    <div class="d-flex align-items-center gap-3">
        @auth
        @php
            $dashRoute = match(auth()->user()->role) {
                'admin'   => 'admin.dashboard',
                'teacher' => 'teacher.dashboard',
                'student' => 'student.dashboard',
                'parent'  => 'parent.dashboard',
                default   => 'login',
            };
            $roleColor = match(auth()->user()->role) {
                'admin'   => '#4f46e5',
                'teacher' => '#059669',
                'student' => '#2563eb',
                'parent'  => '#d97706',
                default   => '#4f46e5',
            };
        @endphp
        <a href="{{ route($dashRoute) }}"
           style="font-size:.83rem;color:{{ $roleColor }};font-weight:600;
                  background:{{ $roleColor }}18;padding:5px 14px;border-radius:8px;
                  text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <i class="fas fa-tachometer-alt" style="font-size:.78rem;"></i>
            Go to Dashboard
        </a>
        <div class="dropdown">
            <div class="dropdown-toggle" data-bs-toggle="dropdown"
                 style="width:38px;height:38px;border-radius:50%;
                        background:{{ $roleColor }};color:#fff;
                        display:flex;align-items:center;justify-content:center;
                        font-size:.8rem;font-weight:700;cursor:pointer;">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                style="min-width:200px;border-radius:10px;margin-top:6px;border:1px solid #e8eaf0;">
                <li>
                    <div class="px-3 py-2 border-bottom">
                        <div style="font-weight:600;font-size:.875rem;color:#1a1d2e;">{{ auth()->user()->name }}</div>
                        <div style="font-size:.75rem;color:#9ca3af;">{{ auth()->user()->email }}</div>
                        <span style="display:inline-block;margin-top:5px;padding:2px 10px;border-radius:20px;
                                     font-size:.7rem;font-weight:600;
                                     background:{{ $roleColor }}20;color:{{ $roleColor }};">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </div>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route($dashRoute) }}"
                       style="font-size:.875rem;">
                        <i class="fas fa-tachometer-alt me-2 text-muted"></i>My Dashboard
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger"
                                style="font-size:.875rem;">
                            <i class="fas fa-sign-out-alt me-2"></i>Sign Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        @else
        <a href="{{ route('login') }}" class="nav-login-btn">
            <i class="fas fa-sign-in-alt me-2"></i>Sign In
        </a>
        @endauth
    </div>
</nav>

<section class="hero">
    <div class="hero-text">
        <div class="hero-badge">
            <i class="fas fa-star" style="font-size:.7rem;"></i>
            Complete Student Management
        </div>
        <h1 class="hero-title">
            Manage Your Students<br>
            <span>Smarter & Faster</span>
        </h1>
        <p class="hero-sub">
            A unified platform for admins, teachers, students and parents.
            Track grades, attendance, analytics and communications — all in one place.
        </p>
        <div class="hero-actions">
            @auth
            @php
                $dashRoute = match(auth()->user()->role) {
                    'admin'   => 'admin.dashboard',
                    'teacher' => 'teacher.dashboard',
                    'student' => 'student.dashboard',
                    'parent'  => 'parent.dashboard',
                    default   => 'login',
                };
            @endphp
            <a href="{{ route($dashRoute) }}" class="btn-primary-hero">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            @else
            <a href="{{ route('login') }}" class="btn-primary-hero">
                <i class="fas fa-rocket"></i> Get Started
            </a>
            @endauth
            <a href="#features" class="btn-outline-hero">
                <i class="fas fa-play-circle"></i> See Features
            </a>
        </div>
    </div>

    <div class="hero-visual">
        <div class="stat-tile accent">
            <div class="tile-icon"><i class="fas fa-user-graduate"></i></div>
            <div class="tile-val">{{ number_format($count['student']) }}</div>
            <div class="tile-label">Students</div>
        </div>
        <div class="stat-tile" style="background:#f0fdf4;border-color:#bbf7d0;">
            <div class="tile-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="tile-val">{{ number_format($count['teacher']) }}</div>
            <div class="tile-label" style="color:#166534;">Teachers</div>
        </div>
        <div class="stat-tile">
            <div class="tile-icon"><i class="fas fa-users"></i></div>
            <div class="tile-val">{{ number_format($count['parent']) }}</div>
            <div class="tile-label">parents</div>
        </div>
    </div>
</section>

{{-- ── Features ──────────────────────────────────────────────────── --}}
<section class="features" id="features">
    <div class="section-label">Features</div>
    <h2 class="section-title">Everything your student needs</h2>
    <p class="section-sub">Built for all 4 user types with role-based access and dedicated dashboards.</p>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon" style="background:#ede9fe;"><i class="fas fa-chart-bar" style="color:#7c3aed;"></i></div>
            <div class="feature-title">Analytics & Reports</div>
            <div class="feature-desc">Grade distributions, attendance heatmaps, at-risk student alerts and monthly performance trends.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:#d1fae5;"><i class="fas fa-calendar-check" style="color:#059669;"></i></div>
            <div class="feature-title">Attendance Tracking</div>
            <div class="feature-desc">Take attendance per session, auto-notify parents on absence, and view weekly heatmaps.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:#dbeafe;"><i class="fas fa-star" style="color:#2563eb;"></i></div>
            <div class="feature-title">Grade Management</div>
            <div class="feature-desc">Quizzes, midterms and finals. Live grade preview, bulk entry, and per-student reports.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:#fef3c7;"><i class="fas fa-envelope" style="color:#d97706;"></i></div>
            <div class="feature-title">Parent Messaging</div>
            <div class="feature-desc">Parents can message teachers directly, receive absence alerts and follow their child's progress.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:#fee2e2;"><i class="fas fa-bell" style="color:#dc2626;"></i></div>
            <div class="feature-title">Notifications</div>
            <div class="feature-desc">Real-time in-app notifications with mark-as-read and delete. Keeps every user informed instantly.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:#f0fdf4;"><i class="fas fa-shield-alt" style="color:#059669;"></i></div>
            <div class="feature-title">Role-Based Access</div>
            <div class="feature-desc">Admin, Teacher, Student and Parent roles — each with their own scoped dashboard and permissions.</div>
        </div>
    </div>
</section>

{{-- ── Roles ──────────────────────────────────────────────────────── --}}
<section class="roles">
    <div class="roles-inner">
        <div class="section-label">User Roles</div>
        <h2 class="section-title">One system, four experiences</h2>
        <div class="roles-grid">
            <div class="role-card" style="background:#faf9ff;border-color:#ddd6fe;">
                <div class="role-avatar" style="background:#ede9fe;"><i class="fas fa-shield-alt" style="color:#7c3aed;"></i></div>
                <div class="role-name">Admin</div>
                <ul class="role-perms">
                    <li>Manage all users</li>
                    <li>Full analytics</li>
                    <li>Classrooms & subjects</li>
                    <li>System reports</li>
                </ul>
            </div>
            <div class="role-card" style="background:#f0fdf4;border-color:#bbf7d0;">
                <div class="role-avatar" style="background:#d1fae5;"><i class="fas fa-chalkboard-teacher" style="color:#059669;"></i></div>
                <div class="role-name">Teacher</div>
                <ul class="role-perms">
                    <li>Enter grades</li>
                    <li>Take attendance</li>
                    <li>Class reports</li>
                    <li>Message parents</li>
                </ul>
            </div>
            <div class="role-card" style="background:#eff6ff;border-color:#bfdbfe;">
                <div class="role-avatar" style="background:#dbeafe;"><i class="fas fa-user-graduate" style="color:#2563eb;"></i></div>
                <div class="role-name">Student</div>
                <ul class="role-perms">
                    <li>View grades</li>
                    <li>View attendance</li>
                    <li>Class schedule</li>
                    <li>Exam calendar</li>
                </ul>
            </div>
            <div class="role-card" style="background:#fffbeb;border-color:#fed7aa;">
                <div class="role-avatar" style="background:#fef3c7;"><i class="fas fa-users" style="color:#d97706;"></i></div>
                <div class="role-name">Parent</div>
                <ul class="role-perms">
                    <li>Follow child progress</li>
                    <li>Absence alerts</li>
                    <li>Message teachers</li>
                    <li>Multi-child support</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<footer class="site-footer">
    <div class="site-footer-top">
        <div class="footer-brand-col">
            <a href="{{ url('/') }}" class="footer-brand">
                <div class="footer-brand-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span>Centarica</span>
            </a>
            <p class="footer-tagline">
                A unified Student management platform for admins, teachers, students and parents —
                built to make education smarter.
            </p>
            <div class="footer-badges">
                <span class="footer-badge"><i class="fas fa-shield-alt"></i> Secure</span>
                <span class="footer-badge"><i class="fas fa-bolt"></i> Fast</span>
                <span class="footer-badge"><i class="fas fa-mobile-alt"></i> Responsive</span>
            </div>
        </div>

        <div class="footer-links-col">
            <div class="footer-col-title">Quick Links</div>
            <ul class="footer-links">
                <li><a href="{{ url('/') }}"><i class="fas fa-chevron-right"></i> Home</a></li>
                <li>
                    @auth
                    <a href="{{ route($dashRoute) }}"><i class="fas fa-chevron-right"></i>
                        @if((auth()->user()->role) === 'admin')
                        Admin
                        @elseif((auth()->user()->role) === 'teacher')
                        Teacher
                        @elseif((auth()->user()->role) === 'student')
                        Student
                        @elseif((auth()->user()->role) === 'parent')
                        Parent
                        @endif
                        Panel
                    </a>
                    @else
                    <a href="{{ route('login') }}"><i class="fas fa-chevron-right"></i>Log In</a>
                    @endauth
                </li>
            </ul>
        </div>

        <div class="footer-contact-col">
            <div class="footer-col-title">Contact</div>
            <ul class="footer-contact-list">
                <li>
                    <div class="footer-contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <div class="footer-contact-label">Address</div>
                        <div class="footer-contact-value">123 Street, Cairo, Egypt</div>
                    </div>
                </li>
                <li>
                    <div class="footer-contact-icon"><i class="fas fa-phone-alt"></i></div>
                    <div>
                        <div class="footer-contact-label">Phone</div>
                        <div class="footer-contact-value"><a href="tel:+201000000000">+20 100 000 0000</a></div>
                    </div>
                </li>
                <li>
                    <div class="footer-contact-icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="footer-contact-label">Email</div>
                        <div class="footer-contact-value"><a href="mailto:info@gmail.com">info@gmail.com</a></div>
                    </div>
                </li>
                <li>
                    <div class="footer-contact-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="footer-contact-label">Working Hours</div>
                        <div class="footer-contact-value">All The Week | From 8:00 AM – To 4:00 PM</div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="footer-social-col">
            <div class="footer-col-title">Follow Us</div>
            <div class="footer-social-grid">
                <a href="#" class="footer-social-btn facebook" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                    <span>Facebook</span>
                </a>
                <a href="#" class="footer-social-btn twitter" title="X / Twitter">
                    <i class="fab fa-x-twitter"></i>
                    <span>Twitter</span>
                </a>
                <a href="#" class="footer-social-btn instagram" title="Instagram">
                    <i class="fab fa-instagram"></i>
                    <span>Instagram</span>
                </a>
                <a href="#" class="footer-social-btn linkedin" title="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                    <span>LinkedIn</span>
                </a>
                <a href="#" class="footer-social-btn youtube" title="YouTube">
                    <i class="fab fa-youtube"></i>
                    <span>YouTube</span>
                </a>
                <a href="#" class="footer-social-btn whatsapp" title="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
            </div>
            <div class="footer-newsletter">
                <div class="footer-col-title" style="margin-top:20px;">Newsletter</div>
                <p style="font-size:.78rem;color:rgba(255,255,255,.45);margin-bottom:10px;line-height:1.5;">
                    Get Student updates straight to your inbox.
                </p>
                <div class="footer-newsletter-form">
                    <input type="email" placeholder="your@email.com" class="footer-newsletter-input">
                    <button class="footer-newsletter-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="site-footer-bottom">
        <span>© {{ date('Y') }} <strong>Centarica</strong> — Student Management System. All rights reserved.</span>
        <div class="footer-bottom-links">
            <a href="#">Privacy Policy</a>
            <span>·</span>
            <a href="#">Terms of Use</a>
            <span>·</span>
            <a href="#">Support</a>
        </div>
    </div>
</footer>

<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
