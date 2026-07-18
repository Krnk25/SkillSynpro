<?php
session_start();
require_once "../db.php";

$error = "";

if (isset($_POST['login'])) {
    $identity = trim($_POST['identity']);
    $password = trim($_POST['password']);

    if (empty($identity) || empty($password)) {
        $error = "All fields are required";
    } else {
        $stmt = $conn->prepare("
            SELECT id, password 
            FROM company_users 
            WHERE username = ? OR email = ?
            LIMIT 1
        ");
        $stmt->bind_param("ss", $identity, $identity);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['company_user_id'] = $row['id'];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "Account not found";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Company Login | SkillSyncPro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
        background: linear-gradient(120deg, #00111f, #02182f, #00111f);
        background-size: 400% 400%;
        animation: bgMove 20s ease infinite;
        overflow: hidden;
        color: #fff;
    }

    @keyframes bgMove {
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

    /* PARTICLES */
    #tsparticles {
        position: fixed;
        inset: 0;
        z-index: -1;
        pointer-events: none;
    }

    .card {
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(18px);
        width: 100%;
        max-width: 480px;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 0 25px rgba(0, 255, 255, 0.25);
        animation: floatUp 4s ease-in-out infinite;
    }

    @keyframes floatUp {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-4px);
        }
    }

    h3 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 2rem;
        font-family: Algerian, serif;
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    input {
        width: 100%;
        padding: 11px 14px;
        margin-bottom: 12px;
        border-radius: 12px;
        border: 2px solid #00f7ff;
        background: rgba(0, 0, 0, 0.35);
        color: #fff;
        transition: 0.3s;
    }

    input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    input:focus {
        outline: none;
        border-color: #ff00ff;
        box-shadow: 0 0 12px #ff00ff88, 0 0 20px #00f7ff66;
    }

    .btn-info {
        width: 100%;
        padding: 12px 0;
        border-radius: 30px;
        border: none;
        font-weight: 700;
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        color: #000;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-info:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px #ff00ff88, 0 0 40px #00ffff88;
    }

    .links {
        margin-top: 15px;
        font-size: 0.9rem;
        text-align: center;
    }

    .links a {
        color: #00f7ff;
        text-decoration: none;
    }

    .links a:hover {
        text-decoration: underline;
    }

    .alert {
        background: rgba(255, 0, 0, 0.25);
        color: #ffd2d2;
        padding: 10px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 10px;
    }

    @media (max-width: 768px) {
        .card {
            padding: 20px;
            margin: 0 10px;
        }

        h3 {
            font-size: 1.6rem;
        }
    }
    </style>
</head>

<body>

    <!-- PARTICLES CONTAINER -->
    <div id="tsparticles"></div>

    <div class="card">
        <h3>🏢 Company Login</h3>

        <?php if ($error): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="identity" placeholder="Username or Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button class="btn-info" name="login">Login</button>
        </form>

        <div class="links">
            <p>
                Don't have an account?
                <a href="register.php">Create Account</a> |
                <a href="forgot-password.php">Forgot Password</a>
            </p>
            <p><a href="../index.php">← Back To Home</a></p>
        </div>
    </div>

    <!-- tsParticles LIBRARY (🔥 THIS WAS MISSING) -->
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>

    <script>
    tsParticles.load("tsparticles", {
        fpsLimit: 60,
        particles: {
            number: {
                value: 80,
                density: {
                    enable: true,
                    area: 800
                }
            },
            color: {
                value: ["#00f7ff", "#ff00ff", "#ffffff"]
            },
            shape: {
                type: "circle"
            },
            opacity: {
                value: 0.3
            },
            size: {
                value: {
                    min: 1,
                    max: 3
                }
            },
            links: {
                enable: true,
                distance: 120,
                color: "#ffffff",
                opacity: 0.15
            },
            move: {
                enable: true,
                speed: 1.2,
                outModes: {
                    default: "out"
                }
            }
        },
        detectRetina: true
    });
    </script>

</body>

</html>