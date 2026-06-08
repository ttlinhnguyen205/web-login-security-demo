<?php

function require_login()
{
    if (!isset($_SESSION["username"])) {
        header("Location: login_secure.php");
        exit;
    }
}

function require_admin()
{
    if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
        die("Access Denied: Bạn không có quyền truy cập chức năng này.");
    }
}

?>