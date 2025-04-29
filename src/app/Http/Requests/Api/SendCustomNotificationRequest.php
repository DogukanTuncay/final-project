<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SendCustomNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('send_notifications');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_ids' => 'required|array',
            'user_ids.*' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'additional_data' => 'sometimes|array',
            'additional_data.*.key' => 'string|max:100',
            'additional_data.*.value' => 'string|max:1000',
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_ids.required' => 'Bildirim gönderilecek kullanıcılar belirtilmelidir.',
            'user_ids.array' => 'Kullanıcı ID\'leri bir dizi olmalıdır.',
            'user_ids.*.exists' => 'Belirtilen kullanıcı ID\'si geçerli değil.',
            'title.required' => 'Bildirim başlığı gereklidir.',
            'title.max' => 'Bildirim başlığı en fazla :max karakter olmalıdır.',
            'message.required' => 'Bildirim mesajı gereklidir.',
            'message.max' => 'Bildirim mesajı en fazla :max karakter olmalıdır.',
        ];
    }
} 