<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\CourseChapterServiceInterface;
use App\Http\Requests\Admin\CourseChapterRequest;
use App\Http\Requests\Admin\CourseChapterOrderRequest;
use App\Http\Resources\Admin\CourseChapterResource;
use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
    use Illuminate\Database\QueryException;

class CourseChapterController extends BaseController
{
    private CourseChapterServiceInterface $courseChapterService;

    public function __construct(CourseChapterServiceInterface $courseChapterService)
    {
        $this->courseChapterService = $courseChapterService;
    }

    /**
     * Tüm bölümleri listeler
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $courseChapters = $this->courseChapterService->all();
        return $this->successResponse(
            CourseChapterResource::collection($courseChapters),
            'responses.course_chapters.list_success'
        );
    }

    /**
     * Kursa göre bölümleri listeler
     * 
     * @param int $courseId
     * @return JsonResponse
     */
    public function byCourse(int $courseId): JsonResponse
    {
        $courseChapters = $this->courseChapterService->findByCourse($courseId);
        return $this->successResponse(
            CourseChapterResource::collection($courseChapters),
            'responses.course_chapter.list_by_course_success'
        );
    }

    /**
     * Yeni bölüm oluşturur
     * 
     * @param CourseChapterRequest $request
     * @return JsonResponse
     */
    public function store(CourseChapterRequest $request): JsonResponse
    {
        try {
            $courseChapter = $this->courseChapterService->create($request->validated());
            return $this->successResponse(
                new CourseChapterResource($courseChapter),
                'responses.course_chapter.created',
                Response::HTTP_CREATED
            );
        } catch (QueryException $e) {
            // TODO: Geçici çözüm - Exception Handler çalışmadığı için controller'da yakalıyoruz
            $message = $e->getMessage();
            $sqlState = $e->errorInfo[0] ?? null;
            
            if ($sqlState === '23505' && strpos($message, 'course_chapters_slug_unique') !== false) {
                return $this->errorResponse('errors.duplicate_course_chapter_slug', Response::HTTP_CONFLICT,$e->getMessage());
            }
            
            throw $e; // Başka bir hata ise yeniden fırlat
        }
    }

    /**
     * Bölüm detayını getirir
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $courseChapter = $this->courseChapterService->find($id);
        
        if (!$courseChapter) {
            return $this->errorResponse(
                'responses.course_chapter.not_found',
                Response::HTTP_NOT_FOUND
            );
        }
        
        return $this->successResponse(
            new CourseChapterResource($courseChapter),
            'responses.course_chapter.detail_success'
        );
    }

    /**
     * Bölüm bilgilerini günceller
     * 
     * @param CourseChapterRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(CourseChapterRequest $request, int $id): JsonResponse
    {
        try {
            
            $courseChapter = $this->courseChapterService->find($id);
            
            if (!$courseChapter) {
                return $this->errorResponse(
                    'responses.course_chapter.not_found',
                    Response::HTTP_NOT_FOUND
                );
            }
            $result = $this->courseChapterService->update($id, $request->validated());
            if(!$result){
                return $this->errorResponse('responses.course_chapter.update_failed', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return $this->successResponse(
                new CourseChapterResource($courseChapter),
                'responses.course_chapter.updated'
            );
        } catch (QueryException $e) {
            // TODO: Geçici çözüm - Exception Handler çalışmadığı için controller'da yakalıyoruz
            $message = $e->getMessage();
            $sqlState = $e->errorInfo[0] ?? null;
            
            if ($sqlState === '23505' && strpos($message, 'course_chapters_slug_unique') !== false) {
                return $this->errorResponse('errors.duplicate_course_chapter_slug', Response::HTTP_CONFLICT);
            }
            
            throw $e; // Başka bir hata ise yeniden fırlat
        }
    }

    /**
     * Bölümü siler
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->courseChapterService->delete($id);
        
        if (!$result) {
            return $this->errorResponse(
                'responses.course_chapter.not_found',
                Response::HTTP_NOT_FOUND
            );
        }
        
        return $this->successResponse(
            null,
            'responses.course_chapter.deleted'
        );
    }

    /**
     * Bölüm durumunu değiştirir
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $courseChapter = $this->courseChapterService->find($id);
        
        if (!$courseChapter) {
            return $this->errorResponse(
                'responses.course_chapter.not_found',
                Response::HTTP_NOT_FOUND
            );
        }
        $courseChapter = $this->courseChapterService->toggleStatus($id);
        
        return $this->successResponse(
            new CourseChapterResource($courseChapter),
            'responses.course_chapter.status_updated'
        );
    }

    /**
     * Bölüm sırasını günceller
     * 
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateOrder(int $id,   CourseChapterOrderRequest $request): JsonResponse
    {
        $courseChapter = $this->courseChapterService->find($id);
        if (!$courseChapter) {
            return $this->errorResponse(
                'responses.course_chapter.not_found',
                Response::HTTP_NOT_FOUND
            );
        }

        $result = $this->courseChapterService->updateOrder($id, $request->input('order'));
        

        return $this->successResponse(
            new CourseChapterResource($result),
            'responses.course_chapter.order_updated'
        );
    }
}