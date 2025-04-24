<?php

namespace Database\Seeders;

use App\Models\CourseChapterLesson;
use App\Models\CourseChapterLessonContent;
use App\Models\MatchingQuestion;
use App\Models\MatchingPair;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class MatchingQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $this->command->warn('Admin user not found. Skipping MatchingQuestionSeeder.');
            return;
        }

        $lesson = CourseChapterLesson::first();
        if (!$lesson) {
            $this->command->warn('No lessons found. Skipping MatchingQuestionSeeder.');
            return;
        }

        $this->createMatchingQuestions($lesson, $admin->id);
        $this->command->info('Matching questions seeded.');
    }

    private function createMatchingQuestions($lesson, $createdBy)
    {
        $questions = [
            [
                'question' => ['en' => 'Match the framework with its primary language.', 'tr' => 'Çerçeveyi ana diliyle eşleştirin.'],
                'shuffle_items' => true,
                'points' => 4,
                'feedback' => ['en' => 'Match the popular web framework to its language.', 'tr' => 'Popüler web çerçevesini diliyle eşleştirin.'],
                'pairs' => [
                    ['left_item' => ['en' => 'Laravel', 'tr' => 'Laravel'], 'right_item' => ['en' => 'PHP', 'tr' => 'PHP']],
                    ['left_item' => ['en' => 'React', 'tr' => 'React'], 'right_item' => ['en' => 'JavaScript', 'tr' => 'JavaScript']],
                    ['left_item' => ['en' => 'Django', 'tr' => 'Django'], 'right_item' => ['en' => 'Python', 'tr' => 'Python']],
                    ['left_item' => ['en' => 'Ruby on Rails', 'tr' => 'Ruby on Rails'], 'right_item' => ['en' => 'Ruby', 'tr' => 'Ruby']]
                ]
            ],
            [
                'question' => ['en' => 'Match the country with its currency.', 'tr' => 'Ülkeyi para birimiyle eşleştirin.'],
                'shuffle_items' => true,
                'points' => 3,
                'feedback' => ['en' => 'Match the country to its official currency.', 'tr' => 'Ülkeyi resmi para birimiyle eşleştirin.'],
                'pairs' => [
                    ['left_item' => ['en' => 'USA', 'tr' => 'ABD'], 'right_item' => ['en' => 'Dollar', 'tr' => 'Dolar']],
                    ['left_item' => ['en' => 'Japan', 'tr' => 'Japonya'], 'right_item' => ['en' => 'Yen', 'tr' => 'Yen']],
                    ['left_item' => ['en' => 'United Kingdom', 'tr' => 'Birleşik Krallık'], 'right_item' => ['en' => 'Pound Sterling', 'tr' => 'Sterlin']]
                ]
            ],
        ];

        foreach ($questions as $index => $qData) {
            try {
                $pairsData = $qData['pairs'];
                unset($qData['pairs']); // Remove pairs before creating the question

                $qData['created_by'] = $createdBy;
                $qData['is_active'] = true;

                $question = MatchingQuestion::create($qData);

                // Create pairs
                foreach ($pairsData as $pIndex => $pairData) {
                    MatchingPair::create([
                        'matching_question_id' => $question->id,
                        'left_item' => $pairData['left_item'],
                        'right_item' => $pairData['right_item'],
                        'order' => $pIndex + 1
                    ]);
                }

                // Add to lesson content
                CourseChapterLessonContent::create([
                    'course_chapter_lesson_id' => $lesson->id,
                    'contentable_id' => $question->id,
                    'contentable_type' => MatchingQuestion::class,
                    'order' => $index + 1, // Adjust order
                    'is_active' => true
                ]);
            } catch (\Exception $e) {
                $this->command->error("Error seeding Matching question '{$qData['question']['en']}': " . $e->getMessage());
                Log::error("Error seeding MatchingQuestion: " . $e->getMessage());
            }
        }
    }
} 