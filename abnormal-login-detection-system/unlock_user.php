<?php
session_start();

require_once 'config.php';
require_once 'auth.php';
require_once 'logger.php';

require_login();
require_admin();

$id = $_GET["id"] ?? 0;

$stmt = $pdo->prepare(
    "UPDATE users
     SET failed_attempts = 0,
         locked_until = NULL
     WHERE id = ?"
);

$stmt->execute([$id]);

write_log(
    $pdo,
    $_SESSION["username"],
    "ADMIN_UNLOCK_USER",
    "INFO",
    "Admin manually unlocked account"
);

header("Location: users.php");
exit;
