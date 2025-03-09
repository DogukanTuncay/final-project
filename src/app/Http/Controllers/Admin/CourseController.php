<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CourseRequest;
use App\Http\Resources\Admin\CourseResource;
use App\Interfaces\Services\Admin\CourseServiceInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;

class CourseController extends BaseController
{
    protected $service;

    public function __construct(CourseServiceInterface $service)
    {
        $this->service = $service;
    }
    
    public function index()
    {
        $courses = $this->service->all();
        return $this->successResponse(
            CourseResource::collection($courses),
            'responses.courses.listed'
        );
    }
    
    public function store(CourseRequest $request)
    {
        try {
            $course = $this->service->create($request->validated());
            if(!$course){
                return $this->errorResponse('responses.courses.not_found', 404);
            }
            return $this->successResponse(
                new CourseResource($course),
                'responses.courses.created',
                201
            );
        } catch (QueryException $e) {
            // TODO: Geçici çözüm - Exception Handler çalışmadığı için controller'da yakalıyoruz
            $message = $e->getMessage();
            $sqlState = $e->errorInfo[0] ?? null;
            
            if ($sqlState === '23505' && strpos($message, 'courses_slug_unique') !== false) {
                return $this->errorResponse('errors.duplicate_course_slug', Response::HTTP_CONFLICT);
            }
            
            // Genel veritabanı hatası
            return $this->errorResponse('responses.courses.create_failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return $this->errorResponse('responses.courses.create_failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function show($id)
    {
        try {
            $course = $this->service->find($id);
            if(!$course){
                return $this->errorResponse('responses.courses.not_found', 404);

            }
            return $this->successResponse(
                new CourseResource($course),
                'responses.courses.retrieved'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('responses.courses.not_found', 404);
        }
    }
    
    public function update(CourseRequest $request, $id)
    {
        try {
            $course = $this->service->find($id);
            if(!$course){
                return $this->errorResponse('responses.courses.not_found', 404);
            }
            
            $course = $this->service->update($id, $request->validated());
            return $this->successResponse(
                new CourseResource($course),
                'responses.courses.updated'
            );
        } catch (QueryException $e) {
            // TODO: Geçici çözüm - Exception Handler çalışmadığı için controller'da yakalıyoruz
            $message = $e->getMessage();
            $sqlState = $e->errorInfo[0] ?? null;
            
            if ($sqlState === '23505' && strpos($message, 'courses_slug_unique') !== false) {
                return $this->errorResponse('errors.duplicate_course_slug', Response::HTTP_CONFLICT);
            }
            
            // Genel veritabanı hatası
            return $this->errorResponse('responses.courses.update_failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return $this->errorResponse('responses.courses.update_error', 400);
        }
    }
    
    public function destroy($id)
    {
        try {
            $course = $this->service->find($id);
            if(!$course){
                return $this->errorResponse('responses.courses.not_found', 404);

            }
            $this->service->delete($id);
            return $this->successResponse(
                [],
                'responses.courses.deleted'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('responses.courses.delete_error', 400);
        }
    }
    
    public function updateOrder(Request $request, $id)
    {
        try {
            $course = $this->service->find($id);
            if(!$course){
                return $this->errorResponse('responses.courses.not_found', 404);

            }
            $validator = \Validator::make($request->all(), [
                'order' => 'required|integer|min:0'
            ]);
    
            if ($validator->fails()) {
                return $this->errorResponse(
                    'responses.courses.order_invalid',
                    422,
                    $validator->errors()
                );
            }
    
            $result = $this->service->updateOrder($id, $request->order);
            if(!$result){
                return $this->errorResponse('responses.courses.order_error', 400);
            }
            
            return $this->successResponse(
                new CourseResource($course),
                'responses.courses.order_updated'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('responses.courses.order_error', 400);
        }
    }
    
    public function toggleStatus($id)
    {
        try {
            $course = $this->service->find($id);
            if(!$course){
                return $this->errorResponse('responses.courses.not_found', 404);

            }
            $course = $this->service->toggleStatus($id);
            $messageKey = $course->is_active ? 
                'responses.courses.status_active' : 
                'responses.courses.status_inactive';
            
            return $this->successResponse(
                new CourseResource($course),
                $messageKey
            );
        } catch (\Exception $e) {
            return $this->errorResponse('responses.courses.status_error', 400);
        }
    }
    
    public function toggleFeatured($id)
    {
        try {
            $course = $this->service->find($id);
            if(!$course){
                return $this->errorResponse('responses.courses.not_found', 404);

            }
            $course = $this->service->toggleFeatured($id);
            $messageKey = $course->is_featured ? 
                'responses.courses.featured' : 
                'responses.courses.unfeatured';
            
            return $this->successResponse(
                new CourseResource($course),
                $messageKey
            );
        } catch (\Exception $e) {
            return $this->errorResponse('responses.courses.featured_error', 400);
        }
    }
    
    public function byCategory($category)
    {
        try {
            $courses = $this->service->findByCategory($category);
            return $this->successResponse(
                CourseResource::collection($courses),
                'responses.courses.by_category'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('responses.courses.by_category_error', 400);
        }
    }
}