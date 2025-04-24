<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserLocaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Sadece giriş yapmış kullanıcılar kendi locale'lerini değiştirebilir.
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Desteklenen dilleri al (config dosyasından veya sabit olarak)
        // Varsayılan: ['en', 'tr']
        $supportedLocales = config('app.available_locales', ['en', 'tr']);

        return [
            'locale' => [
                'required',
                'string',
                Rule::in($supportedLocales),
            ],
        ];
    }

     /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'locale.required' => 'Dil kodu zorunludur.',
            'locale.string'   => 'Dil kodu metin formatında olmalıdır.',
            'locale.in'       => 'Gönderilen dil kodu desteklenmiyor.',
        ];
    }
} 