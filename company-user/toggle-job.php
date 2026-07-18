<?php
session_start();
require "../db.php";

if (!isset($_SESSION['company_user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['company_user_id']);


$company_id = $_SESSION['user_id'];
$job_id = intval($_GET['id']);

$stmt = $conn->prepare("
UPDATE jobs 
SET status = IF(status='active','inactive','active') 
WHERE id=? AND company_user_id=?
");
$stmt->bind_param("ii",$job_id,$company_id);
$stmt->execute();

header("Location: manage-jobs.php");
exit;