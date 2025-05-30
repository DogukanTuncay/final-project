<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class CourseChapterRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'course_id' => 'required|exists:courses,id',
            
            'name' => 'required|array',
            'name.tr' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            
            'description' => 'nullable|array',
            'description.tr' => 'nullable|string',
            'description.en' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'integer',
            'difficulty' => 'required|integer|in:1,2,3',
            
            'meta_title' => 'nullable|array',
            'meta_title.tr' => 'nullable|string|max:255',
            'meta_title.en' => 'nullable|string|max:255',
            
            'meta_description' => 'nullable|array',
            'meta_description.tr' => 'nullable|string',
            'meta_description.en' => 'nullable|string',
            
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'sometimes|nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        return $rules;
    }
}