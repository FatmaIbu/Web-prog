
<?php

require_once __DIR__ . '/../dao/BaseDAO.php';

try {
    $dao = new BaseDAO('products');
    $products = $dao->getAll();

    echo "Database connected successfully!\n";
    echo "Products found: " . count($products);

} catch (PDOException $e) {
    echo "Database connection failed. Check your configuration.";
    echo $e->getMessage();
}
