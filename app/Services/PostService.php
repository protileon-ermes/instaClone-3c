<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function createPost(int $userId, $image, ?string $caption)
    {
        $path = $image->store('posts', 'public');

        return Post::create([
            'user_id' => $userId,
            'image_url' => Storage::url($path),
            'caption' => $caption
        ]);
    }

    public function deletePost(Post $post)
    {
        // Remove a imagem física do storage
        $path = str_replace('/storage/', '', $post->image_url);
        Storage::disk('public')->delete($path);

        return $post->delete();
    }

    public function getUserPosts(int $userId)
    {
        return Post::where('user_id', $userId)
            ->latest()
            ->paginate(12);
    }
}
