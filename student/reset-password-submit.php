<?php
session_start();
require_once "../db.php";

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($password !== $confirm) {
    $_SESSION['msg'] = "Passwords do not match.";
    header("Location: reset-password.php?token=$token");
    exit;
}

/* Validate token */
$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token=? AND reset_expire > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) die("Token invalid or expired.");

/* Update password & clear token */
$hash = password_hash($password, PASSWORD_DEFAULT);
$upd = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expire=NULL WHERE id=?");
$upd->bind_param("si", $hash, $user['id']);
$upd->execute();

$_SESSION['msg'] = "Password updated successfully!";
header("Location: login.php");
exit;