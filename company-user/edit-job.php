<?php
session_start();
require "../db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role']!='company'){
    header("Location: ../login.php");
    exit;
}

$company_id = $_SESSION['user_id'];
$job_id = intval($_GET['id']);
$message = "";

/* FETCH JOB */
$stmt = $conn->prepare("
SELECT * FROM jobs 
WHERE id=? AND company_user_id=?
");
$stmt->bind_param("ii",$job_id,$company_id);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();

if(!$job){
    die("Job not found");
}

/* UPDATE JOB */
if(isset($_POST['update_job'])){
    $title = $_POST['title'];
    $location = $_POST['location'];
    $salary = $_POST['salary'];
    $skills = $_POST['required_skills'];
    $type = $_POST['job_type'];

    $up = $conn->prepare("
    UPDATE jobs SET
    title=?, location=?, salary=?, required_skills=?, job_type=?
    WHERE id=? AND company_user_id=?
    ");
    $up->bind_param("sssssii",$title,$location,$salary,$skills,$type,$job_id,$company_id);
    $up->execute();

    header("Location: manage-jobs.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Job</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-white">
    <div class="container mt-5">

        <h3>✏ Edit Job</h3>
        <a href="manage-jobs.php" class="btn btn-secondary mb-3">⬅ Back</a>

        <div class="card bg-black p-4">
            <form method="POST">

                <input type="hidden" name="update_job">

                <input class="form-control mb-2" name="title" value="<?= htmlspecialchars($job['title']) ?>" required>

                <input class="form-control mb-2" name="location" value="<?= htmlspecialchars($job['location']) ?>"
                    required>

                <input class="form-control mb-2" name="salary" value="<?= htmlspecialchars($job['salary']) ?>" required>

                <select name="job_type" class="form-control mb-2">
                    <option <?= $job['job_type']=="Full Time"?'selected':'' ?>>Full Time</option>
                    <option <?= $job['job_type']=="Part Time"?'selected':'' ?>>Part Time</option>
                    <option <?= $job['job_type']=="Internship"?'selected':'' ?>>Internship</option>
                    <option <?= $job['job_type']=="Remote"?'selected':'' ?>>Remote</option>
                </select>

                <textarea class="form-control mb-2" name="required_skills"
                    required><?= htmlspecialchars($job['required_skills']) ?></textarea>

                <button class="btn btn-success w-100">Update Job</button>

            </form>
        </div>

    </div>
</body>

</html>