<?php

namespace Database\Seeders;

use App\Models\Story;
use App\Models\StoryCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tüm kategorileri çekip manuel olarak mapping yapalım
        $categories = StoryCategory::all();
        $categoryIds = [];
        
        foreach ($categories as $category) {
            // Translatable alanların doğru şekilde alınması
            $trName = $category->getTranslation('name', 'tr');
            $categoryIds[$trName] = $category->id;
        }

        // Eğer kategoriler yoksa seeder çalıştırılamaz
        if (empty($categoryIds)) {
            $this->command->info('Önce StoryCategory seeder çalıştırılmalıdır!');
            return;
        }

        // Hikayeler için örnek içerikler
        $stories = [
            [
                'category_name' => 'Başlangıç Hikayeleri',
                'title' => [
                    'tr' => 'İlk Gün',
                    'en' => 'First Day',
                ],
                'is_active' => true,
                'order_column' => 1,
            ],
            [
                'category_name' => 'Başlangıç Hikayeleri',
                'title' => [
                    'tr' => 'Ailem',
                    'en' => 'My Family',
                ],
                'is_active' => true,
                'order_column' => 2,
            ],
            [
                'category_name' => 'Orta Seviye Hikayeler',
                'title' => [
                    'tr' => 'Şehir Turu',
                    'en' => 'City Tour',
                ],
                'is_active' => true,
                'order_column' => 1,
            ],
            [
                'category_name' => 'İleri Seviye Hikayeler',
                'title' => [
                    'tr' => 'İş Toplantısı',
                    'en' => 'Business Meeting',
                ],
                'is_active' => true,
                'order_column' => 1,
            ],
            [
                'category_name' => 'Günlük Konuşmalar',
                'title' => [
                    'tr' => 'Restoranda',
                    'en' => 'At the Restaurant',
                ],
                'is_active' => true,
                'order_column' => 1,
            ],
            [
                'category_name' => 'Kültür ve Gelenek',
                'title' => [
                    'tr' => 'Bayram Kutlamaları',
                    'en' => 'Holiday Celebrations',
                ],
                'is_active' => true,
                'order_column' => 1,
            ],
        ];

        foreach ($stories as $storyData) {
            $categoryName = $storyData['category_name'];
            unset($storyData['category_name']);

            // Kategori ID'sini bul
            if (isset($categoryIds[$categoryName])) {
                $storyData['story_category_id'] = $categoryIds[$categoryName];
                
                // Aynı kategori ve sıra numarasında bir hikaye var mı kontrol et
                $existingStory = Story::where('story_category_id', $storyData['story_category_id'])
                                      ->where('order_column', $storyData['order_column'])
                                      ->first();
                
                if (!$existingStory) {
                    Story::create($storyData);
                }
            }
        }
    }
} 