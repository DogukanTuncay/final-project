<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ön tanımlı kurslar

        // Random kurslar
        Course::factory()
            ->count(7)
            ->create();
        
        // Her kategoriden en az bir kurs
        foreach (array_keys(Course::CATEGORIES) as $category) {
            Course::factory()
                ->count(1)
                ->category($category)
                ->featured()
                ->active()
                ->create();
        }
    }
} 