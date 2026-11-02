
<?php

require_once __DIR__ . '/BaseDAO.php';

class CategoryDAO extends BaseDAO {

    public function __construct() {
        parent::__construct('categories');
    }

    // CREATE - Add a category
    public function create($data) {
        $sql = "INSERT INTO categories (name, description)
                VALUES (:name, :description)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ]);

        return (int) $this->conn->lastInsertId();
    }

    // READ - getAll() and getById()
    // Inherited from BaseDAO

    // UPDATE - Edit a category
    public function update($id, $data) {
        $sql = "UPDATE categories
                SET name = :name,
                    description = :description
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ]);
    }

    // DELETE - Inherited from BaseDAO
}
