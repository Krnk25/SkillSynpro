<?php
session_start();
require 'db.php';

// अगर form submit हुआ है तो update करो
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE jobs 
        SET title=?, company=?, location=?, salary=?, required_skills=?, job_type=?, status=?, description=?, updated_at=NOW() 
        WHERE id=?");
    $stmt->bind_param(
        "ssssssssi",
        $_POST['title'],
        $_POST['company'],
        $_POST['location'],
        $_POST['salary'],
        $_POST['required_skills'],
        $_POST['job_type'],
        $_POST['status'],
        $_POST['description'],
        $_POST['id']
    );
    $stmt->execute();
    $stmt->close();
    header("Location: manage-job.php");
    exit;
}




?>