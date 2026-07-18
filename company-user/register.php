<?php
session_start();
require_once "../db.php";

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $hr_name       = trim($_POST['name']);
    $email         = trim($_POST['email']);
    $username      = trim($_POST['username']);
    $password      = trim($_POST['password']);
    $company_name  = trim($_POST['company_name']);
    $industry      = trim($_POST['industry']);
    $company_size  = trim($_POST['company_size']);
    $website       = trim($_POST['website']);
    $city          = trim($_POST['city']);
    $description   = trim($_POST['description']);

    // Validate required fields
    if (empty($hr_name) || empty($email) || empty($username) || empty($password) || empty($company_name) || empty($industry)) {
        $message = "All required fields must be filled.";
    } else {
        // Check if email or username already exists
        $check = $conn->prepare("SELECT id FROM company_users WHERE email=? OR username=?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email or Username already exists.";
        } else {
            // Insert into company_users table
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO company_users (hr_name, username, email, password, company_name, industry, company_size, website, city, description, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,NOW())");
            $stmt->bind_param("ssssssssss", $hr_name, $username, $email, $hashed, $company_name, $industry, $company_size, $website, $city, $description);

            if ($stmt->execute()) {
                // Registration success, set session and redirect
                $_SESSION['user_id'] = $stmt->insert_id;

                // Redirect to login
                header("Location: login.php");
                exit;
            } else {
                $message = "Registration failed. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Company Register - SkillSyncPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
    <style>
    /* Keep your existing CSS from the previous code */
    body {
        margin: 0;
        min-height: 100vh;
        font-family: 'Poppins', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(120deg, #00111f, #003366, #00111f);
        background-size: 400% 400%;
        animation: bgMove 20s ease infinite;
        overflow: hidden;
        color: #fff;
    }

    @keyframes bgMove {
        0% {
            background-position: 0% 50%
        }

        50% {
            background-position: 100% 50%
        }

        100% {
            background-position: 0% 50%
        }
    }

    #tsparticles {
        position: fixed;
        inset: 0;
        z-index: -1;
    }

    .card {
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(20px);
        color: #fff;
        width: 100%;
        max-width: 480px;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 0 25px rgba(0, 255, 255, 0.2);
        animation: floatUp 4s ease-in-out infinite;
    }

    @keyframes floatUp {

        0%,
        100% {
            transform: translateY(0)
        }

        50% {
            transform: translateY(-4px);
        }
    }

    h3 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 2rem;
        font-family: 'Algerian', 'Times New Roman', serif;
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    input,
    textarea,
    select {
        background: rgba(0, 0, 0, 0.3);
        color: #fff;
        border: 2px solid #00f7ff;
        padding: 6px 12px;
        margin-bottom: 10px;
        border-radius: 10px;
        font-size: 0.9rem;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 0 4px #00f7ff55;
    }

    input::placeholder,
    textarea::placeholder {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.85rem;
    }

    input:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: #ff00ff;
        box-shadow: 0 0 10px #ff00ff88, 0 0 20px #00f7ff55;
        background: rgba(0, 0, 0, 0.45);
    }

    select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='10' viewBox='0 0 14 10'%3E%3Cpath fill='%23fff' d='M7 10L0 0h14z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 12px;
    }

    .btn-info {
        width: 100%;
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        border: none;
        padding: 10px 0;
        font-weight: 700;
        border-radius: 30px;
        transition: 0.3s;
        color: #000;
    }

    .btn-info:hover {
        transform: scale(1.05) rotate(-1deg);
        box-shadow: 0 0 20px #ff00ff88, 0 0 40px #00ffff88;
    }

    .links {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        font-size: 0.9rem;
    }

    .alert {
        background: rgba(255, 0, 0, 0.2);
        color: #ffbbbb;
        border: none;
    }

    @media(max-width:768px) {
        .card {
            padding: 15px;
        }

        .links {
            flex-direction: column;
            gap: 5px;
        }
    }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="card">
        <h3>Company Registration</h3>

        <?php if($message): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <h6>Company Details</h6>
            <input name="company_name" placeholder="Company Name" required>
            <input name="industry" placeholder="Industry (IT, Finance etc)" required>
            <select name="company_size">
                <option value="">Company Size</option>
                <option>1-10</option>
                <option>10-50</option>
                <option>50-200</option>
                <option>200+</option>
            </select>
            <input name="website" placeholder="Company Website">
            <input name="city" placeholder="City">
            <textarea name="description" placeholder="Company Description"></textarea>

            <h6>HR / Login Details</h6>
            <input name="name" placeholder="HR Name" required>
            <input name="email" placeholder="Official Email" required>
            <input name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>

            <button class="btn btn-info mt-2">Register Company</button>
            <div class="links">
                <p>Already have an account? <a style="color: #00f7ff;" href="login.php"> Login</a><br></p>

                <p><a style="color: #00f7ff;" href="../index.php"> ← Back to Home</a></p>
            </div>
        </form>
    </div>

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
                color: "#fff",
                opacity: 0.15,
                width: 1
            },
            move: {
                enable: true,
                speed: 1.2,
                outModes: {
                    default: "out"
                }
            }
        },
        interactivity: {
            events: {
                resize: true
            },
            detectRetina: true
        }
    });
    </script>
</body>

</html>