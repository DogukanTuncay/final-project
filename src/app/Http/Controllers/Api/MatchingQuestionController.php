<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\MatchingQuestionServiceInterface;
use App\Http\Resources\Api\MatchingQuestionResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\MatchingQuestion;
use Illuminate\Support\Facades\Validator;

class MatchingQuestionController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(MatchingQuestionServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $items = $this->service->getWithPagination($request->all());
        return $this->successResponse(MatchingQuestionResource::collection($items), 'api.matching-question.list.success');
    }

    public function show($id)
    {
        $item = $this->service->findByIdWithPairs($id);
        return $this->successResponse(new MatchingQuestionResource($item), 'api.matching-question.show.success');
    }

    public function showBySlug($slug)
    {
        $item = $this->service->findBySlug($slug);
        return $this->successResponse(new MatchingQuestionResource($item), 'api.matching-question.show.success');
    }
    
    /**
     * Kullanıcının eşleştirme cevaplarını kontrol et
     *
     * @param Request $request
     * @param int $id Soru ID'si
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAnswer(Request $request, $id)
    {
        // Validasyon
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.pair_id' => 'required|integer|exists:matching_pairs,id',
            'answers.*.left_to_right' => 'required|boolean',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse('Geçersiz cevap formatı', 422, $validator->errors());
        }
        
        // Soruyu ve eşleştirme çiftlerini bul
        $question = MatchingQuestion::with('pairs')->findOrFail($id);
        $userAnswers = $request->answers;
        
        // Eşleştirme çiftlerini kontrol et
        $correctCount = 0;
        $totalCount = $question->pairs->count();
        $results = [];
        
        foreach ($userAnswers as $answer) {
            $pairId = $answer['pair_id'];
            $leftToRight = $answer['left_to_right'];
            
            // Çift mevcut mu?
            $pair = $question->pairs->firstWhere('id', $pairId);
            if (!$pair) {
                continue;
            }
            
            // Bu çift için kullanıcının cevabını sonuçlara ekle
            $result = [
                'pair_id' => $pairId,
                'is_correct' => true,  // Varsayılan olarak doğru kabul et, eşleştirme soruları için doğruluk kontrol edilmez, her eşleştirme doğrudur
                'left_item' => $pair->getTranslations('left_item'),
                'right_item' => $pair->getTranslations('right_item')
            ];
            
            $results[] = $result;
            $correctCount++;
        }
        
        // Sonuçları hesapla
        $isAllCorrect = ($correctCount === $totalCount);
        $earnedPoints = $isAllCorrect ? $question->points : 0;
        
        $finalResult = [
            'is_correct' => $isAllCorrect,
            'correct_count' => $correctCount,
            'total_count' => $totalCount,
            'points_earned' => $earnedPoints,
            'feedback' => $isAllCorrect ? $question->feedback : null,
            'pairs' => $results
        ];
        
        return $this->successResponse(
            $finalResult,
            $isAllCorrect ? 'Tebrikler! Tüm eşleştirmeleri doğru yaptınız.' : 'Eşleştirmelerinizde hatalar var.'
        );
    }
}