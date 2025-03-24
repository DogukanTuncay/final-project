<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\QuestionContentServiceInterface;
use App\Http\Resources\Api\QuestionContentResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class QuestionContentController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(QuestionContentServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $items = $this->service->getWithPagination($request->all());
        return $this->successResponse(QuestionContentResource::collection($items), 'api.QuestionContent.list.success');
    }

    public function show($id)
    {
        $item = $this->service->findById($id);
        return $this->successResponse(new QuestionContentResource($item), 'api.QuestionContent.show.success');
    }

    public function showBySlug($slug)
    {
        $item = $this->service->findBySlug($slug);
        return $this->successResponse(new QuestionContentResource($item), 'api.QuestionContent.show.success');
    }
}