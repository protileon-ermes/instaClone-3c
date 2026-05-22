<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class FeedController extends Controller
{
    public function __construct(
        protected FeedService $feedService
    ) {}

    #[OA\Get(
        path: '/feed',
        tags: ['Feed'],
        summary: 'Obter feed de postagens',
        description: 'Retorna os posts paginados dos usuários seguidos e do próprio usuário logado. Requer autenticação.',
        operationId: 'getFeed'
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'Número da página (padrão: 1)',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)
    )]
    #[OA\Parameter(
        name: 'per_page',
        description: 'Quantidade de posts por página (padrão: 15)',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 15, minimum: 1, maximum: 100)
    )]
    #[OA\Response(
        response: 200,
        description: 'Feed retornado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'user_id', type: 'integer', example: 5),
                        new OA\Property(property: 'caption', type: 'string', example: 'Dia lindo no parque! 🌞'),
                        new OA\Property(property: 'image_url', type: 'string', example: '/storage/posts/image_1.jpg'),
                        new OA\Property(property: 'likes_count', type: 'integer', example: 42),
                        new OA\Property(property: 'comments_count', type: 'integer', example: 8),
                        new OA\Property(property: 'is_liked', type: 'boolean', example: false),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 5),
                                new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                                new OA\Property(property: 'username', type: 'string', example: 'joao_silva'),
                                new OA\Property(property: 'avatar', type: 'string', example: '/storage/avatars/joao.jpg'),
                                new OA\Property(property: 'is_following', type: 'boolean', example: true)
                            ]
                        )
                    ]
                )),
                new OA\Property(property: 'first_page_url', type: 'string', example: '/api/feed?page=1'),
                new OA\Property(property: 'from', type: 'integer', example: 1),
                new OA\Property(property: 'last_page', type: 'integer', example: 5),
                new OA\Property(property: 'last_page_url', type: 'string', example: '/api/feed?page=5'),
                new OA\Property(property: 'next_page_url', type: 'string', example: '/api/feed?page=2'),
                new OA\Property(property: 'path', type: 'string', example: '/api/feed'),
                new OA\Property(property: 'per_page', type: 'integer', example: 15),
                new OA\Property(property: 'prev_page_url', type: 'string', example: null),
                new OA\Property(property: 'to', type: 'integer', example: 15),
                new OA\Property(property: 'total', type: 'integer', example: 75)
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Security(name: 'bearerAuth')]
    public function index(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $feed = $this->feedService->getHomeFeed($user);

        return response()->json($feed);
    }
}