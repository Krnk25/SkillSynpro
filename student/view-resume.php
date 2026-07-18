<?php
session_start();
require_once "../db.php";

/* Basic validation */
if (!isset($_GET['user_id'])) {
    die("Invalid Request");
}

$user_id = intval($_GET['user_id']);

/* =========================
   1️⃣ STUDENT DETAILS
   ========================= */
$userQ = $conn->prepare("
    SELECT name, degree, gender, age, address, mobile, email
    FROM users
    WHERE id = ?
");
$userQ->bind_param("i", $user_id);
$userQ->execute();
$user = $userQ->get_result()->fetch_assoc();

if (!$user) {
    die("Student not found");
}

/* =========================
   2️⃣ LATEST RESUME
   ========================= */
$resQ = $conn->prepare("
    SELECT resume_file
    FROM resumes
    WHERE user_id = ?
    ORDER BY uploaded_at DESC
    LIMIT 1
");
$resQ->bind_param("i", $user_id);
$resQ->execute();
$resume = $resQ->get_result()->fetch_assoc();

/* =========================
   3️⃣ RESUME PATH FIX (IMPORTANT)
   ========================= */
$resumePath = "";

if (!empty($resume['resume_file'])) {

    // remove ../ or ./ if mistakenly saved
    $cleanPath = str_replace(['../', './'], '', $resume['resume_file']);

    // final path (because this file is inside /user/)
    $resumePath = "../" . $cleanPath;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Resume Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-white">

    <div class="container mt-5 mb-5">

        <!-- =========================
         STUDENT DETAILS
         ========================= -->
        <div class="card bg-black text-white p-4 mb-4">
            <h4 class="text-info">👤 Student Details</h4>
            <p><b>Name:</b> <?= htmlspecialchars($user['name']) ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>
            <p><b>Mobile:</b> <?= htmlspecialchars($user['mobile']) ?></p>
            <p><b>Degree:</b> <?= htmlspecialchars($user['degree']) ?></p>
            <p><b>Gender:</b> <?= htmlspecialchars($user['gender']) ?></p>
            <p><b>Age:</b> <?= htmlspecialchars($user['age']) ?></p>
            <p><b>Address:</b> <?= htmlspecialchars($user['address']) ?></p>
        </div>

        <!-- =========================
         RESUME PREVIEW
         ========================= -->
        <div class="card bg-black text-white p-4 mb-4">
            <h4 class="text-success">📄 Resume Preview</h4>

            <?php if (!empty($resumePath) && file_exists($resumePath)): ?>

            <!-- PDF / DOC Preview -->
            <iframe src="<?= htmlspecialchars($resumePath) ?>" width="100%" height="650px"
                style="border:1px solid #444; background:#fff;">
            </iframe>

            <div class="text-center mt-3">
                <a href="<?= htmlspecialchars($resumePath) ?>" download class="btn btn-info px-4">
                    ⬇ Download Resume
                </a>
            </div>

            <?php else: ?>
            <p class="text-warning mt-3">
                ⚠ Resume not found or file missing.
            </p>
            <?php endif; ?>
        </div>

        <!-- =========================
         BACK BUTTON
         ========================= -->
        <div class="text-center">
            <a href="dashboard.php" class="btn btn-outline-light px-4">
                ⬅ Back to Dashboard
            </a>
        </div>

    </div>

</body>

</html>