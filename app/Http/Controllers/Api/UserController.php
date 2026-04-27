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

    // GET /api/users/{username}
    #[OA\Get(path: '/users/{username}', tags: ['User'], summary: 'Obter perfil do usuário')]
    #[OA\Response(response: 200, description: 'Dados do usuário')]
    public function show($username)
    {
        // O frontend Vue.js usa o username na URL, então buscamos por ele
        $user = User::where('username', $username)
            ->withCount(['followers', 'following', 'posts'])
            ->firstOrFail();

        return response()->json($user);
    }

    // PUT /api/users/me
    #[OA\Put(path: '/users/me', tags: ['User'], summary: 'Atualizar perfil do usuário')]
    #[OA\Response(response: 200, description: 'Perfil atualizado')]
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

    // POST /api/users/me/avatar
    #[OA\Post(path: '/users/me/avatar', tags: ['User'], summary: 'Atualizar avatar do usuário')]
    #[OA\Response(response: 200, description: 'Avatar atualizado com sucesso!')]
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $url = $this->userService->uploadAvatar($user, $request->file('avatar'));

        return response()->json([
            'message' => 'Avatar atualizado com sucesso!',
            'url'     => $url
        ]);
    }

    #[OA\Get(path: '/users/search', tags: ['User'], summary: 'Buscar usuários')]
    #[OA\Response(response: 200, description: 'Lista de usuários')]
    public function search(Request $request)
    {
        $query = $request->query('q');

        // Busca usuários pelo username ou nome real
        $users = User::where('username', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->paginate(10);

        return response()->json($users);
    }

    // GET /api/users/{id}/followers
    #[OA\Get(path: '/users/{id}/followers', tags: ['User'], summary: 'Listar seguidores do usuário')]
    #[OA\Response(response: 200, description: 'Lista de seguidores')]
    public function followers($id)
    {
        $user = User::findOrFail($id);
        return $user->followers()->paginate(20);
    }

    // GET /api/users/{id}/following
    #[OA\Get(path: '/users/{id}/following', tags: ['User'], summary: 'Listar usuários que está seguindo')]
    #[OA\Response(response: 200, description: 'Lista de usuários seguidos')]
    public function following($id)
    {
        $user = User::findOrFail($id);
        return $user->following()->paginate(20);
    }
}
