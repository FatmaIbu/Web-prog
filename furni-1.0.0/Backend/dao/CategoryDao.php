
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
        $allowed = ['name', 'description'];
        $fields = [];
        $params = ['id' => $id];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            throw new InvalidArgumentException(
                "No valid fields to update"
            );
        }

        $sql = "UPDATE categories SET " .
            implode(', ', $fields) .
            " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($params);
    }


    // DELETE - Inherited from BaseDAO
}
