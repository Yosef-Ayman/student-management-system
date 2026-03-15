# Centarica — Student Management System

A full-stack student management platform built with **Laravel 12** and **MySQL**, designed for admins, teachers, students, and parents.

---

## Tech Stack

| Layer      | Technology   |
|------------|--------------|
| Backend    | Laravel 12   |
| Database   | MySQL        |
| Frontend   | Bootstrap    |
| Charts     | Chart.js     |
| Icons      | Font Awesome |
| Deployment | Render       |

---

## Requirements

- PHP 8.2+
- Composer
- MySQL 8+

---

## Installation

### 1. Clone the project
```bash
git clone https://github.com/Yosef-Ayman/student-management-system student-management-system
cd student-management-system
```

### 2. Install dependencies
```bash
composer install
```

### 3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=teacher_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Create the database
```sql
CREATE DATABASE teacher_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run migrations
```bash
php artisan migrate
```

### 6. Place assets in `public/`

**Bootstrap**
```
public/bootstrap/css/bootstrap.min.css
public/bootstrap/js/bootstrap.bundle.min.js
```

**Font Awesome**
```
public/fontawesome/css/all.min.css
public/fontawesome/webfonts/  (all .woff2 files)
```

**Chart.js**
```
public/ChartJS/chart.umd.min.js
```

### 7. Start the server
```bash
php artisan serve
```

Visit: [http://localhost:XXXX](http://localhost:XXXX)

---

## Demo Accounts

| Role    | Email               | Password   |
|---------|---------------------|------------|
| Admin   | admin@example.com   | admin123   |
| Teacher | teacher@example.com | teacher123 |
| Student | student@example.com | student123 |
| Parent  | parent@example.com  | parent123  |

---

## Grading Scale

| % Range | Grade        |
|---------|--------------|
| ≥ 95%   | Excellent    |
| ≥ 90%   | Very Good    |
| ≥ 80%   | Good         |
| ≥ 70%   | Average Fair |
| ≥ 60%   | Pass         |
| < 60%   | Failure      |

---

## User Roles

**Admin** — Full system control: manage users, classrooms, subjects, analytics and reports.

**Teacher** — Manage assigned classes: enter grades, take attendance, view class reports.

**Student** — Read-only portal: view grades, attendance history, schedule and upcoming exams.

**Parent** — Follow child progress: grades per subject, attendance rate, messages to teachers, absence alerts. Supports multiple children.

---

## Key Features

- Role-based access control (Admin / Teacher / Student / Parent)
- Grade management with exam types (Quiz, Midterm, Final, Assignment)
- Attendance tracking per session with parent auto-notification on absence
- Analytics dashboard with grade distribution, monthly trends, at-risk student detection, class performance heatmaps
- In-app notification system with mark-as-read and delete
- Teacher ↔ Parent messaging
- Admin announcements targeted by audience
- Responsive UI with mobile sidebar and bottom-sheet notifications

---

## Project Structure

```
app/
  Models/              User, Classroom, ClassSubject, Enrollment,
                       Exam, Grade, AttendanceSession, AttendanceRecord,
                       Message, Announcement, AcademicYear ...
  Http/
    Controllers/
      Admin/           Dashboard, User, Classroom, ClassSubject, Analytics
      Teacher/         Dashboard, Grade, Attendance
      Student/         Dashboard, Grade, Attendance
      Parent/          Dashboard, Grade, Attendance, Message
    Middleware/        RoleMiddleware
database/
  migrations/          migration files
resources/views/
  layouts/             app.blade.php, error.blade.php
  admin/               dashboard, analytics, users, classrooms
  teacher/             dashboard, grades, attendance
  student/             dashboard, grades, attendance
  parent/              dashboard, grades, attendance, messages
  errors/              401, 402, 403, 404, 419, 429, 500, 503
public/
  css/                 app.css (global stylesheet)
  bootstrap/           Bootstrap
  fontawesome/         Font Awesome
  ChartJS/             Chart.js
```

---

Author: [Yosef Ayman](https://github.com/Yosef-Ayman)
