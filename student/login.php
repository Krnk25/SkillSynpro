<?php
require_once "../db.php";
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // SAFE INPUT
    $login    = trim($_POST["login"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($login === "" || $password === "") {
        $message = "Please fill all fields.";
    } else {

        $sql = "SELECT id, password 
                FROM users 
                WHERE email = ? OR username = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $message = "Database error: " . $conn->error;
        } else {

            $stmt->bind_param("ss", $login, $login);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {

                $stmt->bind_result($user_id, $hashed_password);
                $stmt->fetch();

                // CHECK IF PASSWORD IS A STRING
                if (!is_string($hashed_password) || empty($hashed_password)) {
                    $message = "Password not set properly in database.";
                } else {

                    if (password_verify($password, $hashed_password)) {
                        // SUCCESS: Login user
                        session_regenerate_id(true);
                        $_SESSION["user_id"] = $user_id;
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $message = "Invalid Username / Email or Password.";
                    }

                }

            } else {
                $message = "Invalid Username / Email or Password.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>

    <style>
    body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Poppins', sans-serif;
        overflow: hidden;
        color: #fff;
    }



    @keyframes gradientBG {
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



    .login-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        padding: 40px 25px;
        max-width: 420px;
        width: 100%;
        text-align: center;
        box-shadow: 0 0 30px rgba(255, 255, 255, 0.1);
        animation: floatUp 4s ease-in-out infinite;
    }

    @keyframes floatUp {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-8px);
        }
    }

    h2 {
        font-weight: 700;
        margin-bottom: 20px;
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        border: none;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        box-shadow: 0 0 10px #00f7ff;
    }

    .btn-neon {
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        color: #000;
        font-weight: 700;
        border-radius: 30px;
        width: 100%;
    }

    .alert {
        background: rgba(255, 0, 0, 0.2);
        border: none;
        color: #ffcccc;
    }

    a {
        color: #00f7ff;
        text-decoration: none;
    }
    </style>
</head>

<body>


    <?php include 'particles.php'; ?>



    <div class="login-card">
        <h2>Student Login</h2>

        <?php if ($message): ?>
        <div class="alert alert-danger"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3 text-start">
                <label>Email / Username</label>
                <input type="text" name="login" class="form-control" placeholder="Enter Username or Email" required>
            </div>

            <div class="mb-3 text-start">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
            </div>

            <button type="submit" class="btn btn-neon mt-3">Login</button>

            <p class="mt-3">
                Don't have an account?
                <a href="register.php">Register</a><br>
            <p><a href="forgot-password.php">Forgot Password</a> | <a href="../index.php">Back To Home</a></p>
            </p>

        </form>
    </div>



</body>

</html>