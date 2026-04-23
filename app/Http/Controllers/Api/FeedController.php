<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $feed = $this->feedService->getHomeFeed(auth()->user());

        return response()->json($feed);
    }
}