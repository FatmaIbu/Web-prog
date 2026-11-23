
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
        $allowed = ['status'];
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

        $sql = "UPDATE orders SET " .
            implode(', ', $fields) .
            " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($params);
    }

    // DELETE - Inherited from BaseDAO
    
/**
 * Create an order and its items in one transaction.
 *
 * Product prices are retrieved from the database.
 */
public function createWithItems(int $userId, array $items): int
{
    if ($userId <= 0 || empty($items)) {
        throw new InvalidArgumentException(
            "Valid user ID and order items are required"
        );
    }

    $this->conn->beginTransaction();

    try {
        $totalCents = 0;
        $orderItems = [];

        $productStmt = $this->conn->prepare(
            "SELECT id, price, stock
             FROM products
             WHERE id = :id
             FOR UPDATE"
        );

        foreach ($items as $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException("Invalid order item");
            }

            $productId = filter_var(
                $item['product_id'] ?? null,
                FILTER_VALIDATE_INT
            );

            $quantity = filter_var(
                $item['quantity'] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$productId || !$quantity ||
                $productId <= 0 || $quantity <= 0) {
                throw new InvalidArgumentException(
                    "Invalid product ID or quantity"
                );
            }

            $productStmt->execute(['id' => $productId]);
            $product = $productStmt->fetch();
            $productStmt->closeCursor();

            if (!$product) {
                throw new InvalidArgumentException(
                    "Product not found: $productId"
                );
            }

            $orderItems[] = [
                'product_id' => $productId,
                'quantity' => $quantity
            ];

            // Prices are stored as decimal strings, e.g. 50.00.
            $price = (string)$product['price'];
            if (!preg_match('/^\d+\.\d{2}$/', $price)) {
                throw new RuntimeException("Invalid stored product price");
            }

            [$whole, $fraction] = explode('.', $price);
            $unitCents = ((int)$whole * 100) + (int)$fraction;

            $totalCents += $unitCents * $quantity;
        }

        // Reject duplicate products rather than overselling stock.
        $quantities = [];
        foreach ($orderItems as $item) {
            $id = $item['product_id'];
            if (isset($quantities[$id])) {
                throw new InvalidArgumentException(
                    "Duplicate product IDs are not allowed"
                );
            }
            $quantities[$id] = $item['quantity'];
        }

        // Lock and check all products in a consistent order.
        ksort($quantities);

        $totalCents = 0;
        $validatedItems = [];

        foreach ($quantities as $productId => $quantity) {
            $productStmt->execute(['id' => $productId]);
            $product = $productStmt->fetch();
            $productStmt->closeCursor();

            if (!$product || (int)$product['stock'] < $quantity) {
                throw new InvalidArgumentException(
                    "Product unavailable or insufficient stock: $productId"
                );
            }

            $price = (string)$product['price'];
            if (!preg_match('/^\d+\.\d{2}$/', $price)) {
                throw new RuntimeException("Invalid stored product price");
            }

            [$whole, $fraction] = explode('.', $price);
            $unitCents = ((int)$whole * 100) + (int)$fraction;
            $totalCents += $unitCents * $quantity;

            if ($totalCents > 9999999999) {
                throw new InvalidArgumentException("Order total is too large");
            }

            $validatedItems[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $price
            ];
        }

        $total = sprintf(
            '%d.%02d',
            intdiv($totalCents, 100),
            $totalCents % 100
        );

        $orderStmt = $this->conn->prepare(
            "INSERT INTO orders (user_id, total_amount, status)
             VALUES (:user_id, :total_amount, 'pending')"
        );

        $orderStmt->execute([
            'user_id' => $userId,
            'total_amount' => $total
        ]);

        $orderId = (int)$this->conn->lastInsertId();

        $itemStmt = $this->conn->prepare(
            "INSERT INTO order_items
             (order_id, product_id, quantity, unit_price)
             VALUES (:order_id, :product_id, :quantity, :unit_price)"
        );

        
        $stockStmt = $this->conn->prepare(
            "UPDATE products
            SET stock = stock - :subtract_quantity
            WHERE id = :product_id
            AND stock >= :check_quantity"
        );


        foreach ($validatedItems as $item) {
            $itemStmt->execute([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price']
            ]);

            
            $stockStmt->execute([
                'subtract_quantity' => $item['quantity'],
                'product_id' => $item['product_id'],
                'check_quantity' => $item['quantity']
            ]);


            if ($stockStmt->rowCount() !== 1) {
                throw new RuntimeException("Unable to reserve product stock");
            }
        }

        $this->conn->commit();

        return $orderId;

    } catch (Throwable $e) {
        if ($this->conn->inTransaction()) {
            $this->conn->rollBack();
        }

        throw $e;
    }
}

}
