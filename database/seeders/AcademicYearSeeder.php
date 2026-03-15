<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        AcademicYear::insert([
            [
                'name'       => '2023-2024',
                'start_date' => '2023-09-01',
                'end_date'   => '2024-06-30',
                'is_current' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => '2024-2025',
                'start_date' => '2024-09-01',
                'end_date'   => '2025-06-30',
                'is_current' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
