<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Api\CourseChapterServiceInterface;
use App\Http\Requests\Api\CourseChapterRequest;
use App\Http\Resources\Api\CourseChapterResource;
use App\Http\Resources\Api\ChapterCompletionResource;

class CourseChapterController extends BaseController
{
    protected $service;

    public function __construct(CourseChapterServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Tüm aktif bölümleri listele
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $courseChapters = $this->service->all();
        return $this->successResponse(CourseChapterResource::collection($courseChapters), 'responses.course_chapters.list_success');
    }

    /**
     * Kursa göre aktif bölümleri listele
     * @param int $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function byCourse(int $courseId)
    {
        $courseChapters = $this->service->findByCourse($courseId);
        if(!$courseChapters){
            return $this->errorResponse('responses.course_chapters.not_found', 404);
        }
        return $this->successResponse(CourseChapterResource::collection($courseChapters), 'responses.course_chapters.list_by_course_success');
    }

    /**
     * Bölüm detayını getir
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $courseChapter = $this->service->findActiveWithCourse($id);
        if(!$courseChapter){
            return $this->errorResponse('responses.course_chapter.not_found', 404);
        }
        return $this->successResponse(new CourseChapterResource($courseChapter), 'responses.course_chapter.detail_success');
    }
    
    /**
     * Bölümü tamamlandı olarak işaretle
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(int $id)
    {
        $result = $this->service->markAsCompleted($id);
        
        if (!$result['success']) {
            return $this->errorResponse(
                $result['message'],
                $result['code'],
                $result['data'] ?? []
            );
        }
        
        return $this->successResponse(
            new ChapterCompletionResource($result['data']),
            $result['message']
        );
    }
    
    /**
     * Bölümün ön koşullarını getir
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function prerequisites(int $id)
    {
        $prerequisites = $this->service->getPrerequisites($id);
        
        return $this->successResponse(
            CourseChapterResource::collection($prerequisites),
            'responses.course_chapter.prerequisites_success'
        );
    }
    
    /**
     * Bölümün kilit durumunu kontrol et
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkLockStatus(int $id)
    {
        $lockStatus = $this->service->checkLockStatus($id);
        
        return $this->successResponse(
            $lockStatus,
            'responses.course_chapter.lock_status_success'
        );
    }
}