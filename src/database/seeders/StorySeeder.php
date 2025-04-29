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
                'content' => 'Bu, başlangıç seviyesindeki öğrenciler için hazırlanmış basit bir hikayedir. Temel kelimeler ve cümleler içerir.',
                'media_url' => 'images/stories/first-day.jpg',
                'is_active' => true,
                'order_column' => 1,
            ],
            [
                'category_name' => 'Başlangıç Hikayeleri',
                'title' => [
                    'tr' => 'Ailem',
                    'en' => 'My Family',
                ],
                'content' => 'Aile üyelerini tanıtmak ve temel aile kavramlarını öğretmek için basit bir hikaye.',
                'media_url' => 'images/stories/family.jpg',
                'is_active' => true,
                'order_column' => 2,
            ],
            [
                'category_name' => 'Orta Seviye Hikayeler',
                'title' => [
                    'tr' => 'Şehir Turu',
                    'en' => 'City Tour',
                ],
                'content' => 'Şehir hayatı, ulaşım, yön tarifleri ve temel alışveriş diyalogları içeren orta seviye bir hikaye.',
                'media_url' => 'images/stories/city-tour.jpg',
                'is_active' => true,
                'order_column' => 1,
            ],
            [
                'category_name' => 'İleri Seviye Hikayeler',
                'title' => [
                    'tr' => 'İş Toplantısı',
                    'en' => 'Business Meeting',
                ],
                'content' => 'İş ortamında kullanılan dil, toplantı kavramları ve profesyonel diyaloglar içeren ileri seviye bir hikaye.',
                'media_url' => 'images/stories/business-meeting.jpg',
                'is_active' => true,
                'order_column' => 1,
            ],
            [
                'category_name' => 'Günlük Konuşmalar',
                'title' => [
                    'tr' => 'Restoranda',
                    'en' => 'At the Restaurant',
                ],
                'content' => 'Restoranda sipariş verme, yemek seçimi ve ödeme yapma ile ilgili günlük konuşmalar içeren bir hikaye.',
                'media_url' => 'images/stories/restaurant.jpg',
                'is_active' => true,
                'order_column' => 1,
            ],
            [
                'category_name' => 'Kültür ve Gelenek',
                'title' => [
                    'tr' => 'Bayram Kutlamaları',
                    'en' => 'Holiday Celebrations',
                ],
                'content' => 'Kültürel bayramlar, gelenekler ve kutlamalar hakkında bilgi veren bir hikaye.',
                'media_url' => 'images/stories/holiday.jpg',
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