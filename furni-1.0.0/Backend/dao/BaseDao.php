
<?php

class BaseDAO {
    protected PDO $conn;
    protected string $table;

    public function __construct(string $table) {
        $config = require __DIR__ . '/../config/config.php';

        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";

        $this->conn = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );

        $this->table = $table;
    }

    public function getAll(): array {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table}"
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function delete(int $id): bool {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE id = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
}
