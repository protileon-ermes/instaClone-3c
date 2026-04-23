<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Registro de novo usuário
     */
    public function register(array $data)
    {
        $user = User::create([
            'name'     => $data['name'],
            'username' => $data['username'], // Importante para o Passo 3
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return [
            'user'  => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }

    /**
     * Login de usuário
     */
    public function login(string $email, string $password)
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        return [
            'user'  => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }

    /**
     * "Refresh" do Token (Sanctum Style)
     * Deleta o token atual e gera um novo.
     */
    public function refreshToken($user)
    {
        $user->currentAccessToken()->delete();
        return $user->createToken('auth_token')->plainTextToken;
    }
}