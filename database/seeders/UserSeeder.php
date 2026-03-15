<?php

namespace Database\Seeders;

use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name'       => 'System Admin',
            'email'      => 'admin@gmail.com',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'phone'      => '01000000000',
            'gender'     => 'male',
            'is_active'  => true,
        ]);

        $teachers = [];

        foreach ($teachers as $i => $t) {
            $user = User::create([
                'name'      => $t['name'],
                'email'     => $t['email'],
                'password'  => Hash::make('password'),
                'role'      => 'teacher',
                'gender'    => $t['gender'],
                'phone'     => '0100000' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            TeacherProfile::create([
                'user_id'         => $user->id,
                'employee_code'   => 'TCH' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'qualification'   => 'Bachelor of Education',
                'specialization'  => $t['specialization'],
                'hire_date'       => now()->subYears(rand(1, 10))->toDateString(),
                'salary'          => rand(5000, 15000),
            ]);
        }

        $students = [
            ['name' => 'Sara Ahmed',      'email' => 'sara.ahmed@student.com',      'gender' => 'female',
             'parent_name' => 'Mohamed Ahmed',    'parent_email' => 'mohamed.ahmed@parent.com'],
            ['name' => 'Ali Hassan',      'email' => 'ali.hassan@student.com',      'gender' => 'male',
             'parent_name' => 'Hassan Ibrahim',   'parent_email' => 'hassan.ibrahim@parent.com'],
            ['name' => 'Mona Tarek',      'email' => 'mona.tarek@student.com',      'gender' => 'female',
             'parent_name' => 'Tarek Sayed',      'parent_email' => 'tarek.sayed@parent.com'],
            ['name' => 'Omar Samir',      'email' => 'omar.samir@student.com',      'gender' => 'male',
             'parent_name' => 'Samir Omar',       'parent_email' => 'samir.omar@parent.com'],
            ['name' => 'Rania Nour',      'email' => 'rania.nour@student.com',      'gender' => 'female',
             'parent_name' => 'Nour Fathy',       'parent_email' => 'nour.fathy@parent.com'],
            ['name' => 'Youssef Karim',   'email' => 'youssef.karim@student.com',   'gender' => 'male',
             'parent_name' => 'Karim Mostafa',    'parent_email' => 'karim.mostafa@parent.com'],
            ['name' => 'Dina Mostafa',    'email' => 'dina.mostafa@student.com',    'gender' => 'female',
             'parent_name' => 'Mostafa Ali',      'parent_email' => 'mostafa.ali@parent.com'],
            ['name' => 'Kareem Fathy',    'email' => 'kareem.fathy@student.com',    'gender' => 'male',
             'parent_name' => 'Fathy Karim',      'parent_email' => 'fathy.karim@parent.com'],
            ['name' => 'Layla Ibrahim',   'email' => 'layla.ibrahim@student.com',   'gender' => 'female',
             'parent_name' => 'Ibrahim Hassan',   'parent_email' => 'ibrahim.hassan@parent.com'],
            ['name' => 'Mahmoud Samir',   'email' => 'mahmoud.samir@student.com',   'gender' => 'male',
             'parent_name' => 'Amira Samir',      'parent_email' => 'amira.samir@parent.com'],
        ];

        foreach ($students as $i => $s) {
            $student = User::create([
                'name'      => $s['name'],
                'email'     => $s['email'],
                'password'  => Hash::make('password'),
                'role'      => 'student',
                'gender'    => $s['gender'],
                'phone'     => '0110000' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            StudentProfile::create([
                'user_id'         => $student->id,
                'student_code'    => 'STU' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'enrollment_date' => now()->subYears(rand(1, 3))->toDateString(),
                'status'          => 'active',
            ]);

            $parent = User::create([
                'name'      => $s['parent_name'],
                'email'     => $s['parent_email'],
                'password'  => Hash::make('password'),
                'role'      => 'parent',
                'gender'    => 'male',
                'phone'     => '0120000' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            ParentProfile::create([
                'user_id'    => $parent->id,
                'occupation' => 'Professional',
            ]);

            $parent->children()->attach($student->id, [
                'relation'              => 'father',
                'is_primary'            => true,
                'can_pickup'            => true,
                'receive_notifications' => true,
            ]);
        }
    }
}
