<?php
session_start();
require_once "../db.php";

/* LOGIN CHECK */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
$student_id = $_SESSION['user_id'];

if(!isset($_GET['job_id'])){
    header("Location: student-job.php");
    exit;
}

$job_id = intval($_GET['job_id']);

/* JOB DETAILS */
$stmt = $conn->prepare("
    SELECT j.*, cu.company_name, cu.hr_name
    FROM jobs j
    INNER JOIN company_users cu ON cu.id = j.company_user_id
    WHERE j.id = ?
");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();
if(!$job){ die("Job not found"); }

/* STUDENT */
$student_stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student = $student_stmt->get_result()->fetch_assoc();

/* CHECK APPLY */
$check = $conn->prepare("SELECT * FROM job_applications WHERE student_id=? AND job_id=?");
$check->bind_param("ii", $student_id, $job_id);
$check->execute();
$already_applied = $check->get_result()->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Apply Job</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    * {
        box-sizing: border-box
    }

    body {
        background: #121212;
        color: #fff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
    }

    /* 🔒 HEADER GAP */
    .page-wrapper {
        padding-top: 100px;
        /* EXACT 100px below header */
        min-height: 100vh;
        display: flex;
        justify-content: center;
    }

    /* CONTAINER */
    .apply-container {
        width: 100%;
        max-width: 900px;
        padding: 0 15px;
    }

    /* CARD (HEIGHT COMPACT) */
    .card-apply {
        background: #1f1f1f;
        padding: 20px 22px;
        /* ⬅ reduced padding */
        border-radius: 12px;
        box-shadow: 0 0 18px rgba(0, 255, 255, .18);
    }

    h2 {
        font-size: 22px;
        /* ⬅ smaller heading */
        color: #00f7ff;
        margin-bottom: 12px;
    }

    .label {
        font-weight: 600;
        color: #00f7ff;
    }

    .card-apply p {
        margin-bottom: 6px;
        /* ⬅ reduced vertical space */
        font-size: 14px;
    }

    hr {
        margin: 12px 0;
        /* ⬅ compact hr */
        border-color: #00f7ff;
    }

    .alert-warning {
        background: #ff9800;
        color: #000;
        font-weight: 600;
    }

    .btn {
        padding: 8px 18px;
        /* ⬅ smaller buttons */
    }

    .btn-back {
        background: #6c757d;
        color: #fff;
    }

    .btn-back:hover {
        background: #5a6268
    }

    @media(max-width:600px) {
        h2 {
            font-size: 20px
        }

        .card-apply {
            padding: 18px
        }
    }
    </style>
</head>

<body>

    <?php include 'particles.php'; ?>
    <?php include 'header.php'; ?>

    <div class="page-wrapper">
        <div class="apply-container">
            <div class="card-apply">

                <h2>Apply Job: <?= htmlspecialchars($job['title']) ?></h2>

                <p><span class="label">Company:</span> <?= htmlspecialchars($job['company_name']) ?></p>
                <p><span class="label">HR:</span> <?= htmlspecialchars($job['hr_name']) ?></p>

                <p><span class="label">Location:</span> <?= htmlspecialchars($job['location']) ?></p>
                <p><span class="label">Skills:</span> <?= htmlspecialchars($job['required_skills']) ?></p>
                <p><span class="label">Salary:</span> <?= htmlspecialchars($job['salary']) ?></p>
                <p><span class="label">Type:</span> <?= htmlspecialchars($job['job_type']) ?></p>

                <hr>

                <h6>Student Details</h6>
                <p><span class="label">Name:</span> <?= htmlspecialchars($student['name']) ?></p>
                <p><span class="label">Email:</span> <?= htmlspecialchars($student['email']) ?></p>
                <p><span class="label">Mobile:</span> <?= htmlspecialchars($student['mobile']) ?></p>
                <p><span class="label">Degree:</span> <?= htmlspecialchars($student['degree']) ?></p>

                <hr>

                <?php if($already_applied): ?>
                <div class="alert alert-warning mt-2">
                    ⚠️ You have already applied for this job
                </div>
                <a href="student-jobs.php" class="btn btn-back mt-2">⬅ Back</a>
                <?php else: ?>
                <form method="POST" action="apply-job-submit.php" enctype="multipart/form-data" class="mt-2">
                    <input type="hidden" name="job_id" value="<?= $job_id ?>">
                    <div class="mb-2">
                        <label class="form-label">Upload Resume (PDF)</label>
                        <input type="file" name="resume" class="form-control" accept=".pdf" required>
                    </div>
                    <button type="submit" class="btn btn-success">Submit</button>
                    <a href="student-jobs.php" class="btn btn-back">Back</a>
                </form>
                <?php endif; ?>

            </div>
        </div>
    </div>

</body>

</html>