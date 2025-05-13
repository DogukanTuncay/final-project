<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Admin\CourseChapterServiceInterface;
use App\Http\Requests\Admin\PrerequisiteRequest;
use App\Http\Resources\Admin\PrerequisiteResource;
use App\Http\Resources\Admin\CourseChapterResource;

class ChapterPrerequisiteController extends BaseController
{
    protected $service;

    public function __construct(CourseChapterServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Bir bölümün mevcut ön koşullarını listele
     * 
     * @param int $chapterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $chapterId)
    {
        $chapter = $this->service->find($chapterId);
        
        if (!$chapter) {
            return $this->errorResponse('responses.chapter.not_found', 404);
        }
        
        $prerequisites = $this->service->getPrerequisites($chapterId);
        
        return $this->successResponse(
            PrerequisiteResource::collection($prerequisites),
            'responses.chapter_prerequisites.list_success'
        );
    }

    /**
     * Bir bölüme yeni ön koşul ekle
     * 
     * @param PrerequisiteRequest $request
     * @param int $chapterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PrerequisiteRequest $request, int $chapterId)
    {
        $chapter = $this->service->find($chapterId);
        
        if (!$chapter) {
            return $this->errorResponse('responses.chapter.not_found', 404);
        }
        
        $prerequisiteIds = $request->prerequisite_ids;
        
        $success = $this->service->addPrerequisites($chapterId, $prerequisiteIds);
        if (!$success) {
            return $this->errorResponse('responses.chapter_prerequisites.add_error', 400);
        }
        
        $prerequisites = $this->service->getPrerequisites($chapterId);
        
        return $this->successResponse(
            PrerequisiteResource::collection($prerequisites),
            'responses.chapter_prerequisites.add_success'
        );
    }

    /**
     * Bir bölümün tüm ön koşullarını güncelle
     * 
     * @param PrerequisiteRequest $request
     * @param int $chapterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PrerequisiteRequest $request, int $chapterId)
    {
        $chapter = $this->service->find($chapterId);
        
        if (!$chapter) {
            return $this->errorResponse('responses.chapter.not_found', 404);
        }
        
        $prerequisiteIds = $request->prerequisite_ids;
        
        $success = $this->service->updatePrerequisites($chapterId, $prerequisiteIds);
        
        if (!$success) {
            return $this->errorResponse('responses.chapter_prerequisites.update_error', 400);
        }
        
        $prerequisites = $this->service->getPrerequisites($chapterId);
        
        return $this->successResponse(
            PrerequisiteResource::collection($prerequisites),
            'responses.chapter_prerequisites.update_success'
        );
    }

    /**
     * Bir bölümden belirli bir ön koşulu kaldır
     * 
     * @param int $chapterId
     * @param int $prerequisiteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $chapterId, int $prerequisiteId)
    {
        $chapter = $this->service->find($chapterId);
        
        if (!$chapter) {
            return $this->errorResponse('responses.chapter.not_found', 404);
        }
        
        $success = $this->service->removePrerequisites($chapterId, [$prerequisiteId]);
        
        if (!$success) {
            return $this->errorResponse('responses.chapter_prerequisites.remove_error', 400);
        }
        
        return $this->successResponse(
            null,
            'responses.chapter_prerequisites.remove_success'
        );
    }

    /**
     * Bir bölümün tüm ön koşullarını temizle
     * 
     * @param int $chapterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear(int $chapterId)
    {
        $chapter = $this->service->find($chapterId);
        
        if (!$chapter) {
            return $this->errorResponse('responses.chapter.not_found', 404);
        }
        
        $success = $this->service->clearPrerequisites($chapterId);
        
        if (!$success) {
            return $this->errorResponse('responses.chapter_prerequisites.clear_error', 400);
        }
        
        return $this->successResponse(
            null,
            'responses.chapter_prerequisites.clear_success'
        );
    }

    /**
     * Bir bölüm için mevcut olabilecek potansiyel ön koşulları listele
     * 
     * @param int $chapterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function availablePrerequisites(int $chapterId)
    {
        $chapter = $this->service->find($chapterId);
        
        if (!$chapter) {
            return $this->errorResponse('responses.chapter.not_found', 404);
        }
        
        // Aynı kursa ait olan, mevcut bölümden farklı ve aktif olan bölümleri getir
        $courseId = $chapter->course_id;
        $allChapters = $this->service->findByCourse($courseId);
        
        // Mevcut bölüm ve halihazırdaki ön koşulları hariç tut
        $existingPrerequisiteIds = $this->service->getPrerequisites($chapterId)->pluck('id')->toArray();
        $availableChapters = $allChapters->filter(function($item) use ($chapterId, $existingPrerequisiteIds) {
            return $item->id != $chapterId && !in_array($item->id, $existingPrerequisiteIds);
        });
        
        return $this->successResponse(
            CourseChapterResource::collection($availableChapters),
            'responses.chapter_prerequisites.available_success'
        );
    }
} 