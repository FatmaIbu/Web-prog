
<?php

require_once __DIR__ . '/../dao/ProductDAO.php';

$dao = new ProductDAO();

// CREATE a temporary product
$id = $dao->create([
    'category_id' => 1,
    'name' => 'Temporary Test Chair',
    'description' => 'Product for testing DELETE',
    'price' => 10.00,
    'stock' => 1
]);

echo "Created product ID: $id\n";

// DELETE the temporary product
$dao->delete($id);

echo "Deleted product ID: $id\n";

// VERIFY deletion
$product = $dao->getById($id);

if ($product === false) {
    echo "DELETE operation successful!\n";
}
