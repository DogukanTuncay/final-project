<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Api\CourseChapterLessonServiceInterface;
use App\Http\Resources\Api\CourseChapterLessonResource;

class CourseChapterLessonController extends BaseController
{
    protected $service;

    public function __construct(CourseChapterLessonServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Bölüme ait dersleri listele
     * @param int $chapterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function byChapter(int $chapterId)
    {
        $lessons = $this->service->findByChapter($chapterId);
        if(!$lessons){
            return $this->errorResponse('responses.course_chapter_lesson.not_found', 404);
        }

        return $this->successResponse(CourseChapterLessonResource::collection($lessons), 'responses.course_chapter_lesson.list_by_chapter_success');
    }

    /**
     * Ders detayını göster
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $lesson = $this->service->findActive($id);
        if(!$lesson){
            return $this->errorResponse('responses.course_chapter_lesson.not_found', 404);
        }
        return $this->successResponse(new CourseChapterLessonResource($lesson), 'responses.course_chapter_lesson.detail_success');
    }

    /**
     * Ders tamamlama durumunu güncelle
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsCompleted(int $id)
    {
        $lesson = $this->service->findActive($id);
        if(!$lesson){
            return $this->errorResponse('responses.course_chapter_lesson.not_found', 404);
        }
        $completion = $this->service->markAsCompleted($id);
        if(!$completion){
            return $this->errorResponse('responses.lesson_completion.already_completed', 400);
        }
        return $this->successResponse([
            'completed' => true,
            'completion_id' => $completion->id
        ], 'responses.lesson_completion.completed');
    }
}