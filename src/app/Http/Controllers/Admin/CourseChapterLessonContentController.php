<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CourseChapterLessonContentRequest;
use App\Http\Resources\Admin\CourseChapterLessonContentResource;
use App\Interfaces\Services\Admin\CourseChapterLessonContentServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Interfaces\Services\Admin\TrueFalseQuestionServiceInterface;

class CourseChapterLessonContentController extends BaseController
{
    
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
        if(!$content){
            return $this->errorResponse('responses.admin.lesson-contents.not_found', 404);
        }
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
        $content = $this->service->find($id);
        if(!$content){
            return $this->errorResponse('responses.admin.lesson-contents.not_found', 404);
        }
        $content = $this->service->update($id, $request->validated());
        return $this->successResponse(new CourseChapterLessonContentResource($content), 'responses.admin.lesson-contents.update.success');
    }
    
    /**
     * İçerik sil
     */
    public function destroy($id)
    {
        $content = $this->service->find($id);
        if(!$content){
            return $this->errorResponse('responses.admin.lesson-contents.not_found', 404);
        }
        $this->service->delete($id);
        return $this->successResponse(null, 'responses.admin.lesson-contents.delete.success');
    }
    
    /**
     * İçeriğin aktiflik durumunu değiştir
     */
    public function toggleStatus(Request $request, $id)
    {
        $content = $this->service->find($id);
        if(!$content){
            return $this->errorResponse('responses.admin.lesson-contents.not_found', 404);
        }
        $content = $this->service->update($id, ['is_active' => $request->is_active]);
        return $this->successResponse(new CourseChapterLessonContentResource($content), 'responses.admin.lesson-contents.status.success');
    }
    
    /**
     * İçeriğin sırasını güncelle
     */
    public function updateOrder(Request $request, $id)
    {
        $content = $this->service->find($id);
        if(!$content){
            return $this->errorResponse('responses.admin.lesson-contents.not_found', 404);
        }
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
        if(!$contents){
            return $this->errorResponse('responses.admin.lesson-contents.not_found', 404);
        }

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
    
    /**
     * Çoktan seçmeli soru içeriği oluştur
     */
    public function createMultipleChoiceContent(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:course_chapter_lessons,id',
            'question' => 'required|array',
            'question.tr' => 'required|string',
            'question.en' => 'required|string',
            'feedback' => 'nullable|array',
            'points' => 'nullable|integer|min:1',
            'is_multiple_answer' => 'boolean',
            'shuffle_options' => 'boolean',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|array',
            'options.*.text.tr' => 'required|string',
            'options.*.text.en' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'meta_data' => 'nullable|array'
        ]);
        
        $content = $this->service->createMultipleChoiceContent(
            $request->lesson_id,
            [
                'question' => $request->question,
                'feedback' => $request->feedback,
                'points' => $request->points ?? 1,
                'is_multiple_answer' => $request->is_multiple_answer ?? false,
                'shuffle_options' => $request->shuffle_options ?? true,
                'created_by' => auth()->id(),
                'is_active' => true,
                'options' => $request->options
            ],
            [
                'order' => $request->order ?? 0,
                'is_active' => $request->is_active ?? true,
                'meta_data' => $request->meta_data ?? null
            ]
        );
        
        return $this->successResponse(new CourseChapterLessonContentResource($content), 'responses.admin.lesson-contents.create-multiple-choice.success');
    }
    
    /**
     * Doğru/Yanlış soru içeriği oluştur
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createTrueFalseContent(Request $request): JsonResponse
    {
        try {
            $validator = validator($request->all(), [
                'lesson_id' => 'required|exists:course_chapter_lessons,id',
                'question' => 'required|array',
                'question.tr' => 'required|string|min:2|max:1000',
                'question.en' => 'required|string|min:2|max:1000',
                'correct_answer' => 'required|boolean',
                'custom_text' => 'nullable|array',
                'feedback' => 'nullable|array',
                'points' => 'nullable|integer|min:0|max:100',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
                'meta_data' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'errors.validation',
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    $validator->errors()
                );
            }

            // TrueFalseQuestion oluştur
            $trueFalseService = app(TrueFalseQuestionServiceInterface::class);
            $question = $trueFalseService->create([
                'question' => $request->question,
                'correct_answer' => $request->correct_answer,
                'custom_text' => $request->custom_text,
                'feedback' => $request->feedback,
                'points' => $request->points ?? 10,
                'created_by' => auth()->id(),
                'is_active' => true
            ]);

            // Ders içeriği olarak ekle
            $lessonContent = $this->service->createWithContent(
                $request->lesson_id,
                $question,
                [
                    'order' => $request->order ?? 0,
                    'is_active' => $request->is_active ?? true,
                    'meta_data' => $request->meta_data
                ]
            );

            return $this->successResponse(
                new CourseChapterLessonContentResource($lessonContent),
                'responses.lesson_content.true_false_created',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'errors.lesson_content.create_failed',
                Response::HTTP_BAD_REQUEST,
                ['error' => $e->getMessage()]
            );
        }
    }
}