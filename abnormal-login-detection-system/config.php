<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');

$db_path = __DIR__ . '/database/users.db';

try {
    $pdo = new PDO("sqlite:" . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die($e->getMessage());
}
