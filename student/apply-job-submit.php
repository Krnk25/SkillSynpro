<?php
session_start();
require_once "../db.php";

/* ================= MESSAGE ================= */
function setMsg($msg){
    $_SESSION['msg'] = $msg;
}

/* ================= LOGIN CHECK ================= */
if (!isset($_SESSION['user_id'])) {
    setMsg("❌ Please login first");
    header("Location: ../login.php");
    exit;
}

$student_id = intval($_SESSION['user_id']); // ✅ users.id
$job_id     = intval($_POST['job_id'] ?? 0);

if ($job_id <= 0) {
    setMsg("❌ Invalid job");
    header("Location: apply-success.php");
    exit;
}

/* ================= CHECK JOB ================= */
$jobQ = $conn->prepare("SELECT title FROM jobs WHERE id=?");
$jobQ->bind_param("i", $job_id);
$jobQ->execute();
$job = $jobQ->get_result()->fetch_assoc();

if (!$job) {
    setMsg("❌ Job not found");
    header("Location: apply-success.php");
    exit;
}

/* ================= DUPLICATE APPLY CHECK ================= */
$dup = $conn->prepare(
    "SELECT id FROM job_applications WHERE student_id=? AND job_id=?"
);
$dup->bind_param("ii", $student_id, $job_id);
$dup->execute();
$dup->store_result();

if ($dup->num_rows > 0) {
    setMsg("⚠️ You already applied for this job");
    header("Location: apply-success.php?job_id=$job_id");
    exit;
}

/* ================= INSERT APPLICATION ================= */
$ins = $conn->prepare(
    "INSERT INTO job_applications
     (job_id, student_id, applied_at, status)
     VALUES (?, ?, NOW(), 'Applied')"
);
$ins->bind_param("ii", $job_id, $student_id);
$ins->execute();

/* ================= FETCH USER ================= */
$u = $conn->prepare("SELECT name, email FROM users WHERE id=?");
$u->bind_param("i", $student_id);
$u->execute();
$user = $u->get_result()->fetch_assoc();

/* ================= PHPMailer ================= */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__."/../PHPMailer/src/PHPMailer.php";
require_once __DIR__."/../PHPMailer/src/SMTP.php";
require_once __DIR__."/../PHPMailer/src/Exception.php";

$mailMsg = "";

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'kabadearjun0@gmail.com';   // 🔴 your gmail
    $mail->Password   = 'dgastplftiiruklk';         // 🔴 app password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('kabadearjun0@gmail.com', 'SkillSyncPro');
    $mail->addAddress($user['email'], $user['name']);

    $mail->isHTML(true);
    $mail->Subject = "Job Application Submitted - {$job['title']}";
    $mail->Body = "
        <h2>Application Submitted ✅</h2>
        <p>Hello <b>{$user['name']}</b>,</p>
        <p>You have successfully applied for:</p>
        <p><b>{$job['title']}</b></p>
        <p>Status: <b>Applied</b></p>
        <br>
        <p>SkillSyncPro Team</p>
    ";

    $mail->send();
    $mailMsg = "📧 Email sent successfully";

} catch (Exception $e) {
    $mailMsg = "⚠️ Email failed: " . $mail->ErrorInfo;
}

/* ================= FINAL MSG ================= */
setMsg("✅ Job applied successfully<br>$mailMsg");

/* ================= REDIRECT ================= */
header("Location: apply-success.php?job_id=$job_id");
exit;