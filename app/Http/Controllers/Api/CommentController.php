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

    #[OA\Get(path: '/posts/{postId}/comments', tags: ['Comment'], summary: 'Listar comentários de um post')]
    #[OA\Response(response: 200, description: 'Lista de comentários')]
    public function index($postId)
    {
        return response()->json($this->commentService->getPostComments($postId));
    }

    #[OA\Post(path: '/posts/{id}/comments', tags: ['Comment'], summary: 'Comentar em post')]
    #[OA\Response(response: 201, description: 'Comentário criado')]
    public function store(Request $request, $postId)
    {
        $data = $request->validate(['body' => 'required|string|max:1000']);
        /** @var int $userId */
        $userId = auth()->id();
        $comment = $this->commentService->store($userId, $postId, $data['body']);
        return response()->json($comment, 201);
    }

    #[OA\Put(path: '/comments/{comment}', tags: ['Comment'], summary: 'Atualizar comentário')]
    #[OA\Response(response: 200, description: 'Comentário atualizado')]
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);
        $data = $request->validate(['body' => 'required|string|max:1000']);
        return response()->json($this->commentService->update($comment, $data['body']));
    }

    #[OA\Delete(path: '/comments/{comment}', tags: ['Comment'], summary: 'Deletar comentário')]
    #[OA\Response(response: 200, description: 'Removido')]
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return response()->json(['message' => 'Removido com sucesso']);
    }
}