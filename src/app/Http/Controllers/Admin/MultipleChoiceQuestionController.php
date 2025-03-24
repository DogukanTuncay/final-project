<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MultipleChoiceQuestionRequest;
use App\Http\Resources\Admin\MultipleChoiceQuestionResource;
use App\Interfaces\Services\Admin\MultipleChoiceQuestionServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class MultipleChoiceQuestionController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;
    
    public function __construct(MultipleChoiceQuestionServiceInterface $service)
    {
        $this->service = $service;
    }
    
    /**
     * Tüm çoktan seçmeli soruları listele
     */
    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(MultipleChoiceQuestionResource::collection($items), 'Çoktan seçmeli sorular başarıyla listelendi.');
    }
    
    /**
     * Tekil çoktan seçmeli soru göster
     */
    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new MultipleChoiceQuestionResource($item), 'Çoktan seçmeli soru başarıyla gösterildi.');
    }
    
    /**
     * Çoktan seçmeli soru oluştur
     */
    public function store(MultipleChoiceQuestionRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new MultipleChoiceQuestionResource($item), 'Çoktan seçmeli soru başarıyla oluşturuldu.');
    }
    
    /**
     * Çoktan seçmeli soru güncelle
     */
    public function update(MultipleChoiceQuestionRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return $this->successResponse(new MultipleChoiceQuestionResource($item), 'Çoktan seçmeli soru başarıyla güncellendi.');
    }
    
    /**
     * Çoktan seçmeli soru sil
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'Çoktan seçmeli soru başarıyla silindi.');
    }
    
    /**
     * Çoktan seçmeli sorunun aktiflik durumunu değiştir
     */
    public function toggleStatus(Request $request, $id)
    {
        $item = $this->service->update($id, ['is_active' => $request->is_active]);
        return $this->successResponse(new MultipleChoiceQuestionResource($item), 'Çoktan seçmeli sorunun aktiflik durumu başarıyla değiştirildi.');
    }
}