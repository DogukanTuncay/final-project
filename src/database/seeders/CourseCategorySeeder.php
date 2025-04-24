<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CourseCategory;

class CourseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'General English', 
                'slug' => 'general-english',
                'description' => 'Courses covering general English topics.',
                'icon' => 'fas fa-globe', // Örnek FontAwesome ikonu
                'is_active' => true,
            ],
            [
                'name' => 'Business English', 
                'slug' => 'business-english',
                'description' => 'Courses focused on English for business environments.',
                'icon' => 'fas fa-briefcase',
                'is_active' => true,
            ],
            [
                'name' => 'Exam Preparation', 
                'slug' => 'exam-preparation',
                'description' => 'Courses to prepare for English proficiency exams like TOEFL, IELTS.',
                'icon' => 'fas fa-graduation-cap',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $index => $categoryData) {
            CourseCategory::updateOrCreate(
                ['slug' => $categoryData['slug']], // Slug'a göre kontrol et
                array_merge($categoryData, ['order' => $index + 1]) // Order ekle
            );
        }
    }
} 