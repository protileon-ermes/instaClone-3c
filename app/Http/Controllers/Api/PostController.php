<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Importante para as Policies

class PostController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected PostService $postService)
    {
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image|max:5120', // 5MB
            'caption' => 'nullable|string|max:2200'
        ]);

        $post = $this->postService->createPost(
            auth()->id(),
            $request->file('image'),
            $data['caption'] ?? null
        );

        return response()->json($post, 201);
    }

    public function show(Post $post)
    {
        return $post->load('user');
    }

    public function destroy(Post $post)
    {
        // Aqui verificamos se o user_id do post é o mesmo do usuário logado
        if ($post->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $this->postService->deletePost($post);
        return response()->json(['message' => 'Post deletado']);
    }

    public function userPosts($userId)
    {
        return Post::where('user_id', $userId)
            ->latest()
            ->paginate(12);
    }

    public function update(Request $request, Post $post)
    {
        // Verifica se o usuário é o dono do post
        $this->authorize('update', $post);

        $data = $request->validate([
            'caption' => 'nullable|string|max:2200'
        ]);

        $post->update($data);

        return response()->json($post);
    }
}
