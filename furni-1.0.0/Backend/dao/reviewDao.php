
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
        $sql = "UPDATE reviews
                SET user_id = :user_id,
                    product_id = :product_id,
                    rating = :rating,
                    comment = :comment
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'user_id' => $data['user_id'],
            'product_id' => $data['product_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null
        ]);
    }

    // DELETE - Inherited from BaseDAO
}
