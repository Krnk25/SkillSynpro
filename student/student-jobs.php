<?php
session_start();
require_once "../db.php";

/* LOGIN CHECK */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$student_id = intval($_SESSION['user_id']);

/* SEARCH */
$search = $_GET['search'] ?? '';
$searchParam = "%$search%";

/* ACTIVE JOBS */
$jobs_stmt = $conn->prepare("
    SELECT id, title, company, location, required_skills, salary
    FROM jobs
    WHERE status='active'
      AND (title LIKE ? OR company LIKE ?)
    ORDER BY id DESC
");
$jobs_stmt->bind_param("ss", $searchParam, $searchParam);
$jobs_stmt->execute();
$active_jobs = $jobs_stmt->get_result();

/* APPLIED JOBS */
$applied_stmt = $conn->prepare("
    SELECT 
        j.title,
        j.company,
        j.location,
        j.required_skills,
        j.salary,
        ja.applied_at,
        ja.status
    FROM job_applications ja
    INNER JOIN jobs j ON j.id = ja.job_id
    WHERE ja.student_id = ?
    ORDER BY ja.applied_at DESC
");
$applied_stmt->bind_param("i", $student_id);
$applied_stmt->execute();
$applied_jobs = $applied_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Jobs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html,
    body {
        width: 100%;
        max-width: 100vw;
        background: #0f0f0f;
        color: #ffffff;
        font-family: 'Inter', sans-serif;

        /* Scrollbar hide for Firefox */
        scrollbar-width: none;
        /* Scrollbar hide for IE/Edge */
        -ms-overflow-style: none;
    }

    /* Scrollbar hide for Chrome, Safari, and Opera */
    html::-webkit-scrollbar,
    body::-webkit-scrollbar {
        display: none;
        width: 0 !important;
        height: 0 !important;
    }

    /* ===== 2. PARTICLES FIXED BACKGROUND ===== */
    #particles-js {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        pointer-events: none;
    }

    /* ===== 3. PAGE WRAPPER & CONTAINER ===== */
    .page-wrapper {
        padding-top: 110px;
        /* Header space */
        width: 100%;
        min-height: 100vh;
        position: relative;
        z-index: 1;
    }

    .container {
        max-width: 1140px;
        margin: 0 auto;
        padding: 0 15px;
        /* Ensure no horizontal overflow inside container */
        overflow-x: hidden;
    }

    /* ===== 4. SEARCH & TOGGLES ===== */
    .search-box {
        background: #ffffff !important;
        color: #000 !important;
        border: 2px solid #00f7ff !important;
        border-radius: 10px;
        padding: 12px 20px;
        margin-bottom: 25px;
        box-shadow: 0 0 15px rgba(0, 247, 255, 0.2);
    }

    .section-toggle {
        background: rgba(16, 16, 16, 0.8);
        border: 1px solid #00f7ff;
        color: #00f7ff;
        font-weight: 600;
        width: 100%;
        text-align: left;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        transition: 0.3s;
        backdrop-filter: blur(5px);
        cursor: pointer;
    }

    .section-toggle:hover {
        background: #00f7ff;
        color: #000;
    }

    /* ===== 5. ACCORDION STYLING ===== */
    .accordion-item {
        background: rgba(27, 27, 27, 0.9) !important;
        border: 1px solid #333 !important;
        margin-bottom: 15px;
        border-radius: 12px !important;
        overflow: hidden;
    }

    .accordion-button {
        background: transparent !important;
        color: #00f7ff !important;
        font-weight: 600;
        padding: 18px;
        box-shadow: none !important;
        border: none !important;
    }

    .accordion-button:not(.collapsed) {
        background: rgba(0, 247, 255, 0.1) !important;
        color: #00f7ff !important;
    }

    .accordion-body {
        background: transparent !important;
        color: #e0e0e0 !important;
        padding: 20px;
        border-top: 1px solid #333;
    }

    /* ===== 6. BUTTONS & GLOW BADGES ===== */
    .btn-apply {
        background: linear-gradient(45deg, #00f7ff, #00d0d6);
        color: #000 !important;
        font-weight: 700;
        border: none;
        padding: 10px 25px;
        border-radius: 6px;
        transition: 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-apply:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 247, 255, 0.4);
    }

    .badge {
        padding: 6px 12px;
        border-radius: 5px;
        font-weight: 600;
    }

    .bg-success {
        background-color: #28a745 !important;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
    }

    .bg-danger {
        background-color: #dc3545 !important;
        box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
    }

    .bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
        box-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
    }

    /* ===== 7. MOBILE OPTIMIZATION ===== */
    @media (max-width: 768px) {
        .page-wrapper {
            padding-top: 90px;
        }

        .container {
            padding: 0 10px;
        }

        .accordion-button {
            font-size: 14px;
            padding: 15px;
        }
    }
    </style>
