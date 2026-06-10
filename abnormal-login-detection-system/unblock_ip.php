<?php
session_start();

require_once 'config.php';
require_once 'auth.php';
require_once 'logger.php';

require_login();
require_admin();

$id = $_GET["id"] ?? 0;

$stmt = $pdo->prepare(
    "DELETE FROM blocked_ips
     WHERE id = ?"
);

$stmt->execute([$id]);

write_log(
    $pdo,
    $_SESSION["username"],
    "ADMIN_UNBLOCK_IP",
    "INFO",
    "Admin manually removed blocked IP"
);

header("Location: security_dashboard.php");
exit;