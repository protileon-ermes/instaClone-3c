<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\CommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - InstaClone
|--------------------------------------------------------------------------
*/

// ============================================================
// 1. ROTAS PÚBLICAS (Passo 2)
// ============================================================
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);


// ============================================================
// 2. ROTAS PROTEGIDAS (Sanctum)
// ============================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // --- Autenticação (Passo 2) ---
    Route::get('/auth/me', function () { return auth()->user(); });
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // --- Feed do Usuário (Passo 6) ---
    Route::get('/feed', [FeedController::class, 'index']);

    // --- Perfil de Usuário (Passo 3) ---
    Route::get('/users/search', [UserController::class, 'search']); 
    Route::get('/users/{username}', [UserController::class, 'show']);
    Route::put('/users/me', [UserController::class, 'update']);
    Route::post('/users/me/avatar', [UserController::class, 'updateAvatar']);
    
    // --- Sistema de Follow (Passo 4) ---
    Route::post('/users/{id}/follow', [FollowController::class, 'follow']);
    Route::delete('/users/{id}/unfollow', [FollowController::class, 'unfollow']);
    Route::get('/users/{id}/is-following', [FollowController::class, 'checkFollow']);
    Route::get('/users/{id}/followers', [UserController::class, 'followers']);
    Route::get('/users/{id}/following', [UserController::class, 'following']);

    // --- Publicações/Posts (Passo 5) ---
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']); // DELETE do Post
    Route::get('/users/{id}/posts', [PostController::class, 'userPosts']);

    // --- Curtidas/Likes (Passo 7) ---
    Route::post('/posts/{id}/like', [LikeController::class, 'toggle']);
    Route::delete('/posts/{id}/unlike', [LikeController::class, 'unlike']); // DELETE do Like
    Route::get('/posts/{id}/likes', [LikeController::class, 'index']);

    // --- Comentários (Passo 8) ---
    // Listar e Criar (usam o ID do Post)
    Route::get('/posts/{id}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);

    // Editar e Deletar (usam o ID do Comentário diretamente)
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    
});