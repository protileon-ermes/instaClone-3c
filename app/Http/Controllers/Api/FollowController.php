<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function __construct(protected FollowService $followService)
    {
    }

    public function follow($id)
    {
        try {
            $this->followService->follow(auth()->user(), (int)$id);
            return response()->json(['message' => 'Seguindo com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function unfollow($id)
    {
        $this->followService->unfollow(auth()->user(), (int)$id);
        return response()->json(['message' => 'Deixou de seguir']);
    }

    public function checkFollow($id)
    {
        $isFollowing = $this->followService->isFollowing(auth()->user(), (int)$id);
        return response()->json(['is_following' => $isFollowing]);
    }
}
