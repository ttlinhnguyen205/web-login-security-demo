<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login_secure.php");
    exit;
}

require_once 'config.php';

$role = $_SESSION["role"] ?? "user";

$stmt = $pdo->prepare(
    "SELECT *
     FROM security_logs
     WHERE username = ?
     AND severity IN ('WARNING','ALERT')
     ORDER BY event_time DESC
     LIMIT 5"
);

$stmt->execute([$_SESSION["username"]]);

$alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Security Management Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="dashboard-wrapper">

        <div class="dashboard-header">
            <h1>Security Management Dashboard</h1>

            <p>
                Xin chào,
                <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>
            </p>

            <p>
                Quyền hiện tại:
                <b><?php echo htmlspecialchars($role); ?></b>
            </p>
        </div>

        <?php if (!empty($alerts)): ?>

            <div class="security-info">
                <h3>⚠ Cảnh báo bảo mật gần đây</h3>

                <ul>
                    <?php foreach ($alerts as $alert): ?>
                        <li>
                            <strong>
                                <?php echo htmlspecialchars($alert["event_time"]); ?>
                            </strong>
                            -
                            <?php echo htmlspecialchars($alert["description"]); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        <?php endif; ?>

        <div class="card-grid">

            <?php if ($role === "admin"): ?>

                <div class="card">
                    <h3>Quản lý người dùng</h3>
                    <p>
                        Xem danh sách người dùng và quản lý thông tin tài khoản.
                    </p>

                    <a href="users.php">Danh sách người dùng</a>

                    <a href="register.php">Tạo tài khoản mới</a>
                </div>

                <div class="card">
                    <h3>Giám sát an ninh</h3>

                    <p>
                        Theo dõi đăng nhập, phát hiện Brute Force,
                        chặn IP và xem log.
                    </p>

                    <a href="security_dashboard.php">
                        Security Dashboard
                    </a>
                </div>

            <?php endif; ?>

            <div class="card">
                <h3>Bảo mật tài khoản</h3>

                <p>
                    Đổi mật khẩu và bảo vệ tài khoản bằng cơ chế
                    mã hóa mật khẩu.
                </p>

                <a href="change_password.php">
                    Đổi mật khẩu
                </a>
            </div>

        </div>

        <div class="security-info">

            <h3>Chức năng hệ thống</h3>

            <?php if ($role === "admin"): ?>

                <ul>
                    <li>Theo dõi đăng nhập thành công và thất bại</li>
                    <li>Phát hiện hành vi đăng nhập bất thường</li>
                    <li>Khóa tài khoản tự động khi nghi ngờ Brute Force</li>
                    <li>Chặn IP khi phát hiện tần suất đăng nhập bất thường</li>
                    <li>Quản lý nhật ký bảo mật</li>
                    <li>Phân quyền truy cập Admin/User</li>
                </ul>

            <?php else: ?>

                <ul>
                    <li>Đăng nhập hệ thống</li>
                    <li>Đổi mật khẩu cá nhân</li>
                    <li>Đăng xuất an toàn</li>
                </ul>

            <?php endif; ?>

        </div>

        <a class="logout-btn" href="logout.php">
            Đăng xuất
        </a>

    </div>

</body>

</html>