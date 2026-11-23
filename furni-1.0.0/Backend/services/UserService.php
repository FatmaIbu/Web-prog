
<?php

require_once __DIR__ . '/../dao/UserDao.php';

class UserService {
    private UserDao $dao;

    public function __construct(UserDao $dao) {
        $this->dao = $dao;
    }

    public function getAll(): array {
        return $this->dao->getAll();
    }

    public function getById(int $id): array {
        $user = $this->dao->getById($id);

        if (!$user) {
            throw new OutOfBoundsException(
                "User not found"
            );
        }

        return $user;
    }

    public function create(array $data): array {
        $name = trim((string)($data['full_name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $password = $data['password'] ?? '';

        if ($name === '') {
            throw new InvalidArgumentException(
                "Full name is required"
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                "Valid email is required"
            );
        }

        if (!is_string($password) || strlen($password) < 8) {
            throw new InvalidArgumentException(
                "Password must be at least 8 characters"
            );
        }

        $data['full_name'] = $name;
        $data['email'] = strtolower($email);

        $id = $this->dao->create($data);

        return $this->getById((int)$id);
    }

    public function update(int $id, array $data): array {
        $this->getById($id);

        if (array_key_exists('full_name', $data)) {
            if (
                !is_string($data['full_name']) ||
                trim($data['full_name']) === ''
            ) {
                throw new InvalidArgumentException(
                    "Full name cannot be empty"
                );
            }

            $data['full_name'] = trim($data['full_name']);
        }

        if (array_key_exists('email', $data)) {
            if (
                !is_string($data['email']) ||
                !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
            ) {
                throw new InvalidArgumentException(
                    "Invalid email address"
                );
            }

            $data['email'] = strtolower(trim($data['email']));
        }

        $data = array_intersect_key(
            $data,
            array_flip(['full_name', 'email'])
        );

        if (empty($data)) {
            throw new InvalidArgumentException(
                "No valid fields to update"
            );
        }

        $this->dao->update($id, $data);

        return $this->getById($id);
    }

    public function delete(int $id): void {
        $this->getById($id);
        $this->dao->delete($id);
    }
}
