<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;

class CourseChapterLessonContentResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource->contentable);
        
        return [
            'id' => $this->id,
            'course_chapter_lesson_id' => $this->course_chapter_lesson_id,
            'content_type' => $this->getContentTypeName($this->contentable_type),
            'order' => $this->order,
            'content' => $this->formatContent($this->contentable, $translated),
        ];
    }
    
    /**
     * İçerik tipini insana okunabilir formata çevir
     */
    private function getContentTypeName($contentableType)
    {
        $types = [
            'App\\Models\\Contents\\TextContent' => 'text',
            'App\\Models\\Contents\\VideoContent' => 'video',
            'App\\Models\\FillInTheBlank' => 'fill-in-the-blank',
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
                ];
                
            default:
                return $contentable;
        }
    }
}