
<?php

require_once __DIR__ . '/../dao/CategoryDAO.php';

$dao = new CategoryDAO();
$id = null;

try {
    echo "=== CATEGORY CRUD TEST ===\n";

    // 1. CREATE
    $id = $dao->create([
        'name' => 'CRUD Test Category',
        'description' => 'Temporary category for testing'
    ]);

    echo "CREATE: Category ID $id created\n";

    // 2. READ
    $category = $dao->getById($id);

    if (!$category || $category['name'] !== 'CRUD Test Category') {
        throw new Exception("READ test failed");
    }

    echo "READ: Category found successfully\n";

    // 3. UPDATE
    $dao->update($id, [
        'name' => 'Updated CRUD Category',
        'description' => 'Updated temporary category'
    ]);

    $updated = $dao->getById($id);

    if (!$updated || $updated['name'] !== 'Updated CRUD Category') {
        throw new Exception("UPDATE test failed");
    }

    echo "UPDATE: Category updated successfully\n";

    // 4. DELETE
    $dao->delete($id);
    $deleted = $dao->getById($id);

    if ($deleted !== false && $deleted !== null) {
        throw new Exception("DELETE test failed");
    }

    $id = null;

    echo "DELETE: Category deleted successfully\n";
    echo "\nALL CATEGORY CRUD TESTS PASSED!\n";

} catch (Throwable $e) {
    echo "TEST FAILED: " . $e->getMessage() . "\n";

    // Try to clean up the temporary category.
    if ($id !== null) {
        try {
            $dao->delete($id);
        } catch (Throwable $cleanupError) {
            echo "Cleanup failed: "
                . $cleanupError->getMessage() . "\n";
        }
    }

    exit(1);
}
