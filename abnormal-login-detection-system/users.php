<?php
session_start();

require_once 'config.php';
require_once 'auth.php';

require_login();
require_admin();

$stmt = $pdo->query("SELECT id, username, role, failed_attempts, locked_until, created_at FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="container">
        <h2>Quản lý người dùng</h2>

        <table border="1" cellpadding="8" cellspacing="0" width="100%">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Sai mật khẩu</th>
                <th>Khóa đến</th>
                <th>Thao tác</th>
            </tr>

            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo $u["id"]; ?></td>
                    <td><?php echo htmlspecialchars($u["username"]); ?></td>
                    <td><?php echo htmlspecialchars($u["role"]); ?></td>
                    <td><?php echo $u["failed_attempts"]; ?></td>
                    <td><?php echo $u["locked_until"] ?: "Không"; ?></td>
                    <td>
                        <?php if ($u["locked_until"]): ?>
                            <a href="unlock_user.php?id=<?php echo $u["id"]; ?>">
                                Mở khóa
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <br>
        <a href="dashboard.php">Quay lại Dashboard</a>
    </div>

</body>

</html>