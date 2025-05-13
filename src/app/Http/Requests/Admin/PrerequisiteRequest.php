<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrerequisiteRequest extends FormRequest
{
   

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prerequisite_ids' => 'required|array',
            'prerequisite_ids.*' => 'integer|exists:' . $this->getTable() . ',id'
        ];
    }

    /**
     * Custom messages for validation
     * 
     * @return array
     */
    public function messages(): array
    {
        return [
            'prerequisite_ids.required' => 'Lütfen en az bir ön koşul seçin.',
            'prerequisite_ids.array' => 'Ön koşullar bir dizi olmalıdır.',
            'prerequisite_ids.*.integer' => 'Geçersiz ön koşul ID değeri.',
            'prerequisite_ids.*.exists' => 'Seçilen ön koşul bulunamadı.',
        ];
    }

    /**
     * Tahmini tablo adını belirle
     */
    private function getTable(): string
    {
        // İstek rotasından kaynak türünü belirle (chapters veya lessons)
        $resource = explode('/', $this->path())[1] ?? '';

        if ($resource === 'chapters') {
            return 'course_chapters';
        } elseif ($resource === 'lessons') {
            return 'course_chapter_lessons';
        }

        return 'course_chapters'; // Varsayılan tablo
    }
} 