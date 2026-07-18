<?php
require "../db.php";

if (!isset($_POST['token'], $_POST['password'])) {
    die("Invalid request");
}

$token = $_POST['token'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

$stmt = $conn->prepare(
    "SELECT id FROM users WHERE reset_token=? AND reset_expire > NOW()"
);
$stmt->bind_param("s", $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("❌ Token expired");
}

$up = $conn->prepare(
    "UPDATE users SET password=?, reset_token=NULL, reset_expire=NULL WHERE id=?"
);
$up->bind_param("si", $password, $user['id']);
$up->execute();

echo "✅ Password reset successful. <a href='login.php'>Login</a>";