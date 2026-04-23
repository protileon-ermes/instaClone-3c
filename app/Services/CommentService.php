<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;

class CommentService
{
    public function getPostComments(int $postId)
    {
        return Comment::where('post_id', $postId)
            ->with('user:id,username,avatar') // Traz quem comentou
            ->latest()
            ->paginate(15);
    }

    public function store(int $userId, int $postId, string $body)
    {
        return Comment::create([
            'user_id' => $userId,
            'post_id' => $postId,
            'body'    => $body
        ]);
    }

    public function update(Comment $comment, string $body)
    {
        $comment->update(['body' => $body]);
        return $comment;
    }
}