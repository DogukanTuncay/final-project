<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => 'sometimes|required|string|max:255',
            'username' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'alpha_dash', // Sadece harf, rakam, tire ve alt çizgi
                Rule::unique('users')->ignore($userId),
            ],
            'phone' => 'sometimes|nullable|string|max:20', // Formatı daha spesifik hale getirebilirsiniz
            'zip_code' => 'sometimes|nullable|string|max:10',
            'profile_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Email, locale, password gibi alanlar bu request ile güncellenmemeli
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
           'name.required' => 'İsim alanı zorunludur.',
           'name.string'   => 'İsim metin formatında olmalıdır.',
           'name.max'      => 'İsim en fazla 255 karakter olabilir.',
           'username.required' => 'Kullanıcı adı alanı zorunludur.',
           'username.string'   => 'Kullanıcı adı metin formatında olmalıdır.',
           'username.max'      => 'Kullanıcı adı en fazla 255 karakter olabilir.',
           'username.alpha_dash' => 'Kullanıcı adı sadece harf, rakam, tire ve alt çizgi içerebilir.',
           'username.unique'   => 'Bu kullanıcı adı zaten alınmış.',
           'phone.max'         => 'Telefon numarası en fazla 20 karakter olabilir.',
           'zip_code.max'      => 'Posta kodu en fazla 10 karakter olabilir.',
           'profile_image.image' => 'Profil resmi bir görsel dosyası olmalıdır.',
           'profile_image.mimes' => 'Profil resmi jpeg, png, jpg veya gif formatında olmalıdır.',
           'profile_image.max'  => 'Profil resmi en fazla 2MB olabilir.',
       ];
   }
} 