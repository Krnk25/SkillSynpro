<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "../db.php";

/* ================= CHECK SESSION ================= */
if (!isset($_SESSION['user_id'])) {
    die("❌ USER NOT LOGGED IN");
}

$user_id = $_SESSION['user_id'];
$job_id  = intval($_POST['job_id'] ?? 0);

if ($job_id <= 0) {
    die("❌ INVALID JOB ID");
}

echo "✅ STEP 1: SESSION OK<br>";

/* ================= FETCH USER ================= */
$u = $conn->prepare("SELECT name,email FROM users WHERE id=?");
$u->bind_param("i", $user_id);
$u->execute();
$user = $u->get_result()->fetch_assoc();

if (!$user) {
    die("❌ USER DATA NOT FOUND");
}

echo "✅ STEP 2: USER FETCHED ({$user['email']})<br>";

/* ================= FETCH JOB ================= */
$j = $conn->prepare("
    SELECT 
        title,
        location,
        job_type,
        salary,
        required_skills,
        company,
        hr_name,
        hr_email
    FROM jobs
    WHERE id=?
");

$j->bind_param("i", $job_id);
$j->execute();
$job = $j->get_result()->fetch_assoc();

if (!$job) {
    die("❌ JOB NOT FOUND");
}

echo "✅ STEP 3: JOB FETCHED ({$job['title']})<br>";

/* ================= INSERT APPLICATION ================= */
$ins = $conn->prepare(
    "INSERT INTO job_applications (user_id, job_id, applied_at)
     VALUES (?, ?, NOW())"
);
$ins->bind_param("ii", $user_id, $job_id);
$ins->execute();

echo "✅ STEP 4: APPLICATION INSERTED<br>";

/* ================= PHPMailer ================= */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "../PHPMailer/src/PHPMailer.php";
require_once "../PHPMailer/src/SMTP.php";
require_once "../PHPMailer/src/Exception.php";

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'kabadearjun0@gmail.com'; // ✅ SAME AS TEST
    $mail->Password   = 'dgastplftiiruklk';      // ✅ SAME AS TEST
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // 🔥 FULL DEBUG
    $mail->SMTPDebug  = 2;
    $mail->Debugoutput = 'html';

    $mail->setFrom('kabadearjun0@gmail.com', 'SkillSyncPro');
    $mail->addAddress($user['email'], $user['name']);

    if (!empty($job['hr_email'])) {
        $mail->addCC($job['hr_email']);
    }

    $mail->isHTML(true);
    $mail->Subject = "Job Applied Successfully - {$job['title']}";
    $mail->Body = "
        <h2>Application Submitted</h2>
        <p>Hello {$user['name']},</p>
        <p>You applied for <b>{$job['title']}</b> at {$job['company_name']}.</p>
    ";

    $mail->send();

    echo "<br><h2 style='color:green'>✅ MAIL SENT SUCCESSFULLY</h2>";

} catch (Exception $e) {
    echo "<br><h2 style='color:red'>❌ MAIL FAILED</h2>";
    echo "<pre>".$mail->ErrorInfo."</pre>";
}

echo "<hr><b>🔥 SCRIPT COMPLETED 🔥</b>";