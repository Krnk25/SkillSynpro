<?php
session_start();
require_once "../db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__."/../PHPMailer/src/PHPMailer.php";
require_once __DIR__."/../PHPMailer/src/SMTP.php";
require_once __DIR__."/../PHPMailer/src/Exception.php";

date_default_timezone_set("Asia/Kolkata");

$msg = "";
$msg_type = "";

/* ================= SEND RESET LINK ================= */
if (isset($_POST['send_link'])) {

    $email = strtolower(trim($_POST['email']));

    /* 🔐 BASIC VALIDATION */
    if (empty($email)) {
        $msg = "❌ Email is required";
        $msg_type = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "❌ Invalid email format";
        $msg_type = "danger";
    } else {

        $q = $conn->prepare("SELECT id FROM company_users WHERE email=?");
        $q->bind_param("s", $email);
        $q->execute();
        $res = $q->get_result();

        if ($res->num_rows == 0) {
            $msg = "❌ Email not found in system";
            $msg_type = "danger";
        } else {

            $user = $res->fetch_assoc();
            $token  = sha1(uniqid(true));
            $expire = date("Y-m-d H:i:s", time() + 3600); // 1 hour

            $up = $conn->prepare(
                "UPDATE company-users SET reset_token=?, reset_expire=? WHERE id=?"
            );
            $up->bind_param("ssi", $token, $expire, $user['id']);
            $up->execute();

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = "smtp.gmail.com";
                $mail->SMTPAuth = true;
                $mail->Username = "kabadearjun0@gmail.com"; // Gmail
                $mail->Password = "dgastplftiiruklk";      // App password
                $mail->SMTPSecure = "tls";
                $mail->Port = 587;

                $mail->setFrom("kabadearjun0@gmail.com", "SkillSyncPro");
                $mail->addAddress($email);

                $link = "http://localhost/Project_2025/SkillSyncPro/student/reset-password.php?token=$token";

                $mail->isHTML(true);
                $mail->Subject = "🔐 Reset Your Password - SkillSyncPro";
                $mail->Body = "
                    <div style='font-family:Arial'>
                        <h2>Password Reset Request</h2>
                        <p>You requested to reset your password.</p>
                        <p>
                            <a href='$link' 
                               style='padding:10px 15px;
                                      background:#0d6efd;
                                      color:#fff;
                                      text-decoration:none;
                                      border-radius:5px;'>
                               Reset Password
                            </a>
                        </p>
                        <p>This link is valid for 1 hour.</p>
                        <p>— SkillSyncPro Team</p>
                    </div>
                ";

                $mail->send();
                $msg = "✅ Reset link sent successfully to your email";
                $msg_type = "success";

            } catch (Exception $e) {
                $msg = "❌ Mail sending failed. Try again later.";
                $msg_type = "danger";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password | SkillSyncPro</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        width: 100%;
        max-width: 420px;
        border-radius: 15px;
    }
    </style>
</head>

<body>

    <div class="card shadow-lg p-4 bg-dark text-white">
        <h3 class="text-center text-info mb-3">🔐 Forgot Password</h3>

        <p class="text-center text-muted">
            Enter your registered email to receive reset link
        </p>

        <?php if (!empty($msg)): ?>
        <div class="alert alert-<?= $msg_type ?> text-center">
            <?= $msg ?>
        </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
            </div>

            <button type="submit" name="send_link" class="btn btn-info w-100 fw-bold">
                Send Reset Link
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="../company-user/login.php" class="text-decoration-none text-light">
                ⬅ Back to Login
            </a>
        </div>
    </div>

</body>

</html>