<?php
session_start();
require "../db.php";

$cid = $_SESSION['user_id'];

$jobs = $conn->query("SELECT COUNT(*) c FROM jobs WHERE company_user_id=$cid")->fetch_assoc();
$apps = $conn->query("
SELECT COUNT(*) c FROM job_applications ja 
JOIN jobs j ON ja.job_id=j.id 
WHERE j.company_user_id=$cid")->fetch_assoc();
$active = $conn->query("
SELECT COUNT(*) c FROM jobs 
WHERE company_user_id=$cid AND status='active'")->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Company Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-white">
    <div class="container mt-5">

        <h2>📊 Company Dashboard</h2>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-black text-center p-4">
                    <h4><?= $jobs['c'] ?></h4>
                    <p>Total Jobs</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-black text-center p-4">
                    <h4><?= $active['c'] ?></h4>
                    <p>Active Jobs</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-black text-center p-4">
                    <h4><?= $apps['c'] ?></h4>
                    <p>Total Applications</p>
                </div>
            </div>
        </div>

        <a href="manage-jobs.php" class="btn btn-info mt-4">Manage Jobs</a>

    </div>
</body>

</html>