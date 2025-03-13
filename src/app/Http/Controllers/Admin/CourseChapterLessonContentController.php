<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseChapterLessonContentRequest;
use App\Http\Resources\Admin\CourseChapterLessonContentResource;
use App\Interfaces\Services\Admin\CourseChapterLessonContentServiceInterface;
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
     * Tüm içerikleri listele
     */
    public function index()
    {
        $contents = $this->service->all();
        return $this->successResponse(CourseChapterLessonContentResource::collection($contents), 'responses.admin.lesson-contents.list.success');
    }
    
    /**
     * Tekil içerik göster
     */
    public function show($id)
    {
        $content = $this->service->find($id);
        return $this->successResponse(new CourseChapterLessonContentResource($content), 'responses.admin.lesson-contents.show.success');
    }
    
    /**
     * İçerik oluştur (genel)
     */
    public function store(CourseChapterLessonContentRequest $request)
    {
        $content = $this->service->create($request->validated());
        return $this->successResponse(new CourseChapterLessonContentResource($content), 'responses.admin.lesson-contents.create.success');
    }
    
    /**
     * İçerik güncelle
     */
    public function update(CourseChapterLessonContentRequest $request, $id)
    {
        $content = $this->service->update($id, $request->validated());
        return $this->successResponse(new CourseChapterLessonContentResource($content), 'responses.admin.lesson-contents.update.success');
    }
    
    /**
     * İçerik sil
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'responses.admin.lesson-contents.delete.success');
    }
    
    /**
     * İçeriğin aktiflik durumunu değiştir
     */
    public function toggleStatus(Request $request, $id)
    {
        $content = $this->service->update($id, ['is_active' => $request->is_active]);
        return $this->successResponse(new CourseChapterLessonContentResource($content), 'responses.admin.lesson-contents.status.success');
    }
    
    /**
     * İçeriğin sırasını güncelle
     */
    public function updateOrder(Request $request, $id)
    {
        $this->service->updateOrder($id, $request->order);
        return $this->successResponse(null, 'responses.admin.lesson-contents.order.success');
    }
    
    /**
     * İçeriklerin sırasını toplu güncelle
     */
    public function bulkUpdateOrder(Request $request)
    {
        $this->service->bulkUpdateOrder($request->orders);
        return $this->successResponse(null, 'responses.admin.lesson-contents.bulk-order.success');
    }
    
    /**
     * Derse göre içerikleri getir
     */
    public function byLesson($lessonId)
    {
        $contents = $this->service->getByLessonId($lessonId);
        return $this->successResponse(CourseChapterLessonContentResource::collection($contents), 'responses.admin.lesson-contents.by-lesson.success');
    }
    
    /**
     * Metin içeriği oluştur
     */
    public function createTextContent(Request $request)
    {
        $content = $this->service->createTextContent(
            $request->lesson_id,
            ['content' => $request->content],
            [
                'order' => $request->order ?? 0,
                'is_active' => $request->is_active ?? true,
                'meta_data' => $request->meta_data ?? null
            ]
        );
        
        return $this->successResponse(new CourseChapterLessonContentResource($content), 'responses.admin.lesson-contents.create-text.success');
    }
    
    /**
     * Video içeriği oluştur
     */
    public function createVideoContent(Request $request)
    {
        $content = $this->service->createVideoContent(
            $request->lesson_id,
            [
                'title' => $request->title,
                'description' => $request->description,
                'video_url' => $request->video_url,
                'provider' => $request->provider ?? 'youtube',
                'duration' => $request->duration,
                'thumbnail' => $request->thumbnail,
            ],
            [
                'order' => $request->order ?? 0,
                'is_active' => $request->is_active ?? true,
                'meta_data' => $request->meta_data ?? null
            ]
        );
        
        return $this->successResponse(new CourseChapterLessonContentResource($content), 'responses.admin.lesson-contents.create-video.success');
    }
    
    /**
     * Boşluk doldurma içeriği oluştur
     */
    public function createFillInTheBlankContent(Request $request)
    {
        $content = $this->service->createFillInTheBlankContent(
            $request->lesson_id,
            [
                'question' => $request->question,
                'answers' => $request->answers,
            ],
            [
                'order' => $request->order ?? 0,
                'is_active' => $request->is_active ?? true,
                'meta_data' => $request->meta_data ?? null
            ]
        );
        
        return $this->successResponse(new CourseChapterLessonContentResource($content), 'responses.admin.lesson-contents.create-fill-in-the-blank.success');
    }
}