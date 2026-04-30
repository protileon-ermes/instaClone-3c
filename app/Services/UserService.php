<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Atualiza os dados básicos do perfil.
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    /**
     * Lida com o upload e substituição do avatar.
     */
    public function uploadAvatar(User $user, $image): void
    {
        $oldAvatar = $user->getRawOriginal('avatar'); // path cru, ignora o accessor

        if ($oldAvatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        $path = $image->store('avatars', 'public');
        $user->update(['avatar' => $path]);
    }
}