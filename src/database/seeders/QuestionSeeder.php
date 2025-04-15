<?php

namespace Database\Seeders;

use App\Models\CourseChapterLesson;
use App\Models\CourseChapterLessonContent;
use App\Models\FillInTheBlank;
use App\Models\MatchingPair;
use App\Models\MatchingQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\QuestionOption;
use App\Models\ShortAnswerQuestion;
use App\Models\TrueFalseQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin kullanıcısını al veya oluştur
        $admin = User::first();
        

        // Bir ders al veya oluştur
        $lesson = CourseChapterLesson::first();
        if (!$lesson) {
            $this->command->info('Ders bulunamadı. Lütfen önce CourseSeeder çalıştırın.');
            return;
        }

        // Çoktan Seçmeli Soru örnekleri oluştur
        $this->createMultipleChoiceQuestions($lesson, $admin->id);
        
        // Doğru/Yanlış Soru örnekleri oluştur
        $this->createTrueFalseQuestions($lesson, $admin->id);
        
        // Kısa Cevap Soru örnekleri oluştur
        $this->createShortAnswerQuestions($lesson, $admin->id);
        
        // Eşleştirme Soru örnekleri oluştur
        $this->createMatchingQuestions($lesson, $admin->id);
        
        // Boşluk Doldurma Soru örnekleri oluştur
        $this->createFillInTheBlankQuestions($lesson, $admin->id);
        
        $this->command->info('Sorular başarıyla oluşturuldu!');
    }

    /**
     * Çoktan Seçmeli Soru örnekleri oluştur
     */
    private function createMultipleChoiceQuestions($lesson, $createdBy)
    {
        // Örnek 1: Tek doğru cevaplı
        $mcq1 = MultipleChoiceQuestion::create([
            'question' => [
                'en' => 'What is the capital of France?',
                'tr' => 'Fransa\'nın başkenti nedir?'
            ],
            'feedback' => [
                'en' => 'Paris is the capital city of France.',
                'tr' => 'Paris, Fransa\'nın başkentidir.'
            ],
            'points' => 1,
            'is_multiple_answer' => false,
            'shuffle_options' => true,
            'created_by' => $createdBy,
            'is_active' => true
        ]);

        // Seçenekler ekle
        QuestionOption::create([
            'question_id' => $mcq1->id,
            'text' => [
                'en' => 'Paris', 
                'tr' => 'Paris'
            ],
            'is_correct' => true,
            'order' => 1
        ]);

        QuestionOption::create([
            'question_id' => $mcq1->id,
            'text' => [
                'en' => 'London',
                'tr' => 'Londra'
            ],
            'is_correct' => false,
            'order' => 2
        ]);

        QuestionOption::create([
            'question_id' => $mcq1->id,
            'text' => [
                'en' => 'Berlin',
                'tr' => 'Berlin'
            ],
            'is_correct' => false,
            'order' => 3
        ]);

        QuestionOption::create([
            'question_id' => $mcq1->id,
            'text' => [
                'en' => 'Madrid',
                'tr' => 'Madrid'
            ],
            'is_correct' => false,
            'order' => 4
        ]);

        // Dersin içeriği olarak ekle
        CourseChapterLessonContent::create([
            'course_chapter_lesson_id' => $lesson->id,
            'contentable_id' => $mcq1->id,
            'contentable_type' => MultipleChoiceQuestion::class,
            'order' => 1,
            'is_active' => true
        ]);

        // Örnek 2: Çoklu doğru cevaplı
        $mcq2 = MultipleChoiceQuestion::create([
            'question' => [
                'en' => 'Which of the following are programming languages?',
                'tr' => 'Aşağıdakilerden hangileri programlama dilleridir?'
            ],
            'feedback' => [
                'en' => 'PHP, JavaScript and Python are programming languages. HTML is a markup language.',
                'tr' => 'PHP, JavaScript ve Python programlama dilleridir. HTML bir işaretleme dilidir.'
            ],
            'points' => 2,
            'is_multiple_answer' => true,
            'shuffle_options' => true,
            'created_by' => $createdBy,
            'is_active' => true
        ]);

        // Seçenekler ekle
        QuestionOption::create([
            'question_id' => $mcq2->id,
            'text' => [
                'en' => 'PHP', 
                'tr' => 'PHP'
            ],
            'is_correct' => true,
            'order' => 1
        ]);

        QuestionOption::create([
            'question_id' => $mcq2->id,
            'text' => [
                'en' => 'HTML',
                'tr' => 'HTML'
            ],
            'is_correct' => false,
            'order' => 2
        ]);

        QuestionOption::create([
            'question_id' => $mcq2->id,
            'text' => [
                'en' => 'JavaScript',
                'tr' => 'JavaScript'
            ],
            'is_correct' => true,
            'order' => 3
        ]);

        QuestionOption::create([
            'question_id' => $mcq2->id,
            'text' => [
                'en' => 'Python',
                'tr' => 'Python'
            ],
            'is_correct' => true,
            'feedback' => [
                'en' => 'Correct! Python is a versatile programming language.',
                'tr' => 'Doğru! Python çok yönlü bir programlama dilidir.'
            ],
            'order' => 4
        ]);

        // Dersin içeriği olarak ekle
        CourseChapterLessonContent::create([
            'course_chapter_lesson_id' => $lesson->id,
            'contentable_id' => $mcq2->id,
            'contentable_type' => MultipleChoiceQuestion::class,
            'order' => 2,
            'is_active' => true
        ]);
    }

    /**
     * Doğru/Yanlış Soru örnekleri oluştur
     */
    private function createTrueFalseQuestions($lesson, $createdBy)
    {
        // Örnek 1
        $tf1 = TrueFalseQuestion::create([
            'question' => [
                'en' => 'The Earth is flat.',
                'tr' => 'Dünya düzdür.'
            ],
            'correct_answer' => false,
            'custom_text' => [
                'true' => [
                    'en' => 'Yes, it is flat',
                    'tr' => 'Evet, düzdür'
                ],
                'false' => [
                    'en' => 'No, it is not flat',
                    'tr' => 'Hayır, düz değildir'
                ]
            ],
            'feedback' => [
                'en' => 'The Earth is an oblate spheroid, not flat.',
                'tr' => 'Dünya basık bir küre şeklindedir, düz değildir.'
            ],
            'points' => 1,
            'created_by' => $createdBy,
            'is_active' => true
        ]);

        CourseChapterLessonContent::create([
            'course_chapter_lesson_id' => $lesson->id,
            'contentable_id' => $tf1->id,
            'contentable_type' => TrueFalseQuestion::class,
            'order' => 3,
            'is_active' => true
        ]);

        // Örnek 2
        $tf2 = TrueFalseQuestion::create([
            'question' => [
                'en' => 'Laravel is a PHP framework.',
                'tr' => 'Laravel bir PHP çerçevesidir.'
            ],
            'correct_answer' => true,
            'feedback' => [
                'en' => 'Yes, Laravel is a popular PHP framework.',
                'tr' => 'Evet, Laravel popüler bir PHP çerçevesidir.'
            ],
            'points' => 1,
            'created_by' => $createdBy,
            'is_active' => true
        ]);

        CourseChapterLessonContent::create([
            'course_chapter_lesson_id' => $lesson->id,
            'contentable_id' => $tf2->id,
            'contentable_type' => TrueFalseQuestion::class,
            'order' => 4,
            'is_active' => true
        ]);
    }

    /**
     * Kısa Cevaplı Soru örnekleri oluştur
     */
    private function createShortAnswerQuestions($lesson, $createdBy)
    {
        // Örnek 1
        $sa1 = ShortAnswerQuestion::create([
            'question' => [
                'en' => 'What is the capital of Turkey?',
                'tr' => 'Türkiye\'nin başkenti nedir?'
            ],
            'allowed_answers' => [
                'en' => ['Ankara', 'ankara'],
                'tr' => ['Ankara', 'ankara']
            ],
            'case_sensitive' => false,
            'max_attempts' => 3,
            'points' => 1,
            'feedback' => [
                'en' => 'The capital of Turkey is Ankara.',
                'tr' => 'Türkiye\'nin başkenti Ankara\'dır.'
            ],
            'created_by' => $createdBy,
            'is_active' => true
        ]);

        CourseChapterLessonContent::create([
            'course_chapter_lesson_id' => $lesson->id,
            'contentable_id' => $sa1->id,
            'contentable_type' => ShortAnswerQuestion::class,
            'order' => 5,
            'is_active' => true
        ]);

        // Örnek 2
        $sa2 = ShortAnswerQuestion::create([
            'question' => [
                'en' => 'What programming language is Laravel based on?',
                'tr' => 'Laravel hangi programlama diline dayanır?'
            ],
            'allowed_answers' => [
                'en' => ['PHP', 'php'],
                'tr' => ['PHP', 'php']
            ],
            'case_sensitive' => false,
            'max_attempts' => 2,
            'points' => 1,
            'feedback' => [
                'en' => 'Laravel is a PHP framework.',
                'tr' => 'Laravel bir PHP çerçevesidir.'
            ],
            'created_by' => $createdBy,
            'is_active' => true
        ]);

        CourseChapterLessonContent::create([
            'course_chapter_lesson_id' => $lesson->id,
            'contentable_id' => $sa2->id,
            'contentable_type' => ShortAnswerQuestion::class,
            'order' => 6,
            'is_active' => true
        ]);
    }

    /**
     * Eşleştirme Soru örnekleri oluştur
     */
    private function createMatchingQuestions($lesson, $createdBy)
    {
        // Örnek 1: Ülkeler ve Başkentler
        $mq1 = MatchingQuestion::create([
            'question' => [
                'en' => 'Match the countries with their capitals.',
                'tr' => 'Ülkeleri başkentleriyle eşleştirin.'
            ],
            'shuffle_items' => true,
            'points' => 4,
            'feedback' => [
                'en' => 'Each country has one capital city.',
                'tr' => 'Her ülkenin bir başkenti vardır.'
            ],
            'created_by' => $createdBy,
            'is_active' => true
        ]);

        // Eşleştirme çiftleri
        $pairs1 = [
            [
                'left_item' => ['en' => 'France', 'tr' => 'Fransa'],
                'right_item' => ['en' => 'Paris', 'tr' => 'Paris']
            ],
            [
                'left_item' => ['en' => 'Germany', 'tr' => 'Almanya'],
                'right_item' => ['en' => 'Berlin', 'tr' => 'Berlin']
            ],
            [
                'left_item' => ['en' => 'Italy', 'tr' => 'İtalya'],
                'right_item' => ['en' => 'Rome', 'tr' => 'Roma']
            ],
            [
                'left_item' => ['en' => 'Spain', 'tr' => 'İspanya'],
                'right_item' => ['en' => 'Madrid', 'tr' => 'Madrid']
            ]
        ];

        foreach ($pairs1 as $index => $pair) {
            MatchingPair::create([
                'matching_question_id' => $mq1->id,
                'left_item' => $pair['left_item'],
                'right_item' => $pair['right_item'],
                'order' => $index + 1
            ]);
        }

        // Dersin içeriği olarak ekle
        CourseChapterLessonContent::create([
            'course_chapter_lesson_id' => $lesson->id,
            'contentable_id' => $mq1->id,
            'contentable_type' => MatchingQuestion::class,
            'order' => 7,
            'is_active' => true
        ]);
        
        // Örnek 2: Programlama Dilleri ve Uygulama Alanları
        $mq2 = MatchingQuestion::create([
            'question' => [
                'en' => 'Match the programming languages with their primary use cases.',
                'tr' => 'Programlama dillerini temel kullanım alanlarıyla eşleştirin.'
            ],
            'shuffle_items' => true,
            'points' => 5,
            'feedback' => [
                'en' => 'Each programming language has specific strengths in different areas.',
                'tr' => 'Her programlama dilinin farklı alanlarda özel güçlü yanları vardır.'
            ],
            'created_by' => $createdBy,
            'is_active' => true
        ]);

        // Eşleştirme çiftleri
        $pairs2 = [
            [
                'left_item' => ['en' => 'JavaScript', 'tr' => 'JavaScript'],
                'right_item' => ['en' => 'Web Development', 'tr' => 'Web Geliştirme']
            ],
            [
                'left_item' => ['en' => 'Python', 'tr' => 'Python'],
                'right_item' => ['en' => 'Data Science', 'tr' => 'Veri Bilimi']
            ],
            [
                'left_item' => ['en' => 'Swift', 'tr' => 'Swift'],
                'right_item' => ['en' => 'iOS Development', 'tr' => 'iOS Geliştirme']
            ],
            [
                'left_item' => ['en' => 'Kotlin', 'tr' => 'Kotlin'],
                'right_item' => ['en' => 'Android Development', 'tr' => 'Android Geliştirme']
            ],
            [
                'left_item' => ['en' => 'PHP', 'tr' => 'PHP'],
                'right_item' => ['en' => 'Backend Web Development', 'tr' => 'Backend Web Geliştirme']
            ]
        ];

        foreach ($pairs2 as $index => $pair) {
            MatchingPair::create([
                'matching_question_id' => $mq2->id,
                'left_item' => $pair['left_item'],
                'right_item' => $pair['right_item'],
                'order' => $index + 1
            ]);
        }

        // Dersin içeriği olarak ekle
        CourseChapterLessonContent::create([
            'course_chapter_lesson_id' => $lesson->id,
            'contentable_id' => $mq2->id,
            'contentable_type' => MatchingQuestion::class,
            'order' => 8,
            'is_active' => true
        ]);
    }

    /**
     * Boşluk Doldurma Soru örnekleri oluştur
     */
    private function createFillInTheBlankQuestions($lesson, $createdBy)
    {
        // Örnek 1
        $fib1 = FillInTheBlank::create([
            'question' => [
                'en' => 'The process of photosynthesis occurs in the [blank] of plants.',
                'tr' => 'Fotosentez süreci bitkilerin [blank] içinde gerçekleşir.'
            ],
            'answers' => [
                'en' => ['chloroplasts', 'chloroplast'],
                'tr' => ['kloroplastlar', 'kloroplast']
            ],
            'points' => 1,
            'feedback' => [
                'en' => 'Chloroplasts are organelles that conduct photosynthesis in plants.',
                'tr' => 'Kloroplastlar, bitkilerde fotosentezi gerçekleştiren organellerdir.'
            ],
            'case_sensitive' => false,
            'created_by' => $createdBy,
            'is_active' => true
        ]);

        CourseChapterLessonContent::create([
            'course_chapter_lesson_id' => $lesson->id,
            'contentable_id' => $fib1->id,
            'contentable_type' => FillInTheBlank::class,
            'order' => 9,
            'is_active' => true
        ]);

        // Örnek 2
        $fib2 = FillInTheBlank::create([
            'question' => [
                'en' => 'HTML stands for [blank] Markup Language.',
                'tr' => 'HTML, [blank] İşaretleme Dili anlamına gelir.'
            ],
            'answers' => [
                'en' => ['Hypertext', 'hyper text', 'hyper-text'],
                'tr' => ['Hiper Metin', 'hipermetin', 'hiper-metin']
            ],
            'points' => 1,
            'feedback' => [
                'en' => 'HTML (HyperText Markup Language) is the standard markup language for documents designed to be displayed in a web browser.',
                'tr' => 'HTML (Hiper Metin İşaretleme Dili), bir web tarayıcısında görüntülenmek üzere tasarlanmış belgeler için standart işaretleme dilidir.'
            ],
            'case_sensitive' => false,
            'created_by' => $createdBy,
            'is_active' => true
        ]);

        CourseChapterLessonContent::create([
            'course_chapter_lesson_id' => $lesson->id,
            'contentable_id' => $fib2->id,
            'contentable_type' => FillInTheBlank::class,
            'order' => 10,
            'is_active' => true
        ]);
    }
} 