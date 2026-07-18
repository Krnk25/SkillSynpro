<?php
require_once "../db.php";
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $degree = trim($_POST['degree']);
    $gender = trim($_POST['gender']);
    $age = trim($_POST['age']);
    $mobile = trim($_POST['mobile']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($password === '') {
        $message = "Password cannot be empty.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $message = "Username or Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $insert = $conn->prepare("INSERT INTO users (name, degree,gender,age, mobile, address, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?,?,?)");
            $insert->bind_param("sssssssss", $name, $degree, $gender, $age, $mobile, $address, $email, $username, $hashed_password);
            if ($insert->execute()) {
                header("Location: login.php");
            } else {
                $message = "Error: " . $conn->error;
                
            }
            $insert->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SkillSyncPro</title>
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




    /* login card */
    .login-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        color: #fff;
        padding: 25px;
        width: 100%;
        max-width: 400px;
        /* remove min-height */
        text-align: center;
        box-shadow: 0 0 30px rgba(255, 255, 255, 0.1),
            0 0 80px rgba(0, 255, 255, 0.05);
        animation: floatUp 4s ease-in-out infinite;
        position: relative;
        z-index: 1;
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

    .login-card h2 {
        font-weight: 700;
        margin-bottom: 20px;
        font-size: 2rem;
        font-family: 'Algerian', 'Times New Roman', serif;
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* form controls */


    /* input fields */
    .form-control {
        border: 2px solid transparent;
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        border-radius: 8px;
        padding: 7px 10px;
        /* pehle 10px 15px tha, thoda kam kiya */
        font-size: 0.95rem;
        /* thoda chhota font */
        transition: border 0.3s, box-shadow 0.3s;
    }

    .form-control::placeholder {
        color: #ccc;
        /* placeholder halka grey */
    }

    .form-control:hover,
    .form-control:focus {
        border: 2px solid #00f7ff;
        box-shadow: 0 0 10px #00f7ff, 0 0 20px #ff00ff;
        outline: none;
    }

    .btn-neon {
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        color: #000;
        font-weight: 700;
        border: none;
        border-radius: 30px;
        transition: 0.3s;
        width: 100%;
        padding: 12px 0;
        font-size: 1rem;
        box-shadow: 0 0 15px #00ffff88;
    }

    .btn-neon:hover {
        box-shadow: 0 0 20px #ff00ff88, 0 0 40px #00ffff88;
        background: linear-gradient(90deg, #ff00ff, #00ffff88);
        transform: scale(1.05) rotate(-1deg);
    }

    a {
        color: #00f7ff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    .alert {
        background: rgba(255, 0, 0, 0.2);
        color: #ffbbbb;
        border: none;
    }

    @media(max-width: 500px) {
        .login-card {
            padding: 20px;
            min-height: auto;
        }
    }
    </style>
</head>

<body>

    <?php include 'particles.php'; ?>



    <div class="login-card">
        <h2> Student Register</h2>

        <?php if ($message): ?>
        <div class="alert alert-danger">
            <?= $message ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3 text-start">
                <input type="text" name="name" class="form-control" placeholder="Enter Your Full Name" required>
            </div>
            <div class="mb-3 text-start">
                <input type="text" name="degree" class="form-control" placeholder="Enter Your Degree/Diploma" required>
            </div>
            <div class="mb-3 text-start">
                <select name="gender" class="form-control" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3 text-start">
                <input type="text" name="age" class="form-control" placeholder="Enter Your Age" required>
            </div>
            <div class="mb-3 text-start">
                <input type="text" name="mobile" class="form-control" placeholder="Enter Your Mobile Number" required>
            </div>
            <div class="mb-3 text-start">
                <input type="text" name="address" class="form-control" placeholder="Enter Your Address" required>
            </div>
            <div class="mb-3 text-start">
                <input type="email" name="email" class="form-control" placeholder="Enter Your Email" required>
            </div>
            <div class="mb-3 text-start">
                <input type="text" name="username" class="form-control" placeholder="Enter Your Username" required>
            </div>
            <div class="mb-3 text-start">
                <input type="password" name="password" class="form-control" placeholder="Enter Your Password" required>
            </div>
            <button type="submit" class="btn btn-neon mt-3">Register</button>
            <p class="mt-3">Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>




</body>

</html>