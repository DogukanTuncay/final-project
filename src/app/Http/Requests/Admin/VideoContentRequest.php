<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class VideoContentRequest extends BaseRequest
{
    /**
     * Video içerik formları için doğrulama kuralları
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => ['required', 'array'],
            'title.tr' => ['required', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            
            'description' => ['nullable', 'array'],
            'description.tr' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            
            'video_url' => ['required', 'url'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ];

        // Güncelleme yaparken belirli alanlar için unique kontrolü
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            if ($this->has('video_url')) {
                $rules['video_url'] = ['required', 'url'];
            }
        }

        return $rules;
    }

    /**
     * Özelleştirilmiş hata mesajları
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Video başlığı gereklidir.',
            'title.tr.required' => 'Türkçe video başlığı gereklidir.',
            'title.tr.max' => 'Türkçe video başlığı en fazla 255 karakter olabilir.',
            'title.en.max' => 'İngilizce video başlığı en fazla 255 karakter olabilir.',
            
            'video_url.required' => 'Video URL adresi gereklidir.',
            'video_url.url' => 'Geçerli bir URL adresi giriniz.',
            
            'duration.integer' => 'Video süresi tam sayı olmalıdır.',
            'duration.min' => 'Video süresi en az 0 saniye olmalıdır.',
        ];
    }

    /**
     * Attribute isimleri
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'title' => 'Video Başlığı',
            'title.tr' => 'Türkçe Video Başlığı',
            'title.en' => 'İngilizce Video Başlığı',
            'description' => 'Video Açıklaması',
            'description.tr' => 'Türkçe Video Açıklaması',
            'description.en' => 'İngilizce Video Açıklaması',
            'video_url' => 'Video URL',
            'duration' => 'Video Süresi',
            'is_active' => 'Aktif Durumu',
            'metadata' => 'Meta Veriler',
        ];
    }
}