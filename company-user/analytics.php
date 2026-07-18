<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['company_user_id'])) {
    header("Location: login.php");
    exit;
}

$company_id = (int)$_SESSION['company_user_id'];

function getCount($conn,$q){
    $r = $conn->query($q)->fetch_assoc();
    return $r['total'] ?? 0;
}

$totalJobs = getCount($conn,"SELECT COUNT(*) total FROM jobs WHERE company_user_id=$company_id");

$totalApplicants = getCount($conn,"
    SELECT COUNT(*) total FROM job_applications a
    JOIN jobs j ON a.job_id=j.id
    WHERE j.company_user_id=$company_id
");

$shortlisted = getCount($conn,"
    SELECT COUNT(*) total FROM job_applications a
    JOIN jobs j ON a.job_id=j.id
    WHERE j.company_user_id=$company_id AND a.status='approved'
");

$rejected = getCount($conn,"
    SELECT COUNT(*) total FROM job_applications a
    JOIN jobs j ON a.job_id=j.id
    WHERE j.company_user_id=$company_id AND a.status='rejected'
");

$todayApplications = getCount($conn,"
    SELECT COUNT(*) total FROM job_applications a
    JOIN jobs j ON a.job_id=j.id
    WHERE j.company_user_id=$company_id AND DATE(a.applied_at)=CURDATE()
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Analytics | SkillSyncPro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: radial-gradient(circle at top, #0f2027, #203a43, #0f2027);
        color: #fff;
        height: 100vh;
    }



    /* ===== BELOW HEADER ===== */
    .dashboard-container {
        max-width: 1400px;
        margin: auto;
        padding: 100px;
    }

    /* ===== TITLE ===== */
    .dashboard-header {
        margin-bottom: 25px;
    }

    .dashboard-header h1 {
        font-size: 30px;
        font-weight: 700;
    }

    .dashboard-header p {
        opacity: .75;
        margin-top: 6px;
    }

    /* ===== GRID (NO SCROLL) ===== */
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
    }

    /* ===== CARD ===== */
    .stat-card {
        padding: 24px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(3px);
        border: 1px solid rgba(255, 255, 255, .18);
        text-align: center;
        transition: .3s ease;
    }

    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, .5);
    }

    /* ===== ICON ===== */
    .icon-box {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 14px;
        background: rgba(0, 0, 0, .4);
    }

    /* ===== TEXT ===== */
    .stat-card h3 {
        font-size: 14px;
        opacity: .85;
    }

    .stat-value {
        font-size: 30px;
        font-weight: 700;
        margin-top: 6px;
    }

    /* ===== COLORS ===== */
    .jobs .icon-box {
        color: #ffca28;
    }

    .applicants .icon-box {
        color: #4fc3f7;
    }

    .shortlisted .icon-box {
        color: #66bb6a;
    }

    .rejected .icon-box {
        color: #ef5350;
    }

    .today .icon-box {
        color: #ab47bc;
    }

    /* ===== RESPONSIVE ===== */
    @media(max-width:1100px) {
        .analytics-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media(max-width:700px) {
        .analytics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width:420px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>

<body>

    <?php include 'particles.php'; ?>
    <?php include 'header.php'; ?>

    <div class="dashboard-container">

        <div class="dashboard-header">
            <h1>📊 Hiring Analytics</h1>
            <p>Real-time insights of your hiring activity</p>
        </div>

        <div class="analytics-grid">

            <div class="stat-card jobs">
                <div class="icon-box"><i class="fas fa-briefcase"></i></div>
                <h3>Total Jobs</h3>
                <div class="stat-value"><?= $totalJobs ?></div>
            </div>

            <div class="stat-card applicants">
                <div class="icon-box"><i class="fas fa-users"></i></div>
                <h3>Total Applicants</h3>
                <div class="stat-value"><?= $totalApplicants ?></div>
            </div>

            <div class="stat-card shortlisted">
                <div class="icon-box"><i class="fas fa-star"></i></div>
                <h3>Shortlisted</h3>
                <div class="stat-value"><?= $shortlisted ?></div>
            </div>

            <div class="stat-card rejected">
                <div class="icon-box"><i class="fas fa-user-times"></i></div>
                <h3>Rejected</h3>
                <div class="stat-value"><?= $rejected ?></div>
            </div>

            <div class="stat-card today">
                <div class="icon-box"><i class="fas fa-calendar-day"></i></div>
                <h3>Today's Applications</h3>
                <div class="stat-value"><?= $todayApplications ?></div>
            </div>

        </div>
    </div>

</body>

</html>