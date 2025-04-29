<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class CourseChapterLessonContentResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'course_chapter_lesson_id' => $this->course_chapter_lesson_id,
            'contentable_id' => $this->contentable_id,
            'contentable_type' => $this->contentable_type,
            'content_type' => $this->getContentTypeName($this->contentable_type),
            'order' => $this->order,
            'is_active' => $this->is_active,
            'meta_data' => $this->meta_data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'content' => $this->formatContent($this->contentable, $translated),
        ]);
    }
    
    /**
     * İçerik tipini insana okunabilir formata çevir
     */
    private function getContentTypeName($contentableType)
    {
        $types = [
            'App\\Models\\TextContent' => 'text',
            'App\\Models\\VideoContent' => 'video',
            'App\\Models\\FillInTheBlank' => 'fill-in-the-blank',
            'App\\Models\\MultipleChoiceQuestion' => 'multiple-choice',
            'App\\Models\\TrueFalseQuestion' => 'true-false',
            'App\\Models\\ShortAnswerQuestion' => 'short-answer',
            'App\\Models\\MatchingQuestion' => 'matching',
        ];
        
        return $types[$contentableType] ?? 'unknown';
    }
    
    /**
     * İçeriği tipine göre formatlayarak döndür
     */
    private function formatContent($contentable, $translated)
    {
        if (!$contentable) {
            return null;
        }
        
        $contentType = $this->getContentTypeName($this->contentable_type);

        switch ($contentType) {
            case 'text':
                return [
                    'content' => $translated['content'] ?? $contentable->content,
                ];
                
            case 'video':
                return [
                    'title' => $translated['title'] ?? $contentable->title,
                    'description' => $translated['description'] ?? $contentable->description,
                    'video_url' => $contentable->video_url,
                    'provider' => $contentable->provider,
                    'duration' => $contentable->duration,
                    'thumbnail_url' => $contentable->thumbnail_url,
                ];
                
            case 'fill-in-the-blank':
                return [
                    'question' => $translated['question'] ?? $contentable->question,
                    'answers' => $translated['answers'] ?? $contentable->answers,
                    'feedback' => $translated['feedback'] ?? $contentable->feedback,
                    'points' => $contentable->points,
                    'case_sensitive' => $contentable->case_sensitive,
                ];
                
            case 'multiple-choice':
                return [
                    'question' => $translated['question'] ?? $contentable->question,
                    'feedback' => $translated['feedback'] ?? $contentable->feedback,
                    'points' => $contentable->points,
                    'is_multiple_answer' => $contentable->is_multiple_answer,
                    'shuffle_options' => $contentable->shuffle_options,
                    'options' => $contentable->options ? QuestionOptionResource::collection($contentable->options) : [],
                ];
                
            case 'true-false':
                return [
                    'question' => $translated['question'] ?? $contentable->question,
                    'feedback' => $translated['feedback'] ?? $contentable->feedback,
                    'points' => $contentable->points,
                    'correct_answer' => $contentable->correct_answer,
                    'custom_text' => $translated['custom_text'] ?? $contentable->custom_text,
                ];
                
            case 'short-answer':
                return [
                    'question' => $translated['question'] ?? $contentable->question,
                    'feedback' => $translated['feedback'] ?? $contentable->feedback,
                    'points' => $contentable->points,
                    'allowed_answers' => $translated['allowed_answers'] ?? $contentable->allowed_answers,
                    'case_sensitive' => $contentable->case_sensitive,
                    'max_attempts' => $contentable->max_attempts,
                ];
                
            case 'matching':
                $pairs = $contentable->pairs ? $contentable->pairs->map(function($pair) {
                    return [
                        'id' => $pair->id,
                        'left_item' => $pair->getTranslations('left_item'),
                        'right_item' => $pair->getTranslations('right_item'),
                        'order' => $pair->order,
                    ];
                }) : [];
                
                return [
                    'question' => $translated['question'] ?? $contentable->question,
                    'feedback' => $translated['feedback'] ?? $contentable->feedback,
                    'points' => $contentable->points,
                    'shuffle_items' => $contentable->shuffle_items,
                    'pairs' => $pairs,
                ];
                
            default:
                return $contentable;
        }
    }
}