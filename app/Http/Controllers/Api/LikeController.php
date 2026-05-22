<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class LikeController extends Controller
{
    public function __construct(protected LikeService $likeService) {}

    #[OA\Post(
        path: '/posts/{postId}/like',
        tags: ['Like'],
        summary: 'Curtir post',
        description: 'Permite ao usuário autenticado curtir um post específico. Requer autenticação.'
    )]
    #[OA\Parameter(
        name: 'postId',
        description: 'ID do post a curtir',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Post curtido com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Post curtido')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 404, description: 'Post não encontrado')]
    #[OA\Response(response: 409, description: 'Post já foi curtido')]
    #[OA\Security(name: 'bearerAuth')]
    public function like($postId): JsonResponse
    {
        /** @var int $userId */
        $userId = auth()->id();
        $this->likeService->likePost($userId, (int)$postId);
        return response()->json(['message' => 'Post curtido']);
    }

    #[OA\Delete(
        path: '/posts/{postId}/unlike',
        tags: ['Like'],
        summary: 'Descurtir post',
        description: 'Permite ao usuário autenticado remover sua curtida de um post específico. Requer autenticação.'
    )]
    #[OA\Parameter(
        name: 'postId',
        description: 'ID do post para remover curtida',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Curtida removida com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Post descurtido')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 404, description: 'Post não encontrado')]
    #[OA\Response(response: 409, description: 'Post não foi curtido')]
    #[OA\Security(name: 'bearerAuth')]
    public function unlike($postId): JsonResponse
    {
        /** @var int $userId */
        $userId = auth()->id();
        $this->likeService->unlikePost($userId, (int)$postId);
        return response()->json(['message' => 'Post descurtido']);
    }

    #[OA\Get(
        path: '/posts/{postId}/likes',
        tags: ['Like'],
        summary: 'Listar usuários que curtiram',
        description: 'Retorna a lista paginada de usuários que curtiram um post específico.'
    )]
    #[OA\Parameter(
        name: 'postId',
        description: 'ID do post',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
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
        description: 'Quantidade de registros por página (padrão: 20)',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 20, minimum: 1, maximum: 100)
    )]
    #[OA\Response(
        response: 200,
        description: 'Lista de usuários que curtiram',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                        new OA\Property(property: 'username', type: 'string', example: 'joao_silva'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                        new OA\Property(property: 'avatar', type: 'string', example: '/storage/avatars/joao.jpg'),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z')
                    ]
                )),
                new OA\Property(property: 'last_page', type: 'integer', example: 3),
                new OA\Property(property: 'total', type: 'integer', example: 42)
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Post não encontrado')]
    public function index($postId): JsonResponse
    {
        $likers = $this->likeService->getPostLikers((int)$postId);
        return response()->json($likers);
    }
}