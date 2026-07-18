<?php
session_start();
require_once "../db.php";

/* =========================
   LOGIN CHECK
========================= */
if (!isset($_SESSION['company_user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['company_user_id']);

/* =========================
   FETCH HR NAME
========================= */
$hr_name = "HR";

$stmt = $conn->prepare("SELECT hr_name FROM company_users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($db_hr_name);
if ($stmt->fetch() && !empty($db_hr_name)) {
    $hr_name = $db_hr_name;
}
$stmt->close();

/* =========================
   JOB COUNTS
========================= */
$totalJobs  = 0;
$activeJobs = 0;

// Total Jobs
$stmt = $conn->prepare("SELECT COUNT(*) FROM jobs WHERE company_user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($totalJobs);
$stmt->fetch();
$stmt->close();

// Active Jobs
$stmt = $conn->prepare("SELECT COUNT(*) FROM jobs WHERE company_user_id = ? AND status = 'active'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($activeJobs);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Company Dashboard | SkillSyncPro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    /* ================= GLOBAL ================= */
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #050d14, #0a1f2e, #041018);
        color: #fff;
        min-height: 100vh;
    }

    :root {
        --header-height: 80px;
    }

    .main {
        max-width: 1300px;
        margin: auto;
        /* padding-top adjusted for fixed header */
        padding: calc(var(--header-height) + 30px) 40px 20px;
    }

    /* Welcome Text */
    .welcome {
        font-size: 2.5rem;


        font-family: 'Algerian', 'Times New Roman', serif;
        background: linear-gradient(90deg, #ff00ff, #00f7ff, #00ff1a);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientMove 5s ease infinite;
    }

    @keyframes gradientMove {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .subtext {
        opacity: .75;
        margin-bottom: 30px;
    }

    /* Stats Cards */
    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 22px;
        margin-bottom: 40px;
    }

    .card-box {
        position: relative;
        background: rgba(255, 255, 255, 0.0.2);
        border-radius: 22px;
        padding: 26px 20px;
        text-align: center;
        backdrop-filter: blur(3px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        transition: .4s;
    }

    .card-box:hover {
        transform: translateY(-6px);
        box-shadow: 0 0 40px rgba(0, 255, 255, .35);
    }

    .card-box i {
        font-size: 2.4rem;
        color: #00ffff;
        margin-bottom: 8px;
    }

    .card-box h2 {
        font-size: 2.6rem;
        font-weight: 900;
        margin: 0;
    }

    .card-box p {
        font-size: .95rem;
        opacity: .75;
    }

    /* Action Boxes */
    .actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 24px;
    }

    .action {
        text-decoration: none;
        color: #fff;
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.02));
        padding: 34px 20px;
        border-radius: 26px;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(3px);
        transition: .45s;
        position: relative;
        overflow: hidden;
    }

    .action::before {
        content: '';
        position: absolute;
        inset: -2px;
        background: linear-gradient(120deg, #ff00ff, #00ffff, #00ff99);
        opacity: 0;
        transition: .4s;
    }

    .action:hover::before {
        opacity: .25;
    }

    .action:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 30px 60px rgba(255, 0, 255, .35);
    }

    .action i {
        font-size: 3rem;
        margin-bottom: 14px;
        background: linear-gradient(90deg, #ff00ff, #00ffff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .action h4 {
        font-size: 1.1rem;
        font-weight: 700;
    }

    /* Footer */
    .footer {
        text-align: center;
        padding: 14px;
        font-size: .85rem;
        opacity: .6;
        margin-top: 80px;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .stats {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }

        .card-box h2 {
            font-size: 2rem;
        }

        .card-box i {
            font-size: 2rem;
        }

        .actions {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
        }

        .action i {
            font-size: 2.5rem;
        }

        .action h4 {
            font-size: 1rem;
        }
    }

    @media (max-width: 768px) {
        .main {
            padding: calc(var(--header-height) + 20px) 15px 20px;
        }

        .welcome {
            font-size: 1.8rem;
        }

        .subtext {
            font-size: 0.95rem;
        }

        .stats,
        .actions {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .card-box {
            padding: 20px 15px;
        }

        .action {
            padding: 25px 15px;
        }

        .action i {
            font-size: 2.2rem;
        }

        .action h4 {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 480px) {
        .welcome {
            font-size: 1.5rem;
        }

        .subtext {
            font-size: 0.9rem;
        }

        .card-box h2 {
            font-size: 1.8rem;
        }

        .card-box i {
            font-size: 1.8rem;
        }

        .action i {
            font-size: 2rem;
        }

        .action h4 {
            font-size: 0.9rem;
        }

        .main {
            padding: calc(var(--header-height) + 15px) 10px 15px;
        }
    }
    </style>
</head>

<body>
    <?php include 'particles.php'; ?>
    <!-- ================= HEADER ================= -->
    <?php include 'header.php'; ?>

    <!-- ================= MAIN CONTENT ================= -->
    <section class="main">
        <h1 class="welcome">Welcome, <?= htmlspecialchars($hr_name) ?></h1>
        <p class="subtext">Manage hiring, jobs & candidates with skill matching tools</p>

        <!-- STATS -->
        <div class="stats">
            <div class="card-box">
                <i class="fas fa-briefcase"></i>
                <h2><?= $totalJobs ?></h2>
                <p>Total Jobs Posted</p>
            </div>

            <div class="card-box">
                <i class="fas fa-toggle-on"></i>
                <h2><?= $activeJobs ?></h2>
                <p>Active Jobs</p>
            </div>

            <div class="card-box">
                <i class="fas fa-brain"></i>
                <h2>Skill Based</h2>
                <p>Resume Matching</p>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="actions">
            <a href="manage-job.php" class="action">
                <i class="fas fa-plus-circle"></i>
                <h4>Post / Manage Jobs</h4>
            </a>

            <a href="view-applicants.php" class="action">
                <i class="fas fa-users"></i>
                <h4>View Applicants</h4>
            </a>

            <a href="company-profile.php" class="action">
                <i class="fas fa-building"></i>
                <h4>Company Profile</h4>
            </a>

            <a href="analytics.php" class="action">
                <i class="fas fa-chart-line"></i>
                <h4>Insights & Analytics</h4>
            </a>
        </div>
    </section>

    <!-- ================= FOOTER ================= -->
    <div class="footer">
        © <?= date("Y") ?> SkillSyncPro · Company Dashboard
    </div>

</body>

</html>