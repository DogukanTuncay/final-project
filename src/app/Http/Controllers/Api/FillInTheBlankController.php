<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\FillInTheBlankServiceInterface;
use App\Http\Resources\Api\FillInTheBlankResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\FillInTheBlank;
use Illuminate\Support\Facades\Validator;

class FillInTheBlankController extends Controller
{
    use ApiResponseTrait;
    
    protected $fillInTheBlankService;

    public function __construct(FillInTheBlankServiceInterface $fillInTheBlankService)
    {
        $this->fillInTheBlankService = $fillInTheBlankService;
    }

    /**
     * ID'ye göre boşluk doldurma sorusu getir
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $fillInTheBlank = $this->fillInTheBlankService->findById($id);
        
        if (!$fillInTheBlank) {
            return response()->json([
                'success' => false,
                'message' => 'Boşluk doldurma sorusu bulunamadı.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new FillInTheBlankResource($fillInTheBlank)
        ]);
    }

    /**
     * Slug'a göre boşluk doldurma sorusu getir
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBySlug($slug)
    {
        $fillInTheBlank = $this->fillInTheBlankService->findBySlug($slug);
        
        if (!$fillInTheBlank) {
            return response()->json([
                'success' => false,
                'message' => 'Boşluk doldurma sorusu bulunamadı.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new FillInTheBlankResource($fillInTheBlank)
        ]);
    }

    /**
     * Boşluk doldurma sorularını sayfalama ile listele
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $fillInTheBlanks = $this->fillInTheBlankService->getWithPagination($params);
        
        return response()->json([
            'success' => true,
            'data' => FillInTheBlankResource::collection($fillInTheBlanks),
            'pagination' => [
                'total' => $fillInTheBlanks->total(),
                'per_page' => $fillInTheBlanks->perPage(),
                'current_page' => $fillInTheBlanks->currentPage(),
                'last_page' => $fillInTheBlanks->lastPage(),
                'from' => $fillInTheBlanks->firstItem(),
                'to' => $fillInTheBlanks->lastItem(),
            ]
        ]);
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
            'answers' => 'required|array',
            'answers.*' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse('Geçersiz cevap formatı', 422, $validator->errors());
        }
        
        // Soruyu bul
        $question = FillInTheBlank::findOrFail($id);
        $userAnswers = $request->answers;
        
        // Doğru cevapları al
        $correctAnswers = $question->answers;
        
        // Boşluk sayısı ve kullanıcı cevap sayısı uyuşuyor mu kontrol et
        if (count($correctAnswers) !== count($userAnswers)) {
            return $this->errorResponse('Cevap sayısı boşluk sayısıyla uyuşmuyor.', 422);
        }
        
        // Cevapları kontrol et
        $isAllCorrect = true;
        $results = [];
        
        foreach ($userAnswers as $index => $userAnswer) {
            // Doğru cevap setini al
            $possibleAnswers = $correctAnswers[$index] ?? [];
            
            // Büyük/küçük harf duyarlılığı
            if (!$question->case_sensitive) {
                $userAnswer = strtolower($userAnswer);
                $possibleAnswers = array_map('strtolower', (array)$possibleAnswers);
            }
            
            // Cevap doğru mu kontrol et
            $isCorrect = in_array($userAnswer, (array)$possibleAnswers);
            
            if (!$isCorrect) {
                $isAllCorrect = false;
            }
            
            $results[] = [
                'index' => $index,
                'is_correct' => $isCorrect,
                'user_answer' => $userAnswer,
                'correct_answers' => $possibleAnswers
            ];
        }
        
        $finalResult = [
            'is_correct' => $isAllCorrect,
            'points_earned' => $isAllCorrect ? $question->points : 0,
            'feedback' => $isAllCorrect ? $question->feedback : null,
            'results' => $results
        ];
        
        return $this->successResponse(
            $finalResult,
            $isAllCorrect ? 'Doğru cevap!' : 'Yanlış cevap!'
        );
    }
} 