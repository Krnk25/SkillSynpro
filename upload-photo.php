<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['photo']['tmp_name'];
    $file_name = time() . '_' . basename($_FILES['photo']['name']);
    $target_dir = 'uploads/';
    $target = $target_dir . $file_name;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($file_tmp, $target)) {
        $sql = "UPDATE users SET photo=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $file_name, $user_id);
        $stmt->execute();
    }
}

header("Location: profile.php");
exit;