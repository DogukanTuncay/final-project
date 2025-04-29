<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\VideoContentServiceInterface;
use App\Http\Resources\Api\VideoContentResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

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
        $params = $request->all();
        $items = $this->service->getWithPagination($params);
        
        return $this->successResponse(
            $items->response()->getData(true), 
            'api.video_content.list.success'
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
        return $this->successResponse($item, 'api.video_content.show.success');
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
     * Aktif videoları getir
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveVideos(Request $request)
    {
        $limit = $request->input('limit', 10);
        $videos = $this->service->getActiveVideos($limit);
        
        return $this->successResponse(
            $videos,
            'api.video_content.active.success'
        );
    }

    /**
     * Belirli bir provider'a ait videoları getir
     * 
     * @param Request $request
     * @param string $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVideosByProvider(Request $request, $provider)
    {
        $limit = $request->input('limit', 10);
        $videos = $this->service->getVideosByProvider($provider, $limit);
        
        return $this->successResponse(
            $videos,
            'api.video_content.provider.success'
        );
    }

    /**
     * Video URL'sinin geçerli olup olmadığını kontrol et
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateVideoUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $isValid = $this->service->isValidVideoUrl($request->input('url'));
        
        return $this->successResponse(
            ['is_valid' => $isValid],
            'api.video_content.validate.success'
        );
    }
}