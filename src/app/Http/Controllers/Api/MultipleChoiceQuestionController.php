<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\MultipleChoiceQuestionServiceInterface;
use App\Http\Resources\Api\MultipleChoiceQuestionResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\MultipleChoiceQuestion;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\Validator;

class MultipleChoiceQuestionController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(MultipleChoiceQuestionServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $items = $this->service->getWithPagination($request->all());
        return $this->successResponse(MultipleChoiceQuestionResource::collection($items), 'api.MultipleChoiceQuestion.list.success');
    }

    public function show($id)
    {
        $item = $this->service->findById($id);
        return $this->successResponse(new MultipleChoiceQuestionResource($item), 'api.MultipleChoiceQuestion.show.success');
    }

    public function showBySlug($slug)
    {
        $item = $this->service->findBySlug($slug);
        return $this->successResponse(new MultipleChoiceQuestionResource($item), 'api.MultipleChoiceQuestion.show.success');
    }
    
    /**
     * Kullanıcının cevabını kontrol et ve sonucu döndür
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
            'answers.*' => 'required|integer|exists:question_options,id'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse('Geçersiz cevap formatı', 422, $validator->errors());
        }
        
        // Soruyu bul
        $question = MultipleChoiceQuestion::with('options')->findOrFail($id);
        
        // Kullanıcının cevapları
        $userAnswerIds = $request->answers;
        
        // Cevapların tek seçim mi yoksa çoklu seçim mi olduğunu kontrol et
        if (!$question->is_multiple_answer && count($userAnswerIds) > 1) {
            return $this->errorResponse('Bu soru için sadece bir cevap seçilebilir', 422);
        }
        
        // Doğru cevapları bul
        $correctOptions = $question->options->where('is_correct', true)->pluck('id')->toArray();
        
        // Cevapları karşılaştır ve sonucu hesapla
        $isCorrect = false;
        
        // Çoklu seçim ise, tüm doğru cevaplar seçilmiş ve yanlış hiçbir cevap seçilmemiş olmalı
        if ($question->is_multiple_answer) {
            $correctlySelected = true;
            $incorrectlySelected = false;
            
            // Tüm doğru cevaplar seçilmiş mi?
            foreach ($correctOptions as $correctId) {
                if (!in_array($correctId, $userAnswerIds)) {
                    $correctlySelected = false;
                    break;
                }
            }
            
            // Yanlış bir cevap seçilmiş mi?
            foreach ($userAnswerIds as $answerId) {
                if (!in_array($answerId, $correctOptions)) {
                    $incorrectlySelected = true;
                    break;
                }
            }
            
            $isCorrect = $correctlySelected && !$incorrectlySelected;
        } else {
            // Tek seçim ise, seçilen tek cevabın doğru olup olmadığını kontrol et
            $isCorrect = in_array($userAnswerIds[0], $correctOptions);
        }
        
        // Geribildirim metni (doğru cevaplar için)
        $feedback = $question->feedback;
        
        // Cevaplara göre geribildirim
        $answerFeedback = [];
        if (!$isCorrect) {
            // Yanlış cevaplar için seçeneklerdeki geribildirimleri ekle
            foreach ($userAnswerIds as $answerId) {
                $option = QuestionOption::find($answerId);
                if ($option && $option->feedback) {
                    $answerFeedback[$answerId] = $option->feedback;
                }
            }
        }
        
        // Puanı hesapla
        $score = $isCorrect ? $question->points : 0;
        
        // Sonucu döndür
        return $this->successResponse([
            'is_correct' => $isCorrect,
            'score' => $score,
            'feedback' => $feedback,
            'answer_feedback' => $answerFeedback,
            'correct_options' => $correctOptions,
            'user_answers' => $userAnswerIds
        ], $isCorrect ? 'api.MultipleChoiceQuestion.correct_answer' : 'api.MultipleChoiceQuestion.incorrect_answer');
    }
}