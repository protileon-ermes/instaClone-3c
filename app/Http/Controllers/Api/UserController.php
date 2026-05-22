<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    #[OA\Get(
        path: '/users/{username}',
        operationId: 'getUserByUsername',
        tags: ['User'],
        summary: 'Obter perfil do usuário',
        description: 'Retorna os dados completos do perfil de um usuário específico incluindo contadores de seguidores, seguindo e posts.'
    )]
    #[OA\Parameter(
        name: 'username',
        description: 'Nome de usuário',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Dados do usuário retornados com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                new OA\Property(property: 'username', type: 'string', example: 'joao_silva'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                new OA\Property(property: 'avatar', type: 'string', example: '/storage/avatars/joao.jpg'),
                new OA\Property(property: 'bio', type: 'string', example: 'Fotógrafo e viajante 📸'),
                new OA\Property(property: 'followers_count', type: 'integer', example: 125),
                new OA\Property(property: 'following_count', type: 'integer', example: 89),
                new OA\Property(property: 'posts_count', type: 'integer', example: 42),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-05-22T10:30:00Z')
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Usuário não encontrado')]
    public function show($username)
    {
        $user = User::where('username', $username)
            ->withCount(['followers', 'following', 'posts'])
            ->firstOrFail();

        return response()->json($user);
    }

    #[OA\Put(
        path: '/users/me',
        operationId: 'updateUserProfile',
        tags: ['User'],
        summary: 'Atualizar perfil do usuário',
        description: 'Atualiza os dados do perfil do usuário autenticado (name, username, bio). Requer autenticação.'
    )]
    #[OA\RequestBody(
        description: 'Dados do perfil a atualizar',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'João Silva Santos'),
                new OA\Property(property: 'username', type: 'string', maxLength: 30, example: 'joao_silva_2'),
                new OA\Property(property: 'bio', type: 'string', maxLength: 150, example: 'Fotógrafo profissional e amante de viagens 📸')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Perfil updated com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'João Silva Santos'),
                new OA\Property(property: 'username', type: 'string', example: 'joao_silva_2'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                new OA\Property(property: 'avatar', type: 'string', example: '/storage/avatars/joao.jpg'),
                new OA\Property(property: 'bio', type: 'string', example: 'Fotógrafo profissional e amante de viagens 📸'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-05-22T11:45:00Z')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 422, description: 'Dados inválidos ou username já em uso')]
    #[OA\Security(name: 'bearerAuth')]
    public function update(Request $request)
    {
        /** @var \App\Models\User&object{id: int} $user */
        $user = auth()->user();

        $data = $request->validate([
            'name'     => 'string|max:255',
            'username' => 'string|max:30|unique:users,username,' . $user->id,
            'bio'      => 'nullable|string|max:150',
        ]);

        $updatedUser = $this->userService->updateProfile($user, $data);

        return response()->json($updatedUser);
    }

    #[OA\Post(
        path: '/users/me/avatar',
        operationId: 'updateUserAvatar',
        tags: ['User'],
        summary: 'Atualizar avatar do usuário',
        description: 'Faz upload de uma nova imagem de avatar do usuário autenticado. Requer autenticação.'
    )]
    #[OA\RequestBody(
        description: 'Arquivo de imagem do avatar',
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['avatar'],
                properties: [
                    new OA\Property(property: 'avatar', type: 'string', format: 'binary', description: 'Imagem do avatar (JPEG, PNG, JPG, WebP - máx 2MB)')
                ]
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Avatar atualizado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                new OA\Property(property: 'username', type: 'string', example: 'joao_silva'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                new OA\Property(property: 'avatar', type: 'string', example: '/storage/avatars/joao_1234567890.jpg'),
                new OA\Property(property: 'bio', type: 'string', example: 'Fotógrafo e viajante'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-05-22T11:50:00Z')
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Arquivo inválido ou não é uma imagem')]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\Response(response: 422, description: 'Arquivo muito grande (máx 2MB)')]
    #[OA\Security(name: 'bearerAuth')]
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $this->userService->uploadAvatar($user, $request->file('avatar'));
        
        $user->refresh();
        return response()->json($user);
    }

    #[OA\Get(
        path: '/users/search',
        operationId: 'searchUsers',
        tags: ['User'],
        summary: 'Buscar usuários',
        description: 'Realiza busca de usuários por username ou nome real, retornando resultados paginados.'
    )]
    #[OA\Parameter(name: 'q', in: 'query', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1))]
    #[OA\Response(
        response: 200,
        description: 'Resultados da busca retornados',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'last_page', type: 'integer', example: 2),
                new OA\Property(property: 'per_page', type: 'integer', example: 10),
                new OA\Property(property: 'total', type: 'integer', example: 15),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                            new OA\Property(property: 'username', type: 'string', example: 'joao_silva'),
                            new OA\Property(property: 'email', type: 'string', example: 'joao@example.com'),
                            new OA\Property(property: 'avatar', type: 'string', example: '/storage/avatars/joao.jpg'),
                            new OA\Property(property: 'bio', type: 'string', example: 'Fotógrafo e viajante')
                        ]
                    )
                )
            ]
        )
    )]
    public function search(Request $request)
    {
        $query = $request->query('q');

        $users = User::where('username', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->paginate(10);

        return response()->json($users);
    }

    public function followers($id)
    {
        $user = User::findOrFail($id);
        return $user->followers()->paginate(20);
    }

    public function following($id)
    {
        $user = User::findOrFail($id);
        return $user->following()->paginate(20);
    }
}