<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mission;

class MissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $missions = [
            [
                'title' => [
                    'tr' => 'İlk Görev',
                    'en' => 'First Mission',
                ],
                'description' => [
                    'tr' => 'Bu, oyun içindeki ilk görevdir. Başarılı bir şekilde tamamlanmalıdır.',
                    'en' => 'This is the first mission in the game. It must be completed successfully.',
                ],
                'type' => 'basic',
                'requirements' => [
                    'tr' => ['Kayıt ol', 'Profil bilgilerini tamamla'],
                    'en' => ['Sign up', 'Complete profile information'],
                ],
                'xp_reward' => 100,
                'is_active' => true,
            ],
            [
                'title' => [
                    'tr' => 'İkinci Görev',
                    'en' => 'Second Mission',
                ],
                'description' => [
                    'tr' => 'Bu görevde daha zorlayıcı bir test var. Hedefinize ulaşmalısınız.',
                    'en' => 'This mission involves a more challenging test. You must reach your goal.',
                ],
                'type' => 'intermediate',
                'requirements' => [
                    'tr' => ['İlk görevi tamamla', 'Seviye atla'],
                    'en' => ['Complete the first mission', 'Level up'],
                ],
                'xp_reward' => 200,
                'is_active' => true,
            ],
            [
                'title' => [
                    'tr' => 'Zorlu Görev',
                    'en' => 'Hard Mission',
                ],
                'description' => [
                    'tr' => 'Bu görev çok daha zordur. Deneyimli oyuncular için uygundur.',
                    'en' => 'This mission is much harder. It is for experienced players.',
                ],
                'type' => 'hard',
                'requirements' => [
                    'tr' => ['İkinci görevi tamamla', 'Özel başarımları tamamla'],
                    'en' => ['Complete the second mission', 'Complete special achievements'],
                ],
                'xp_reward' => 500,
                'is_active' => false,
            ],
        ];

        foreach ($missions as $mission) {
            Mission::create($mission);
        }
    }
}
