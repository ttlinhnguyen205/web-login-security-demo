<?php
session_start();

require_once 'config.php';
require_once 'auth.php';

require_login();
require_admin();

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$totalSuccess = $pdo->query("SELECT COUNT(*) FROM security_logs WHERE event_type = 'LOGIN_SUCCESS'")->fetchColumn();

$totalFailed = $pdo->query("SELECT COUNT(*) FROM security_logs WHERE event_type = 'LOGIN_FAILED'")->fetchColumn();

$totalLocked = $pdo->query("SELECT COUNT(*) FROM security_logs WHERE event_type = 'ACCOUNT_LOCKED'")->fetchColumn();

$totalBlocked = $pdo->query("SELECT COUNT(*) FROM security_logs WHERE event_type = 'LOGIN_BLOCKED'")->fetchColumn();

$totalRateLimit = $pdo->query("SELECT COUNT(*) FROM security_logs WHERE event_type = 'RATE_LIMIT_EXCEEDED'")->fetchColumn();

$totalBlockedIps = $pdo->query("SELECT COUNT(*) FROM blocked_ips WHERE blocked_until > datetime('now')")->fetchColumn();

$failedLast10Min = $pdo->query(
    "SELECT COUNT(*) 
     FROM security_logs 
     WHERE event_type = 'LOGIN_FAILED'
     AND event_time >= datetime('now', '-10 minutes')"
)->fetchColumn();

if ($failedLast10Min >= 20 || $totalBlockedIps > 0) {
    $riskLevel = "HIGH";
    $riskClass = "risk-high";
} elseif ($failedLast10Min >= 5) {
    $riskLevel = "MEDIUM";
    $riskClass = "risk-medium";
} else {
    $riskLevel = "LOW";
    $riskClass = "risk-low";
}

$recentAlerts = $pdo->query(
    "SELECT * FROM security_logs
     WHERE severity IN ('ALERT', 'WARNING')
     ORDER BY event_time DESC
     LIMIT 10"
)->fetchAll(PDO::FETCH_ASSOC);

$logs = $pdo->query(
    "SELECT * FROM security_logs 
     ORDER BY event_time DESC 
     LIMIT 30"
)->fetchAll(PDO::FETCH_ASSOC);

$blockedIps = $pdo->query(
    "SELECT * FROM blocked_ips 
     ORDER BY created_at DESC 
     LIMIT 10"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Security Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="dashboard-wrapper">

    <div class="dashboard-header">
        <h1>Security Dashboard</h1>
        <p>Giám sát đăng nhập bất thường và cảnh báo an ninh</p>
    </div>

    <div class="risk-box <?php echo $riskClass; ?>">
        <h2>Current Risk Level: <?php echo $riskLevel; ?></h2>
        <p>Số lần đăng nhập thất bại trong 10 phút gần nhất: <?php echo $failedLast10Min; ?></p>
    </div>

    <div class="card-grid">

        <div class="card">
            <h3>Tổng người dùng</h3>
            <h1><?php echo $totalUsers; ?></h1>
        </div>

        <div class="card">
            <h3>Login thành công</h3>
            <h1><?php echo $totalSuccess; ?></h1>
        </div>

        <div class="card">
            <h3>Login thất bại</h3>
            <h1><?php echo $totalFailed; ?></h1>
        </div>

        <div class="card">
            <h3>Tài khoản bị khóa</h3>
            <h1><?php echo $totalLocked; ?></h1>
        </div>

        <div class="card">
            <h3>Login bị chặn</h3>
            <h1><?php echo $totalBlocked; ?></h1>
        </div>

        <div class="card">
            <h3>IP bị chặn</h3>
            <h1><?php echo $totalBlockedIps; ?></h1>
        </div>

    </div>

    <div class="security-info">
        <h3>Recent Alerts</h3>

        <table>
            <tr>
                <th>Time</th>
                <th>User</th>
                <th>IP</th>
                <th>Event</th>
                <th>Severity</th>
                <th>Description</th>
            </tr>

            <?php foreach ($recentAlerts as $alert): ?>
            <tr>
                <td><?php echo htmlspecialchars($alert["event_time"]); ?></td>
                <td><?php echo htmlspecialchars($alert["username"]); ?></td>
                <td><?php echo htmlspecialchars($alert["ip_address"]); ?></td>
                <td><?php echo htmlspecialchars($alert["event_type"]); ?></td>
                <td><?php echo htmlspecialchars($alert["severity"]); ?></td>
                <td><?php echo htmlspecialchars($alert["description"]); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="security-info">
        <h3>Danh sách IP bị chặn gần đây</h3>

        <table>
            <tr>
                <th>ID</th>
                <th>IP Address</th>
                <th>Lý do</th>
                <th>Bị chặn đến</th>
                <th>Thời gian tạo</th>
            </tr>

            <?php foreach ($blockedIps as $ip): ?>
            <tr>
                <td><?php echo htmlspecialchars($ip["id"]); ?></td>
                <td><?php echo htmlspecialchars($ip["ip_address"]); ?></td>
                <td><?php echo htmlspecialchars($ip["reason"]); ?></td>
                <td><?php echo htmlspecialchars($ip["blocked_until"]); ?></td>
                <td><?php echo htmlspecialchars($ip["created_at"]); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="security-info">
        <h3>Nhật ký bảo mật gần đây</h3>

        <table>
            <tr>
                <th>ID</th>
                <th>Thời gian</th>
                <th>User</th>
                <th>IP</th>
                <th>Sự kiện</th>
                <th>Mức độ</th>
                <th>Mô tả</th>
            </tr>

            <?php foreach ($logs as $log): ?>
            <tr>
                <td><?php echo htmlspecialchars($log["id"]); ?></td>
                <td><?php echo htmlspecialchars($log["event_time"]); ?></td>
                <td><?php echo htmlspecialchars($log["username"]); ?></td>
                <td><?php echo htmlspecialchars($log["ip_address"]); ?></td>
                <td><?php echo htmlspecialchars($log["event_type"]); ?></td>
                <td><?php echo htmlspecialchars($log["severity"]); ?></td>
                <td><?php echo htmlspecialchars($log["description"]); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <a class="logout-btn" href="dashboard.php">Quay lại Dashboard</a>

</div>

</body>
</html>