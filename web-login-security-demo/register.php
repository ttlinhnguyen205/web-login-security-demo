<?php
require_once 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role = "user";

    if ($username == "" || $password == "") {
        $message = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users 
                (username, password, role) 
                VALUES (?, ?, ?)"
            );

            $stmt->execute([
                $username,
                $hashed_password,
                $role
            ]);

            $message = "Đăng ký thành công. Bạn có thể đăng nhập.";
        } catch (PDOException $e) {
            $message = "Tên đăng nhập đã tồn tại.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">

    <h2>Đăng ký tài khoản</h2>

    <form method="POST">

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Đăng ký</button>

    </form>

    <p class="message">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <a href="login_secure.php">Quay lại đăng nhập</a>

</div>

</body>
</html>