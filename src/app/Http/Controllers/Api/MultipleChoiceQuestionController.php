<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\MultipleChoiceQuestionServiceInterface;
use App\Http\Resources\Api\MultipleChoiceQuestionResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

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
}