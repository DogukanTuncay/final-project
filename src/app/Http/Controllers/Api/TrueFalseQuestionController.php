<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Api\TrueFalseQuestionResource;
use App\Interfaces\Services\Api\TrueFalseQuestionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrueFalseQuestionController extends BaseController
{
    private TrueFalseQuestionServiceInterface $trueFalseQuestionService;

    public function __construct(TrueFalseQuestionServiceInterface $trueFalseQuestionService)
    {
        $this->trueFalseQuestionService = $trueFalseQuestionService;
    }

    /**
     * Belirli bir Doğru/Yanlış sorusunu göster
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $question = $this->trueFalseQuestionService->findActive($id);
            
            if (!$question) {
                return $this->errorResponse('errors.true_false_question.not_found', Response::HTTP_NOT_FOUND);
            }
            
            return $this->successResponse(
                new TrueFalseQuestionResource($question),
                'responses.true_false_question.retrieve_success'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'errors.true_false_question.retrieve_failed',
                Response::HTTP_BAD_REQUEST,
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Sorunun cevabını kontrol et
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function checkAnswer(Request $request, int $id): JsonResponse
    {
        try {
            $validator = validator($request->all(), [
                'answer' => 'required|boolean',
            ]);
            
            if ($validator->fails()) {
                return $this->errorResponse(
                    'errors.validation',
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    $validator->errors()
                );
            }
            
            $result = $this->trueFalseQuestionService->checkAnswer($id, $request->answer);
            
            return $this->successResponse($result, 'responses.true_false_question.answer_checked');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'errors.true_false_question.check_answer_failed',
                Response::HTTP_BAD_REQUEST,
                ['error' => $e->getMessage()]
            );
        }
    }
}