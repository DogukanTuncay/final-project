<?php

namespace Database\Seeders;

use App\Models\CourseChapterLesson;
use App\Models\CourseChapterLessonContent;
use App\Models\MultipleChoiceQuestion;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class MultipleChoiceQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $this->command->warn('Admin user not found. Skipping MultipleChoiceQuestionSeeder.');
            return;
        }

        $lesson = CourseChapterLesson::first();
        if (!$lesson) {
            $this->command->warn('No lessons found. Skipping MultipleChoiceQuestionSeeder.');
            return;
        }

        $this->createMultipleChoiceQuestions($lesson, $admin->id);
        $this->command->info('MultipleChoice questions seeded.');
    }

    private function createMultipleChoiceQuestions($lesson, $createdBy)
    {
        $questions = [
            [
                'question' => ['en' => 'What is the capital of France?', 'tr' => 'Fransa\'nın başkenti nedir?'],
                'feedback' => ['en' => 'Paris is the capital city of France.', 'tr' => 'Paris, Fransa\'nın başkentidir.'],
                'points' => 1,
                'is_multiple_answer' => false,
                'options' => [
                    ['text' => ['en' => 'Paris', 'tr' => 'Paris'], 'is_correct' => true],
                    ['text' => ['en' => 'London', 'tr' => 'Londra'], 'is_correct' => false],
                    ['text' => ['en' => 'Berlin', 'tr' => 'Berlin'], 'is_correct' => false],
                    ['text' => ['en' => 'Madrid', 'tr' => 'Madrid'], 'is_correct' => false],
                ]
            ],
            [
                'question' => ['en' => 'Which of the following are programming languages?', 'tr' => 'Aşağıdakilerden hangileri programlama dilleridir?'],
                'feedback' => ['en' => 'PHP, JavaScript and Python are programming languages. HTML is a markup language.', 'tr' => 'PHP, JavaScript ve Python programlama dilleridir. HTML bir işaretleme dilidir.'],
                'points' => 2,
                'is_multiple_answer' => true,
                'options' => [
                    ['text' => ['en' => 'PHP', 'tr' => 'PHP'], 'is_correct' => true],
                    ['text' => ['en' => 'HTML', 'tr' => 'HTML'], 'is_correct' => false],
                    ['text' => ['en' => 'JavaScript', 'tr' => 'JavaScript'], 'is_correct' => true],
                    ['text' => ['en' => 'Python', 'tr' => 'Python'], 'is_correct' => true, 'feedback' => ['en' => 'Correct! Python is versatile.', 'tr' => 'Doğru! Python çok yönlüdür.']],
                ]
            ],
        ];

        foreach ($questions as $index => $qData) {
            try {
                $optionsData = $qData['options'];
                unset($qData['options']); // Remove options before creating the question

                $qData['created_by'] = $createdBy;
                $qData['is_active'] = true;
                $qData['shuffle_options'] = true;

                $question = MultipleChoiceQuestion::create($qData);

                // Create options
                foreach ($optionsData as $oIndex => $optionData) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'text' => $optionData['text'],
                        'is_correct' => $optionData['is_correct'],
                        'feedback' => $optionData['feedback'] ?? null,
                        'order' => $oIndex + 1
                    ]);
                }

                // Add to lesson content
                CourseChapterLessonContent::create([
                    'course_chapter_lesson_id' => $lesson->id,
                    'contentable_id' => $question->id,
                    'contentable_type' => MultipleChoiceQuestion::class,
                    'order' => $index + 1, // Adjust order based on overall content
                    'is_active' => true
                ]);
            } catch (\Exception $e) {
                $this->command->error("Error seeding MultipleChoice question '{$qData['question']['en']}': " . $e->getMessage());
                 Log::error("Error seeding MultipleChoiceQuestion: " . $e->getMessage());
            }
        }
    }
} 