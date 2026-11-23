
<?php

require_once __DIR__ . '/BaseDAO.php';

class ReviewDAO extends BaseDAO {

    public function __construct() {
        parent::__construct('reviews');
    }

    // CREATE - Add a new review
    public function create($data) {
        $sql = "INSERT INTO reviews
                (user_id, product_id, rating, comment)
                VALUES
                (:user_id, :product_id, :rating, :comment)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            'user_id' => $data['user_id'],
            'product_id' => $data['product_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null
        ]);

        return (int) $this->conn->lastInsertId();
    }

    // READ - getAll() and getById()
    // Inherited from BaseDAO

    // UPDATE - Edit a review
    
    public function update($id, $data) {
        $allowed = ['rating', 'comment'];
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

        $sql = "UPDATE reviews SET " .
            implode(', ', $fields) .
            " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($params);
    }


    // DELETE - Inherited from BaseDAO
}
