<?php
session_start();

require_once 'config.php';
require_once 'auth.php';
require_once 'logger.php';

require_login();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = trim($_POST["current_password"]);
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if ($current_password == "" || $new_password == "" || $confirm_password == "") {
        $message = "Vui lòng nhập đầy đủ thông tin.";
    } elseif ($new_password != $confirm_password) {
        $message = "Mật khẩu mới không khớp.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION["user_id"]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($current_password, $user["password"])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$new_hash, $_SESSION["user_id"]]);

            write_log(
                $pdo,
                $_SESSION["username"],
                "PASSWORD_CHANGED",
                "INFO",
                "User changed password successfully"
            );

            $message = "Đổi mật khẩu thành công.";
        } else {
            write_log(
                $pdo,
                $_SESSION["username"],
                "PASSWORD_CHANGE_FAILED",
                "WARNING",
                "Wrong current password when changing password"
            );

            $message = "Mật khẩu hiện tại không đúng.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">

    <h2>Đổi mật khẩu</h2>

    <form method="POST">

        <label>Mật khẩu hiện tại</label>
        <input type="password" name="current_password" required>

        <label>Mật khẩu mới</label>
        <input type="password" name="new_password" required>

        <label>Nhập lại mật khẩu mới</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Đổi mật khẩu</button>

    </form>

    <p class="message">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <a href="dashboard.php">Quay lại Dashboard</a>

</div>

</body>
</html>