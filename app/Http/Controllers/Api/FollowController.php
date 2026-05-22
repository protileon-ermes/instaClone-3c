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

    #[OA\Post(
        path: '/users/{id}/follow',
        tags: ['Follow'],
        summary: 'Seguir usuário',
        description: 'Permite ao usuário autenticado seguir outro usuário. Requer autenticação.'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID do usuário a ser seguido',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Usuário seguido com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Seguindo com sucesso')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 404, description: 'Usuário não encontrado')]
    #[OA\Response(response: 422, description: 'Erro ao seguir (pode estar seguindo já)')]
    #[OA\Security(name: 'bearerAuth')]
    public function follow($id): JsonResponse
    {
        try {
            $this->followService->follow((int)$id);
            return response()->json(['message' => 'Seguindo com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    #[OA\Delete(
        path: '/users/{id}/unfollow',
        tags: ['Follow'],
        summary: 'Deixar de seguir usuário',
        description: 'Remove o usuário autenticado da lista de seguidores de outro usuário. Requer autenticação.'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID do usuário para deixar de seguir',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Parou de seguir com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Deixou de seguir')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 404, description: 'Usuário não encontrado')]
    #[OA\Security(name: 'bearerAuth')]
    public function unfollow($id): JsonResponse
    {
        $this->followService->unfollow((int)$id);
        return response()->json(['message' => 'Deixou de seguir']);
    }

    #[OA\Get(
        path: '/users/{id}/is-following',
        tags: ['Follow'],
        summary: 'Verificar status de seguimento',
        description: 'Verifica se o usuário autenticado está seguindo um usuário específico. Requer autenticação.'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID do usuário a verificar',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Status de seguimento retornado',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'is_following', type: 'boolean', example: true)
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 404, description: 'Usuário não encontrado')]
    #[OA\Security(name: 'bearerAuth')]
    public function checkFollow($id): JsonResponse
    {
        $isFollowing = $this->followService->isFollowing((int)$id);
        return response()->json(['is_following' => $isFollowing]);
    }

    #[OA\Get(
        path: '/users/{id}/followers',
        tags: ['Follow'],
        summary: 'Listar seguidores do usuário',
        description: 'Retorna a lista paginada de usuários que seguem o usuário especificado.'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID do usuário',
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
    #[OA\Response(
        response: 200,
        description: 'Lista de seguidores retornada',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Maria Silva'),
                        new OA\Property(property: 'username', type: 'string', example: 'maria_silva'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'maria@example.com'),
                        new OA\Property(property: 'avatar', type: 'string', example: '/storage/avatars/maria.jpg'),
                        new OA\Property(property: 'bio', type: 'string', example: 'Fotógrafa e viajante'),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z')
                    ]
                )),
                new OA\Property(property: 'last_page', type: 'integer', example: 3),
                new OA\Property(property: 'total', type: 'integer', example: 42)
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Usuário não encontrado')]
    public function followers($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user->followers()->paginate(20));
    }

    #[OA\Get(
        path: '/users/{id}/following',
        tags: ['Follow'],
        summary: 'Listar usuários seguidos',
        description: 'Retorna a lista paginada de usuários que o usuário especificado está seguindo.'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID do usuário',
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
    #[OA\Response(
        response: 200,
        description: 'Lista de usuários seguidos retornada',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 2),
                        new OA\Property(property: 'name', type: 'string', example: 'João Pedro'),
                        new OA\Property(property: 'username', type: 'string', example: 'joao_pedro'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                        new OA\Property(property: 'avatar', type: 'string', example: '/storage/avatars/joao.jpg'),
                        new OA\Property(property: 'bio', type: 'string', example: 'Desenvolvedor Full Stack'),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-20T15:45:00Z')
                    ]
                )),
                new OA\Property(property: 'last_page', type: 'integer', example: 2),
                new OA\Property(property: 'total', type: 'integer', example: 28)
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Usuário não encontrado')]
    public function following($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user->following()->paginate(20));
    }
}
