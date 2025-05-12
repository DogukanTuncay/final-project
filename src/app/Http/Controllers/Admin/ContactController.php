<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\ContactServiceInterface;
use App\Http\Requests\Admin\ContactRequest;
use App\Http\Resources\Admin\ContactResource;
use App\Traits\ApiResponseTrait;

class ContactController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(ContactServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(ContactResource::collection($items), 'admin.Contact.list.success');
    }

    public function store(ContactRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new ContactResource($item), 'admin.Contact.create.success');
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new ContactResource($item), 'admin.Contact.show.success');
    }

    public function update(ContactRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return $this->successResponse(new ContactResource($item), 'admin.Contact.update.success');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'admin.Contact.delete.success');
    }
}