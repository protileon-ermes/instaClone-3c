<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Post;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPostOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pega o post da rota (funciona se você estiver usando Route Model Binding)
        $post = $request->route('post');

        // Se o parâmetro na rota não for um objeto (id puro), buscamos no banco
        if (!$post instanceof Post) {
            $post = Post::findOrFail($post);
        }

        // Verifica se o usuário logado é o dono
        if ($post->user_id !== auth()->id()) {
            return response()->json([
                'error' => 'Acesso negado. Você não é o dono desta publicação.'
            ], 403);
        }

        return $next($request);
    }
}