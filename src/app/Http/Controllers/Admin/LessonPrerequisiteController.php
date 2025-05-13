<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Admin\CourseChapterLessonServiceInterface;
use App\Http\Requests\Admin\PrerequisiteRequest;
use App\Http\Resources\Admin\PrerequisiteResource;
use App\Http\Resources\Admin\CourseChapterLessonResource;

class LessonPrerequisiteController extends BaseController
{
    protected $service;

    public function __construct(CourseChapterLessonServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Bir dersin mevcut ön koşullarını listele
     * 
     * @param int $lessonId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $lessonId)
    {
        $lesson = $this->service->find($lessonId);
        
        if (!$lesson) {
            return $this->errorResponse('responses.lesson.not_found', 404);
        }
        
        $prerequisites = $this->service->getPrerequisites($lessonId);
        
        return $this->successResponse(
            PrerequisiteResource::collection($prerequisites),
            'responses.lesson_prerequisites.list_success'
        );
    }

    /**
     * Bir derse yeni ön koşul ekle
     * 
     * @param PrerequisiteRequest $request
     * @param int $lessonId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PrerequisiteRequest $request, int $lessonId)
    {
        $lesson = $this->service->find($lessonId);
        
        if (!$lesson) {
            return $this->errorResponse('responses.lesson.not_found', 404);
        }
        
        $prerequisiteIds = $request->prerequisite_ids;
        
        $success = $this->service->addPrerequisites($lessonId, $prerequisiteIds);
        
        if (!$success) {
            return $this->errorResponse('responses.lesson_prerequisites.add_error', 400);
        }
        
        $prerequisites = $this->service->getPrerequisites($lessonId);
        
        return $this->successResponse(
            PrerequisiteResource::collection($prerequisites),
            'responses.lesson_prerequisites.add_success'
        );
    }

    /**
     * Bir dersin tüm ön koşullarını güncelle
     * 
     * @param PrerequisiteRequest $request
     * @param int $lessonId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PrerequisiteRequest $request, int $lessonId)
    {
        $lesson = $this->service->find($lessonId);
        
        if (!$lesson) {
            return $this->errorResponse('responses.lesson.not_found', 404);
        }
        
        $prerequisiteIds = $request->prerequisite_ids;
        
        $success = $this->service->updatePrerequisites($lessonId, $prerequisiteIds);
        
        if (!$success) {
            return $this->errorResponse('responses.lesson_prerequisites.update_error', 400);
        }
        
        $prerequisites = $this->service->getPrerequisites($lessonId);
        
        return $this->successResponse(
            PrerequisiteResource::collection($prerequisites),
            'responses.lesson_prerequisites.update_success'
        );
    }

    /**
     * Bir dersten belirli bir ön koşulu kaldır
     * 
     * @param int $lessonId
     * @param int $prerequisiteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $lessonId, int $prerequisiteId)
    {
        $lesson = $this->service->find($lessonId);
        
        if (!$lesson) {
            return $this->errorResponse('responses.lesson.not_found', 404);
        }
        
        $success = $this->service->removePrerequisites($lessonId, [$prerequisiteId]);
        
        if (!$success) {
            return $this->errorResponse('responses.lesson_prerequisites.remove_error', 400);
        }
        
        return $this->successResponse(
            null,
            'responses.lesson_prerequisites.remove_success'
        );
    }

    /**
     * Bir dersin tüm ön koşullarını temizle
     * 
     * @param int $lessonId
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear(int $lessonId)
    {
        $lesson = $this->service->find($lessonId);
        
        if (!$lesson) {
            return $this->errorResponse('responses.lesson.not_found', 404);
        }
        
        $success = $this->service->clearPrerequisites($lessonId);
        
        if (!$success) {
            return $this->errorResponse('responses.lesson_prerequisites.clear_error', 400);
        }
        
        return $this->successResponse(
            null,
            'responses.lesson_prerequisites.clear_success'
        );
    }

    /**
     * Bir ders için mevcut olabilecek potansiyel ön koşulları listele
     * 
     * @param int $lessonId
     * @return \Illuminate\Http\JsonResponse
     */
    public function availablePrerequisites(int $lessonId)
    {
        $lesson = $this->service->find($lessonId);
        
        if (!$lesson) {
            return $this->errorResponse('responses.lesson.not_found', 404);
        }
        
        // Aynı bölüme ait olan, mevcut dersten farklı ve aktif olan dersleri getir
        $chapterId = $lesson->course_chapter_id;
        $allLessons = $this->service->findByChapter($chapterId);
        
        // Mevcut ders ve halihazırdaki ön koşulları hariç tut
        $existingPrerequisiteIds = $this->service->getPrerequisites($lessonId)->pluck('id')->toArray();
        $availableLessons = $allLessons->filter(function($item) use ($lessonId, $existingPrerequisiteIds) {
            return $item->id != $lessonId && !in_array($item->id, $existingPrerequisiteIds);
        });
        
        return $this->successResponse(
            CourseChapterLessonResource::collection($availableLessons),
            'responses.lesson_prerequisites.available_success'
        );
    }
} 