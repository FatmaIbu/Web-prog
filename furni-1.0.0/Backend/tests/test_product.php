
<?php

require_once __DIR__ . '/../dao/ProductDAO.php';

try {
    $dao = new ProductDAO();

    $products = $dao->getAll();

    echo "Products found: " . count($products) . PHP_EOL;

    foreach ($products as $product) {
        echo $product['id'] . " - "
           . $product['name'] . " - $"
           . $product['price'] . PHP_EOL;
    }

} catch (PDOException $e) {
    echo "Database error. Check your connection.";
    echo $e->getMessage();
}
