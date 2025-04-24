<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'level_number' => 1,
                'title' => [
                    'tr' => 'Başlangıç',
                    'en' => 'Beginner'
                ],
                'description' => [
                    'tr' => 'İslami eğitim yolculuğunuza yeni başladınız.',
                    'en' => 'You have just started your Islamic education journey.'
                ],
                'min_xp' => 0,
                'max_xp' => 100,
                'icon' => 'images/levels/beginner.png',
                'color_code' => '#4CAF50',
                'is_active' => true
            ],
            [
                'level_number' => 2,
                'title' => [
                    'tr' => 'Öğrenci',
                    'en' => 'Student'
                ],
                'description' => [
                    'tr' => 'Temel bilgileri öğrenmeye başladınız.',
                    'en' => 'You have started learning the basics.'
                ],
                'min_xp' => 100,
                'max_xp' => 300,
                'icon' => 'images/levels/student.png',
                'color_code' => '#2196F3',
                'is_active' => true
            ],
            [
                'level_number' => 3,
                'title' => [
                    'tr' => 'Arayıcı',
                    'en' => 'Seeker'
                ],
                'description' => [
                    'tr' => 'İslami bilgiler üzerinde derinleşmeye başladınız.',
                    'en' => 'You have started to deepen your Islamic knowledge.'
                ],
                'min_xp' => 300,
                'max_xp' => 600,
                'icon' => 'images/levels/seeker.png',
                'color_code' => '#FF9800',
                'is_active' => true
            ],
            [
                'level_number' => 4,
                'title' => [
                    'tr' => 'Bilge',
                    'en' => 'Scholar'
                ],
                'description' => [
                    'tr' => 'Artık temel İslami bilgilere hakimsiniz.',
                    'en' => 'You now have mastered the basics of Islamic knowledge.'
                ],
                'min_xp' => 600,
                'max_xp' => 1000,
                'icon' => 'images/levels/scholar.png',
                'color_code' => '#9C27B0',
                'is_active' => true
            ],
            [
                'level_number' => 5,
                'title' => [
                    'tr' => 'Alim',
                    'en' => 'Master'
                ],
                'description' => [
                    'tr' => 'İslami bilgi konusunda üst seviyeye ulaştınız.',
                    'en' => 'You have reached an advanced level of Islamic knowledge.'
                ],
                'min_xp' => 1000,
                'max_xp' => 2000,
                'icon' => 'images/levels/master.png',
                'color_code' => '#E91E63',
                'is_active' => true
            ]
        ];

       // Mevcut son seviyeyi al
$last = end($levels);
$minXp = $last['max_xp'];
$xpStep = $last['max_xp'] - $last['min_xp'];
$levelNumber = 6;

for ($i = 0; $i < 45; $i++) {
    $xpStep = (int)($xpStep * 1.25);
    $maxXp = $minXp + $xpStep;

    $levels[] = [
        'level_number' => $levelNumber,
        'title' => [
            'tr' => "Seviye $levelNumber",
            'en' => "Level $levelNumber"
        ],
        'description' => [
            'tr' => "Seviye $levelNumber için açıklama.",
            'en' => "Description for level $levelNumber."
        ],
        'min_xp' => $minXp,
        'max_xp' => $maxXp,
        'icon' => null,
        'color_code' => null,
        'is_active' => true
    ];

    $minXp = $maxXp;
    $levelNumber++;
}

// Kaydet
foreach ($levels as $level) {
    Level::updateOrCreate(
        ['level_number' => $level['level_number']],
        $level
    );
}

    }
} 