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
    /**
     * @OA\Post(
     * path="/auth/register",
     * tags={"Auth"},
     * summary="Registrar novo usuário",
     * @OA\Response(response=201, description="Usuário criado"),
     * @OA\Response(response=422, description="Dados inválidos")
     * )
     */
    #[OA\Post(path: '/auth/register', tags: ['Auth'], summary: 'Registrar novo usuário')]
    #[OA\Response(response: 201, description: 'Usuário criado')]
    #[OA\Response(response: 422, description: 'Dados inválidos')]
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

    /**
     * @OA\Post(
     * path="/auth/login",
     * tags={"Auth"},
     * summary="Login",
     * @OA\Response(response=200, description="Token retornado")
     * )
     */
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

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Token deletado com sucesso']);
    }

    public function refresh(Request $request)
    {
        $newToken = $this->authService->refreshToken($request->user());

        return response()->json([
            'access_token' => $newToken,
            'token_type'   => 'Bearer'
        ]);
    }
}
