
<?php

require_once __DIR__ . '/../dao/UserDao.php';

use Firebase\JWT\JWT;

class AuthService {
    private UserDao $userDao;

    public function __construct(UserDao $userDao) {
        $this->userDao = $userDao;
    }

    public function login(array $data): array {
        $email = strtolower(trim(
            (string)($data['email'] ?? '')
        ));

        $password = $data['password'] ?? '';

        if (
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            !is_string($password) ||
            $password === ''
        ) {
            throw new InvalidArgumentException(
                "Invalid email or password"
            );
        }

        $user = $this->userDao->getByEmailForAuth($email);

        if (
            !$user ||
            !password_verify($password, $user['password_hash'])
        ) {
            throw new InvalidArgumentException(
                "Invalid email or password"
            );
        }

        $config = require __DIR__ . '/../config/jwt.php';

        $secret = $config['secret'] ?? '';

        if (!is_string($secret) || strlen($secret) < 32) {
            throw new RuntimeException(
                "JWT secret is not configured"
            );
        }

        $now = time();

        $payload = [
            'iss' => 'furniera',
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + (int)$config['expiration'],
            'sub' => (string)$user['id']
        ];

        $token = JWT::encode(
            $payload,
            $secret,
            'HS256'
        );

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => (int)$config['expiration'],
            'user' => [
                'id' => $user['id'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ];
    }
}
