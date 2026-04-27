<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Importante para as Policies
use OpenApi\Attributes as OA;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected PostService $postService)
    {
    }

    #[OA\Post(path: '/posts', tags: ['Post'], summary: 'Criar novo post')]
    #[OA\Response(response: 201, description: 'Post criado')]
    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image|max:5120', // 5MB
            'caption' => 'nullable|string|max:2200'
        ]);

        /** @var int $userId */
        $userId = auth()->id();
        $post = $this->postService->createPost(
            $userId,
            $request->file('image'),
            $data['caption'] ?? null
        );

        return response()->json($post, 201);
    }

    #[OA\Get(path: '/posts/{post}', tags: ['Post'], summary: 'Obter detalhes do post')]
    #[OA\Response(response: 200, description: 'Detalhes do post')]
    public function show(Post $post)
    {
        return $post->load('user');
    }

    #[OA\Delete(path: '/posts/{post}', tags: ['Post'], summary: 'Deletar post')]
    #[OA\Response(response: 200, description: 'Post deletado com sucesso')]
    public function destroy(Post $post)
    {
        // O Laravel verifica se o usuário logado passa na regra 'delete' da PostPolicy
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Post deletado com sucesso']);
    }

    #[OA\Get(path: '/users/{userId}/posts', tags: ['Post'], summary: 'Obter posts do usuário')]
    #[OA\Response(response: 200, description: 'Lista de posts do usuário')]
    public function userPosts($userId)
    {
        return Post::where('user_id', $userId)
            ->latest()
            ->paginate(12);
    }

    #[OA\Put(path: '/posts/{post}', tags: ['Post'], summary: 'Atualizar post')]
    #[OA\Response(response: 200, description: 'Post atualizado')]
    public function update(Request $request, Post $post)
    {
        // O Laravel verifica se o usuário logado passa na regra 'update' da PostPolicy
        $this->authorize('update', $post);

        $data = $request->validate([
            'caption' => 'nullable|string|max:2200'
        ]);

        $post->update($data);

        return response()->json($post);
    }
}
