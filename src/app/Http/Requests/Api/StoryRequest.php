<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoryRequest extends BaseRequest
{
    /**
     * Bu isteğin yapılıp yapılamayacağını belirler.
     * API istekleri genellikle herkese açıktır veya middleware ile korunur.
     */
    public function authorize(): bool
    {
        return true; // Herkese açık olduğunu varsayalım
    }

    /**
     * İstek için geçerli doğrulama kurallarını alır.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Sadece index metodunda kullanılacak parametreler için kurallar
        if ($this->routeIs('api.stories.index')) { // Route ismini kontrol etmek daha güvenli
            return [
                'story_category_id' => ['nullable', 'integer', Rule::exists('story_categories', 'id')],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
                'page' => ['nullable', 'integer', 'min:1'],
                'order_by' => ['nullable', 'string', Rule::in(['id', 'order_column', 'created_at'])], // İzin verilen sıralama alanları
                'order_direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])]
            ];
        }

        // Diğer API endpointleri için kurallar buraya eklenebilir (varsa)
        return [];
    }
}