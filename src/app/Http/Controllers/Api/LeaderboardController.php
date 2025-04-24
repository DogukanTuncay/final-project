<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Api\UserResource; // Kullanıcı verisini formatlamak için

class LeaderboardController extends BaseController
{
    /**
     * Display a listing of the top users by XP.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // En yüksek XP'ye sahip ilk 15 kullanıcıyı al
        $topUsers = User::orderByDesc('experience_points')->limit(15)->get();

        // Veriyi UserResource kullanarak formatla
        $formattedUsers = UserResource::collection($topUsers);

        return $this->successResponse($formattedUsers, 'responses.leaderboard.success');
    }
} 