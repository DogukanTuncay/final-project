<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CourseChapterLessonContentResource;
use App\Interfaces\Services\Api\CourseChapterLessonContentServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CourseChapterLessonContentController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;
    
    public function __construct(CourseChapterLessonContentServiceInterface $service)
    {
        $this->service = $service;
    }
    
    /**
     * ID'ye göre içerik bul
     */
    public function findById($id)
    {
        $content = $this->service->findById($id);
        if(!$content){
            return $this->errorResponse('responses.api.lesson-contents.not_found', 404);
        }
        return $this->successResponse(new CourseChapterLessonContentResource($content), 'responses.api.lesson-contents.find.success');
    }
    
    /**
     * Ders ID'sine göre içerikleri getir
     */
    public function getByLessonId($lessonId)
    {
        $contents = $this->service->getByLessonId($lessonId);
        if($contents->isEmpty()){
            return $this->errorResponse('responses.api.lesson-contents.not_found', 404);
        }
        return $this->successResponse(CourseChapterLessonContentResource::collection($contents), 'responses.api.lesson-contents.by-lesson.success');
    }
    
    /**
     * Ders ID'sine ve içerik tipine göre içerikleri getir
     */
    public function getByContentType($lessonId, $contentType)
    {
        // İçerik tipini sınıf adına dönüştür
        $contentTypeClass = $this->getContentTypeClass($contentType);
        if (!$contentTypeClass) {
            return $this->errorResponse('api.lesson-contents.invalid-type', 400);
        }
        
        $contents = $this->service->getByContentType($lessonId, $contentTypeClass);
        if(!$contents){
            return $this->errorResponse('responses.api.lesson-contents.not_found', 404);
        }
        return $this->successResponse(CourseChapterLessonContentResource::collection($contents), 'responses.api.lesson-contents.by-type.success');
    }
    
    /**
     * İçerik tipini sınıf adına dönüştür
     */
    private function getContentTypeClass($type)
    {
        $typeMap = [
            'text' => 'App\\Models\\Contents\\TextContent',
            'video' => 'App\\Models\\Contents\\VideoContent',
            'fill-in-the-blank' => 'App\\Models\\FillInTheBlank',
            'quiz' => 'App\\Models\\Quiz',
        ];
        
        return $typeMap[$type] ?? null;
    }
}