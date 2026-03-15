<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\ClassSubjectController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Teacher\GradeController as TeacherGradeController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\GradeController as StudentGradeController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboard;
use App\Http\Controllers\Parent\GradeController as ParentGradeController;
use App\Http\Controllers\Parent\AttendanceController as ParentAttendanceController;
use App\Http\Controllers\Parent\MessageController as ParentMessageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'count'])->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class,   'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

    Route::get('/users',                          [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create',                   [UserController::class, 'create'])->name('users.create');
    Route::post('/users',                         [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}',                   [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit',              [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}',                   [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}',                [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/restore',            [UserController::class, 'restore'])->name('users.restore');
    Route::patch('/users/{user}/toggle-active',   [UserController::class, 'toggleActive'])->name('users.toggle-active');

    Route::get('/classrooms',                     [ClassroomController::class, 'index'])->name('classrooms.index');
    Route::get('/classrooms/create',              [ClassroomController::class, 'create'])->name('classrooms.create');
    Route::post('/classrooms',                    [ClassroomController::class, 'store'])->name('classrooms.store');
    Route::get('/classrooms/{classroom}',         [ClassroomController::class, 'show'])->name('classrooms.show');
    Route::get('/classrooms/{classroom}/edit',    [ClassroomController::class, 'edit'])->name('classrooms.edit');
    Route::put('/classrooms/{classroom}',         [ClassroomController::class, 'update'])->name('classrooms.update');
    Route::delete('/classrooms/{classroom}',      [ClassroomController::class, 'destroy'])->name('classrooms.destroy');
    Route::post('/classrooms/{classroom}/enroll', [ClassroomController::class, 'enroll'])->name('classrooms.enroll');

    Route::get('/class-subjects/create',         [ClassSubjectController::class, 'create'])->name('class-subjects.create');
    Route::post('/class-subjects',               [ClassSubjectController::class, 'store'])->name('class-subjects.store');
    Route::get('/class-subjects/{classSubject}/edit',   [ClassSubjectController::class, 'edit'])->name('class-subjects.edit');
    Route::put('/class-subjects/{classSubject}',        [ClassSubjectController::class, 'update'])->name('class-subjects.update');
    Route::delete('/class-subjects/{classSubject}',     [ClassSubjectController::class, 'destroy'])->name('class-subjects.destroy');
});

Route::prefix('teacher')->name('teacher.')->middleware(['auth','role:teacher'])->group(function () {
    Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('dashboard');

    Route::get('/grades',                          [TeacherGradeController::class, 'index'])->name('grades.index');
    Route::post('/grades',                         [TeacherGradeController::class, 'store'])->name('grades.store');
    Route::get('/grades/report/{classSubject}',    [TeacherGradeController::class, 'classReport'])->name('grades.report');

    Route::get('/attendance',                      [TeacherAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance',                     [TeacherAttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/summary',             [TeacherAttendanceController::class, 'studentSummary'])->name('attendance.summary');
    Route::get('/attendance/session/{session}',   [TeacherAttendanceController::class, 'sessionDetail'])->name('attendance.session');
});

Route::prefix('student')->name('student.')->middleware(['auth','role:student'])->group(function () {
    Route::get('/dashboard',  [StudentDashboard::class,    'index'])->name('dashboard');
    Route::get('/grades',     [StudentGradeController::class,  'index'])->name('grades.index');
    Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance.index');
});

Route::prefix('parent')->name('parent.')->middleware(['auth','role:parent'])->group(function () {
    Route::get('/dashboard',  [ParentDashboard::class,        'index'])->name('dashboard');
    Route::get('/grades',     [ParentGradeController::class,  'index'])->name('grades.index');
    Route::get('/attendance', [ParentAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/messages',   [ParentMessageController::class, 'index'])->name('messages.index');
    Route::post('/messages',  [ParentMessageController::class, 'store'])->name('messages.store');
});

Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/',              [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read',    [App\Http\Controllers\NotificationController::class, 'markRead'])->name('read');
    Route::post('/read-all',     [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('read-all');
    Route::delete('/{id}',       [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/',           [App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('destroy-all');
});
