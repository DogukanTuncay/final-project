<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mission;
use App\Models\Course;
use App\Models\CourseChapter;
use App\Events\LessonCompleted;
use App\Events\CourseCompleted;
use App\Events\ChapterCompleted;

class MissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Örnek: Genel Ders Tamamlama Görevi (5 ders)
        Mission::updateOrCreate(
            [
                'title->en' => 'Complete 5 Lessons'
            ],
            [
                'title' => ['en' => 'Complete 5 Lessons', 'tr' => '5 Dersi Tamamla'],
                'description' => ['en' => 'Finish any 5 lessons in the platform.', 'tr' => 'Platformdaki herhangi 5 dersi bitir.'],
                'type' => 'one_time',
                'xp_reward' => 50,
                'is_active' => true,
                'required_amount' => 5,
                'trigger_event' => LessonCompleted::class,
                'completable_type' => null,
                'completable_id' => null,
                'requirements' => ['type' => 'lesson_completion', 'value' => 5],
            ]
        );

        // Örnek: Spesifik Kurs Tamamlama Görevi (ID: 1 varsayılıyor)
        Mission::updateOrCreate(
            ['title->en' => 'Complete the Introduction Course'],
            [
                'title' => ['en' => 'Complete the Introduction Course', 'tr' => 'Giriş Kursunu Tamamla'],
                'description' => ['en' => 'Finish all chapters and lessons in the Introduction course.', 'tr' => 'Giriş kursundaki tüm bölümleri ve dersleri bitir.'],
                'type' => 'one_time',
                'xp_reward' => 150,
                'is_active' => true,
                'required_amount' => 1,
                'trigger_event' => CourseCompleted::class,
                'completable_type' => Course::class,
                'completable_id' => 1,
                'requirements' => ['type' => 'course_completion', 'value' => 1],
            ]
        );

        // Örnek: Spesifik Bölüm Tamamlama Görevi (ID: 5 varsayılıyor)
        Mission::updateOrCreate(
            ['title->en' => 'Master the First Chapter'],
            [
                'title' => ['en' => 'Master the First Chapter', 'tr' => 'İlk Bölümde Ustalaş'],
                'description' => ['en' => 'Complete all lessons in the first chapter of the Intro course.', 'tr' => 'Giriş kursunun ilk bölümündeki tüm dersleri tamamla.'],
                'type' => 'one_time',
                'xp_reward' => 75,
                'is_active' => true,
                'required_amount' => 1,
                'trigger_event' => ChapterCompleted::class,
                'completable_type' => CourseChapter::class,
                'completable_id' => 5,
                'requirements' => ['type' => 'chapter_completion', 'value' => 5],
            ]
        );

        // Örnek: Günlük Görev (Örn: 1 ders tamamla)
        Mission::updateOrCreate(
            ['title->en' => 'Daily Lesson'],
            [
                'title' => ['en' => 'Daily Lesson', 'tr' => 'Günlük Ders'],
                'description' => ['en' => 'Complete at least one lesson today.', 'tr' => 'Bugün en az bir ders tamamla.'],
                'type' => 'daily',
                'xp_reward' => 20,
                'is_active' => true,
                'required_amount' => 1,
                'trigger_event' => LessonCompleted::class,
                'completable_type' => null,
                'completable_id' => null,
                'requirements' => ['type' => 'daily_lesson', 'value' => 1],
            ]
        );
    }
}
