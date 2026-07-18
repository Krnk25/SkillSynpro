<?php
session_start();
require_once "../db.php";

/* ================= LOGIN CHECK ================= */
if (!isset($_SESSION['company_user_id'])) {
    header("Location: login.php");
    exit;
}

$company_id = intval($_SESSION['company_user_id']);
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

/* ================= PHPMailer ================= */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

/* ================= APPROVE / REJECT ================= */
if (isset($_GET['action'], $_GET['app_id'])) {

    $app_id = intval($_GET['app_id']);
    $action = $_GET['action'];

    if (in_array($action, ['approved', 'rejected'])) {

        /* Fetch student + job info */
        $info = $conn->prepare("
            SELECT 
                s.name AS student_name,
                s.email,
                j.title AS job_title,
                j.company
            FROM job_applications ja
            JOIN users s ON s.id = ja.student_id
            JOIN jobs j ON j.id = ja.job_id
            WHERE ja.id = ? AND j.company_user_id = ?
        ");
        $info->bind_param("ii", $app_id, $company_id);
        $info->execute();
        $student = $info->get_result()->fetch_assoc();
        $info->close();

        if ($student) {

            /* Update status */
            $upd = $conn->prepare("
                UPDATE job_applications ja
                JOIN jobs j ON j.id = ja.job_id
                SET ja.status = ?
                WHERE ja.id = ? AND j.company_user_id = ?
            ");
            $upd->bind_param("sii", $action, $app_id, $company_id);
            $upd->execute();
            $upd->close();

            /* SEND EMAIL */
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'kabadearjun0@gmail.com'; // 🔴 CHANGE
                $mail->Password   = 'dgastplftiiruklk';       // 🔴 CHANGE
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('kabadearjun0@gmail.com', 'SkillSyncPro');
                $mail->addAddress($student['email'], $student['student_name']);
                $mail->isHTML(true);

                if ($action === 'approved') {
                    $mail->Subject = "🎉 Job Application Approved";
                    $mail->Body = "
                        <h2>Congratulations " . htmlspecialchars($student['student_name']) . " 🎉</h2>
                        <p>Your application for <b>" . htmlspecialchars($student['job_title']) . "</b>
                        at <b>" . htmlspecialchars($student['company']) . "</b> has been
                        <span style='color:green'><b>APPROVED</b></span>.</p>
                        <p>Company will contact you soon.</p>
                        <br>
                        <b>SkillSyncPro Team</b>
                    ";
                } else {
                    $mail->Subject = "❌ Job Application Rejected";
                    $mail->Body = "
                        <h2>Hello " . htmlspecialchars($student['student_name']) . "</h2>
                        <p>Your application for <b>" . htmlspecialchars($student['job_title']) . "</b>
                        at <b>" . htmlspecialchars($student['company']) . "</b> has been
                        <span style='color:red'><b>REJECTED</b></span>.</p>
                        <p>Keep applying, best opportunities await you.</p>
                        <br>
                        <b>SkillSyncPro Team</b>
                    ";
                }

                $mail->send();
            } catch (Exception $e) {
                error_log("Mail Error: " . $mail->ErrorInfo);
            }
        }
    }

    header("Location: view-applicants.php" . ($job_id ? "?job_id=$job_id" : ""));
    exit;
}

/* ================= FETCH APPLICANTS ================= */
if ($job_id > 0) {
    $stmt = $conn->prepare("
        SELECT 
            ja.id AS application_id,
            ja.status,
            s.name AS student_name,
            s.email,
            s.mobile,
            s.degree,
            ja.resume_file,
            ja.applied_at,
            j.title AS job_title
        FROM job_applications ja
        JOIN users s ON s.id = ja.student_id
        JOIN jobs j ON j.id = ja.job_id
        WHERE j.company_user_id = ? AND j.id = ?
        ORDER BY ja.applied_at DESC
    ");
    $stmt->bind_param("ii", $company_id, $job_id);
} else {
    $stmt = $conn->prepare("
        SELECT 
            ja.id AS application_id,
            ja.status,
            s.name AS student_name,
            s.email,
            s.mobile,
            s.degree,
            ja.resume_file,
            ja.applied_at,
            j.title AS job_title
        FROM job_applications ja
        JOIN users s ON s.id = ja.student_id
        JOIN jobs j ON j.id = ja.job_id
        WHERE j.company_user_id = ?
        ORDER BY ja.applied_at DESC
    ");
    $stmt->bind_param("i", $company_id);
}

$stmt->execute();
$applicants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Applicants | SkillSyncPro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    body {
        background-color: #0f0f0f;
        color: #fff;
        min-height: 100vh;
        font-family: 'Poppins', sans-serif;
    }

    h2 {
        margin-bottom: 20px;
        color: #00f7ff;
        text-shadow: 0 0 8px #00f7ff;
        
    }

    table th,
    table td {
        vertical-align: middle;
    }

    td:nth-child(6) {
        text-transform: capitalize;
    }

    /* ================= HEADER SPACING ================= */
    .content-wrapper {
        padding-top: 100px;
    }

    @media (max-width: 768px) {
        h2 {
            font-size: 1.5rem;
        }

        table th,
        table td {
            font-size: 0.85rem;
            padding: 0.35rem 0.5rem;
        }

        .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 1.3rem;
        }

        table th,
        table td {
            font-size: 0.75rem;
            padding: 0.3rem 0.4rem;
        }

        .btn-sm {
            padding: 0.2rem 0.35rem;
            font-size: 0.65rem;
        }
    }
    </style>
</head>

<body>
    <?php include 'particles.php'; ?>
    <!-- Header Include -->

    <?php include 'header.php'; ?>

    <div class="container content-wrapper">
        <h2>Applicants</h2>

        <div class="table-responsive">
            <table class="table table-dark table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Degree</th>
                        <th>Job</th>
                        <th>Status</th>
                        <th>Resume</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($applicants): foreach ($applicants as $i => $app): ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= htmlspecialchars($app['student_name']) ?></td>
                        <td><?= htmlspecialchars($app['email']) ?></td>
                        <td><?= htmlspecialchars($app['degree']) ?></td>
                        <td><?= htmlspecialchars($app['job_title']) ?></td>
                        <td><?= ucfirst($app['status']) ?></td>
                        <td>
                            <?php if ($app['resume_file']): ?>
                            <a href="../uploads/resumes/<?= htmlspecialchars($app['resume_file']) ?>"
                                target="_blank">View</a>
                            <?php else: ?> N/A <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($app['status'] === 'pending'): ?>
                            <a href="?action=approved&app_id=<?= $app['application_id'] ?>"
                                class="btn btn-success btn-sm">Approve</a>
                            <a href="?action=rejected&app_id=<?= $app['application_id'] ?>"
                                class="btn btn-danger btn-sm">Reject</a>
                            <?php else: ?> — <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="8" class="text-warning text-center">No applicants found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>