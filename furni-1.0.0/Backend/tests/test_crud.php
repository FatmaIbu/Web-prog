
<?php

require_once __DIR__ . '/../dao/ProductDAO.php';

try {
    $dao = new ProductDAO();

    echo "=== PRODUCT CRUD TEST ===\n";

    // 1. CREATE
    $id = $dao->create([
        'category_id' => 1,
        'name' => 'CRUD Test Chair',
        'description' => 'Temporary product for testing',
        'price' => 25.00,
        'stock' => 10,
        'image_url' => null
    ]);

    echo "CREATE: Product ID $id created\n";

    // 2. READ
    $product = $dao->getById($id);

    if (!$product || $product['name'] !== 'CRUD Test Chair') {
        throw new Exception("READ test failed");
    }

    echo "READ: Product found successfully\n";

    // 3. UPDATE
    $dao->update($id, [
        'category_id' => 1,
        'name' => 'Updated CRUD Chair',
        'description' => 'Updated product for testing',
        'price' => 30.00,
        'stock' => 5,
        'image_url' => null
    ]);

    $updated = $dao->getById($id);

    if (!$updated || $updated['name'] !== 'Updated CRUD Chair') {
        throw new Exception("UPDATE test failed");
    }

    echo "UPDATE: Product updated successfully\n";

    // 4. DELETE
    $dao->delete($id);

    $deleted = $dao->getById($id);

    if ($deleted !== false && $deleted !== null) {
        throw new Exception("DELETE test failed");
    }

    echo "DELETE: Product deleted successfully\n";

    echo "\nALL PRODUCT CRUD TESTS PASSED!\n";

} catch (Throwable $e) {
    echo "TEST FAILED: " . $e->getMessage() . "\n";
    exit(1);
}
