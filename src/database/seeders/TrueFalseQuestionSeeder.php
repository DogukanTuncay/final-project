<?php

namespace Database\Seeders;

use App\Models\CourseChapterLesson;
use App\Models\CourseChapterLessonContent;
use App\Models\TrueFalseQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class TrueFalseQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $this->command->warn('Admin user not found. Skipping TrueFalseQuestionSeeder.');
            return;
        }

        $lesson = CourseChapterLesson::first();
        if (!$lesson) {
            $this->command->warn('No lessons found. Skipping TrueFalseQuestionSeeder.');
            return;
        }

        $this->createTrueFalseQuestions($lesson, $admin->id);
        $this->command->info('TrueFalse questions seeded.');
    }

    private function createTrueFalseQuestions($lesson, $createdBy)
    {
        $questions = [
            [
                'question' => ['en' => 'The Earth is flat.', 'tr' => 'Dünya düzdür.'],
                'correct_answer' => false,
                'custom_text' => [
                    'true' => ['en' => 'Yes, it is flat', 'tr' => 'Evet, düzdür'],
                    'false' => ['en' => 'No, it is not flat', 'tr' => 'Hayır, düz değildir']
                ],
                'feedback' => ['en' => 'The Earth is an oblate spheroid.', 'tr' => 'Dünya basık bir küre şeklindedir.'],
                'points' => 1,
            ],
            [
                'question' => ['en' => 'Laravel is a PHP framework.', 'tr' => 'Laravel bir PHP çerçevesidir.'],
                'correct_answer' => true,
                'feedback' => ['en' => 'Yes, Laravel is a popular PHP framework.', 'tr' => 'Evet, Laravel popüler bir PHP çerçevesidir.'],
                'points' => 1,
            ],
        ];

        foreach ($questions as $index => $qData) {
            try {
                $qData['created_by'] = $createdBy;
                $qData['is_active'] = true;

                $question = TrueFalseQuestion::create($qData);

                // Add to lesson content
                CourseChapterLessonContent::create([
                    'course_chapter_lesson_id' => $lesson->id,
                    'contentable_id' => $question->id,
                    'contentable_type' => TrueFalseQuestion::class,
                    'order' => $index + 1, // Adjust order
                    'is_active' => true
                ]);
            } catch (\Exception $e) {
                $this->command->error("Error seeding TrueFalse question '{$qData['question']['en']}': " . $e->getMessage());
                Log::error("Error seeding TrueFalseQuestion: " . $e->getMessage());
            }
        }
    }
} 