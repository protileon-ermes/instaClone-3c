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
    public function uploadAvatar(User $user, $image): string
    {
        // Se já existir um avatar, deletamos o arquivo antigo
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Salva a nova imagem na pasta 'avatars' dentro do disco public
        $path = $image->store('avatars', 'public');
        
        $user->update(['avatar' => $path]);

        return Storage::url($path);
    }
}