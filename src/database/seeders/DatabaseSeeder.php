<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

       

        // Diğer seederlar burada çağrılabilir

        // Kurs, bölüm ve dersler için seederları çağır
        $this->call([
            // Temel veriler önce
            LevelSeeder::class, // Level'lar diğer modelleri ilgilendirdiği için ilk sıralarda olmalı
            CourseSeeder::class,
            CourseChapterSeeder::class,
            CourseChapterLessonSeeder::class,
            LessonPrerequisiteSeeder::class,
            LessonCompletionTestSeeder::class,
            QuestionSeeder::class, // Soru modelleri için seeder
        ]);
    }
}
