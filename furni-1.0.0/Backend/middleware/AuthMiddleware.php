
<?php

require_once __DIR__ . '/../dao/UserDao.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public static function requireUser(): array
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!preg_match('/^Bearer\s+(\S+)$/i', $header, $matches)) {
            throw new UnexpectedValueException('Missing authentication token');
        }

        $config = require __DIR__ . '/../config/jwt.php';

        if (empty($config['secret']) || strlen($config['secret']) < 32) {
            throw new RuntimeException('JWT secret is not configured');
        }

        try {
            $decoded = JWT::decode(
                $matches[1],
                new Key($config['secret'], 'HS256')
            );
        } catch (Throwable $e) {
            throw new UnexpectedValueException('Invalid or expired token', 0, $e);
        }

        if (($decoded->iss ?? null) !== 'furniera' ||
            !isset($decoded->sub) ||
            !ctype_digit((string) $decoded->sub) ||
            (int) $decoded->sub < 1) {
            throw new UnexpectedValueException('Invalid token claims');
        }

        $userDao = new UserDao();
        $user = $userDao->getById((int) $decoded->sub);

        if (!$user) {
            throw new UnexpectedValueException('User no longer exists');
        }

        Flight::set('user', $user);

        return $user;
    }
}