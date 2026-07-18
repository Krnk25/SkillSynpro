<?php
session_start();
require_once "../db.php";

/* LOGIN CHECK */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['job_id']) || empty($_FILES['resume']['name'])) {
        die("Invalid request");
    }

    $job_id = intval($_POST['job_id']);

    // Check if already applied
    $check = $conn->prepare("SELECT * FROM job_applications WHERE student_id=? AND job_id=?");
    $check->bind_param("ii", $student_id, $job_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        die("You have already applied for this job");
    }

    // Handle resume upload
    $resume = $_FILES['resume'];
    $allowed_ext = ['pdf'];
    $ext = strtolower(pathinfo($resume['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        die("Only PDF resumes allowed");
    }

    $new_file = time() . "_" . basename($resume['name']);
    $upload_dir = "../uploads/resumes/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (move_uploaded_file($resume['tmp_name'], $upload_dir . $new_file)) {
        // Insert application
        $stmt = $conn->prepare("INSERT INTO job_applications (student_id, job_id, resume_file, applied_at) VALUES (?,?,?,NOW())");
        $stmt->bind_param("iis", $student_id, $job_id, $new_file);
        if ($stmt->execute()) {
            $success = "Application submitted successfully!";
        } else {
            $error = "Failed to submit application.";
        }
    } else {
        $error = "Failed to upload resume.";
    }
} else {
    die("Invalid request");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Application Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: #121212;
        color: #fff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
        margin-top: 50px;
        text-align: center;
    }

    .alert-success {
        background-color: #28a745;
        color: #fff;
        padding: 20px;
        border-radius: 10px;
        font-weight: bold;
    }

    .alert-danger {
        background-color: #dc3545;
        color: #fff;
        padding: 20px;
        border-radius: 10px;
        font-weight: bold;
    }

    a.btn-back {
        display: inline-block;
        margin-top: 20px;
        background: #00f7ff;
        color: #000;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
    }

    a.btn-back:hover {
        background: #00c2cc;
        color: #fff;
    }
    </style>
</head>

<body>
    <div class="container">
        <?php if(isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php else: ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <a href="student-jobs.php" class="btn-back">Back to Jobs</a>
    </div>
</body>

</html>