<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    public function __construct(protected LikeService $likeService)
    {
    }

    public function toggle($postId): JsonResponse
    {
        $result = $this->likeService->toggleLike(auth()->id(), $postId);
        return response()->json($result);
    }

    public function index($postId): JsonResponse
    {
        $likers = $this->likeService->getPostLikers($postId);
        return response()->json($likers);
    }

    public function unlike($postId)
    {
        \App\Models\Like::where('user_id', auth()->id())
            ->where('post_id', $postId)
            ->delete();
        return response()->json(['status' => 'unliked']);
    }
}
