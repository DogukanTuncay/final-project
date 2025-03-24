<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\MatchingQuestionServiceInterface;
use App\Http\Resources\Api\MatchingQuestionResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

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
        return $this->successResponse(MatchingQuestionResource::collection($items), 'api.MatchingQuestion.list.success');
    }

    public function show($id)
    {
        $item = $this->service->findById($id);
        return $this->successResponse(new MatchingQuestionResource($item), 'api.MatchingQuestion.show.success');
    }

    public function showBySlug($slug)
    {
        $item = $this->service->findBySlug($slug);
        return $this->successResponse(new MatchingQuestionResource($item), 'api.MatchingQuestion.show.success');
    }
}