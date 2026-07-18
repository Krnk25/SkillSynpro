<?php
require_once "../db.php";

$token = $_GET['token'] ?? '';

if (!$token) {
    die("❌ Token missing");
}

/* ================= TOKEN CHECK ================= */
$stmt = $conn->prepare("SELECT id FROM company_users WHERE reset_token=?");
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    die("❌ Invalid token (not found in database)");
}

$user = $res->fetch_assoc();

/* ================= UPDATE PASSWORD ================= */
if (isset($_POST['update'])) {

    $pass = $_POST['password'];
    $cpass = $_POST['confirm_password'];

    if ($pass !== $cpass) {
        $error = "Passwords do not match";
    } elseif (strlen($pass) < 6) {
        $error = "Password must be at least 6 characters";
    } else {

        $hash = password_hash($pass, PASSWORD_BCRYPT);

        $up = $conn->prepare("
            UPDATE company_users 
            SET password=?, reset_token=NULL, reset_expire=NULL 
            WHERE id=?
        ");
        $up->bind_param("si", $hash, $user['id']);
        $up->execute();

        echo "✅ Password changed successfully <br><a href='../company-user/login.php'>Login</a>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password | SkillSyncPro</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .reset-box {
        background: #fff;
        width: 380px;
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .reset-box h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .reset-box p {
        text-align: center;
        margin-bottom: 15px;
        font-size: 14px;
        color: red;
    }

    .input-group {
        margin-bottom: 15px;
    }

    .input-group input {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        outline: none;
        font-size: 14px;
        transition: 0.3s;
    }

    .input-group input:focus {
        border-color: #667eea;
        box-shadow: 0 0 5px rgba(102, 126, 234, 0.5);
    }

    .btn {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
        border-radius: 8px;
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn:hover {
        opacity: 0.9;
        transform: scale(1.02);
    }

    .footer-text {
        text-align: center;
        margin-top: 15px;
        font-size: 13px;
        color: #666;
    }
    </style>
</head>

<body>

    <div class="reset-box">
        <h2>🔒 Reset Password</h2>

        <?php if (!empty($error)) echo "<p>$error</p>"; ?>

        <form method="post">
            <div class="input-group">
                <input type="password" name="password" placeholder="New Password" required>
            </div>

            <div class="input-group">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <button type="submit" name="update" class="btn">
                Update Password
            </button>
        </form>

        <div class="footer-text">
            SkillSyncPro © <?php echo date("Y"); ?>
        </div>
    </div>

</body>

</html>