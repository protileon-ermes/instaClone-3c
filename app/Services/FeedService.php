<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FeedService
{
    /**
     * Busca os posts de quem o usuário segue + seus próprios posts.
     */
    public function getHomeFeed(User $user): LengthAwarePaginator
    {
        // Obtém apenas os IDs dos usuários que o usuário logado segue
        $followingIds = $user->following()->pluck('users.id');

        // Adiciona o ID do próprio usuário para que ele veja seus posts no feed também
        $idsForFeed = $followingIds->push($user->id);

        return Post::whereIn('user_id', $idsForFeed)
            ->with(['user:id,username,avatar']) // Eager loading otimizado (só o necessário)
            ->withCount(['likes', 'comments'])  // Preparado para os passos 7 e 8
            ->latest()                          // Mais recentes primeiro (created_at DESC)
            ->paginate(15);                     // Paginação por Offset (padrão do Laravel)
    }
}