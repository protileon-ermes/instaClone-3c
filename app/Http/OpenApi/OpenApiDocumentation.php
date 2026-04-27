<?php

namespace App\Http\OpenApi;

/**
 * @OA\Info(
 *     title="InstaClone API Documentation",
 *     version="1.0.0",
 *     description="Documentação da API do projeto InstaClone (Backend Laravel)",
 *     @OA\Contact(
 *         email="seu-email@exemplo.com",
 *         name="Ermes Developer"
 *     )
 * )
 * @OA\Server(
 *     url="/api",
 *     description="Servidor de API Principal"
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Use um Token Sanctum para autenticar",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */
class OpenApiDocumentation
{
}

