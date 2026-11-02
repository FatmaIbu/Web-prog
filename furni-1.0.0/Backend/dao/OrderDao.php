
<?php

require_once __DIR__ . '/BaseDAO.php';

class OrderDAO extends BaseDAO {

    public function __construct() {
        parent::__construct('orders');
    }

    // CREATE - Add a new order
    public function create($data) {
        $sql = "INSERT INTO orders
                (user_id, total_amount, status)
                VALUES
                (:user_id, :total_amount, :status)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            'user_id' => $data['user_id'],
            'total_amount' => $data['total_amount'],
            'status' => $data['status'] ?? 'pending'
        ]);

        return (int) $this->conn->lastInsertId();
    }

    // READ - getAll() and getById()
    // Inherited from BaseDAO

    // UPDATE - Edit an order
    public function update($id, $data) {
        $sql = "UPDATE orders
                SET user_id = :user_id,
                    total_amount = :total_amount,
                    status = :status
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'user_id' => $data['user_id'],
            'total_amount' => $data['total_amount'],
            'status' => $data['status'] ?? 'pending'
        ]);
    }

    // DELETE - Inherited from BaseDAO
}
