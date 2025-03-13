<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class CourseChapterLessonContentResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource->contentable);
        
        return [
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
                    'thumbnail' => $contentable->thumbnail,
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