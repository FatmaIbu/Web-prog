
<?php

require_once __DIR__ . '/../dao/CategoryDAO.php';

try {
    $dao = new CategoryDAO();

    $categories = $dao->getAll();

    echo "Categories found: " . count($categories) . PHP_EOL;

    foreach ($categories as $category) {
        echo $category['id'] . " - "
           . $category['name'] . PHP_EOL;
    }

} catch (PDOException $e) {
    echo "Database error. Check your connection.";
    echo $e->getMessage();
}
