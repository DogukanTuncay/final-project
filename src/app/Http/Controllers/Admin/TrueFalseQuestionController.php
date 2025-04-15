<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Admin\TrueFalseQuestionServiceInterface;
use App\Http\Requests\Admin\TrueFalseQuestionRequest;
use App\Http\Resources\Admin\TrueFalseQuestionResource;
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
     * Tüm Doğru/Yanlış sorularını listele
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $questions = $this->trueFalseQuestionService->all();
        return $this->successResponse(
            TrueFalseQuestionResource::collection($questions),
            'responses.true_false_question.list_success'
        );
    }

    /**
     * Yeni Doğru/Yanlış sorusu oluştur
     *
     * @param TrueFalseQuestionRequest $request
     * @return JsonResponse
     */
    public function store(TrueFalseQuestionRequest $request): JsonResponse
    {
        try {
            $question = $this->trueFalseQuestionService->create($request->validated());
            return $this->successResponse(
                new TrueFalseQuestionResource($question),
                'responses.true_false_question.create_success',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'errors.true_false_question.create_failed',
                Response::HTTP_BAD_REQUEST,
                ['error' => $e->getMessage()]
            );
        }
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
            $question = $this->trueFalseQuestionService->find($id);
            
            if (!$question) {
                return $this->errorResponse(
                    'errors.true_false_question.not_found',
                    Response::HTTP_NOT_FOUND
                );
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
     * Belirli bir Doğru/Yanlış sorusunu güncelle
     *
     * @param TrueFalseQuestionRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(TrueFalseQuestionRequest $request, int $id): JsonResponse
    {
        try {
            $question = $this->trueFalseQuestionService->update($id, $request->validated());
            
            if (!$question) {
                return $this->errorResponse(
                    'errors.true_false_question.not_found',
                    Response::HTTP_NOT_FOUND
                );
            }
            
            return $this->successResponse(
                new TrueFalseQuestionResource($question),
                'responses.true_false_question.update_success'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'errors.true_false_question.update_failed',
                Response::HTTP_BAD_REQUEST,
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Belirli bir Doğru/Yanlış sorusunu sil
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->trueFalseQuestionService->delete($id);
            
            if (!$result) {
                return $this->errorResponse(
                    'errors.true_false_question.not_found',
                    Response::HTTP_NOT_FOUND
                );
            }
            
            return $this->successResponse(
                null,
                'responses.true_false_question.delete_success'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'errors.true_false_question.delete_failed',
                Response::HTTP_BAD_REQUEST,
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Doğru/Yanlış sorusunun aktiflik durumunu değiştir
     *
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $question = $this->trueFalseQuestionService->toggleStatus($id);
            
            if (!$question) {
                return $this->errorResponse(
                    'errors.true_false_question.not_found',
                    Response::HTTP_NOT_FOUND
                );
            }
            
            $messageKey = $question->is_active ? 
                'responses.true_false_question.status_active' : 
                'responses.true_false_question.status_inactive';
            
            return $this->successResponse(
                new TrueFalseQuestionResource($question),
                $messageKey
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'errors.true_false_question.status_update_failed',
                Response::HTTP_BAD_REQUEST,
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Doğru/Yanlış sorusunu bir derse ekle
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function addToLesson(Request $request, int $id): JsonResponse
    {
        try {
            $validator = validator($request->all(), [
                'lesson_id' => 'required|exists:course_chapter_lessons,id',
                'order' => 'integer|min:0',
                'is_active' => 'boolean',
                'meta_data' => 'nullable|array'
            ]);
            
            if ($validator->fails()) {
                return $this->errorResponse(
                    'errors.validation',
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    $validator->errors()
                );
            }
            
            $lessonContent = $this->trueFalseQuestionService->addToLesson(
                $id,
                $request->lesson_id,
                $request->only(['order', 'is_active', 'meta_data'])
            );
            
            return $this->successResponse(
                $lessonContent,
                'responses.true_false_question.add_to_lesson_success'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'errors.true_false_question.add_to_lesson_failed',
                Response::HTTP_BAD_REQUEST,
                ['error' => $e->getMessage()]
            );
        }
    }
}