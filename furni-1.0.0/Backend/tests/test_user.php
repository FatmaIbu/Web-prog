
<?php

require_once __DIR__ . '/../dao/UserDAO.php';

try {
    $dao = new UserDAO();

    $users = $dao->getAll();

    echo "Users found: " . count($users) . PHP_EOL;

    foreach ($users as $user) {
        echo $user['id'] . " - "
           . $user['full_name'] . " - "
           . $user['email'] . PHP_EOL;
    }

} catch (PDOException $e) {
    echo "Database error. Check your connection.";
    echo $e->getMessage();
}
