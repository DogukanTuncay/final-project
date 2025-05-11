<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

trait HasImage
{
    /**
     * Model silindiğinde resimleri de sil
     */
    protected static function bootHasImage()
    {
        static::deleting(function ($model) {
            $model->deleteImages();
        });
    }

    /**
     * Resim yükleme
     */
    public function uploadImage(UploadedFile $image, string $field = 'image'): string
    {
        // Eski resmi sil
        if ($this->{$field}) {
            $this->deleteImage($this->{$field});
        }
        // Yeni resmi yükle
        $path = $image->store($this->getImagePath(), 'public');
        
        // Tam URL oluştur
        $fullUrl = $this->getFullImageUrl($path);
        
        // Veritabanına tam URL'yi kaydet
        $this->{$field} = $fullUrl;
        $this->save();

        return $fullUrl;
    }

    /**
     * Çoklu resim yükleme
     */
    public function uploadImages(array $images, string $field = 'images'): array
    {
        $urls = [];

        // Mevcut resimleri al
        $currentImages = json_decode($this->{$field} ?? '[]', true);

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store($this->getImagePath(), 'public');
                $fullUrl = $this->getFullImageUrl($path);
                $urls[] = $fullUrl;
            }
        }

        // Yeni ve eski resimleri birleştir
        $allImages = array_merge($currentImages, $urls);
        
        // Modeli güncelle
        $this->{$field} = json_encode($allImages);
        $this->save();

        return $urls;
    }

    /**
     * Belirli bir resmi sil
     */
    public function deleteImage(string $url): bool
    {
        // URL'den path kısmını çıkart
        $path = $this->getPathFromUrl($url);
        if (!$path) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }

    /**
     * Tüm resimleri sil
     */
    public function deleteImages(): void
    {
        // Tekli resim
        if (isset($this->profile_image) && $this->profile_image) {
            $this->deleteImage($this->profile_image);
        }

        if (isset($this->image) && $this->image) {
            $this->deleteImage($this->image);
        }

        // Çoklu resim
        if (isset($this->images) && $this->images) {
            $images = json_decode($this->images, true);
            foreach ($images as $image) {
                $this->deleteImage($image);
            }
        }
    }

    /**
     * Resim yolunu al
     */
    protected function getImagePath(): string
    {
        return 'images/' . strtolower(class_basename($this));
    }

    /**
     * URL'den dosya yolunu çıkarır
     */
    protected function getPathFromUrl(string $url): ?string
    {
        // URL'yi parçalara ayır
        $urlParts = parse_url($url);
        
        // URL içinde '/storage/' yolunu bul
        if (isset($urlParts['path'])) {
            $path = $urlParts['path'];
            
            // '/storage/' öneki varsa kaldır
            if (Str::contains($path, '/storage/')) {
                return Str::after($path, '/storage/');
            }
        }
        
        return null;
    }

    /**
     * Tam resim URL'sini oluştur
     */
    protected function getFullImageUrl(string $path): string
    {
        return URL::to('/storage/' . $path);
    }

    /**
     * Resim URL'ini al - Geriye dönük uyumluluk için
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ?? null;
    }

    /**
     * Profil resmi URL'ini al
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        return $this->profile_image ?? null;
    }

    /**
     * Tüm resimlerin URL'lerini al - Geriye dönük uyumluluk için
     */
    public function getImagesUrlAttribute(): array
    {
        if (!isset($this->images) || !$this->images) {
            return [];
        }

        return json_decode($this->images, true);
    }
}
