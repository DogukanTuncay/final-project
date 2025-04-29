<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Translatable\HasTranslations;

class VideoContent extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, HasTranslations;

    /**
     * Çevirilecek alanlar
     */
    public $translatable = [
        'title',
        'description',
    ];

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'title',
        'description',
        'video_url',
        'provider',
        'video_id',
        'duration',
        'metadata',
        'is_active',
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'is_active' => 'boolean',
        'duration' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Otomatik eklenen özellikler
     */
    protected $appends = [
        'formatted_duration',
    ];

    /**
     * Bu içeriğe sahip olan ders içerikleri
     */
    public function lessonContents(): MorphMany
    {
        return $this->morphMany(CourseChapterLessonContent::class, 'contentable');
    }

    /**
     * Sadece aktif videoları getiren scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Provider'a göre filtreleyen scope
     */
    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Süreyi formatlı olarak döndüren accessor
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return null;
        }

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Video URL'sini ayarlarken provider ve video_id alanlarını otomatik doldur
     */
    public function setVideoUrlAttribute($value)
    {
        $this->attributes['video_url'] = $value;

        // YouTube URL'si
        if (preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $value, $matches)) {
            $this->attributes['provider'] = 'youtube';
            $this->attributes['video_id'] = $matches[1];
        }
        // Vimeo URL'si
        elseif (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|)(\d+)(?:|\/\?)/', $value, $matches)) {
            $this->attributes['provider'] = 'vimeo';
            $this->attributes['video_id'] = $matches[1];
        }
        // Diğer URL'ler
        else {
            $this->attributes['provider'] = 'custom';
            $this->attributes['video_id'] = null;
        }
    }

    /**
     * Embed URL'sini döndüren metot
     */
    public function getEmbedUrl()
    {
        switch ($this->provider) {
            case 'youtube':
                return "https://www.youtube.com/embed/{$this->video_id}";
            case 'vimeo':
                return "https://player.vimeo.com/video/{$this->video_id}";
            case 'h5p':
                return $this->video_url; // H5P içeriklerinde doğrudan URL kullanılır
            default:
                return $this->video_url;
        }
    }

    /**
     * Configure the options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('video_content')
            ->setDescriptionForEvent(fn(string $eventName) => "Video içeriği '{$this->title}' {$eventName}");
    }
}
