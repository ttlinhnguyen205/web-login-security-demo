<?php
session_start();

require_once 'config.php';
require_once 'logger.php';

$message = "";
$ip_address = get_client_ip();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Kiểm tra IP có đang bị chặn không
    $checkIp = $pdo->prepare(
        "SELECT * FROM blocked_ips 
         WHERE ip_address = ? 
         AND blocked_until > datetime('now')"
    );
    $checkIp->execute([$ip_address]);
    $blockedIp = $checkIp->fetch(PDO::FETCH_ASSOC);

    if ($blockedIp) {
        $message = "IP của bạn đang bị chặn tạm thời do đăng nhập bất thường.";

        write_log(
            $pdo,
            $username,
            "IP_BLOCKED_ATTEMPT",
            "ALERT",
            "Blocked login attempt from blocked IP"
        );
    } else {

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            if (
                $user["locked_until"] != null &&
                strtotime($user["locked_until"]) > time()
            ) {

                write_log(
                    $pdo,
                    $username,
                    "LOGIN_BLOCKED",
                    "ALERT",
                    "Blocked login attempt because account is locked"
                );

                $countIpFail = $pdo->prepare(
                    "SELECT COUNT(*)
         FROM security_logs
         WHERE ip_address = ?
         AND event_type IN ('LOGIN_FAILED','LOGIN_BLOCKED')
         AND event_time >= datetime('now', '-10 minute')"
                );

                $countIpFail->execute([$ip_address]);
                $ipFailCount = $countIpFail->fetchColumn();

                if ($ipFailCount >= 10) {

                    $blockedUntil = date("Y-m-d H:i:s", time() + 300);

                    $blockIp = $pdo->prepare(
                        "INSERT INTO blocked_ips
            (ip_address, reason, blocked_until)
            VALUES (?, ?, ?)"
                    );

                    $blockIp->execute([
                        $ip_address,
                        "Too many failed login attempts from same IP",
                        $blockedUntil
                    ]);

                    write_log(
                        $pdo,
                        $username,
                        "RATE_LIMIT_EXCEEDED",
                        "ALERT",
                        "IP blocked because of too many failed login attempts"
                    );

                    $message = "IP đã bị chặn trong 5 phút.";
                } else {
                    $message = "Tài khoản đang bị khóa trong 5 phút.";
                }
            } else {
                if (
                    $user["locked_until"] != null &&
                    strtotime($user["locked_until"]) <= time()
                ) {

                    $resetLock = $pdo->prepare(
                        "UPDATE users
             SET failed_attempts = 0,
                 locked_until = NULL
             WHERE id = ?"
                    );

                    $resetLock->execute([$user["id"]]);

                    $user["failed_attempts"] = 0;
                    $user["locked_until"] = null;
                }

                if (password_verify($password, $user["password"])) {

                    $reset = $pdo->prepare(
                        "UPDATE users
                         SET failed_attempts = 0,
                             locked_until = NULL
                         WHERE id = ?"
                    );
                    $reset->execute([$user["id"]]);

                    write_log(
                        $pdo,
                        $user["username"],
                        "LOGIN_SUCCESS",
                        "INFO",
                        "User logged in successfully"
                    );

                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["username"] = $user["username"];
                    $_SESSION["role"] = $user["role"];

                    header("Location: dashboard.php");
                    exit;
                } else {

                    $failed = $user["failed_attempts"] + 1;

                    write_log(
                        $pdo,
                        $username,
                        "LOGIN_FAILED",
                        "WARNING",
                        "Wrong password attempt. Failed count: " . $failed
                    );

                    // Đếm số lần login fail từ IP này trong 1 phút gần nhất
                    $countIpFail = $pdo->prepare(
                        "SELECT COUNT(*) 
                         FROM security_logs
                         WHERE ip_address = ?
                         AND event_type = 'LOGIN_FAILED'
                         AND event_time >= datetime('now', '-10 minute')"
                    );
                    $countIpFail->execute([$ip_address]);
                    $ipFailCount = $countIpFail->fetchColumn();

                    if ($ipFailCount >= 10) {
                        $blockedUntil = date("Y-m-d H:i:s", time() + 300);

                        $blockIp = $pdo->prepare(
                            "INSERT INTO blocked_ips
                             (ip_address, reason, blocked_until)
                             VALUES (?, ?, ?)"
                        );
                        $blockIp->execute([
                            $ip_address,
                            "Too many failed login attempts from same IP",
                            $blockedUntil
                        ]);

                        write_log(
                            $pdo,
                            $username,
                            "RATE_LIMIT_EXCEEDED",
                            "ALERT",
                            "IP blocked because of too many failed login attempts"
                        );

                        $message = "Phát hiện đăng nhập bất thường. IP bị chặn trong 5 phút.";
                    }

                    if ($failed >= 5) {

                        $locked_until = date("Y-m-d H:i:s", time() + 300);

                        $update = $pdo->prepare(
                            "UPDATE users
                             SET failed_attempts = ?,
                                 locked_until = ?
                             WHERE id = ?"
                        );
                        $update->execute([
                            $failed,
                            $locked_until,
                            $user["id"]
                        ]);

                        write_log(
                            $pdo,
                            $username,
                            "ACCOUNT_LOCKED",
                            "ALERT",
                            "Account locked for 5 minutes because of multiple failed login attempts"
                        );

                        $message = "Sai 5 lần. Tài khoản bị khóa trong 5 phút.";
                    } else {

                        $update = $pdo->prepare(
                            "UPDATE users
                             SET failed_attempts = ?
                             WHERE id = ?"
                        );
                        $update->execute([
                            $failed,
                            $user["id"]
                        ]);

                        if ($message == "") {
                            $message = "Sai mật khẩu. Số lần sai: $failed/5";
                        }
                    }
                }
            }
        } else {

            $message = "Tài khoản không tồn tại.";

            write_log(
                $pdo,
                $username,
                "LOGIN_UNKNOWN_USER",
                "WARNING",
                "Login attempt with non-existing username"
            );
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập hệ thống</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="container">

        <h2>Đăng nhập hệ thống</h2>

        <form method="POST">

            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Đăng nhập</button>

        </form>

        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p>

        <a href="register.php">Đăng ký tài khoản</a>

    </div>

</body>

</html>