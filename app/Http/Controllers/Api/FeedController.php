<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(path: '/feed', tags: ['Feed'], summary: 'Obter feed de postagens', description: 'Retorna os posts dos usuários seguidos e do próprio usuário logado.', operationId: 'getFeed')]
#[OA\Response(response: 200, description: 'Sucesso', content: new OA\JsonContent(type: 'object'))]
#[OA\Response(response: 401, description: 'Não autenticado')]
class FeedController extends Controller
{
    public function __construct(
        protected FeedService $feedService
    ) {}

    /**
     * Retorna o feed paginado do usuário logado.
     */
    public function index(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $feed = $this->feedService->getHomeFeed($user);

        return response()->json($feed);
    }
}