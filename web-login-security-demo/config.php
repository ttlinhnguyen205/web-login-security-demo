<?php

$db_path = __DIR__ . '/database/users.db';

try {
    $pdo = new PDO("sqlite:" . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

?>