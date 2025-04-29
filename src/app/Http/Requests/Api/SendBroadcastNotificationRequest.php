<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SendBroadcastNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('send_broadcast_notifications');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
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
            'title.required' => 'Bildirim başlığı gereklidir.',
            'title.max' => 'Bildirim başlığı en fazla :max karakter olmalıdır.',
            'message.required' => 'Bildirim mesajı gereklidir.',
            'message.max' => 'Bildirim mesajı en fazla :max karakter olmalıdır.',
        ];
    }
} 