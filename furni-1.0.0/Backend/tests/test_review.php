
<?php

require_once __DIR__ . '/../dao/reviewDao.php';

try {
    $dao = new ReviewDAO();

    $reviews = $dao->getAll();

    echo "Reviews found: " . count($reviews) . PHP_EOL;

    foreach ($reviews as $review) {
        echo "Review #" . $review['id']
           . " | Product: " . $review['product_id']
           . " | Rating: " . $review['rating']
           . " | Comment: " . $review['comment']
           . PHP_EOL;
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
