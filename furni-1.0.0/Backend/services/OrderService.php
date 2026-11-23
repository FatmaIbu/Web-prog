
<?php

require_once __DIR__ . '/../dao/OrderDao.php';

class OrderService {
    private OrderDao $dao;

    private const ALLOWED_STATUSES = [
        'pending',
        'processing',
        'shipped',
        'delivered',
        'completed',
        'cancelled'
    ];

    public function __construct(OrderDao $dao) {
        $this->dao = $dao;
    }

    public function getAll(): array {
        return $this->dao->getAll();
    }

    public function getById(int $id): array {
        $order = $this->dao->getById($id);

        if (!$order) {
            throw new OutOfBoundsException(
                "Order not found"
            );
        }

        return $order;
    }

    public function create(array $data): array {
        $userId = filter_var(
            $data['user_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($userId === false || $userId === null || $userId <= 0) {
            throw new InvalidArgumentException(
                "Valid user ID is required"
            );
        }

        if (
            !isset($data['total_amount']) ||
            !is_numeric($data['total_amount']) ||
            (float)$data['total_amount'] < 0
        ) {
            throw new InvalidArgumentException(
                "Total amount must be non-negative"
            );
        }

        $status = $data['status'] ?? 'pending';

        if (!in_array($status, self::ALLOWED_STATUSES, true)) {
            throw new InvalidArgumentException(
                "Invalid order status"
            );
        }

        $data['user_id'] = (int)$userId;
        $data['status'] = $status;

        $id = $this->dao->create($data);

        return $this->getById((int)$id);
    }

    /**
     * Create an order using authenticated user information
     * and product prices from the database.
     */
    public function createWithItems(int $userId, array $items): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Valid user ID is required"
            );
        }

        if (empty($items)) {
            throw new InvalidArgumentException(
                "At least one order item is required"
            );
        }

        $orderId = $this->dao->createWithItems($userId, $items);

        return $this->getById($orderId);
    }


    public function update(int $id, array $data): array {
        $this->getById($id);

        if (
            !array_key_exists('status', $data) ||
            !in_array(
                $data['status'],
                self::ALLOWED_STATUSES,
                true
            )
        ) {
            throw new InvalidArgumentException(
                "Valid order status is required"
            );
        }

        $this->dao->update($id, [
            'status' => $data['status']
        ]);

        return $this->getById($id);
    }

    public function delete(int $id): void {
        $this->getById($id);
        $this->dao->delete($id);
    }
}
