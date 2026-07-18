<?php
session_start();
require_once "../db.php";

/* ================= LOGIN CHECK ================= */
if (!isset($_SESSION['company_user_id'])) {
    header("Location: login.php");
    exit;
}

/* ================= POST CHECK ================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profile.php");
    exit;
}

$user_id = intval($_POST['user_id']);

/* ================= FETCH OLD DATA ================= */
$stmt = $conn->prepare("SELECT * FROM company_users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$old = $stmt->get_result()->fetch_assoc();

if (!$old) {
    die("User not found");
}

/* ================= SANITIZE INPUT ================= */
function clean($data) {
    return trim(htmlspecialchars($data));
}

/* ================= DATA PREP ================= */
$fields = [];
$params = [];
$types  = "";

/* ===== HR DETAILS ===== */
if (!empty($_POST['hr_name'])) {
    $fields[] = "hr_name=?";
    $params[] = clean($_POST['hr_name']);
    $types .= "s";
}

if (!empty($_POST['username'])) {
    $fields[] = "username=?";
    $params[] = clean($_POST['username']);
    $types .= "s";
}

if (!empty($_POST['email'])) {
    $fields[] = "email=?";
    $params[] = clean($_POST['email']);
    $types .= "s";
}

/* ===== COMPANY DETAILS ===== */
if (isset($_POST['company_name'])) {
    $fields[] = "company_name=?";
    $params[] = clean($_POST['company_name']);
    $types .= "s";
}

if (isset($_POST['industry'])) {
    $fields[] = "industry=?";
    $params[] = clean($_POST['industry']);
    $types .= "s";
}

if (isset($_POST['company_size'])) {
    $fields[] = "company_size=?";
    $params[] = clean($_POST['company_size']);
    $types .= "s";
}

if (isset($_POST['website'])) {
    $fields[] = "website=?";
    $params[] = clean($_POST['website']);
    $types .= "s";
}

if (isset($_POST['city'])) {
    $fields[] = "city=?";
    $params[] = clean($_POST['city']);
    $types .= "s";
}

if (isset($_POST['description'])) {
    $fields[] = "description=?";
    $params[] = clean($_POST['description']);
    $types .= "s";
}

/* ================= UPDATE ================= */
if (count($fields) > 0) {
    $types .= "i";
    $params[] = $user_id;

    $sql = "UPDATE company_users SET " . implode(", ", $fields) . " WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
}

/* ================= SUCCESS ================= */
$_SESSION['success_msg'] = "Profile updated successfully!";
header("Location: profile.php");
exit;