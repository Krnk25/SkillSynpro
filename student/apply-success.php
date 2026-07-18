<?php
session_start();
require_once "../db.php";

/* Login check */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$job_id  = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

/* ===============================
   1️⃣ Job + Company + Required Skills + HR
   =============================== */
$stmt = $conn->prepare("
    SELECT 
        j.title,
        j.company,
        j.location,
        j.salary,
        j.required_skills,
        cu.hr_name  AS hr_name,
        cu.email AS hr_email
    FROM jobs j
    LEFT JOIN company_users cu ON j.company_user_id = cu.id
    WHERE j.id = ?
");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();

/* ===============================
   2️⃣ Student Latest Resume
   =============================== */
$res = $conn->prepare("
    SELECT filename 
    FROM resumes 
    WHERE user_id = ?
    ORDER BY uploaded_at DESC
    LIMIT 1
");
$res->bind_param("i", $user_id);
$res->execute();
$resume = $res->get_result()->fetch_assoc();

/* Resume path handling */
$resumePath = "";
if (!empty($resume['filename'])) {
    // DB me: uploads/resumes/filename.pdf
    $resumePath = "../" . $resume['filename'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Application Submitted - SkillSyncPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    /* Make the body full height and remove default margin */
    body,
    html {
        height: 100%;
        margin: 0;
    }

    /* Header assumed to have fixed height, e.g., 100px */
    header {
        height: 100px;
        /* adjust if your header has different height */
    }

    /* Container takes remaining viewport height minus header */
    .container-fullscreen {
        min-height: calc(100vh - 100px);
        /* 100px = header height */
        padding-top: 20px;
        /* small padding inside container */
        overflow: hidden;
        /* hide any overflow to prevent scrolling */
    }
    </style>

</head>

<body class="bg-dark text-white">
    <?php include 'particles.php'; ?>

    <?php include 'header.php'; ?>

    <div class="container container-fullscreen d-flex flex-column justify-content-center" style="padding-top: 100px;">
        <!-- JOB DETAILS -->
        <div class="card bg-black text-white p-4 mb-4">
            <h3 class="text-info"><?= htmlspecialchars($job['title'] ?? 'Job Title') ?></h3>
            <p><b>Company:</b> <?= htmlspecialchars($job['company'] ?? 'N/A') ?></p>
            <p><b>Location:</b> <?= htmlspecialchars($job['location'] ?? 'N/A') ?></p>
            <p><b>Salary:</b> <?= htmlspecialchars($job['salary'] ?? 'N/A') ?></p>
            <p class="mt-3"><b>Required Skills:</b></p>
            <div>
                <?php
                if (!empty($job['required_skills'])) {
                    $skills = explode(',', $job['required_skills']);
                    foreach ($skills as $skill) {
                        echo "<span class='badge bg-info text-dark me-2 mb-2'>" . htmlspecialchars(trim($skill)) . "</span>";
                    }
                } else {
                    echo "<span class='text-warning'>No skills mentioned</span>";
                }
                ?>
            </div>
        </div>

        <!-- HR DETAILS -->
        <div class="card bg-black text-white p-4 mb-4">
            <h5 class="text-warning">👤 HR / Recruiter Details</h5>
            <p><b>Name:</b> <?= htmlspecialchars($job['hr_name'] ?? 'N/A') ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($job['hr_email'] ?? 'N/A') ?></p>
        </div>

        <!-- APPLICATION SUCCESS MESSAGE -->
        <div class="alert alert-success text-center fw-bold">
            ✅ Your application has been successfully submitted!
        </div>
        <div class="text-center mt-4">
            <a href="student-jobs.php" class="btn btn-outline-info px-5 py-2 fw-bold">⬅ Back to Jobs</a>
        </div>
    </div>

</body>

</html>