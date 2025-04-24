<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FillInTheBlankRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Desteklenen dilleri al (config dosyasından veya sabit olarak)
        // Örnek: $supportedLocales = config('app.available_locales', ['en', 'tr']);
        $supportedLocales = ['en', 'tr']; // Şimdilik sabit varsayalım

        $rules = [
            // Translatable alanlar için her dilde validasyon
            'question' => 'required|array', // Anahtarın array olmasını bekle
            'feedback' => 'nullable|array',

            // Dil bazlı kurallar
            'answers' => 'required|array', // Answers çevrilebilir değil
            'answers.*' => 'required|array|max:255',
            'is_active' => 'sometimes|boolean',
            'case_sensitive' => 'sometimes|boolean',
            'points' => 'nullable|integer|min:0',
        ];

        // Her dil için çevrilebilir alan kurallarını ekle
        foreach ($supportedLocales as $locale) {
            $rules["question.{$locale}"] = 'required|string|max:255';
            $rules["feedback.{$locale}"] = 'nullable|string';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        $supportedLocales = ['en', 'tr']; // Kurallarla aynı olmalı
        $messages = [
            // Genel array mesajları
            'title.required' => 'Başlık alanı zorunludur (tüm diller için).',
            'title.array'    => 'Başlık alanı dil çevirilerini içermelidir.',
            'text.required'  => 'Metin alanı zorunludur (tüm diller için).',
            'text.array'     => 'Metin alanı dil çevirilerini içermelidir.',
            'description.array' => 'Açıklama alanı dil çevirilerini içermelidir.',
            'feedback.array'    => 'Geri bildirim alanı dil çevirilerini içermelidir.',

            // Cevaplar için mesajlar
            'answers.required' => 'En az bir cevap eklemelisiniz.',
            'answers.array' => 'Cevaplar liste formatında olmalıdır.',
            'answers.*.required' => 'Cevap boş olamaz.',
            'answers.*.max' => 'Cevap en fazla 255 karakter olabilir.',

            // Diğer alanlar
            'points.integer' => 'Puan değeri tam sayı olmalıdır.',
            'points.min' => 'Puan değeri 0 veya daha büyük olmalıdır.',
        ];

        // Her dil için çevrilebilir alan mesajlarını ekle
        foreach ($supportedLocales as $locale) {
            $messages["title.{$locale}.required"] = "Başlık alanı ({$locale}) zorunludur.";
            $messages["title.{$locale}.max"] = "Başlık ({$locale}) en fazla 255 karakter olabilir.";
            $messages["text.{$locale}.required"] = "Metin alanı ({$locale}) zorunludur.";
            $messages["description.{$locale}.string"] = "Açıklama ({$locale}) metin formatında olmalıdır.";
            $messages["feedback.{$locale}.string"] = "Geri bildirim ({$locale}) metin formatında olmalıdır.";
        }

        return $messages;
    }
} 