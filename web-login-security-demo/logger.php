<?php

function get_client_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }

    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

function write_log(
    $pdo,
    $username,
    $event_type,
    $severity,
    $description
) {
    $ip_address = get_client_ip();

    $stmt = $pdo->prepare(
        "INSERT INTO security_logs
        (username, event_type, severity, description, ip_address)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $username,
        $event_type,
        $severity,
        $description,
        $ip_address
    ]);
}

?>