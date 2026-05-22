<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use OpenApi\Attributes as OA;

class CommentController extends Controller
{
    use AuthorizesRequests;
    public function __construct(protected CommentService $commentService) {}

    #[OA\Get(
        path: '/posts/{postId}/comments',
        tags: ['Comment'],
        summary: 'Listar comentários de um post',
        description: 'Retorna todos os comentários associados a um post específico'
    )]
    #[OA\Parameter(
        name: 'postId',
        description: 'ID do post',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Lista de comentários retornada com sucesso',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'user_id', type: 'integer', example: 5),
                    new OA\Property(property: 'post_id', type: 'integer', example: 10),
                    new OA\Property(property: 'body', type: 'string', example: 'Ótima foto!'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z'),
                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z'),
                    new OA\Property(
                        property: 'user',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 5),
                            new OA\Property(property: 'name', type: 'string', example: 'Maria Silva'),
                            new OA\Property(property: 'username', type: 'string', example: 'maria_silva'),
                            new OA\Property(property: 'avatar', type: 'string', example: '/storage/avatars/maria.jpg')
                        ]
                    )
                ]
            )
        )
    )]
    #[OA\Response(response: 404, description: 'Post não encontrado')]
    public function index($postId)
    {
        return response()->json($this->commentService->getPostComments($postId));
    }

    #[OA\Post(
        path: '/posts/{postId}/comments',
        tags: ['Comment'],
        summary: 'Criar comentário em um post',
        description: 'Cria um novo comentário associado a um post (requer autenticação)'
    )]
    #[OA\Parameter(
        name: 'postId',
        description: 'ID do post',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        description: 'Dados do comentário a ser criado',
        required: true,
        content: new OA\JsonContent(
            required: ['body'],
            properties: [
                new OA\Property(property: 'body', type: 'string', maxLength: 1000, example: 'Adorei essa foto!')
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Comentário criado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'user_id', type: 'integer', example: 5),
                new OA\Property(property: 'post_id', type: 'integer', example: 10),
                new OA\Property(property: 'body', type: 'string', example: 'Adorei essa foto!'),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-22T10:35:00Z'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-05-22T10:35:00Z')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 422, description: 'Dados inválidos')]
    #[OA\Response(response: 404, description: 'Post não encontrado')]
    #[OA\Security(name: 'bearerAuth')]
    public function store(Request $request, $postId)
    {
        $data = $request->validate(['body' => 'required|string|max:1000']);
        /** @var int $userId */
        $userId = auth()->id();
        $comment = $this->commentService->store($userId, $postId, $data['body']);
        return response()->json($comment, 201);
    }

    #[OA\Put(
        path: '/comments/{commentId}',
        tags: ['Comment'],
        summary: 'Atualizar comentário',
        description: 'Atualiza o conteúdo de um comentário existente (apenas o autor pode atualizar)'
    )]
    #[OA\Parameter(
        name: 'commentId',
        description: 'ID do comentário',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        description: 'Novo conteúdo do comentário',
        required: true,
        content: new OA\JsonContent(
            required: ['body'],
            properties: [
                new OA\Property(property: 'body', type: 'string', maxLength: 1000, example: 'Adorei essa foto! Muito legal.')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Comentário atualizado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'user_id', type: 'integer', example: 5),
                new OA\Property(property: 'post_id', type: 'integer', example: 10),
                new OA\Property(property: 'body', type: 'string', example: 'Adorei essa foto! Muito legal.'),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-22T10:35:00Z'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-05-22T10:40:00Z')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 403, description: 'Acesso negado (apenas o autor pode atualizar)')]
    #[OA\Response(response: 404, description: 'Comentário não encontrado')]
    #[OA\Response(response: 422, description: 'Dados inválidos')]
    #[OA\Security(name: 'bearerAuth')]
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);
        $data = $request->validate(['body' => 'required|string|max:1000']);
        return response()->json($this->commentService->update($comment, $data['body']));
    }

    #[OA\Delete(
        path: '/comments/{commentId}',
        tags: ['Comment'],
        summary: 'Deletar comentário',
        description: 'Remove um comentário existente (apenas o autor ou admin podem deletar)'
    )]
    #[OA\Parameter(
        name: 'commentId',
        description: 'ID do comentário',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Comentário removido com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Removido com sucesso')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 403, description: 'Acesso negado (apenas o autor pode deletar)')]
    #[OA\Response(response: 404, description: 'Comentário não encontrado')]
    #[OA\Security(name: 'bearerAuth')]
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return response()->json(['message' => 'Removido com sucesso']);
    }
}