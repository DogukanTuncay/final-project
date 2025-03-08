<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

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
        $this->{$field} = $path;
        $this->save();

        return $path;
    }

    /**
     * Çoklu resim yükleme
     */
    public function uploadImages(array $images, string $field = 'images'): array
    {
        $paths = [];

        // Mevcut resimleri al
        $currentImages = json_decode($this->{$field} ?? '[]', true);

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store($this->getImagePath(), 'public');
                $paths[] = $path;
            }
        }

        // Yeni ve eski resimleri birleştir
        $allImages = array_merge($currentImages, $paths);
        
        // Modeli güncelle
        $this->{$field} = json_encode($allImages);
        $this->save();

        return $paths;
    }

    /**
     * Belirli bir resmi sil
     */
    public function deleteImage(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Tüm resimleri sil
     */
    public function deleteImages(): void
    {
        // Tekli resim
        if ($this->image) {
            $this->deleteImage($this->image);
        }

        // Çoklu resim
        if ($this->images) {
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
     * Resim URL'ini al
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    /**
     * Tüm resimlerin URL'lerini al
     */
    public function getImagesUrlAttribute(): array
    {
        if (!$this->images) {
            return [];
        }

        $images = json_decode($this->images, true);
        return array_map(fn($image) => Storage::url($image), $images);
    }
}
