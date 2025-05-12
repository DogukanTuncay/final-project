<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\ContactServiceInterface;
use App\Http\Resources\Api\ContactResource;
use App\Http\Requests\Api\ContactRequest;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class ContactController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(ContactServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $items = $this->service->getWithPagination($request->all());
        return $this->successResponse(ContactResource::collection($items), 'api.contact.list.success');
    }

    public function store(ContactRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new ContactResource($item), 'api.contact.create.success');
    }

    public function show($id)
    {
        $item = $this->service->findById($id);
        return $this->successResponse(new ContactResource($item), 'api.Contact.show.success');
    }

    public function showBySlug($slug)
    {
        $item = $this->service->findBySlug($slug);
        return $this->successResponse(new ContactResource($item), 'api.Contact.show.success');
    }
}