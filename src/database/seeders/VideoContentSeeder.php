<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VideoContent;

class VideoContentSeeder extends Seeder
{
    /**
     * H5P video içeriklerini veritabanına ekle
     *
     * @return void
     */
    public function run()
    {
        $h5pVideos = [
            [
                'title' => [
                    'tr' => 'H5P İnteraktif Video Eğitimi',
                    'en' => 'H5P Interactive Video Tutorial'
                ],
                'description' => [
                    'tr' => 'H5P platformunda interaktif video nasıl oluşturulur ve kullanılır.',
                    'en' => 'How to create and use interactive videos in H5P platform.'
                ],
                'video_url' => 'https://www.h5p.org/content/1234567',
                'provider' => 'h5p',
                'duration' => 480, // 8 dakika
                'metadata' => [
                    'author' => 'Eğitim Yöneticisi',
                    'level' => 'başlangıç',
                    'tags' => ['h5p', 'eğitim', 'interaktif']
                ],
                'is_active' => true
            ],
            [
                'title' => [
                    'tr' => 'H5P ile Sürükle & Bırak Aktiviteleri',
                    'en' => 'Drag & Drop Activities with H5P'
                ],
                'description' => [
                    'tr' => 'H5P platformunda sürükle ve bırak aktiviteleri oluşturmayı öğrenin.',
                    'en' => 'Learn how to create drag and drop activities in H5P platform.'
                ],
                'video_url' => 'https://www.h5p.org/content/7654321',
                'provider' => 'h5p',
                'duration' => 360, // 6 dakika
                'metadata' => [
                    'author' => 'Eğitim Tasarımcısı',
                    'level' => 'orta',
                    'tags' => ['h5p', 'sürükle-bırak', 'etkileşimli']
                ],
                'is_active' => true
            ],
            [
                'title' => [
                    'tr' => 'H5P İçeriklerini Dışa Aktarma ve Paylaşma',
                    'en' => 'Exporting and Sharing H5P Contents'
                ],
                'description' => [
                    'tr' => 'H5P içeriklerini farklı platformlarda nasıl paylaşacağınızı öğrenin.',
                    'en' => 'Learn how to share your H5P contents across different platforms.'
                ],
                'video_url' => 'https://www.h5p.org/content/9876543',
                'provider' => 'h5p',
                'duration' => 420, // 7 dakika
                'metadata' => [
                    'author' => 'İçerik Uzmanı',
                    'level' => 'ileri',
                    'tags' => ['h5p', 'paylaşım', 'dışa aktarma']
                ],
                'is_active' => true
            ],
            [
                'title' => [
                    'tr' => 'H5P ile Etkileşimli Sunum Hazırlama',
                    'en' => 'Creating Interactive Presentations with H5P'
                ],
                'description' => [
                    'tr' => 'H5P Course Presentation özelliği ile etkileşimli sunumlar hazırlayın.',
                    'en' => 'Create interactive presentations using H5P Course Presentation feature.'
                ],
                'video_url' => 'https://www.h5p.org/content/5432109',
                'provider' => 'h5p',
                'duration' => 540, // 9 dakika
                'metadata' => [
                    'author' => 'Sunum Uzmanı',
                    'level' => 'orta',
                    'tags' => ['h5p', 'sunum', 'etkileşimli']
                ],
                'is_active' => true
            ],
            [
                'title' => [
                    'tr' => 'H5P Quizler ile Değerlendirme',
                    'en' => 'Assessment with H5P Quizzes'
                ],
                'description' => [
                    'tr' => 'H5P platformunda quiz ve değerlendirme araçlarını kullanma kılavuzu.',
                    'en' => 'Guide for using quiz and assessment tools in H5P platform.'
                ],
                'video_url' => 'https://www.h5p.org/content/2468101',
                'provider' => 'h5p',
                'duration' => 390, // 6.5 dakika
                'metadata' => [
                    'author' => 'Değerlendirme Uzmanı',
                    'level' => 'başlangıç',
                    'tags' => ['h5p', 'quiz', 'değerlendirme']
                ],
                'is_active' => true
            ]
        ];

        foreach ($h5pVideos as $video) {
            VideoContent::create($video);
        }
    }
} 