<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Api\BadgeServiceInterface;
use App\Http\Resources\Api\BadgeResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

class BadgeController extends BaseController
{
    protected $service;

    public function __construct(BadgeServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Tüm aktif rozetleri listele
     */
    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(BadgeResource::collection($items), 'responses.api.badges.list.success');
    }

    /**
     * Rozet detaylarını göster
     */
    public function show($id)
    {
        $item = $this->service->find($id);
        
        if (!$item) {
            return $this->errorResponse('responses.api.badges.show.not_found', 404);
        }
        
        return $this->successResponse(new BadgeResource($item), 'responses.api.badges.show.success');
    }

    /**
     * Kullanıcının kazandığı rozetleri getir
     */
    public function userBadges()
    {
        $items = $this->service->getUserBadges();
        return $this->successResponse(BadgeResource::collection($items), 'responses.api.badges.user_badges.success');
    }

    /**
     * Kullanıcının tüm rozet şartlarını kontrol et ve gerekli rozetleri ver
     */
    public function checkBadges()
    {
        $user = JWTAuth::user();
        
        if (!$user) {
            return $this->errorResponse('responses.api.auth.unauthenticated', 401);
        }
        
        $awardedBadges = $this->service->checkAndAwardBadges($user);
        
        if (count($awardedBadges) > 0) {
            return $this->successResponse([
                'awarded_badges' => $awardedBadges,
                'message' => __('responses.api.badges.awarded', ['count' => count($awardedBadges)])
            ], 'responses.api.badges.check.success');
        }
        
        return $this->successResponse([], 'responses.api.badges.check.no_new_badges');
    }
}