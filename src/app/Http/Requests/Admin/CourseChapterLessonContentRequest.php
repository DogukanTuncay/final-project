<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class CourseChapterLessonContentRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'course_chapter_lesson_id' => 'required|exists:course_chapter_lessons,id',
            'contentable_id' => 'required|integer',
            'contentable_type' => 'required|string',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
            'meta_data' => 'nullable|array',
        ];
    }
}