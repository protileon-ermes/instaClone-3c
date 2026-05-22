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

    #[OA\Post(
        path: '/posts',
        tags: ['Post'],
        summary: 'Criar novo post',
        description: 'Cria um novo post com imagem e caption opcional. Requer autenticação.'
    )]
    #[OA\RequestBody(
        description: 'Dados do post a ser criado',
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['image'],
                properties: [
                    new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Imagem do post (máx 5MB)'),
                    new OA\Property(property: 'caption', type: 'string', maxLength: 2200, example: 'Que dia lindo! ☀️ #natureza #viagem')
                ]
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Post criado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'user_id', type: 'integer', example: 5),
                new OA\Property(property: 'caption', type: 'string', example: 'Que dia lindo! ☀️ #natureza #viagem'),
                new OA\Property(property: 'image_url', type: 'string', example: '/storage/posts/image_1.jpg'),
                new OA\Property(property: 'likes_count', type: 'integer', example: 0),
                new OA\Property(property: 'comments_count', type: 'integer', example: 0),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z')
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Arquivo inválido ou muito grande')]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 422, description: 'Dados inválidos')]
    #[OA\Security(name: 'bearerAuth')]
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

    #[OA\Get(
        path: '/posts/{postId}',
        tags: ['Post'],
        summary: 'Obter detalhes do post',
        description: 'Retorna os detalhes completos de um post específico incluindo informações do autor.'
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
        description: 'Detalhes do post retornado',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'user_id', type: 'integer', example: 5),
                new OA\Property(property: 'caption', type: 'string', example: 'Que dia lindo! ☀️ #natureza #viagem'),
                new OA\Property(property: 'image_url', type: 'string', example: '/storage/posts/image_1.jpg'),
                new OA\Property(property: 'likes_count', type: 'integer', example: 42),
                new OA\Property(property: 'comments_count', type: 'integer', example: 8),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z'),
                new OA\Property(
                    property: 'user',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 5),
                        new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                        new OA\Property(property: 'username', type: 'string', example: 'joao_silva'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                        new OA\Property(property: 'avatar', type: 'string', example: '/storage/avatars/joao.jpg'),
                        new OA\Property(property: 'bio', type: 'string', example: 'Fotógrafo e viajante')
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Post não encontrado')]
    public function show(Post $post)
    {
        return $post->load('user');
    }

    #[OA\Delete(
        path: '/posts/{postId}',
        tags: ['Post'],
        summary: 'Deletar post',
        description: 'Remove um post existente. Apenas o autor do post pode deletá-lo. Requer autenticação.'
    )]
    #[OA\Parameter(
        name: 'postId',
        description: 'ID do post a deletar',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Post deletado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Post deletado com sucesso')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 403, description: 'Acesso negado (apenas o autor pode deletar)')]
    #[OA\Response(response: 404, description: 'Post não encontrado')]
    #[OA\Security(name: 'bearerAuth')]
    public function destroy(Post $post)
    {
        // O Laravel verifica se o usuário logado passa na regra 'delete' da PostPolicy
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Post deletado com sucesso']);
    }

    #[OA\Get(
        path: '/users/{userId}/posts',
        tags: ['Post'],
        summary: 'Obter posts do usuário',
        description: 'Retorna a lista paginada de posts criados por um usuário específico, ordenados por data decrescente.'
    )]
    #[OA\Parameter(
        name: 'userId',
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
        description: 'Lista de posts do usuário retornada',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'user_id', type: 'integer', example: 5),
                        new OA\Property(property: 'caption', type: 'string', example: 'Que dia lindo! ☀️'),
                        new OA\Property(property: 'image_url', type: 'string', example: '/storage/posts/image_1.jpg'),
                        new OA\Property(property: 'likes_count', type: 'integer', example: 42),
                        new OA\Property(property: 'comments_count', type: 'integer', example: 8),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z')
                    ]
                )),
                new OA\Property(property: 'last_page', type: 'integer', example: 5),
                new OA\Property(property: 'total', type: 'integer', example: 56)
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Usuário não encontrado')]
    public function userPosts($userId)
    {
        return Post::where('user_id', $userId)
            ->latest()
            ->paginate(12);
    }

    #[OA\Put(
        path: '/posts/{postId}',
        tags: ['Post'],
        summary: 'Atualizar post',
        description: 'Atualiza a caption de um post existente. Apenas o autor pode atualizar. Requer autenticação.'
    )]
    #[OA\Parameter(
        name: 'postId',
        description: 'ID do post a atualizar',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        description: 'Dados a atualizar no post',
        required: true,
        content: new OA\JsonContent(
            required: ['caption'],
            properties: [
                new OA\Property(property: 'caption', type: 'string', maxLength: 2200, example: 'Nova caption editada! 📸 #atualizando')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Post atualizado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'user_id', type: 'integer', example: 5),
                new OA\Property(property: 'caption', type: 'string', example: 'Nova caption editada! 📸 #atualizando'),
                new OA\Property(property: 'image_url', type: 'string', example: '/storage/posts/image_1.jpg'),
                new OA\Property(property: 'likes_count', type: 'integer', example: 42),
                new OA\Property(property: 'comments_count', type: 'integer', example: 8),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-05-22T11:45:00Z')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 403, description: 'Acesso negado (apenas o autor pode atualizar)')]
    #[OA\Response(response: 404, description: 'Post não encontrado')]
    #[OA\Response(response: 422, description: 'Dados inválidos')]
    #[OA\Security(name: 'bearerAuth')]
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
