<?php

namespace App\Services;

use App\Models\Like;
use App\Models\Post;

class LikeService
{
    public function toggleLike(int $userId, int $postId): array
    {
        $post = Post::findOrFail($postId);

        $like = Like::where('user_id', $userId)
                    ->where('post_id', $postId)
                    ->first();

        if ($like) {
            $like->delete();
            return ['status' => 'unliked', 'count' => $post->likes()->count()];
        }

        Like::create([
            'user_id' => $userId,
            'post_id' => $postId
        ]);

        return ['status' => 'liked', 'count' => $post->likes()->count()];
    }

    public function getPostLikers(int $postId)
    {
        $post = Post::findOrFail($postId);
        // Retorna a lista de usuários que curtiram, com paginação
        return $post->likes()->with('user:id,username,name,avatar')->paginate(20);
    }
}