</head>

<body>

    <?php include 'particles.php'; ?>
    <?php include 'header.php'; ?>

    <div class="page-wrapper">
        <div class="container">

            <!-- SEARCH -->
            <form method="GET" class="mb-4">
                <input type="text" name="search" class="form-control search-box" placeholder="Search job or company..."
                    value="<?= htmlspecialchars($search) ?>">
            </form>

            <!-- ================= ACTIVE JOBS DROPDOWN ================= -->
            <button class="section-toggle mb-3" data-bs-toggle="collapse" data-bs-target="#activeJobsBox">
                🔽 Active Jobs
            </button>

            <div class="collapse show" id="activeJobsBox">
                <div class="accordion mb-5" id="activeJobsAccordion">

                    <?php if($active_jobs->num_rows > 0): ?>
                    <?php $a=1; while($job = $active_jobs->fetch_assoc()): ?>

                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                data-bs-target="#active<?= $a ?>">
                                <?= htmlspecialchars($job['title']) ?>
                                <span class="ms-3 text-info">(<?= htmlspecialchars($job['company']) ?>)</span>
                            </button>
                        </h2>

                        <div id="active<?= $a ?>" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <p><b>Location:</b> <?= htmlspecialchars($job['location']) ?></p>
                                <p><b>Skills:</b> <?= htmlspecialchars($job['required_skills']) ?></p>
                                <p><b>Salary:</b> <?= htmlspecialchars($job['salary']) ?></p>

                                <a href="apply-job.php?job_id=<?= $job['id'] ?>" class="btn btn-apply mt-2">
                                    Apply Now
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php $a++; endwhile; else: ?>
                    <p class="text-warning">No active jobs found.</p>
                    <?php endif; ?>

                </div>
            </div>

            <!-- ================= APPLIED JOBS DROPDOWN ================= -->
            <button class="section-toggle mb-3" data-bs-toggle="collapse" data-bs-target="#appliedJobsBox">
                🔽 My Applied Jobs
            </button>

            <div class="collapse" id="appliedJobsBox">
                <div class="accordion mb-5" id="appliedJobsAccordion">

                    <?php if($applied_jobs->num_rows > 0): ?>
                    <?php $i=1; while($job = $applied_jobs->fetch_assoc()): ?>

                    <?php
                    // ✅ Explicit status text
                    if ($job['status'] === 'approved') {
                        $statusText = "Approved";
                        $badge = "success";
                    } elseif ($job['status'] === 'rejected') {
                        $statusText = "Rejected";
                        $badge = "danger";
                    } else {
                        $statusText = "Pending";
                        $badge = "warning";
                    }
                    ?>

                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                data-bs-target="#applied<?= $i ?>">
                                <?= htmlspecialchars($job['title']) ?>
                                <span class="badge bg-<?= $badge ?> ms-3">
                                    <?= $statusText ?>
                                </span>
                            </button>
                        </h2>

                        <div id="applied<?= $i ?>" class="accordion-collapse collapse">
                            <div class="accordion-body" style="color: #fff;">
                                <p><b>Company:</b> <?= htmlspecialchars($job['company']) ?></p>
                                <p><b>Location:</b> <?= htmlspecialchars($job['location']) ?></p>
                                <p><b>Skills:</b> <?= htmlspecialchars($job['required_skills']) ?></p>
                                <p><b>Salary:</b> <?= htmlspecialchars($job['salary']) ?></p>
                                <p><b>Applied At:</b> <?= htmlspecialchars($job['applied_at']) ?></p>

                                <!-- ✅ Status in body with glow -->
                                <p><b>Status:</b>
                                    <span class="badge bg-<?= $badge ?>">
                                        <?= $statusText ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php $i++; endwhile; else: ?>
                    <p class="text-warning">No applied jobs yet.</p>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>