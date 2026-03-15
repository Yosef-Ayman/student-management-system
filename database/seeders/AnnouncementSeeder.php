<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $admin   = User::admins()->first();
        $teacher = User::teachers()->first();

        $announcements = [
            [
                'created_by'   => $admin->id,
                'title'        => 'Welcome to the new academic year 2024-2025',
                'body'         => 'We welcome all students, teachers and parents to the new academic year. We wish everyone a successful and productive year.',
                'audience'     => 'all',
                'is_published' => true,
                'published_at' => now()->subDays(30),
            ],
            [
                'created_by'   => $admin->id,
                'title'        => 'Midterm exam schedule has been published',
                'body'         => 'The midterm exam schedule for all grades is now available. Please check your student portal for your personal schedule.',
                'audience'     => 'students',
                'is_published' => true,
                'published_at' => now()->subDays(10),
            ],
            [
                'created_by'   => $admin->id,
                'title'        => 'Parent-teacher meetings — next Saturday',
                'body'         => 'Parent-teacher meetings will be held next Saturday from 10:00 AM to 2:00 PM. All parents are kindly requested to attend.',
                'audience'     => 'parents',
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'expires_at'   => now()->addDays(7),
            ],
            [
                'created_by'   => $teacher->id,
                'title'        => 'Grade submission reminder',
                'body'         => 'All teachers are reminded to submit midterm grades by end of this week. Please ensure all records are complete and accurate.',
                'audience'     => 'teachers',
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ],
            [
                'created_by'   => $admin->id,
                'title'        => 'Student holiday — National Day',
                'body'         => '',
                'audience'     => 'all',
                'is_published' => true,
                'published_at' => now()->subDay(),
                'expires_at'   => now()->addDays(3),
            ],
        ];

        foreach ($announcements as $ann) {
            Announcement::create($ann);
        }
    }
}
