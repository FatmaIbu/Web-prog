
<?php

require_once __DIR__ . '/../dao/OrderDAO.php';

try {
    $dao = new OrderDAO();

    $orders = $dao->getAll();

    echo "Orders found: " . count($orders) . PHP_EOL;

    foreach ($orders as $order) {
        echo "Order #" . $order['id']
           . " | User: " . $order['user_id']
           . " | Total: $" . $order['total_amount']
           . " | Status: " . $order['status']
           . PHP_EOL;
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
