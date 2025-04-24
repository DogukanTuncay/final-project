<?php
namespace Database\Seeders;

use App\Models\CourseChapterLesson;
use App\Models\CourseChapterLessonContent;
use App\Models\FillInTheBlank;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FillInTheBlankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Önce tabloyu temizleyelim (opsiyonel)
        // DB::table('fill_in_the_blanks')->delete(); 
        // Veya truncate (foreign key varsa dikkat)
        // FillInTheBlank::truncate(); 

        // Örnek kullanıcıyı bul veya oluştur (created_by için)
        $user = User::first(); // Varsa ilk kullanıcıyı al
        if (!$user) {
            // Eğer kullanıcı yoksa, bir tane oluşturabilir veya null bırakabiliriz
            // $user = User::factory()->create();
            $userId = null;
        } else {
            $userId = $user->id;
        }

        $questions = [
            [
                'question' => [
                    'en' => 'She ___ (go) to school every day. He ___ (play) soccer on weekends.',
                    'tr' => 'O her gün okula ___ (gitmek). Erkek kardeşim hafta sonları futbol ___ (oynamak).'
                ],
                'answers' => ['goes', 'plays'], // Düz string dizisi
                'points' => 10,
                'feedback' => [
                    'en' => 'Correct forms: goes, plays.',
                    'tr' => 'Doğru formlar: gider, oynar.'
                ],
                'case_sensitive' => false,
                'is_active' => true,
                'created_by' => $userId
            ],
            [
                'question' => [
                    'en' => 'The capital of Turkey is ___. The currency is ___.',
                    'tr' => 'Türkiye\'nin başkenti ___dır. Para birimi ___dır.'
                ],
                'answers' => ['Ankara', 'Turkish Lira'],
                'points' => 5,
                'feedback' => [
                    'en' => 'Ankara is the capital and Turkish Lira is the currency.',
                    'tr' => 'Başkent Ankara ve para birimi Türk Lirasıdır.'
                ],
                'case_sensitive' => true, // Bu örnekte büyük/küçük harf duyarlı
                'is_active' => true,
                'created_by' => $userId
            ],
            [
                'question' => [
                    'en' => 'Water boils at ___ degrees Celsius.',
                    'tr' => 'Su ___ santigrat derecede kaynar.'
                ],
                'answers' => ['100'],
                'points' => 3,
                'feedback' => null, // Feedback olmayabilir
                'case_sensitive' => false,
                'is_active' => false, // Pasif örnek
                'created_by' => $userId
            ],
        ];

        foreach ($questions as $qData) {
            try {
                // dump($qData); // Debug için gerekirse
                FillInTheBlank::create($qData);
            } catch (\Exception $e) {
                $this->command->error('Error seeding FillInTheBlank: ' . $e->getMessage());
                // Hatalı veriyi loglayabilir veya gösterebiliriz
                // dump($qData);
            }
        }

        $this->command->info('FillInTheBlankSeeder run successfully.');
    }
}
