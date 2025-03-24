<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\ShortAnswerQuestionServiceInterface;
use App\Http\Resources\Api\ShortAnswerQuestionResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

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
}