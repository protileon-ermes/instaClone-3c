<?php

namespace App\Services;

use App\Models\Like;
use App\Models\Post;

class LikeService
{
    /**
     * Adiciona um like. Se já existir, não faz nada (Idempotente).
     */
    public function likePost(int $userId, int $postId): void
    {
        // firstOrCreate garante que não haverá duplicatas no banco
        Like::firstOrCreate([
            'user_id' => $userId,
            'post_id' => $postId
        ]);
    }

    /**
     * Remove um like. Se não existir, não faz nada (Idempotente).
     */
    public function unlikePost(int $userId, int $postId): void
    {
        // O delete() no Query Builder do Laravel não gera erro se o registro não for encontrado
        Like::where('user_id', $userId)
            ->where('post_id', $postId)
            ->delete();
    }

    public function getPostLikers(int $postId)
    {
        $post = Post::findOrFail($postId);
        return $post->likes()->with('user:id,username,name,avatar')->paginate(20);
    }
}