<?php

namespace Database\Seeders;

use App\Models\CourseChapterLesson;
use App\Models\CourseChapterLessonContent;
use App\Models\ShortAnswerQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ShortAnswerQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $this->command->warn('Admin user not found. Skipping ShortAnswerQuestionSeeder.');
            return;
        }

        $lesson = CourseChapterLesson::first();
        if (!$lesson) {
            $this->command->warn('No lessons found. Skipping ShortAnswerQuestionSeeder.');
            return;
        }

        $this->createShortAnswerQuestions($lesson, $admin->id);
        $this->command->info('ShortAnswer questions seeded.');
    }

    private function createShortAnswerQuestions($lesson, $createdBy)
    {
        $questions = [
            [
                'question' => ['en' => 'What is the main purpose of HTML?', 'tr' => 'HTML\'in ana amacı nedir?'],
                'allowed_answers' => [
                    'en' => ['structure web pages', 'web page structure', 'structure'],
                    'tr' => ['web sayfalarını yapılandırmak', 'web sayfası yapısı', 'yapılandırmak']
                ],
                'case_sensitive' => false,
                'max_attempts' => 3,
                'points' => 1,
                'feedback' => ['en' => 'HTML (HyperText Markup Language) is used to structure content on the web.', 'tr' => 'HTML (Hiper Metin İşaretleme Dili) web üzerindeki içeriği yapılandırmak için kullanılır.'],
            ],
            [
                'question' => ['en' => 'Which company developed the PHP language initially?', 'tr' => 'PHP dilini başlangıçta hangi kişi geliştirdi?'], // Şirket değil kişi
                'allowed_answers' => [
                    'en' => ['Rasmus Lerdorf', 'Lerdorf'],
                    'tr' => ['Rasmus Lerdorf', 'Lerdorf']
                ],
                'case_sensitive' => false,
                'max_attempts' => 2,
                'points' => 1,
                'feedback' => ['en' => 'PHP was originally created by Rasmus Lerdorf in 1994.', 'tr' => 'PHP başlangıçta 1994 yılında Rasmus Lerdorf tarafından oluşturulmuştur.'],
            ],
        ];

        foreach ($questions as $index => $qData) {
            try {
                $qData['created_by'] = $createdBy;
                $qData['is_active'] = true;

                // Convert answers to JSON string if model expects JSON
                // If model casts to array, this step might be optional, 
                // but ensuring it's an array is safer.
                $qData['allowed_answers'] = $qData['allowed_answers']; 

                $question = ShortAnswerQuestion::create($qData);

                // Add to lesson content
                CourseChapterLessonContent::create([
                    'course_chapter_lesson_id' => $lesson->id,
                    'contentable_id' => $question->id,
                    'contentable_type' => ShortAnswerQuestion::class,
                    'order' => $index + 1, // Adjust order
                    'is_active' => true
                ]);
            } catch (\Exception $e) {
                $this->command->error("Error seeding ShortAnswer question '{$qData['question']['en']}': " . $e->getMessage());
                Log::error("Error seeding ShortAnswerQuestion: " . $e->getMessage());
            }
        }
    }
} 