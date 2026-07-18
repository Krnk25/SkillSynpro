<?php
session_start();
require_once "../db.php";

/* LOGIN CHECK */
if (!isset($_SESSION['company_user_id'])) {
    header("Location: login.php");
    exit;
}

$company_user_id = (int)$_SESSION['company_user_id'];
$message = "";

/* FETCH COMPANY USER */
$user_stmt = $conn->prepare("
    SELECT company_name, hr_name 
    FROM company_users 
    WHERE id = ? LIMIT 1
");
$user_stmt->bind_param("i", $company_user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

$company_name = $user['company_name'] ?? "Company";
$hr_name = $user['hr_name'] ?? "HR";

/* CREATE / UPDATE JOB */
if (isset($_POST['save_job'])) {

    $job_id   = intval($_POST['job_id'] ?? 0);
    $title    = trim($_POST['title']);
    $location = trim($_POST['location']);
    $skills   = trim($_POST['skills']);
    $salary   = trim($_POST['salary']);
    $type     = trim($_POST['job_type']);

    if ($job_id > 0) {
        $stmt = $conn->prepare("
            UPDATE jobs 
            SET title=?, location=?, required_skills=?, salary=?, job_type=?
            WHERE id=? AND company_user_id=?
        ");
        $stmt->bind_param("sssssii",
            $title,$location,$skills,$salary,$type,
            $job_id,$company_user_id
        );
        $stmt->execute();
        $message = "✅ Job updated successfully";
    } else {
        $status = "active";
        $stmt = $conn->prepare("
            INSERT INTO jobs 
            (company_user_id,title,company,location,required_skills,salary,job_type,status,created_at)
            VALUES (?,?,?,?,?,?,?,?,NOW())
        ");
        $stmt->bind_param("isssssss",
            $company_user_id,$title,$company_name,$location,
            $skills,$salary,$type,$status
        );
        $stmt->execute();
        $message = "✅ Job posted successfully";
    }
}

/* DELETE JOB */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $del = $conn->prepare("DELETE FROM jobs WHERE id=? AND company_user_id=?");
    $del->bind_param("ii",$id,$company_user_id);
    $del->execute();
    header("Location: manage-job.php");
    exit;
}

/* TOGGLE STATUS */
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];

    $res = $conn->prepare("SELECT status FROM jobs WHERE id=? AND company_user_id=?");
    $res->bind_param("ii",$id,$company_user_id);
    $res->execute();
    $job = $res->get_result()->fetch_assoc();

    if ($job) {
        $new = $job['status']=='active'?'inactive':'active';
        $upd = $conn->prepare("UPDATE jobs SET status=? WHERE id=? AND company_user_id=?");
        $upd->bind_param("sii",$new,$id,$company_user_id);
        $upd->execute();
        $message = "✅ Status changed to $new";
    }
}

/* FETCH JOBS */
$jobs = $conn->prepare("
    SELECT * FROM jobs 
    WHERE company_user_id=?
    ORDER BY id DESC
");
$jobs->bind_param("i",$company_user_id);
$jobs->execute();
$jobs_result = $jobs->get_result();

/* EDIT JOB */
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $e = $conn->prepare("SELECT * FROM jobs WHERE id=? AND company_user_id=?");
    $e->bind_param("ii",$id,$company_user_id);
    $e->execute();
    $edit = $e->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Jobs | SkillSyncPro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    /* ===== GLOBAL ===== */
    * {
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: #0f0f0f;
        color: #fff;
        min-height: 100vh;
        padding-top: 100px;
        /* space for fixed header */
    }

    h2 {
        color: #00f7ff;
        text-shadow: 0 0 8px #00f7ff;
    }

    p.text-info {
        opacity: 0.8;
    }

    /* ===== ACCORDION ===== */
    .accordion-item {
        background: #1b1b1b;
        border: 1px solid #333;
    }

    .accordion-button {
        background: #1b1b1b;
        color: #00f7ff;
    }

    .accordion-button:not(.collapsed) {
        background: #101010 !important;
        color: #00f7ff !important;
    }

    .accordion-body,
    .accordion-body p,
    .accordion-body b,
    .accordion-body span,
    .accordion-body small {
        color: #fff !important;
    }

    .badge-active {
        background: #00f7ff;
        color: #000;
    }

    .badge-inactive {
        background: #888;
        color: #000;
    }

    input.form-control {
        background: #0f0f0f;
        border: 1px solid #00f7ff;
        color: #fff;
    }

    .btn-main {
        background: #00f7ff;
        color: #000;
        font-weight: 600;
    }

    .accordion-body a {
        color: #00f7ff;
    }

    /* ===== MOBILE RESPONSIVE ===== */
    @media(max-width:992px) {
        body {
            font-size: 0.95rem;
        }

        .accordion-button {
            font-size: 1rem;
            padding: 0.75rem 1rem;
        }

        input.form-control {
            font-size: 0.95rem;
        }

        .btn-main {
            font-size: 0.9rem;
        }
    }

    @media(max-width:480px) {
        h2 {
            font-size: 1.25rem;
        }

        .accordion-button {
            font-size: 0.85rem;
            padding: 0.5rem 0.65rem;
        }

        input.form-control {
            font-size: 0.85rem;
        }

        .btn-main {
            font-size: 0.85rem;
            padding: 8px 0;
        }
    }
    </style>
</head>

<body>
    <?php include 'particles.php'; ?>
    <!-- ===== HEADER ===== -->
    <?php include 'header.php'; ?>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="container content-wrapper">

        <h2><?= htmlspecialchars($company_name) ?></h2>
        <p class="text-info">HR: <?= htmlspecialchars($hr_name) ?></p>

        <?php if($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>

        <div class="accordion" id="mainAccordion">

            <!-- POST NEW JOB DROPDOWN -->
            <div class="accordion-item mb-4">
                <h2 class="accordion-header">
                    <button class="accordion-button <?= $edit?'':'collapsed' ?>" data-bs-toggle="collapse"
                        data-bs-target="#postJob">
                        <?= $edit ? "✏️ Edit Job" : "➕ Post New Job" ?>
                    </button>
                </h2>

                <div id="postJob" class="accordion-collapse collapse <?= $edit?'show':'' ?>">
                    <div class="accordion-body">
                        <form method="post">
                            <input type="hidden" name="job_id" value="<?= $edit['id'] ?? '' ?>">
                            <input class="form-control mb-2" name="title" placeholder="Job Title" required
                                value="<?= $edit['title'] ?? '' ?>">
                            <input class="form-control mb-2" name="location" placeholder="Location" required
                                value="<?= $edit['location'] ?? '' ?>">
                            <input class="form-control mb-2" name="skills" placeholder="Required Skills"
                                value="<?= $edit['required_skills'] ?? '' ?>">
                            <input class="form-control mb-2" name="salary" placeholder="Salary"
                                value="<?= $edit['salary'] ?? '' ?>">
                            <input class="form-control mb-2" name="job_type" placeholder="Job Type"
                                value="<?= $edit['job_type'] ?? '' ?>">
                            <button class="btn btn-main w-100" name="save_job">
                                <?= $edit ? "Update Job" : "Post Job" ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- POSTED JOBS -->
            <h4 class="mb-3">Posted Jobs</h4>

            <?php if($jobs_result->num_rows): $i=1; while($j=$jobs_result->fetch_assoc()): ?>
            <div class="accordion-item mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#job<?= $i ?>">
                        <?= htmlspecialchars($j['title']) ?>
                        <span class="badge ms-3 <?= $j['status']=='active'?'badge-active':'badge-inactive' ?>">
                            <?= $j['status'] ?>
                        </span>
                    </button>
                </h2>

                <div id="job<?= $i ?>" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <p><b>Location:</b> <?= $j['location'] ?></p>
                        <p><b>Skills:</b> <?= $j['required_skills'] ?></p>
                        <p><b>Salary:</b> <?= $j['salary'] ?></p>
                        <p><b>Type:</b> <?= $j['job_type'] ?></p>

                        <a href="?edit=<?= $j['id'] ?>" class="btn btn-warning btn-sm" style="color: #000">Edit</a>
                        <a href="?toggle=<?= $j['id'] ?>" class="btn btn-info btn-sm" style="color: #000">Toggle</a>
                        <a href="?delete=<?= $j['id'] ?>" class="btn btn-danger btn-sm" style="color: #000"
                            onclick="return confirm('Delete job?')">Delete</a>
                    </div>
                </div>
            </div>
            <?php $i++; endwhile; else: ?>
            <p class="text-warning">No jobs posted yet.</p>
            <?php endif; ?>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>