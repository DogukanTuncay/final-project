<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Api\UserExperienceServiceInterface;
use App\Models\Level;
use Illuminate\Http\JsonResponse;

class UserLevelController extends BaseController
{
    protected $userExperienceService;
    
    public function __construct(UserExperienceServiceInterface $userExperienceService)
    {
        $this->userExperienceService = $userExperienceService;
    }
    
    /**
     * Kullanıcının seviye ve XP bilgilerini getir
     * 
     * @return JsonResponse
     */
    public function getUserExperience(): JsonResponse
    {
        $userId = auth()->id();
        $experienceData = $this->userExperienceService->getUserExperience($userId);
        
        if (!$experienceData) {
            return $this->errorResponse('errors.user_level.not_found', 404);
        }
        
        return $this->successResponse(
            $experienceData,
            'responses.user_level.experience_retrieved'
        );
    }
    
    /**
     * Bir sonraki seviye için gerekli bilgileri getir
     * 
     * @return JsonResponse
     */
    public function getNextLevel(): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            return $this->errorResponse('errors.user.not_authenticated', 401);
        }
        
        // Kullanıcının mevcut seviyesi yoksa ilk seviyeyi getir
        if (!$user->level) {
            $nextLevel = Level::where('is_active', true)
                ->orderBy('level_number', 'asc')
                ->first();
                
            if (!$nextLevel) {
                return $this->errorResponse('errors.user_level.no_levels_defined', 404);
            }
            
            $response = [
                'next_level' => $nextLevel,
                'xp_needed' => $nextLevel->min_xp - $user->experience_points,
                'current_xp' => $user->experience_points
            ];
            
            return $this->successResponse(
                $response,
                'responses.user_level.next_level_retrieved'
            );
        }
        
        // Bir sonraki seviyeyi getir
        $nextLevel = Level::where('level_number', $user->level->level_number + 1)
            ->where('is_active', true)
            ->first();
            
        if (!$nextLevel) {
            return $this->successResponse(
                [
                    'message' => 'Maksimum seviyeye ulaştınız',
                    'current_level' => $user->level,
                    'current_xp' => $user->experience_points
                ],
                'responses.user_level.max_level_reached'
            );
        }
        
        $response = [
            'current_level' => $user->level,
            'next_level' => $nextLevel,
            'current_xp' => $user->experience_points,
            'xp_needed' => $nextLevel->min_xp - $user->experience_points,
            'level_progress' => $user->level_progress
        ];
        
        return $this->successResponse(
            $response,
            'responses.user_level.next_level_retrieved'
        );
    }
} 