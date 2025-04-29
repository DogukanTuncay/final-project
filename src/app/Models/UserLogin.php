<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Request;

class UserLogin extends Model
{
    use HasFactory;

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'user_id',
        'login_date',
        'ip_address',
        'country',
        'city',
        'region',
        'timezone',
        'latitude',
        'longitude',
        'user_agent',
        'location_data',
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'login_date' => 'date',
        'location_data' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Kullanıcı ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kullanıcının bugünkü giriş kaydını oluşturur veya günceller
     * ve IP adresi/konum bilgilerini saklar
     * 
     * @param int $userId
     * @return self|null
     */
    public static function recordLoginToday(int $userId): ?self
    {
        $today = now()->toDateString();
        
        // Bugün için kayıt var mı kontrol et
        $existingRecord = self::where('user_id', $userId)
            ->where('login_date', $today)
            ->first();
            
        // Eğer bugün için zaten bir kayıt varsa, null döndür
        if ($existingRecord) {
            return $existingRecord;
        }
        
        $ip = Request::ip();
        $userAgent = Request::header('User-Agent');
        
        // Varsayılan olarak boş konum bilgileri ayarla
        $locationData = [
            'country' => null,
            'city' => null,
            'region' => null,
            'timezone' => null,
            'latitude' => null,
            'longitude' => null,
        ];

        // Konum bilgilerini al
        try {
            // Geliştirme ortamında ise test IP'si kullanabilirsiniz
            // $position = Location::get('8.8.8.8');
            
            // Gerçek kullanıcı IP'sini kullan
            $position = Location::get($ip);
            
            if ($position) {
                $locationData = [
                    'country' => $position->countryName,
                    'city' => $position->cityName,
                    'region' => $position->regionName,
                    'timezone' => $position->timezone,
                    'latitude' => $position->latitude,
                    'longitude' => $position->longitude,
                ];
            }
        } catch (\Exception $e) {
            // Konum bilgisi alınamazsa hata loglanır
            \Illuminate\Support\Facades\Log::warning("Konum bilgisi alınamadı: " . $e->getMessage());
        }
        
        // Sadece yeni kayıt oluştur, güncelleme yapma
        return self::create([
            'user_id' => $userId,
            'login_date' => $today,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'country' => $locationData['country'],
            'city' => $locationData['city'],
            'region' => $locationData['region'],
            'timezone' => $locationData['timezone'],
            'latitude' => $locationData['latitude'], 
            'longitude' => $locationData['longitude'],
            'location_data' => $position ?? null, // Tüm konum verisini JSON olarak sakla
        ]);
    }
} 