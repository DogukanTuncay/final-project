<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

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
     * Resim yükleme (manuel yöntemle)
     */
    public function uploadImage(UploadedFile $image, string $field = 'image'): string
    {
        // Eski resmi sil
        if ($this->{$field}) {
            $this->deleteImage($this->{$field});
        }
        
        // Upload klasörünü oluştur (yoksa)
        $uploadPath = $this->getUploadPath();
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }
        
        // Dosya adını hazırla
        $fileName = $this->generateFileName($image);
        $fullPath = $uploadPath . '/' . $fileName;
        
        // Dosyayı yükle
        if ($image->move($uploadPath, $fileName)) {
            // Tam URL oluştur
            $fullUrl = $this->getFullImageUrl($fileName);
            
            // Veritabanına tam URL'yi kaydet
            $this->{$field} = $fullUrl;
            $this->save();
            
            return $fullUrl;
        }
        
        return '';
    }

    /**
     * Çoklu resim yükleme (manuel yöntemle)
     */
    public function uploadImages(array $images, string $field = 'images'): array
    {
        $urls = [];

        // Mevcut resimleri al
        $currentImages = json_decode($this->{$field} ?? '[]', true);
        
        // Upload klasörünü oluştur (yoksa)
        $uploadPath = $this->getUploadPath();
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                // Dosya adını hazırla
                $fileName = $this->generateFileName($image);
                $fullPath = $uploadPath . '/' . $fileName;
                
                // Dosyayı yükle
                if ($image->move($uploadPath, $fileName)) {
                    // Tam URL oluştur
                    $fullUrl = $this->getFullImageUrl($fileName);
                    $urls[] = $fullUrl;
                }
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
     * Belirli bir resmi sil (manuel yöntemle)
     */
    public function deleteImage(string $url): bool
    {
        // URL'den dosya adını çıkart
        $fileName = $this->getFileNameFromUrl($url);
        
        if (!$fileName) {
            return false;
        }
        
        $filePath = $this->getUploadPath() . '/' . $fileName;
        
        // Dosya varsa sil
        if (File::exists($filePath)) {
            return File::delete($filePath);
        }
        
        return false;
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
     * Benzersiz dosya adı oluştur
     */
    protected function generateFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $uniqueName = Str::uuid()->toString();
        return $uniqueName . '.' . $extension;
    }

    /**
     * URL'den dosya adını çıkarır
     */
    protected function getFileNameFromUrl(string $url): ?string
    {
        // URL'yi parçalara ayır
        $urlParts = parse_url($url);
        
        // Path kısmını al
        if (isset($urlParts['path'])) {
            $pathParts = explode('/', $urlParts['path']);
            return end($pathParts);
        }
        
        return null;
    }

    /**
     * Upload klasörünün yolunu al
     */
    protected function getUploadPath(): string
    {
        $baseDir = public_path('uploads');
        $modelDir = strtolower(class_basename($this));
        
        return $baseDir . '/' . $modelDir;
    }

    /**
     * Tam resim URL'sini oluştur
     */
    protected function getFullImageUrl(string $fileName): string
    {
        $modelDir = strtolower(class_basename($this));
        return URL::asset('uploads/' . $modelDir . '/' . $fileName);
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
