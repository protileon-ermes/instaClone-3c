<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Info(title: "InstaClone API Documentation", version: "1.0.0")]
#[OA\Server(url: "/api", description: "Servidor de API Principal")]
class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }
    #[OA\Post(
        path: '/auth/register',
        tags: ['Auth'],
        summary: 'Registrar novo usuário',
        description: 'Cria uma nova conta de usuário com os dados fornecidos'
    )]
    #[OA\RequestBody(
        description: 'Dados para registro do novo usuário',
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'username', 'email', 'password', 'password_confirmation'],
            properties: [
                new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'João Silva'),
                new OA\Property(property: 'username', type: 'string', maxLength: 30, example: 'joao_silva'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'SecurePass123'),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'SecurePass123')
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Usuário registrado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGc...'),
                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                new OA\Property(
                    property: 'user',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                        new OA\Property(property: 'email', type: 'string', example: 'joao@example.com'),
                        new OA\Property(property: 'username', type: 'string', example: 'joao_silva')
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 422, description: 'Dados inválidos ou username/email já existe')]
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:30|unique:users',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = $this->authService->register($data);

        return response()->json([
            'access_token' => $result['token'],
            'token_type'   => 'Bearer',
            'user'         => $result['user']
        ], 201);
    }

    #[OA\Post(
        path: '/auth/login',
        tags: ['Auth'],
        summary: 'Fazer login',
        description: 'Autentica um usuário com email e senha, retornando um token de acesso'
    )]
    #[OA\RequestBody(
        description: 'Credenciais do usuário',
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Login realizado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGc...'),
                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                new OA\Property(
                    property: 'user',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                        new OA\Property(property: 'email', type: 'string', example: 'joao@example.com'),
                        new OA\Property(property: 'username', type: 'string', example: 'joao_silva')
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Email ou senha inválidos')]
    #[OA\Response(response: 422, description: 'Dados inválidos')]
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $result = $this->authService->login($data['email'], $data['password']);

        return response()->json([
            'access_token' => $result['token'],
            'token_type'   => 'Bearer',
            'user'         => $result['user']
        ]);
    }

    #[OA\Post(
        path: '/auth/logout',
        tags: ['Auth'],
        summary: 'Fazer logout',
        description: 'Desautentica o usuário atual revogando seu token de acesso'
    )]
    #[OA\Response(
        response: 200,
        description: 'Logout realizado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Token deletado com sucesso')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado')]
    #[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer', bearerFormat: 'JWT')]
    #[OA\Security(name: 'bearerAuth')]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Token deletado com sucesso']);
    }

    #[OA\Post(
        path: '/auth/refresh',
        tags: ['Auth'],
        summary: 'Renovar token de acesso',
        description: 'Gera um novo token de acesso a partir de um token válido'
    )]
    #[OA\Response(
        response: 200,
        description: 'Token renovado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGc...'),
                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Não autenticado ou token inválido')]
    #[OA\Security(name: 'bearerAuth')]
    public function refresh(Request $request)
    {
        $newToken = $this->authService->refreshToken($request->user());

        return response()->json([
            'access_token' => $newToken,
            'token_type'   => 'Bearer'
        ]);
    }
}
