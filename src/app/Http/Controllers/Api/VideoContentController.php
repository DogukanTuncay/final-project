<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\VideoContentServiceInterface;
use App\Http\Resources\Api\VideoContentResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VideoContentController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(VideoContentServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Video içeriklerini listele
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $params = $request->only(['title', 'provider', 'is_active', 'order_by', 'order_direction', 'per_page']);
        $items = $this->service->getWithPagination($params);
        
        return $this->successResponse(
            VideoContentResource::collection($items)->response()->getData(true),
            'responses.api.video_content.list.success'
        );
    }

    /**
     * ID'ye göre video içeriğini göster
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = $this->service->findById($id);
        return $this->successResponse(
            new VideoContentResource($item),
            'responses.api.video_content.show.success'
        );
    }

    /**
     * Slug'a göre video içeriğini göster
     * 
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function showBySlug($slug)
    {
        $item = $this->service->findBySlug($slug);
        return $this->successResponse($item, 'api.video_content.show.success');
    }

    /**
     * Sadece aktif video içeriklerini getir
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveVideos(Request $request)
    {
        $limit = $request->input('limit', 10);
        $items = $this->service->getActiveVideos($limit);
        
        return $this->successResponse(
            VideoContentResource::collection($items),
            'responses.api.video_content.active.success'
        );
    }

    /**
     * Provider'a göre video içeriklerini getir
     * 
     * @param string $provider
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVideosByProvider($provider, Request $request)
    {
        $limit = $request->input('limit', 10);
        $items = $this->service->getVideosByProvider($provider, $limit);
        
        return $this->successResponse(
            VideoContentResource::collection($items),
            'responses.api.video_content.by_provider.success'
        );
    }

    /**
     * Video URL'sini doğrula
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateVideoUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $isValid = $this->service->isValidVideoUrl($request->url);
        
        return $this->successResponse(
            ['is_valid' => $isValid],
            'responses.api.video_content.validate_url.success'
        );
    }
}