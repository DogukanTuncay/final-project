<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class OneSignalCustomNotificationRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'player_id' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'additional_data' => 'nullable|array',
            'additional_data.url' => 'nullable|string|url',
            'additional_data.type' => 'nullable|string|max:100',
            'additional_data.deep_link' => 'nullable|string|max:255',
            'is_scheduled' => 'nullable|boolean',
            'send_after' => 'nullable|required_if:is_scheduled,true|date|after:now',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Kullanıcı ID alanı zorunludur.',
            'user_id.exists' => 'Geçersiz kullanıcı ID.',
            'player_id.required' => 'Player ID alanı zorunludur.',
            'title.required' => 'Başlık alanı zorunludur.',
            'title.max' => 'Başlık 255 karakterden uzun olamaz.',
            'message.required' => 'Mesaj alanı zorunludur.',
            'message.max' => 'Mesaj 2000 karakterden uzun olamaz.',
            'additional_data.url.url' => 'Geçerli bir URL giriniz.',
            'send_after.required_if' => 'Zamanlanmış bildirimler için gönderim zamanı belirtmeniz gerekir.',
            'send_after.after' => 'Gönderim zamanı şu andan sonraki bir tarih olmalıdır.',
        ];
    }
} 