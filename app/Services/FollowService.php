<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FollowService
{
    public function follow(int $userIdToFollow)
    {
        /** @var User $user */
        $user = Auth::user();

        // Impedir de seguir a si mesmo
        if ($user->id === $userIdToFollow) {
            throw new \Exception("Você não pode seguir a si mesmo.");
        }

        // Agora o Intelephense sabe que $user tem o método following()
        return $user->following()->syncWithoutDetaching([$userIdToFollow]);
    }

    public function unfollow(int $userIdToUnfollow)
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->following()->detach($userIdToUnfollow);
    }

    public function isFollowing(int $targetUserId): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->following()->where('following_id', $targetUserId)->exists();
    }
}