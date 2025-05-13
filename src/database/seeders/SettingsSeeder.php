<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Site Ayarları
        $this->createSiteSettings();
        
        // Mobil Uygulama Ayarları
        $this->createMobileSettings();
        
        // Sistem Ayarları
        $this->createSystemSettings();
    }
    
    /**
     * Site ayarlarını oluşturur
     */
    private function createSiteSettings(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => [
                    'tr' => 'Davah Uygulaması',
                    'en' => 'Davah App'
                ],
                'type' => 'text',
                'group' => 'site',
                'description' => [
                    'tr' => 'Site adı',
                    'en' => 'Site name'
                ],
                'is_translatable' => true
            ],
            [
                'key' => 'site_description',
                'value' => [
                    'tr' => 'Davah eğitim uygulaması',
                    'en' => 'Davah education app'
                ],
                'type' => 'text',
                'group' => 'site',
                'description' => [
                    'tr' => 'Site açıklaması',
                    'en' => 'Site description'
                ],
                'is_translatable' => true
            ],
            [
                'key' => 'site_logo',
                'value' => 'https://via.placeholder.com/200x60',
                'type' => 'image',
                'group' => 'site',
                'description' => [
                    'tr' => 'Site logosu',
                    'en' => 'Site logo'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'site_favicon',
                'value' => 'https://via.placeholder.com/32x32',
                'type' => 'image',
                'group' => 'site',
                'description' => [
                    'tr' => 'Site favicon',
                    'en' => 'Site favicon'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'site_email',
                'value' => 'info@davah.com',
                'type' => 'text',
                'group' => 'site',
                'description' => [
                    'tr' => 'İletişim e-postası',
                    'en' => 'Contact email'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'site_phone',
                'value' => '+90 (555) 123 4567',
                'type' => 'text',
                'group' => 'site',
                'description' => [
                    'tr' => 'İletişim telefonu',
                    'en' => 'Contact phone'
                ],
                'is_translatable' => false
            ]
        ];
        
        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
    
    /**
     * Mobil uygulama ayarlarını oluşturur
     */
    private function createMobileSettings(): void
    {
        $settings = [
            [
                'key' => 'ios_min_version',
                'value' => '1.0.0',
                'type' => 'text',
                'group' => 'mobile',
                'description' => [
                    'tr' => 'iOS minimum versiyon',
                    'en' => 'iOS minimum version'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'ios_latest_version',
                'value' => '1.0.0',
                'type' => 'text',
                'group' => 'mobile',
                'description' => [
                    'tr' => 'iOS en son versiyon',
                    'en' => 'iOS latest version'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'ios_force_update',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'mobile',
                'description' => [
                    'tr' => 'iOS zorunlu güncelleme',
                    'en' => 'iOS force update'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'ios_maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'mobile',
                'description' => [
                    'tr' => 'iOS bakım modu',
                    'en' => 'iOS maintenance mode'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'ios_maintenance_message',
                'value' => [
                    'tr' => 'Uygulamamız şu anda bakımda, lütfen daha sonra tekrar deneyin.',
                    'en' => 'Our app is currently under maintenance, please try again later.'
                ],
                'type' => 'text',
                'group' => 'mobile',
                'description' => [
                    'tr' => 'iOS bakım mesajı',
                    'en' => 'iOS maintenance message'
                ],
                'is_translatable' => true
            ],
            [
                'key' => 'ios_store_url',
                'value' => 'https://apps.apple.com/app/davah',
                'type' => 'text',
                'group' => 'mobile',
                'description' => [
                    'tr' => 'iOS App Store URL',
                    'en' => 'iOS App Store URL'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'android_min_version',
                'value' => '1.0.0',
                'type' => 'text',
                'group' => 'mobile',
                'description' => [
                    'tr' => 'Android minimum versiyon',
                    'en' => 'Android minimum version'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'android_latest_version',
                'value' => '1.0.0',
                'type' => 'text',
                'group' => 'mobile',
                'description' => [
                    'tr' => 'Android en son versiyon',
                    'en' => 'Android latest version'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'android_force_update',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'mobile',
                'description' => [
                    'tr' => 'Android zorunlu güncelleme',
                    'en' => 'Android force update'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'android_maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'mobile',
                'description' => [
                    'tr' => 'Android bakım modu',
                    'en' => 'Android maintenance mode'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'android_maintenance_message',
                'value' => [
                    'tr' => 'Uygulamamız şu anda bakımda, lütfen daha sonra tekrar deneyin.',
                    'en' => 'Our app is currently under maintenance, please try again later.'
                ],
                'type' => 'text',
                'group' => 'mobile',
                'description' => [
                    'tr' => 'Android bakım mesajı',
                    'en' => 'Android maintenance message'
                ],
                'is_translatable' => true
            ],
            [
                'key' => 'android_store_url',
                'value' => 'https://play.google.com/store/apps/details?id=com.davah.app',
                'type' => 'text',
                'group' => 'mobile',
                'description' => [
                    'tr' => 'Android Play Store URL',
                    'en' => 'Android Play Store URL'
                ],
                'is_translatable' => false
            ]
        ];
        
        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
    
    /**
     * Sistem ayarlarını oluşturur
     */
    private function createSystemSettings(): void
    {
        $settings = [
            [
                'key' => 'default_language',
                'value' => 'tr',
                'type' => 'text',
                'group' => 'system',
                'description' => [
                    'tr' => 'Varsayılan dil',
                    'en' => 'Default language'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'timezone',
                'value' => 'Europe/Istanbul',
                'type' => 'text',
                'group' => 'system',
                'description' => [
                    'tr' => 'Zaman dilimi',
                    'en' => 'Timezone'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'description' => [
                    'tr' => 'Bakım modu',
                    'en' => 'Maintenance mode'
                ],
                'is_translatable' => false
            ],
            [
                'key' => 'ai_description',
                'value' => [
                    'en' => "You are an AI assistant focused on Islamic knowledge. You provide respectful, accurate, and well-sourced answers based on the Qur'an, authentic Hadiths, and the views of reputable Islamic scholars. You maintain a humble and polite tone, refrain from issuing fatwas, and only present trusted scholarly opinions. You respond in the language used by the user."
                ],
                'type' => 'text',
                'group' => 'system',
                'description' => [
                    'tr' => 'AI açıklama',
                    'en' => 'AI description'
                ],
                'is_translatable' => true
            ],
            [
                'key' => 'ai_key_openai',
                'value' => 'your-api-key',
                'type' => 'text',
                'group' => 'system',
                'description' => [
                    'tr' => 'AI key',
                    'en' => 'AI key'
                ],
            ]
        ];
        
        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
