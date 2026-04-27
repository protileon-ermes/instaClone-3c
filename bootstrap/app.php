<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'is_owner' => \App\Http\Middleware\CheckPostOwner::class,
        ]);
        $middleware->statefulApi();
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        // 1. Quando o usuário NÃO está logado ou o token expirou
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) { // Só aplica isso para rotas de API
                return response()->json([
                    'status' => false,
                    'message' => 'Token inválido ou ausente. Por favor, faça login.'
                ], 401);
            }
        });

        // 2. Quando um ID não é encontrado (ex: post que não existe)
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'O recurso solicitado não foi encontrado.'
                ], 404);
            }
        });

    })->create();