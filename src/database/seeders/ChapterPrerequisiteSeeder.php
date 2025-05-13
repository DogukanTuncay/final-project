<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CourseChapter;
use App\Models\Course;

class ChapterPrerequisiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Önceki kayıtları temizle
        DB::table('chapter_prerequisites')->truncate();
        
        $this->command->info('Bölüm ön koşul ilişkileri temizlendi.');
        
        // Tüm kurslar için
        $courses = Course::with('chapters')->get();
        $totalRelations = 0;
        
        foreach ($courses as $courseIndex => $course) {
            $courseName = is_array($course->name) 
                ? (isset($course->name['tr']) ? $course->name['tr'] : reset($course->name)) 
                : $course->name;
                
            $this->command->info(($courseIndex+1) . '. Kurs: ' . $courseName);
            
            // Kursun bölümlerini al
            $chapters = $course->chapters;
            
            if ($chapters->count() > 1) {
                $this->command->info('  Bölüm sayısı: ' . $chapters->count());
                
                // Her bölüm için bir önceki bölümü ön koşul olarak ekle
                for ($i = 1; $i < $chapters->count(); $i++) {
                    $currentChapter = $chapters[$i];
                    $previousChapter = $chapters[$i - 1];
                    
                    $currentName = is_array($currentChapter->name) 
                        ? (isset($currentChapter->name['tr']) ? $currentChapter->name['tr'] : reset($currentChapter->name)) 
                        : $currentChapter->name;
                        
                    $previousName = is_array($previousChapter->name) 
                        ? (isset($previousChapter->name['tr']) ? $previousChapter->name['tr'] : reset($previousChapter->name)) 
                        : $previousChapter->name;
                    
                    // Ön koşul ilişkisini oluştur
                    DB::table('chapter_prerequisites')->insert([
                        'chapter_id' => $currentChapter->id,
                        'prerequisite_chapter_id' => $previousChapter->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $totalRelations++;
                    $this->command->info('      → ' . $currentName . ' için ön koşul: ' . $previousName);
                }
            }
        }
        
        $this->command->info('Toplam ' . $totalRelations . ' bölüm ön koşul ilişkisi oluşturuldu.');
    }
} 