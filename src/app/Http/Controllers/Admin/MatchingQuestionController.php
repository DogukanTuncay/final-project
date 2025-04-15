<?php

namespace App\Http\Controllers\Admin;

use App\Interfaces\Services\Admin\MatchingQuestionServiceInterface;
use App\Http\Requests\Admin\MatchingQuestionRequest;
use App\Http\Requests\Admin\MatchingPairRequest;
use App\Http\Resources\Admin\MatchingQuestionResource;
use App\Http\Resources\Admin\MatchingPairResource;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\BaseController;
use App\Models\MatchingPair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MatchingQuestionController extends BaseController
{
    
    protected $service;

    public function __construct(MatchingQuestionServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(MatchingQuestionResource::collection($items), 'admin.matching-questions.list.success');
    }

    public function store(MatchingQuestionRequest $request)
    {
        try {
            // Transaction başlat
            \DB::beginTransaction();
            
            // Önce eşleştirme sorusunu oluştur
            $validatedData = $request->validated();
            $pairsData = null;
            
            // Pairs verilerini ayır
            if (isset($validatedData['pairs'])) {
                $pairsData = $validatedData['pairs'];
                unset($validatedData['pairs']);
            }
            
            // Ana soruyu oluştur
            $item = $this->service->create($validatedData);
            // Eğer eşleştirme çiftleri gönderilmişse onları da oluştur
            if ($pairsData && is_array($pairsData)) {
                foreach ($pairsData as $index => $pairData) {
                    // Sıra belirtilmemişse index'i kullan
                    if (!isset($pairData['order'])) {
                        $pairData['order'] = $index + 1;
                    }
                    
                    // Eşleştirme sorusu ID'sini ekle
                    $pairData['matching_question_id'] = $item->id;
                    
                    // Eşleştirme çiftini oluştur
                    MatchingPair::create($pairData);
                }
            }
            
            // İşlemi tamamla
            \DB::commit();
            
            // İlişkili çiftlerle birlikte soruyu getir ve cevap gönder

            return $this->successResponse(new MatchingQuestionResource($item), 'admin.matching-questions.create.success');
            
        } catch (\Exception $e) {
            // Hata durumunda geri al
            \DB::rollBack();
            return $this->errorResponse('Eşleştirme sorusu oluşturulurken bir hata oluştu: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new MatchingQuestionResource($item), 'admin.matching-questions.show.success');
    }

    public function update(MatchingQuestionRequest $request, $id)
    {
        try {
            // Transaction başlat
            \DB::beginTransaction();
            
            // Eşleştirme sorusunu bul
            $question = $this->service->find($id);
            
            if (!$question) {
                return $this->errorResponse('Eşleştirme sorusu bulunamadı.', 404);
            }
            
            // Önce eşleştirme sorusunu güncelle
            $validatedData = $request->validated();
            $pairsData = null;
            
            // Pairs verilerini ayır
            if (isset($validatedData['pairs'])) {
                $pairsData = $validatedData['pairs'];
                unset($validatedData['pairs']);
            }
            
            // Ana soruyu güncelle
            $this->service->update($id, $validatedData);
            
            // Eğer eşleştirme çiftleri gönderilmişse, mevcut çiftleri sil ve yeni çiftleri oluştur
            if ($pairsData && is_array($pairsData)) {
                // Mevcut çiftleri sil (tümünü değiştirebilmek için)
                MatchingPair::where('matching_question_id', $id)->delete();
                
                foreach ($pairsData as $index => $pairData) {
                    // Sıra belirtilmemişse index'i kullan
                    if (!isset($pairData['order'])) {
                        $pairData['order'] = $index + 1;
                    }
                    
                    // Eşleştirme sorusu ID'sini ekle
                    $pairData['matching_question_id'] = $id;
                    
                    // Eşleştirme çiftini oluştur
                    MatchingPair::create($pairData);
                }
            }
            
            // İşlemi tamamla
            \DB::commit();
            
            // İlişkili çiftlerle birlikte soruyu getir ve cevap gönder
            $result = $this->service->with('pairs')->find($id);
            return $this->successResponse(new MatchingQuestionResource($result), 'admin.matching-questions.update.success');
            
        } catch (\Exception $e) {
            // Hata durumunda geri al
            \DB::rollBack();
            return $this->errorResponse('Eşleştirme sorusu güncellenirken bir hata oluştu: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'admin.matching-questions.delete.success');
    }
    
    /**
     * Eşleştirme sorusunun aktiflik durumunu değiştir
     */
    public function toggleStatus($id)
    {
        $question = $this->service->find($id);
        
        if (!$question) {
            return $this->errorResponse('admin.matching-questions.not-found', 404);
        }
        
        $status = $this->service->toggleStatus($id);
        
        return $this->successResponse(
            ['is_active' => $status],
            'admin.matching-questions.status-updated'
        );
    }
    
    /**
     * Eşleştirme sorusunu bir derse ekle
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
            return $this->errorResponse('Eşleştirme sorusu bulunamadı.', 404);
        }
        
        // CourseChapterLessonContentService'e enjekte et
        $lessonContentService = app(\App\Interfaces\Services\Admin\CourseChapterLessonContentServiceInterface::class);
        
        try {
            // Eşleştirme sorusunu ders içeriği olarak ekle
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
                'Eşleştirme sorusu derse başarıyla eklendi.'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse('Soru derse eklenirken bir hata oluştu: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Eşleştirme sorusuna yeni çift ekle
     * 
     * @param MatchingPairRequest $request
     * @param int $questionId Eşleştirme sorusu ID'si
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPair(MatchingPairRequest $request, $questionId)
    {
        // Sorunun var olduğunu kontrol et
        $question = $this->service->find($questionId);
        
        if (!$question) {
            return $this->errorResponse('Eşleştirme sorusu bulunamadı.', 404);
        }
        
        try {
            // Yeni eşleştirme çiftini oluştur
            $validatedData = $request->validated();
            $validatedData['matching_question_id'] = $questionId;
            
            $pair = MatchingPair::create($validatedData);
            
            return $this->successResponse(
                new MatchingPairResource($pair),
                'Eşleştirme çifti başarıyla eklendi.',
                201
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse('Eşleştirme çifti eklenirken bir hata oluştu: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Eşleştirme çiftini güncelle
     * 
     * @param MatchingPairRequest $request
     * @param int $pairId Eşleştirme çifti ID'si
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePair(MatchingPairRequest $request, $pairId)
    {
        try {
            $pair = MatchingPair::find($pairId);
            
            if (!$pair) {
                return $this->errorResponse('Eşleştirme çifti bulunamadı.', 404);
            }
            
            $pair->update($request->validated());
            
            return $this->successResponse(
                new MatchingPairResource($pair),
                'Eşleştirme çifti başarıyla güncellendi.'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse('Eşleştirme çifti güncellenirken bir hata oluştu: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Eşleştirme çiftini sil
     * 
     * @param int $pairId Eşleştirme çifti ID'si
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePair($pairId)
    {
        try {
            $pair = MatchingPair::find($pairId);
            
            if (!$pair) {
                return $this->errorResponse('Eşleştirme çifti bulunamadı.', 404);
            }
            
            $pair->delete();
            
            return $this->successResponse(
                null,
                'Eşleştirme çifti başarıyla silindi.'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse('Eşleştirme çifti silinirken bir hata oluştu: ' . $e->getMessage(), 500);
        }
    }
}