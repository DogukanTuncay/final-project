<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\TrueFalseQuestionServiceInterface;
use App\Http\Resources\Api\TrueFalseQuestionResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class TrueFalseQuestionController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(TrueFalseQuestionServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $items = $this->service->getWithPagination($request->all());
        return $this->successResponse(TrueFalseQuestionResource::collection($items), 'api.TrueFalseQuestion.list.success');
    }

    public function show($id)
    {
        $item = $this->service->findById($id);
        return $this->successResponse(new TrueFalseQuestionResource($item), 'api.TrueFalseQuestion.show.success');
    }

    public function showBySlug($slug)
    {
        $item = $this->service->findBySlug($slug);
        return $this->successResponse(new TrueFalseQuestionResource($item), 'api.TrueFalseQuestion.show.success');
    }
}