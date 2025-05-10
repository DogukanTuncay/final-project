<?php

namespace App\Http\Requests\Admin;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;
class CourseRequest extends BaseRequest
{
    public function authorize()
    {
       

        return true;
    }

    public function rules()
    {
        $locales = config('translatable.locales', ['tr', 'en']);
        $rules = [];

        foreach ($locales as $locale) {
            $rules["name.{$locale}"] = 'required|string|max:255';
            $rules["short_description.{$locale}"] = 'required|string|max:500';
            $rules["description.{$locale}"] = 'required|string';
            $rules["objectives.{$locale}"] = 'required|array';
            $rules["objectives.{$locale}.*"] = 'required|string';
            $rules["meta_title.{$locale}"] = 'nullable|string|max:60';
            $rules["meta_description.{$locale}"] = 'nullable|string|max:160';
        }

        return array_merge($rules, [
            'image' => $this->isMethod('PUT') 
                ? 'nullable|image|mimes:jpeg,png,jpg|max:2048'
                : 'required|image|mimes:jpeg,png,jpg|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'order' => 'integer|min:0',
            'category' => ['required', Rule::in(array_keys(Course::CATEGORIES))],
            'difficulty' => ['required', 'integer', Rule::in([1, 2, 3])],
        ]);
    }
}