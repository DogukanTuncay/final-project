<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FillInTheBlankRequest;
use App\Interfaces\Services\Admin\FillInTheBlankServiceInterface;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\Admin\FillInTheBlankResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FillInTheBlankController extends Controller
{
    use ApiResponseTrait;

    protected $fillInTheBlankService;

    public function __construct(FillInTheBlankServiceInterface $fillInTheBlankService)
    {
        $this->fillInTheBlankService = $fillInTheBlankService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $fillInTheBlanks = $this->fillInTheBlankService->all();
            return $this->successResponse(FillInTheBlankResource::collection($fillInTheBlanks), 'messages.fill_in_the_blank.list_success');
        } catch (\Exception $e) {
            Log::error('FillInTheBlankController Index Error: ' . $e->getMessage());
            return $this->errorResponse('errors.general_error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\FillInTheBlankRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FillInTheBlankRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $fillInTheBlank = $this->fillInTheBlankService->create($validatedData);
            return $this->successResponse(new FillInTheBlankResource($fillInTheBlank), 'messages.fill_in_the_blank.create_success', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('FillInTheBlankController Store Error: ' . $e->getMessage());
            return $this->errorResponse('errors.fill_in_the_blank.create_error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $fillInTheBlank = $this->fillInTheBlankService->find($id);
            if (!$fillInTheBlank) {
                return $this->errorResponse('errors.fill_in_the_blank.not_found', Response::HTTP_NOT_FOUND);
            }
            return $this->successResponse(new FillInTheBlankResource($fillInTheBlank), 'messages.fill_in_the_blank.show_success');
        } catch (\Exception $e) {
            Log::error('FillInTheBlankController Show Error: ' . $e->getMessage());
            return $this->errorResponse('errors.general_error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\FillInTheBlankRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(FillInTheBlankRequest $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $updated = $this->fillInTheBlankService->update($id, $validatedData);
            if (!$updated) {
                $fillInTheBlank = $this->fillInTheBlankService->find($id);
                if(!$fillInTheBlank) {
                    return $this->errorResponse('errors.fill_in_the_blank.not_found', Response::HTTP_NOT_FOUND);
                }                 
                return $this->successResponse(new FillInTheBlankResource($fillInTheBlank), 'messages.fill_in_the_blank.update_success');
            } else {
                $fillInTheBlank = $this->fillInTheBlankService->find($id);
                return $this->successResponse(new FillInTheBlankResource($fillInTheBlank), 'messages.fill_in_the_blank.update_success');
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('FillInTheBlankController Update Warning: ' . $e->getMessage());
            return $this->errorResponse('errors.fill_in_the_blank.not_found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('FillInTheBlankController Update Error: ' . $e->getMessage());
            return $this->errorResponse('errors.fill_in_the_blank.update_error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->fillInTheBlankService->delete($id);
            if (!$deleted) {
                return $this->errorResponse('errors.fill_in_the_blank.not_found', Response::HTTP_NOT_FOUND);
            }
            return $this->successResponse([], 'messages.fill_in_the_blank.delete_success');
        } catch (\Exception $e) {
            Log::error('FillInTheBlankController Destroy Error: ' . $e->getMessage());
            return $this->errorResponse('errors.fill_in_the_blank.delete_error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Toggle the status of the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $status = $this->fillInTheBlankService->toggleStatus($id);
            if ($status === null) {
                return $this->errorResponse('errors.fill_in_the_blank.not_found', Response::HTTP_NOT_FOUND);
            }
            $messageKey = $status ? 'messages.fill_in_the_blank.status_activated' : 'messages.fill_in_the_blank.status_deactivated';
            return $this->successResponse(['is_active' => $status], $messageKey);
        } catch (\Exception $e) {
            Log::error('FillInTheBlankController ToggleStatus Error: ' . $e->getMessage());
            return $this->errorResponse('errors.general_error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 