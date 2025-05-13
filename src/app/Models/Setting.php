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
     * Değeri kaydetmeden önce işleme
     */
    public function setValueAttribute($value)
    {
        // Eğer alan çevrilebilir değilse, normal kayıt
        if (!$this->is_translatable) {
            $this->attributes['value'] = is_array($value) ? json_encode($value) : $value;
            return;
        }

        // Çevrilebilir alanlar için
        if ($this->type === 'json' && is_string($value)) {
            $decodedValue = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->setTranslation('value', app()->getLocale(), $decodedValue);
            } else {
                $this->setTranslation('value', app()->getLocale(), $value);
            }
        } else {
            // Eğer mevcut bir değer varsa ve bu dizi ise, mevcut çevirileri korumak için güncelleme yapalım
            if (isset($this->attributes['value']) && $this->isTranslatableAttribute('value')) {
                try {
                    $translations = $this->getTranslations('value');
                    $translations[app()->getLocale()] = $value;
                    $this->attributes['value'] = json_encode($translations);
                } catch (\Exception $e) {
                    // Herhangi bir hata durumunda sadece mevcut dili güncelleyelim
                    $this->setTranslation('value', app()->getLocale(), $value);
                }
            } else {
                // İlk kayıt veya çevrilebilir olmayan bir alan
                $this->setTranslation('value', app()->getLocale(), $value);
            }
        }
    }

    /**
     * Değeri tiplerine göre dönüştürerek döndürür
     */
    public function getTypedValueAttribute()
    {
        if (!$this->value) {
            return null;
        }

        // Çevirilmeyen değerler için
        if (!$this->is_translatable) {
            switch ($this->type) {
                case 'boolean':
                    return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
                case 'number':
                    return (float) $this->value;
                case 'json':
                    if (is_string($this->value)) {
                        return json_decode($this->value, true);
                    }
                    return $this->value;
                case 'image':
                    return $this->image_url;
                default:
                    return $this->value;
            }
        }
        
        // Çevrilen değerler için (mevcut dildeki değeri al)
        $value = $this->getTranslation('value', app()->getLocale(), '');
        
        switch ($this->type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return (float) $value;
            case 'json':
                if (is_string($value)) {
                    return json_decode($value, true);
                }
                return $value;
            case 'image':
                return $this->image_url;
            default:
                return $value;
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

        return $setting->value;
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
            $result[$setting->key] = $setting->value;
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
