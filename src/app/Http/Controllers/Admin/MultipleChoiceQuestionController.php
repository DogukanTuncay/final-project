<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MultipleChoiceQuestionRequest;
use App\Http\Resources\Admin\MultipleChoiceQuestionResource;
use App\Interfaces\Services\Admin\MultipleChoiceQuestionServiceInterface;
use App\Models\CourseChapterLessonContent;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;

class MultipleChoiceQuestionController extends BaseController
{
    
    protected $service;
    
    public function __construct(MultipleChoiceQuestionServiceInterface $service)
    {
        $this->service = $service;
    }
    
    /**
     * Tüm çoktan seçmeli soruları listele
     */
    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(MultipleChoiceQuestionResource::collection($items), 'Çoktan seçmeli sorular başarıyla listelendi.');
    }
    
    /**
     * Tekil çoktan seçmeli soru göster
     */
    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new MultipleChoiceQuestionResource($item), 'Çoktan seçmeli soru başarıyla gösterildi.');
    }
    
    /**
     * Çoktan seçmeli soru oluştur
     */
    public function store(MultipleChoiceQuestionRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new MultipleChoiceQuestionResource($item), 'Çoktan seçmeli soru başarıyla oluşturuldu.');
    }
    
    /**
     * Çoktan seçmeli soru güncelle
     */
    public function update(MultipleChoiceQuestionRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return $this->successResponse(new MultipleChoiceQuestionResource($item), 'Çoktan seçmeli soru başarıyla güncellendi.');
    }
    
    /**
     * Çoktan seçmeli soru sil
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Çoktan seçmeli soru başarıyla silindi.');
    }
    
    /**
     * Çoktan seçmeli sorunun aktiflik durumunu değiştir
     */
    public function toggleStatus($id)
    {
        $question = $this->service->find($id);
        
        if (!$question) {
            return $this->errorResponse('admin.multiple-choice-questions.not-found', 404);
        }
        
        $status = $this->service->toggleStatus($id);
        
        return $this->successResponse(
            ['is_active' => $status],
            'admin.multiple-choice-questions.status-updated'
        );
    }
    
    /**
     * Çoktan seçmeli soruyu bir derse ekle
     * 
     * @param Request $request
     * @param int $id Soru ID'si
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToLesson(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|exists:course_chapter_lessons,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'meta_data' => 'nullable|array'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse('Validation Error', 422, $validator->errors());
        }
        
        // Önce sorunun var olduğunu kontrol et
        $question = $this->service->find($id);
        
        if (!$question) {
            return $this->errorResponse('Çoktan seçmeli soru bulunamadı.', 404);
        }
        
        // CourseChapterLessonContentService'e enjekte et
        $lessonContentService = app(\App\Interfaces\Services\Admin\CourseChapterLessonContentServiceInterface::class);
        
        try {
            // Çoktan seçmeli soruyu ders içeriği olarak ekle
            $content = $lessonContentService->createWithContent(
                $request->lesson_id,
                $question,
                [
                    'order' => $request->order ?? 0,
                    'is_active' => $request->is_active ?? true,
                    'meta_data' => $request->meta_data ?? null
                ]
            );
            
            return $this->successResponse(
                new \App\Http\Resources\Admin\CourseChapterLessonContentResource($content), 
                'Çoktan seçmeli soru derse başarıyla eklendi.'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse('Soru derse eklenirken bir hata oluştu: ' . $e->getMessage(), 500);
        }
    }
}