<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\VideoContentServiceInterface;
use App\Http\Requests\Admin\VideoContentRequest;
use App\Http\Resources\Admin\VideoContentResource;
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
        $filters = $request->only(['title', 'provider', 'is_active']);
        $perPage = $request->input('per_page', 15);
        
        $items = $this->service->getFilteredAndPaginated($filters, $perPage);
        
        return $this->successResponse(
            VideoContentResource::collection($items)->response()->getData(true),
            'responses.admin.video_content.list.success'
        );
    }

    /**
     * Yeni video içeriği oluştur
     * 
     * @param VideoContentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(VideoContentRequest $request)
    {
        $data = $request->validated();
        $item = $this->service->create($data);
        
        return $this->successResponse(
            new VideoContentResource($item),
            'responses.admin.video_content.create.success',
            Response::HTTP_CREATED
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
        $item = $this->service->find($id);
        return $this->successResponse(
            new VideoContentResource($item),
            'responses.admin.video_content.show.success'
        );
    }

    /**
     * Video içeriğini güncelle
     * 
     * @param VideoContentRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(VideoContentRequest $request, $id)
    {
        $data = $request->validated();
        $item = $this->service->update($id, $data);
        
        return $this->successResponse(
            new VideoContentResource($item),
            'responses.admin.video_content.update.success'
        );
    }

    /**
     * Video içeriğini sil
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(
            null,
            'responses.admin.video_content.delete.success'
        );
    }

    /**
     * Birden fazla video içeriğini toplu olarak güncelle
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:video_contents,id',
            'data' => 'required|array',
        ]);

        $this->service->bulkUpdate($request->input('ids'), $request->input('data'));
        
        return $this->successResponse(
            null,
            'responses.admin.video_content.bulk_update.success'
        );
    }

    /**
     * Video URL'sini doğrula ve bilgileri çıkar
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function parseUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $videoInfo = $this->service->parseVideoUrl($request->url);
        
        return $this->successResponse(
            $videoInfo,
            'responses.admin.video_content.parse_url.success'
        );
    }
}