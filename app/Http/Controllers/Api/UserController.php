<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    // GET /api/users/{username}
    public function show($username)
    {
        // O frontend Vue.js usa o username na URL, então buscamos por ele
        $user = User::where('username', $username)
            ->withCount(['followers', 'following', 'posts'])
            ->firstOrFail();

        return response()->json($user);
    }

    // PUT /api/users/me
    public function update(Request $request)
    {
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
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $url = $this->userService->uploadAvatar(auth()->user(), $request->file('avatar'));

        return response()->json([
            'message' => 'Avatar atualizado com sucesso!',
            'url'     => $url
        ]);
    }

    // GET /api/users/search?q=...
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
    public function followers($id)
    {
        $user = User::findOrFail($id);
        return $user->followers()->paginate(20);
    }

    // GET /api/users/{id}/following
    public function following($id)
    {
        $user = User::findOrFail($id);
        return $user->following()->paginate(20);
    }
}
