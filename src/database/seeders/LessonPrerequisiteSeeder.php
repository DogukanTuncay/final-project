<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CourseChapterLesson;
use App\Models\CourseChapter;
use App\Models\Course;

class LessonPrerequisiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      
        
        $this->command->info('Ders ön koşul ilişkileri temizlendi.');
        
        // Tüm kurslar için
        $courses = Course::with('chapters.lessons')->get();
        $totalRelations = 0;
        
        foreach ($courses as $courseIndex => $course) {
            $courseName = is_array($course->name) 
                ? (isset($course->name['tr']) ? $course->name['tr'] : reset($course->name)) 
                : $course->name;
                
            $this->command->info(($courseIndex+1) . '. Kurs: ' . $courseName);
            
            foreach ($course->chapters as $chapterIndex => $chapter) {
                $chapterName = is_array($chapter->name) 
                    ? (isset($chapter->name['tr']) ? $chapter->name['tr'] : reset($chapter->name)) 
                    : $chapter->name;
                    
                $this->command->info('   ' . ($chapterIndex+1) . '. Bölüm: ' . $chapterName);
                
                $lessons = $chapter->lessons()->orderBy('order')->get();
                
                // En az 2 ders yoksa ön koşul oluşturamayız
                if ($lessons->count() < 2) {
                    $this->command->info('      Bu bölümde yeterli ders yok, atlanıyor.');
                    continue;
                }
                
                $this->command->info('      Bu bölümde ' . $lessons->count() . ' ders var.');
                
                // Her ders için bir önceki dersi ön koşul olarak ayarla
                // İlk ders hariç (ilk dersin ön koşulu olmaz)
                for ($i = 1; $i < $lessons->count(); $i++) {
                    $currentLesson = $lessons[$i];
                    $previousLesson = $lessons[$i - 1];
                    
                    $currentName = is_array($currentLesson->name) 
                        ? (isset($currentLesson->name['tr']) ? $currentLesson->name['tr'] : reset($currentLesson->name)) 
                        : $currentLesson->name;
                        
                    $previousName = is_array($previousLesson->name) 
                        ? (isset($previousLesson->name['tr']) ? $previousLesson->name['tr'] : reset($previousLesson->name)) 
                        : $previousLesson->name;
                    
                    // Ön koşul ilişkisini oluştur
                    DB::table('lesson_prerequisites')->insert([
                        'lesson_id' => $currentLesson->id,
                        'prerequisite_lesson_id' => $previousLesson->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $totalRelations++;
                    $this->command->info('      → ' . $currentName . ' için ön koşul: ' . $previousName);
                }
                
                // İleri seviye dersler için ek ön koşullar ekle (opsiyonel)
                // Sadece 5 veya daha fazla ders olan bölümler için
                if ($lessons->count() >= 5) {
                    // Son ders için ilk dersi de ön koşul olarak ekle
                    $lastLesson = $lessons->last();
                    $firstLesson = $lessons->first();
                    
                    // Son dersin kendi ön koşulu kendisi olmasın
                    if ($lastLesson->id !== $firstLesson->id) {
                        // Aynı ön koşul ilişkisi zaten var mı kontrol et
                        $exists = DB::table('lesson_prerequisites')
                            ->where('lesson_id', $lastLesson->id)
                            ->where('prerequisite_lesson_id', $firstLesson->id)
                            ->exists();
                            
                        if (!$exists) {
                            DB::table('lesson_prerequisites')->insert([
                                'lesson_id' => $lastLesson->id,
                                'prerequisite_lesson_id' => $firstLesson->id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                            
                            $lastLessonName = is_array($lastLesson->name) 
                                ? (isset($lastLesson->name['tr']) ? $lastLesson->name['tr'] : reset($lastLesson->name)) 
                                : $lastLesson->name;
                                
                            $firstLessonName = is_array($firstLesson->name) 
                                ? (isset($firstLesson->name['tr']) ? $firstLesson->name['tr'] : reset($firstLesson->name)) 
                                : $firstLesson->name;
                            
                            $totalRelations++;
                            $this->command->info('      → ' . $lastLessonName . ' için ek ön koşul: ' . $firstLessonName);
                        }
                    }
                }
            }
        }
        
        $this->command->info('Toplam ' . $totalRelations . ' ders ön koşul ilişkisi oluşturuldu.');
    }
} 