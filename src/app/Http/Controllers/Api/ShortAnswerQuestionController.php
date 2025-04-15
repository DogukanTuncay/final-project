<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\ShortAnswerQuestionServiceInterface;
use App\Http\Resources\Api\ShortAnswerQuestionResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\ShortAnswerQuestion;
use Illuminate\Support\Facades\Validator;

class ShortAnswerQuestionController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(ShortAnswerQuestionServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $items = $this->service->getWithPagination($request->all());
        return $this->successResponse(ShortAnswerQuestionResource::collection($items), 'api.ShortAnswerQuestion.list.success');
    }

    public function show($id)
    {
        $item = $this->service->findById($id);
        return $this->successResponse(new ShortAnswerQuestionResource($item), 'api.ShortAnswerQuestion.show.success');
    }

    public function showBySlug($slug)
    {
        $item = $this->service->findBySlug($slug);
        return $this->successResponse(new ShortAnswerQuestionResource($item), 'api.ShortAnswerQuestion.show.success');
    }
    
    /**
     * Kullanıcının cevabını kontrol et
     *
     * @param Request $request
     * @param int $id Soru ID'si
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAnswer(Request $request, $id)
    {
        // Validasyon
        $validator = Validator::make($request->all(), [
            'answer' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse('Geçersiz cevap formatı', 422, $validator->errors());
        }
        
        // Soruyu bul
        $question = ShortAnswerQuestion::findOrFail($id);
        $userAnswer = $request->answer;
        
        // Doğru cevapları al
        $allowedAnswers = $question->allowed_answers;
        
        // Büyük/küçük harf duyarlılığı için kontrol
        if (!$question->case_sensitive) {
            $userAnswer = strtolower($userAnswer);
            // Doğru cevapları da küçük harfe çevir
            $allowedAnswers = array_map(function($answer) {
                return is_string($answer) ? strtolower($answer) : $answer;
            }, (array) $allowedAnswers);
        }
        
        // Cevabı kontrol et
        $isCorrect = in_array($userAnswer, (array) $allowedAnswers);
        
        $result = [
            'is_correct' => $isCorrect,
            'feedback' => $isCorrect ? $question->feedback : null,
            'points_earned' => $isCorrect ? $question->points : 0
        ];
        
        return $this->successResponse($result, $isCorrect ? 'Doğru cevap!' : 'Yanlış cevap!');
    }
}