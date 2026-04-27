<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class LikeController extends Controller
{
    public function __construct(protected LikeService $likeService) {}

    #[OA\Post(path: '/posts/{id}/like', tags: ['Like'], summary: 'Curtir post')]
    #[OA\Response(response: 200, description: 'Curtido')]
    public function like($postId): JsonResponse
    {
        /** @var int $userId */
        $userId = auth()->id();
        $this->likeService->likePost($userId, (int)$postId);
        return response()->json(['message' => 'Post curtido']);
    }

    #[OA\Delete(path: '/posts/{id}/unlike', tags: ['Like'], summary: 'Descurtir post')]
    #[OA\Response(response: 200, description: 'Descurtido')]
    public function unlike($postId): JsonResponse
    {
        /** @var int $userId */
        $userId = auth()->id();
        $this->likeService->unlikePost($userId, (int)$postId);
        return response()->json(['message' => 'Post descurtido']);
    }

    #[OA\Get(path: '/posts/{postId}/likes', tags: ['Like'], summary: 'Listar quem curtiu')]
    #[OA\Response(response: 200, description: 'Lista de usuários')]
    public function index($postId): JsonResponse
    {
        $likers = $this->likeService->getPostLikers((int)$postId);
        return response()->json($likers);
    }
}