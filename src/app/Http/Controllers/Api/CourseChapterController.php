<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Api\CourseChapterServiceInterface;
use App\Http\Requests\Api\CourseChapterRequest;
use App\Http\Resources\Api\CourseChapterResource;

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
}