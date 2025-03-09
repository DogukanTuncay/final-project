<?php

namespace Database\Seeders;

use App\Models\CourseChapter;
use App\Models\CourseChapterLesson;
use Illuminate\Database\Seeder;

class CourseChapterLessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Her bölüm için dersler oluştur
        $chapters = CourseChapter::all();
        
        foreach ($chapters as $chapter) {
            // Bölüme bağlı olarak ders sayısını belirle
            $lessonCount = rand(3, 7);
            
            // Özel bölümler için özel dersler
            if ($chapter->course->slug === 'basic-principles-of-islam' && $chapter->slug === 'principles-of-faith') {
                $this->seedFaithLessons($chapter->id);
            } elseif ($chapter->course->slug === 'reading-understanding-quran' && $chapter->slug === 'arabic-alphabet-and-letters') {
                $this->seedArabicAlphabetLessons($chapter->id);
            } elseif ($chapter->course->slug === 'life-of-prophet-muhammad' && $chapter->slug === 'meccan-period') {
                $this->seedMeccanPeriodLessons($chapter->id);
            } else {
                // Diğer bölümler için rastgele dersler
                CourseChapterLesson::factory()
                    ->count($lessonCount)
                    ->forChapter($chapter->id)
                    ->create();
            }
        }
    }
    
    /**
     * İman Esasları bölümü için dersler
     */
    private function seedFaithLessons(int $chapterId): void
    {
        $lessons = [
            [
                'name' => [
                    'tr' => 'Allah\'a İman',
                    'en' => 'Faith in Allah'
                ],
                'description' => [
                    'tr' => 'İslam\'da Allah\'a imanın önemi ve anlamı',
                    'en' => 'Importance and meaning of faith in Allah in Islam'
                ],
                'order' => 1,
                'is_active' => true,
                'duration' => 1800
            ],
            [
                'name' => [
                    'tr' => 'Meleklere İman',
                    'en' => 'Faith in Angels'
                ],
                'description' => [
                    'tr' => 'Meleklerin özellikleri ve görevleri',
                    'en' => 'Characteristics and duties of angels'
                ],
                'order' => 2,
                'is_active' => true,
                'duration' => 1500
            ],
            [
                'name' => [
                    'tr' => 'Kitaplara İman',
                    'en' => 'Faith in Holy Books'
                ],
                'description' => [
                    'tr' => 'Kur\'an ve diğer kutsal kitapların önemi',
                    'en' => 'The importance of the Quran and other holy books'
                ],
                'order' => 3,
                'is_active' => true,
                'duration' => 900
            ],
            [
                'name' => [
                    'tr' => 'Peygamberlere İman',
                    'en' => 'Faith in Prophets'
                ],
                'description' => [
                    'tr' => 'Peygamberlerin görevleri ve özellikleri',
                    'en' => 'The roles and characteristics of prophets'
                ],
                'order' => 4,
                'is_active' => true,
                'duration' => 1600
            ],
            [
                'name' => [
                    'tr' => 'Ahiret Gününe İman',
                    'en' => 'Faith in the Day of Judgment'
                ],
                'description' => [
                    'tr' => 'Ahiret inancı ve önemi',
                    'en' => 'Belief in the afterlife and its importance'
                ],
                'order' => 5,
                'is_active' => true,
                'duration' => 1700
            ],
            [
                'name' => [
                    'tr' => 'Kader ve Kazaya İman',
                    'en' => 'Faith in Destiny and Decree'
                ],
                'description' => [
                    'tr' => 'Kader ve kaza kavramları',
                    'en' => 'Concepts of destiny and decree'
                ],
                'order' => 6,
                'is_active' => true,
                'duration' => 600
            ]
        ];
        
        foreach ($lessons as $lessonData) {
            $lessonData['course_chapter_id'] = $chapterId;
            $lessonData['slug'] = \Illuminate\Support\Str::slug($lessonData['name']['en']);
            $lessonData['thumbnail'] = 'images/coursechapterlesson/' . $lessonData['slug'] . '.jpg';
            CourseChapterLesson::create($lessonData);
        }
    }
    
    /**
     * Arapça Alfabesi ve Harfler bölümü için dersler
     */
    private function seedArabicAlphabetLessons(int $chapterId): void
    {
        $lessons = [
            [
                'name' => [
                    'tr' => 'Arapça Harflerin Tanıtımı',
                    'en' => 'Introduction to Arabic Letters'
                ],
                'description' => [
                    'tr' => 'Arapça alfabesindeki 28 harfin temel tanıtımı',
                    'en' => 'Basic introduction to the 28 letters in the Arabic alphabet'
                ],
                'order' => 1,
                'is_active' => true,
                'duration' => 1200
            ],
            [
                'name' => [
                    'tr' => 'Harflerin Yazılışı',
                    'en' => 'Writing the Letters'
                ],
                'description' => [
                    'tr' => 'Arapça harflerin farklı pozisyonlarda yazılışı',
                    'en' => 'Writing Arabic letters in different positions'
                ],
                'order' => 2,
                'is_active' => true,
                'duration' => 1400
            ],
            [
                'name' => [
                    'tr' => 'Harflerin Telaffuzu',
                    'en' => 'Pronunciation of Letters'
                ],
                'description' => [
                    'tr' => 'Arapça harflerin doğru telaffuzu',
                    'en' => 'Correct pronunciation of Arabic letters'
                ],
                'order' => 3,
                'is_active' => true,
                'duration' => 1600
            ],
            [
                'name' => [
                    'tr' => 'Harflerin Birleştirilmesi',
                    'en' => 'Connecting the Letters'
                ],
                'description' => [
                    'tr' => 'Arapça harflerin birbiriyle birleştirilmesi',
                    'en' => 'Connecting Arabic letters together'
                ],
                'order' => 4,
                'is_active' => true,
                'duration' => 800
            ],
            [
                'name' => [
                    'tr' => 'Harekelerin Tanıtımı',
                    'en' => 'Introduction to Diacritical Marks'
                ],
                'description' => [
                    'tr' => 'Arapça harflerin okunuşunu belirleyen harekeler',
                    'en' => 'Diacritical marks that determine the pronunciation of Arabic letters'
                ],
                'order' => 5,
                'is_active' => true,
                'duration' => 600
            ]
        ];
        
        foreach ($lessons as $lessonData) {
            $lessonData['course_chapter_id'] = $chapterId;
            $lessonData['slug'] = \Illuminate\Support\Str::slug($lessonData['name']['en']);
            $lessonData['thumbnail'] = 'images/coursechapterlesson/' . $lessonData['slug'] . '.jpg';
            CourseChapterLesson::create($lessonData);
        }
    }
    
    /**
     * Mekke Dönemi bölümü için dersler
     */
    private function seedMeccanPeriodLessons(int $chapterId): void
    {
        $lessons = [
            [
                'name' => [
                    'tr' => 'Doğum ve Çocukluk',
                    'en' => 'Birth and Childhood'
                ],
                'description' => [
                    'tr' => 'Hz. Muhammed\'in doğumu ve çocukluk yılları',
                    'en' => 'Birth and childhood years of Prophet Muhammad'
                ],
                'order' => 1,
                'is_active' => true,
                'duration' => 1500
            ],
            [
                'name' => [
                    'tr' => 'Gençlik ve Evlilik',
                    'en' => 'Youth and Marriage'
                ],
                'description' => [
                    'tr' => 'Hz. Muhammed\'in gençlik dönemi ve Hz. Hatice ile evliliği',
                    'en' => 'Youth of Prophet Muhammad and his marriage to Khadijah'
                ],
                'order' => 2,
                'is_active' => true,
                'duration' => 900
            ],
            [
                'name' => [
                    'tr' => 'İlk Vahiy',
                    'en' => 'First Revelation'
                ],
                'description' => [
                    'tr' => 'Hira Mağarası\'nda ilk vahyin gelişi',
                    'en' => 'The first revelation in the Cave of Hira'
                ],
                'order' => 3,
                'is_active' => true,
                'duration' => 1800
            ],
            [
                'name' => [
                    'tr' => 'İlk Müslümanlar',
                    'en' => 'First Muslims'
                ],
                'description' => [
                    'tr' => 'İslam\'ı ilk kabul eden kişiler',
                    'en' => 'The first people to accept Islam'
                ],
                'order' => 4,
                'is_active' => true,
                'duration' => 1600
            ],
            [
                'name' => [
                    'tr' => 'Mekke\'de Yaşanan Zorluklar',
                    'en' => 'Hardships in Mecca'
                ],
                'description' => [
                    'tr' => 'Mekke döneminde Müslümanların karşılaştığı zorluklar',
                    'en' => 'Hardships faced by Muslims during the Meccan period'
                ],
                'order' => 5,
                'is_active' => true,
                'duration' => 600
            ]
        ];
        
        foreach ($lessons as $lessonData) {
            $lessonData['course_chapter_id'] = $chapterId;
            $lessonData['slug'] = \Illuminate\Support\Str::slug($lessonData['name']['en']);
            $lessonData['thumbnail'] = 'images/coursechapterlesson/' . $lessonData['slug'] . '.jpg';
            CourseChapterLesson::create($lessonData);
        }
    }
} 