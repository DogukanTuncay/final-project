<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Setting extends Model
{
    use HasImage, HasTranslations;

    /**
     * Çevirilecek alanlar
     */
    public $translatable = ['value', 'description'];

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_translatable',
        'is_private'
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'is_translatable' => 'boolean',
        'is_private' => 'boolean',
    ];

    /**
     * Değeri tiplerine göre dönüştürerek döndürür
     */
    public function getTypedValueAttribute()
    {
        if (!$this->value) {
            return null;
        }

        switch ($this->type) {
            case 'boolean':
                return (bool) $this->value;
            case 'number':
                return (float) $this->value;
            case 'json':
                return json_decode($this->value, true);
            case 'image':
                return $this->value; // HasImage trait'i kullanır
            default:
                return $this->value;
        }
    }

    /**
     * Belirli bir ayarı anahtarına göre getirir
     */
    public static function getByKey(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return $setting->typed_value;
    }

    /**
     * Ayarları gruplandırılmış şekilde getirir
     */
    public static function getGrouped(string $group = null)
    {
        $query = self::query();

        if ($group) {
            $query->where('group', $group);
        }

        $settings = $query->get();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->typed_value;
        }

        return $result;
    }
    
    /**
     * Yalnızca genel (public) ayarları döndüren kapsam
     */
    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }
    
    /**
     * Yalnızca özel (private) ayarları döndüren kapsam
     */
    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }
}
