
<?php

require_once __DIR__ . '/BaseDAO.php';

class UserDAO extends BaseDAO {

    public function __construct() {
        parent::__construct('users');
    }

    // CREATE - Add a new user
    public function create($data) {
        $sql = "INSERT INTO users
                (full_name, email, password_hash, role)
                VALUES
                (:full_name, :email, :password_hash, :role)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password_hash' => password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            ),
            'role' => 'customer'
        ]);

        return (int) $this->conn->lastInsertId();
    }

    // READ - Get users without exposing password hashes
    public function getAll(): array {
        $stmt = $this->conn->prepare(
            "SELECT id, full_name, email, role, created_at
             FROM users"
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->conn->prepare(
            "SELECT id, full_name, email, role, created_at
             FROM users WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    // UPDATE - Edit user information
    public function update($id, $data) {
        $sql = "UPDATE users
                SET full_name = :full_name,
                    email = :email
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'full_name' => $data['full_name'],
            'email' => $data['email']
        ]);
    }

    // DELETE - Inherited from BaseDAO
}
