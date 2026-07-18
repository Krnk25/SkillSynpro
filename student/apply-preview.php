<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$job_id  = intval($_GET['job_id']);

/* Job + HR */
$jobQ = $conn->prepare("
SELECT j.*, cu.name AS hr_name, cu.email AS hr_email
FROM jobs j
JOIN company_users cu ON j.company_user_id = cu.id
WHERE j.id=?
");
$jobQ->bind_param("i",$job_id);
$jobQ->execute();
$job = $jobQ->get_result()->fetch_assoc();

/* Student Details */
$userQ = $conn->prepare("
SELECT name,degree,gender,age,address,mobile,email
FROM users WHERE id=?
");
$userQ->bind_param("i",$user_id);
$userQ->execute();
$user = $userQ->get_result()->fetch_assoc();

/* Resume */
$resQ = $conn->prepare("
SELECT resume_file FROM resumes
WHERE user_id=? ORDER BY uploaded_at DESC LIMIT 1
");
$resQ->bind_param("i",$user_id);
$resQ->execute();
$resume = $resQ->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Confirm Job Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-white">

    <div class="container mt-5 mb-5">

        <!-- COMPANY + JOB -->
        <div class="card bg-black p-4 mb-4">
            <h4 class="text-info"><?= $job['title'] ?></h4>
            <p><b>Company:</b> <?= $job['company'] ?></p>
            <p><b>Location:</b> <?= $job['location'] ?></p>
            <p><b>Salary:</b> <?= $job['salary'] ?></p>

            <p><b>Required Skills:</b></p>
            <?php foreach(explode(',',$job['required_skills']) as $s): ?>
            <span class="badge bg-info text-dark me-2"><?= trim($s) ?></span>
            <?php endforeach; ?>
        </div>

        <!-- HR DETAILS -->
        <div class="card bg-black p-4 mb-4">
            <h5 class="text-warning">👤 HR Details</h5>
            <p><b>Name:</b> <?= $job['hr_name'] ?></p>
            <p><b>Email:</b> <?= $job['hr_email'] ?></p>
        </div>

        <!-- STUDENT DETAILS -->
        <div class="card bg-black p-4 mb-4">
            <h5 class="text-success">👨‍🎓 Your Details</h5>
            <p><b>Name:</b> <?= $user['name'] ?></p>
            <p><b>Degree:</b> <?= $user['degree'] ?></p>
            <p><b>Gender:</b> <?= $user['gender'] ?></p>
            <p><b>Age:</b> <?= $user['age'] ?></p>
            <p><b>Address:</b> <?= $user['address'] ?></p>
            <p><b>Mobile:</b> <?= $user['mobile'] ?></p>
            <p><b>Email:</b> <?= $user['email'] ?></p>
        </div>

        <!-- RESUME -->
        <div class="card bg-black p-4 mb-4">
            <h5 class="text-info">📄 Resume</h5>
            <a href=" view-resume.php?user_id=<?= $user_id ?>" target="_blank" class="btn btn-info">
                View Resume
            </a>
        </div>

        <!-- SUBMIT -->
        <form action="apply-job-submit.php" method="POST" class="text-center">
            <input type="hidden" name="job_id" value="<?= $job_id ?>">
            <button class="btn btn-success px-5 py-2">
                ✅ Submit Application
            </button>
        </form>

    </div>
</body>

</html>