<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class TrueFalseQuestionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'correct_answer' => $this->correct_answer,
            'points' => $this->points,
            'created_by' => $this->created_by,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ];
            }),
            'lesson_content' => $this->whenLoaded('lessonContent', function() {
                return [
                    'id' => $this->lessonContent->id,
                    'course_chapter_lesson_id' => $this->lessonContent->course_chapter_lesson_id,
                    'order' => $this->lessonContent->order,
                    'is_active' => $this->lessonContent->is_active,
                ];
            }),
        ]);
    }
}