<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasImage;

class StoryCategory extends Model
{
    use HasFactory, SoftDeletes, HasTranslations, LogsActivity, HasImage;

    /**
     * Çevirilecek alanlar.
     */
    public array $translatable = ['name'];

    /**
     * Toplu atama yapılabilecek alanlar.
     */
    protected $fillable = [
        'name',
        'slug',
        'image',
        'is_active',
        'order',
    ];

    /**
     * Veri tipi dönüşümleri.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'name' => 'array',
        'order' => 'integer',
    ];

    /**
     * Otomatik eklenen özellikler
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * Modelin "booted" metodu.
     */
    protected static function booted(): void
    {
        parent::boot();

        static::creating(function (StoryCategory $category) {
            if (empty($category->slug)) {
                $source = $category->getTranslation('name', config('app.fallback_locale'), false) ?: (is_array($category->name) ? reset($category->name) : 'category');
                $category->slug = static::generateUniqueSlug($source);
            }
        });

        static::updating(function (StoryCategory $category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                 $source = $category->getTranslation('name', config('app.fallback_locale'), false) ?: (is_array($category->name) ? reset($category->name) : 'category');
                $category->slug = static::generateUniqueSlug($source, $category->id);
            }
        });
    }

    /**
     * Benzersiz bir slug oluşturur.
     *
     * @param string $name
     * @param int|null $ignoreId
     * @return string
     */
    protected static function generateUniqueSlug(string $name, int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        if(empty($baseSlug)) $baseSlug = 'category'; // Boş slug ihtimaline karşı
        $slug = $baseSlug;
        $counter = 1;

        $query = static::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        // Soft delete edilmişleri de kontrol et (isteğe bağlı)
        // $query->withTrashed(); 

        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            // while döngüsü için query'i yeniden oluştur
            $query = static::where('slug', $slug);
             if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            // $query->withTrashed();
        }

        return $slug;
    }

    /**
     * Bu kategoriye ait hikayeler.
     */
    public function stories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        // Henüz Story modeli olmadığı için yorumda bırakıldı, Story modeli oluşturulunca açılmalı
        // return $this->hasMany(Story::class);
        return $this->hasMany(Model::class); // Geçici
    }

    /**
     * Sadece aktif kategorileri filtreler.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Sıralamaya göre getirir.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'desc');
    }

    /**
     * Aktivite loglama seçeneklerini yapılandırır.
     */
    public function getActivitylogOptions(): LogOptions
    {
        $name = is_array($this->name) ? ($this->name[config('app.fallback_locale')] ?? reset($this->name)) : $this->name;
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('story_category')
            ->setDescriptionForEvent(fn(string $eventName) => "Story Category '{$name}' has been {$eventName}");
    }
}
