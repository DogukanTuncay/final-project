<?php

namespace Database\Seeders;

use App\Models\StoryCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => [
                    'tr' => 'Başlangıç Hikayeleri',
                    'en' => 'Beginner Stories',
                ],
                'is_active' => true,
                'order' => 1,
                'image' => 'images/story-categories/beginner.jpg',
            ],
            [
                'name' => [
                    'tr' => 'Orta Seviye Hikayeler',
                    'en' => 'Intermediate Stories',
                ],
                'is_active' => true,
                'order' => 2,
                'image' => 'images/story-categories/intermediate.jpg',
            ],
            [
                'name' => [
                    'tr' => 'İleri Seviye Hikayeler',
                    'en' => 'Advanced Stories',
                ],
                'is_active' => true,
                'order' => 3,
                'image' => 'images/story-categories/advanced.jpg',
            ],
            [
                'name' => [
                    'tr' => 'Günlük Konuşmalar',
                    'en' => 'Daily Conversations',
                ],
                'is_active' => true,
                'order' => 4,
                'image' => 'images/story-categories/conversations.jpg',
            ],
            [
                'name' => [
                    'tr' => 'Kültür ve Gelenek',
                    'en' => 'Culture and Traditions',
                ],
                'is_active' => true,
                'order' => 5,
                'image' => 'images/story-categories/culture.jpg',
            ],
        ];

        foreach ($categories as $category) {
            // Aynı sıra numarasında bir kategori var mı kontrol et
            $existingCategory = StoryCategory::where('order', $category['order'])->first();
            
            if (!$existingCategory) {
                StoryCategory::create($category);
            }
        }
    }
} 