<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseChapter;
use Illuminate\Database\Seeder;

class CourseChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Her kurs için bölümler oluştur
        $courses = Course::all();
        
        foreach ($courses as $course) {
            // Bazı özel kurslar için ön tanımlı bölümler
            if ($course->slug === 'basic-principles-of-islam') {
                $this->seedBasicPrinciplesChapters($course->id);
            } elseif ($course->slug === 'reading-understanding-quran') {
                $this->seedQuranChapters($course->id);
            } elseif ($course->slug === 'life-of-prophet-muhammad') {
                $this->seedProphetLifeChapters($course->id);
            } else {
                // Diğer kurslar için rastgele bölümler
                $chapterCount = rand(3, 8);
                CourseChapter::factory()
                    ->count($chapterCount)
                    ->forCourse($course->id)
                    ->create();
            }
        }
    }
    
    /**
     * İslam'ın Temel İlkeleri kursu için bölümler
     */
    private function seedBasicPrinciplesChapters(int $courseId): void
    {
        $chapters = [
            [
                'name' => [
                    'tr' => 'İman Esasları',
                    'en' => 'Principles of Faith'
                ],
                'order' => 1,
                'is_active' => true
            ],
            [
                'name' => [
                    'tr' => 'İslam\'ın Şartları',
                    'en' => 'Pillars of Islam'
                ],
                'order' => 2,
                'is_active' => true
            ],
            [
                'name' => [
                    'tr' => 'İbadetler',
                    'en' => 'Worship Practices'
                ],
                'order' => 3,
                'is_active' => true
            ],
            [
                'name' => [
                    'tr' => 'Ahlak ve Davranışlar',
                    'en' => 'Ethics and Behaviors'
                ],
                'order' => 4,
                'is_active' => true
            ]
        ];
        
        foreach ($chapters as $chapterData) {
            $chapterData['course_id'] = $courseId;
            $chapterData['slug'] = \Illuminate\Support\Str::slug($chapterData['name']['en']);
            CourseChapter::create($chapterData);
        }
    }
    
    /**
     * Kur'an Okuma ve Anlama kursu için bölümler
     */
    private function seedQuranChapters(int $courseId): void
    {
        $chapters = [
            [
                'name' => [
                    'tr' => 'Arapça Alfabesi ve Harfler',
                    'en' => 'Arabic Alphabet and Letters'
                ],
                'order' => 1,
                'is_active' => true
            ],
            [
                'name' => [
                    'tr' => 'Tecvid Kuralları',
                    'en' => 'Tajweed Rules'
                ],
                'order' => 2,
                'is_active' => true
            ],
            [
                'name' => [
                    'tr' => 'Kısa Surelerin Okunması',
                    'en' => 'Reading Short Surahs'
                ],
                'order' => 3,
                'is_active' => true
            ],
            [
                'name' => [
                    'tr' => 'Meal ve Tefsir',
                    'en' => 'Translation and Interpretation'
                ],
                'order' => 4,
                'is_active' => true
            ],
            [
                'name' => [
                    'tr' => 'Kur\'an\'ın Mesajı',
                    'en' => 'Message of the Quran'
                ],
                'order' => 5,
                'is_active' => true
            ]
        ];
        
        foreach ($chapters as $chapterData) {
            $chapterData['course_id'] = $courseId;
            $chapterData['slug'] = \Illuminate\Support\Str::slug($chapterData['name']['en']);
            CourseChapter::create($chapterData);
        }
    }
    
    /**
     * Hz. Muhammed'in Hayatı kursu için bölümler
     */
    private function seedProphetLifeChapters(int $courseId): void
    {
        $chapters = [
            [
                'name' => [
                    'tr' => 'Mekke Dönemi',
                    'en' => 'Meccan Period'
                ],
                'order' => 1,
                'is_active' => true
            ],
            [
                'name' => [
                    'tr' => 'Medine Dönemi',
                    'en' => 'Medinan Period'
                ],
                'order' => 2,
                'is_active' => true
            ],
            [
                'name' => [
                    'tr' => 'Ahlaki Özellikleri',
                    'en' => 'Moral Characteristics'
                ],
                'order' => 3,
                'is_active' => true
            ],
            [
                'name' => [
                    'tr' => 'Öğretileri ve Hadisler',
                    'en' => 'Teachings and Hadiths'
                ],
                'order' => 4,
                'is_active' => true
            ]
        ];
        
        foreach ($chapters as $chapterData) {
            $chapterData['course_id'] = $courseId;
            $chapterData['slug'] = \Illuminate\Support\Str::slug($chapterData['name']['en']);
            CourseChapter::create($chapterData);
        }
    }
} 