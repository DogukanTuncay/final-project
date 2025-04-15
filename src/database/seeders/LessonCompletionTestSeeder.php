<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CourseChapterLesson;
use App\Models\Course;
use App\Models\User;

class LessonCompletionTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Test kullanıcısını bul veya oluştur
        $user = User::first();
        $this->command->info($user->name);
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now()
            ]);
            
            $this->command->info('Test kullanıcısı oluşturuldu: ' . $user->email);
        } else {
            $this->command->info('Mevcut test kullanıcısı kullanılacak: ' . $user->email);
        }
        
        // İlk kursu bul
        $course = Course::first();
        
        if (!$course) {
            $this->command->error('Kurs bulunamadı. Önce kurs seederını çalıştırın.');
            return;
        }
        
        // Kursun ilk bölümünü bul
        $chapter = $course->chapters()->first();
        
        if (!$chapter) {
            $this->command->error('Bölüm bulunamadı. Önce bölüm seederını çalıştırın.');
            return;
        }
        
        // Bölümün derslerini bul
        $lessons = $chapter->lessons()->orderBy('order')->get();
        
        if ($lessons->isEmpty()) {
            $this->command->error('Ders bulunamadı. Önce ders seederını çalıştırın.');
            return;
        }
        
        // Verileri temizle (aynı kullanıcı için)
        DB::table('lesson_completions')->where('user_id', $user->id)->delete();
        $this->command->info('Eski tamamlama kayıtları temizlendi.');
        
        // İlk derslerin yarısını tamamla (böylece bazı dersler kilitli olacak)
        $lessonsToComplete = max(1, ceil($lessons->count() / 2) - 1); // En az 1, en fazla yarı-1
        
        $this->command->info('Toplam ders sayısı: ' . $lessons->count());
        $this->command->info('Tamamlanacak ders sayısı: ' . $lessonsToComplete);
        
        // Tamamlanan derslerin listesi
        $completedLessons = [];
        
        for ($i = 0; $i < $lessonsToComplete; $i++) {
            $lesson = $lessons[$i];
            
            DB::table('lesson_completions')->insert([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'completed_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $lessonName = is_array($lesson->name) 
                ? (isset($lesson->name['tr']) ? $lesson->name['tr'] : reset($lesson->name)) 
                : $lesson->name;
                
            $completedLessons[] = $lesson->id . ' - ' . $lessonName;
            $this->command->info($i+1 . '. ' . $lessonName . ' dersi tamamlandı');
        }
        
        // Ön koşul ilişkilerini göster
        $this->command->info('Ön koşul ilişkileri:');
        
        foreach ($lessons as $index => $lesson) {
            if ($index < 2) continue; // İlk 2 dersin ön koşulu olmayacak
            
            $prerequisiteCount = $lesson->prerequisites()->count();
            
            if ($prerequisiteCount > 0) {
                $lessonName = is_array($lesson->name) 
                    ? (isset($lesson->name['tr']) ? $lesson->name['tr'] : reset($lesson->name)) 
                    : $lesson->name;
                    
                $this->command->info($lesson->id . ' - ' . $lessonName . ' dersinin ' . $prerequisiteCount . ' ön koşulu var:');
                
                $lesson->prerequisites()->get()->each(function ($prerequisite) {
                    $preName = is_array($prerequisite->name) 
                        ? (isset($prerequisite->name['tr']) ? $prerequisite->name['tr'] : reset($prerequisite->name)) 
                        : $prerequisite->name;
                        
                    $this->command->info('   → ' . $prerequisite->id . ' - ' . $preName);
                });
            }
        }
        
        $this->command->info('-----------------------------------------');
        $this->command->info('Test kullanıcısı: ' . $user->email . ' (şifre: password)');
        $this->command->info('Tamamlanan dersler: ' . count($completedLessons));
        foreach ($completedLessons as $index => $lesson) {
            $this->command->info(($index+1) . '. ' . $lesson);
        }
        
        // Kilitli olması gereken dersleri göster
        $this->command->info('Kilitli olması gereken dersler (test için):');
        for ($i = $lessonsToComplete; $i < $lessons->count(); $i++) {
            $lesson = $lessons[$i];
            $lessonName = is_array($lesson->name) 
                ? (isset($lesson->name['tr']) ? $lesson->name['tr'] : reset($lesson->name)) 
                : $lesson->name;
                
            $this->command->info($lesson->id . ' - ' . $lessonName);
        }
    }
} 