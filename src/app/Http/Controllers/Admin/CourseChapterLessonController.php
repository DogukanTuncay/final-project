<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Admin\CourseChapterLessonServiceInterface;
use App\Http\Requests\Admin\CourseChapterLessonRequest;
use App\Http\Requests\Admin\CourseChapterOrderRequest;
use App\Http\Resources\Admin\CourseChapterLessonResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;

class CourseChapterLessonController extends BaseController
{
    protected CourseChapterLessonServiceInterface $courseChapterLessonService;

    public function __construct(CourseChapterLessonServiceInterface $courseChapterLessonService)
    {
        $this->courseChapterLessonService = $courseChapterLessonService;
    }

    /**
     * Tüm dersleri listeler
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $lessons = $this->courseChapterLessonService->all();
        return $this->successResponse(
            CourseChapterLessonResource::collection($lessons),
            'responses.course_chapter_lesson.list_success'
        );
    }

    /**
     * Bölüme göre dersleri listeler
     * 
     * @param int $chapterId
     * @return JsonResponse
     */
    public function byChapter(int $chapterId): JsonResponse
    {
        $lessons = $this->courseChapterLessonService->findByChapter($chapterId);
        return $this->successResponse(
            CourseChapterLessonResource::collection($lessons),
            'responses.course_chapter_lesson.list_by_chapter_success'
        );
    }

    /**
     * Yeni ders oluşturur
     * 
     * @param CourseChapterLessonRequest $request
     * @return JsonResponse
     */
    public function store(CourseChapterLessonRequest $request): JsonResponse
    {
        try {
            $lesson = $this->courseChapterLessonService->create($request->validated());
            return $this->successResponse(
                new CourseChapterLessonResource($lesson),
                'responses.course_chapter_lesson.created',
                Response::HTTP_CREATED
            );
        } catch (QueryException $e) {
            $message = $e->getMessage();
            $sqlState = $e->errorInfo[0] ?? null;
            
            if ($sqlState === '23505' && strpos($message, 'course_chapter_lessons_slug_unique') !== false) {
                return $this->errorResponse('errors.duplicate_lesson_slug', Response::HTTP_CONFLICT);
            }
            
            throw $e; // Başka bir hata ise yeniden fırlat
        }
    }

    /**
     * Ders detayını getirir
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $lesson = $this->courseChapterLessonService->find($id);
        
        if (!$lesson) {
            return $this->errorResponse(
                'responses.course_chapter_lesson.not_found',
                Response::HTTP_NOT_FOUND
            );
        }
        
        return $this->successResponse(
            new CourseChapterLessonResource($lesson),
            'responses.course_chapter_lesson.detail_success'
        );
    }

    /**
     * Ders bilgilerini günceller
     * 
     * @param CourseChapterLessonRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(CourseChapterLessonRequest $request, int $id): JsonResponse
    {
        try {
            $lesson = $this->courseChapterLessonService->find($id);
            
            if (!$lesson) {
                return $this->errorResponse(
                    'responses.course_chapter_lesson.not_found',
                    Response::HTTP_NOT_FOUND
                );
            }
            
            $result = $this->courseChapterLessonService->update($id, $request->validated());
            
            if (!$result) {
                return $this->errorResponse(
                    'responses.course_chapter_lesson.update_failed',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
            
            $lesson = $this->courseChapterLessonService->find($id);
            
            return $this->successResponse(
                new CourseChapterLessonResource($lesson),
                'responses.course_chapter_lesson.updated'
            );
        } catch (QueryException $e) {
            $message = $e->getMessage();
            $sqlState = $e->errorInfo[0] ?? null;
            
            if ($sqlState === '23505' && strpos($message, 'course_chapter_lessons_slug_unique') !== false) {
                return $this->errorResponse('errors.duplicate_lesson_slug', Response::HTTP_CONFLICT);
            }
            
            throw $e; // Başka bir hata ise yeniden fırlat
        }
    }

    /**
     * Dersi siler
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->courseChapterLessonService->delete($id);
        
        if (!$result) {
            return $this->errorResponse(
                'responses.course_chapter_lesson.not_found',
                Response::HTTP_NOT_FOUND
            );
        }
        
        return $this->successResponse(
            null,
            'responses.course_chapter_lesson.deleted'
        );
    }

    /**
     * Ders durumunu değiştirir
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $lesson = $this->courseChapterLessonService->toggleStatus($id);
        
        if (!$lesson) {
            return $this->errorResponse(
                'responses.course_chapter_lesson.not_found',
                Response::HTTP_NOT_FOUND
            );
        }
        
        return $this->successResponse(
            new CourseChapterLessonResource($lesson),
            'responses.course_chapter_lesson.status_updated'
        );
    }

    /**
     * Ders sırasını günceller
     * 
     * @param int $id
     * @param CourseChapterOrderRequest $request
     * @return JsonResponse
     */
    public function updateOrder(int $id, CourseChapterOrderRequest $request): JsonResponse
    {
        $lesson = $this->courseChapterLessonService->updateOrder($id, $request->input('order'));
        
        if (!$lesson) {
            return $this->errorResponse(
                'responses.course_chapter_lesson.not_found',
                Response::HTTP_NOT_FOUND
            );
        }
        
        return $this->successResponse(
            new CourseChapterLessonResource($lesson),
            'responses.course_chapter_lesson.order_updated'
        );
    }
}