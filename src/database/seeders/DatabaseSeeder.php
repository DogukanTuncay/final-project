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
        // Önem sırasına göre Seeder'ları çağırın
        $this->call([
            // 1. Temel ve Bağımsız Veriler
            UserSeeder::class,          // Kullanıcılar (Özellikle Admin)
            LevelSeeder::class,         // Seviyeler
            // CourseCategorySeeder::class,// Kurs Kategorileri

            // 2. Kurs Yapısı
            CourseSeeder::class,          // Kurslar (Kategori ve Seviyeye ihtiyaç duyar)
            CourseChapterSeeder::class,     // Bölümler (Kurslara ihtiyaç duyar)
            CourseChapterLessonSeeder::class, // Dersler (Bölümlere ihtiyaç duyar)

            // 3. Soru Tipleri
            // Not: Bu seeder'lar kendi içlerinde LessonContent de oluşturuyor.
            // Eğer LessonContent ayrı yönetilecekse, soru seeder'ları içindeki
            // CourseChapterLessonContent::create kısımları kaldırılıp
            // ayrı bir CourseChapterLessonContentSeeder oluşturulmalı.
            FillInTheBlankSeeder::class, 
            MultipleChoiceQuestionSeeder::class,
            TrueFalseQuestionSeeder::class,
            ShortAnswerQuestionSeeder::class,
            MatchingQuestionSeeder::class,
            
            // 4. İlişkisel ve Diğer Veriler
            LessonPrerequisiteSeeder::class, // Ders ön koşulları (Dersler oluşturulduktan sonra)
            CoursePermissionSeeder::class, // Kurs İzinleri
            MissionSeeder::class,         // Görevler
            // AchievementSeeder::class, // Başarımlar (Eklenecekse)
            
            // 5. Test/Geliştirme Verileri (Sadece Gerekliyse)
            // LessonCompletionTestSeeder::class, // Test tamamlama verileri (Development/Testing için)
        ]);

        // İsteğe bağlı: Test için Factory Kullanımı
        // if (app()->environment('local')) {
        //     User::factory(10)->create();
        // }
    }
}
