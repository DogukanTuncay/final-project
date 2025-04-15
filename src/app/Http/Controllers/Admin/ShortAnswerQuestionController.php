<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\ShortAnswerQuestionServiceInterface;
use App\Http\Requests\Admin\ShortAnswerQuestionRequest;
use App\Http\Resources\Admin\ShortAnswerQuestionResource;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShortAnswerQuestionController extends BaseController
{
    
    protected $service;

    public function __construct(ShortAnswerQuestionServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(ShortAnswerQuestionResource::collection($items), 'admin.ShortAnswerQuestion.list.success');
    }

    public function store(ShortAnswerQuestionRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new ShortAnswerQuestionResource($item), 'admin.ShortAnswerQuestion.create.success');
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new ShortAnswerQuestionResource($item), 'admin.ShortAnswerQuestion.show.success');
    }

    public function update(ShortAnswerQuestionRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return $this->successResponse(new ShortAnswerQuestionResource($item), 'admin.ShortAnswerQuestion.update.success');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'admin.ShortAnswerQuestion.delete.success');
    }
    
    /**
     * Kısa cevaplı sorunun aktiflik durumunu değiştir
     */
    public function toggleStatus($id)
    {
        $question = $this->service->find($id);
        
        if (!$question) {
            return $this->errorResponse('admin.short-answer-questions.not-found', 404);
        }
        
        $status = $this->service->toggleStatus($id);
        
        return $this->successResponse(
            ['is_active' => $status],
            'admin.short-answer-questions.status-updated'
        );
    }
    
    /**
     * Kısa cevaplı soruyu bir derse ekle
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
            return $this->errorResponse('Kısa cevaplı soru bulunamadı.', 404);
        }
        
        // CourseChapterLessonContentService'e enjekte et
        $lessonContentService = app(\App\Interfaces\Services\Admin\CourseChapterLessonContentServiceInterface::class);
        
        try {
            // Kısa cevaplı soruyu ders içeriği olarak ekle
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
                'Kısa cevaplı soru derse başarıyla eklendi.'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse('Soru derse eklenirken bir hata oluştu: ' . $e->getMessage(), 500);
        }
    }
}