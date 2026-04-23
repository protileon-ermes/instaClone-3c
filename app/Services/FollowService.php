<?php

namespace App\Services;

use App\Models\User;

class FollowService
{
    public function follow(User $follower, int $followedId)
    {
        // Impede de seguir a si mesmo
        if ($follower->id === $followedId) {
            throw new \Exception("Você não pode seguir a si mesmo.");
        }

        // attach() cria a relação na tabela pivô
        return $follower->following()->syncWithoutDetaching([$followedId]);
    }

    public function unfollow(User $follower, int $followedId)
    {
        // detach() remove a relação
        return $follower->following()->detach($followedId);
    }
    
    public function isFollowing(User $follower, int $followedId): bool
    {
        return $follower->following()->where('followed_id', $followedId)->exists();
    }
}