<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FollowService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class FollowController extends Controller
{
    public function __construct(protected FollowService $followService)
    {
    }

    #[OA\Post(path: '/users/{id}/follow', tags: ['Follow'], summary: 'Seguir usuário')]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Sucesso')]
    public function follow($id): JsonResponse
    {
        try {
            $this->followService->follow((int)$id);
            return response()->json(['message' => 'Seguindo com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    #[OA\Delete(path: '/users/{id}/unfollow', tags: ['Follow'], summary: 'Deixar de seguir usuário')]
    #[OA\Response(response: 200, description: 'Deixou de seguir')]
    public function unfollow($id): JsonResponse
    {
        $this->followService->unfollow((int)$id);
        return response()->json(['message' => 'Deixou de seguir']);
    }

    #[OA\Get(path: '/users/{id}/is-following', tags: ['Follow'], summary: 'Verificar se está seguindo')]
    #[OA\Response(response: 200, description: 'Status de follow')]
    public function checkFollow($id): JsonResponse
    {
        $isFollowing = $this->followService->isFollowing((int)$id);
        return response()->json(['is_following' => $isFollowing]);
    }

    public function followers($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user->followers()->paginate(20));
    }

    public function following($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user->following()->paginate(20));
    }
}
