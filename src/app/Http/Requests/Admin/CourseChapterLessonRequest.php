<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CourseChapterLessonRequest extends BaseRequest
{
    public function rules()
    {
        $rules = [
            'course_chapter_id' => 'required|exists:course_chapters,id',
            'name' => 'required|array',
            'name.tr' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.tr' => 'nullable|string',
            'description.en' => 'nullable|string',
            'meta_title' => 'nullable|array',
            'meta_title.tr' => 'nullable|string|max:255',
            'meta_title.en' => 'nullable|string|max:255',
            'meta_description' => 'nullable|array',
            'meta_description.tr' => 'nullable|string',
            'meta_description.en' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'thumbnail' => $this->isMethod('PUT') 
                ? 'nullable|image|mimes:jpeg,png,jpg|max:2048'
                : 'required|image|mimes:jpeg,png,jpg|max:2048',
            'duration' => 'nullable|integer|min:0',
            
        ];

        return $rules;
    }

    
}