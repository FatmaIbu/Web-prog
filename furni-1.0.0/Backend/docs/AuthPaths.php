
<?php

use OpenApi\Attributes as OA;

class AuthPaths
{
    #[OA\Post(
        path: '/auth/register',
        summary: 'Register a new customer',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['full_name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'full_name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Registration successful'),
            new OA\Response(response: 400, description: 'Invalid registration data')
        ]
    )]
    public function register(): void {}

    #[OA\Post(
        path: '/auth/login',
        summary: 'Log in and receive a JWT token',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login successful'),
            new OA\Response(response: 401, description: 'Invalid credentials')
        ]
    )]
    public function login(): void {}

    #[OA\Get(
        path: '/auth/me',
        summary: 'Get the currently authenticated user',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Authenticated user information'),
            new OA\Response(response: 401, description: 'Missing or invalid JWT token')
        ]
    )]
    public function me(): void {}
}
