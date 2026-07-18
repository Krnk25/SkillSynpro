<?php
session_start();
require_once "../db.php";

// LOGIN CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

/* ===== PLAN SELECT HANDLE ===== */
if (isset($_GET['plan'])) {

    $plan = strtolower(trim($_GET['plan']));
    $allowed_plans = ['basic', 'standard', 'premium'];

    if (in_array($plan, $allowed_plans)) {

        // Delete old plan
        $del = $conn->prepare("DELETE FROM subscriptions WHERE user_id = ?");
        $del->bind_param("i", $user_id);
        $del->execute();
        $del->close();

        // Insert new plan
        $stmt = $conn->prepare("
            INSERT INTO subscriptions (user_id, plan_name)
            VALUES (?, ?)
        ");
        $stmt->bind_param("is", $user_id, $plan);
        $stmt->execute();
        $stmt->close();

        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Subscription Plans</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>

    <style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;

        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: #fff;
    }





    /* ===== MAIN CONTAINER ===== */
    .container {
        position: relative;
        z-index: 2;
        max-width: 1200px;
        margin: 0 auto;
        padding: 140px 20px 40px;
        /* 🔥 HEADER GAP FIX */
        text-align: center;
    }

    h1 {
        font-size: 2.8rem;
        background: linear-gradient(90deg, #a2ff00, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .plans {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-top: 50px;
    }

    /* PLAN CARD */
    .plan-card {
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(3px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 25px;
        border-radius: 20px;
        transition: 0.4s;
    }

    .plan-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
    }

    .plan-title {
        font-size: 1.8rem;
        font-weight: 600;
        background: linear-gradient(90deg, #ff00ff, #a2ff00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .plan-price {
        font-size: 2.3rem;
        margin: 20px 0;
    }

    .btn-subscribe {
        display: inline-block;
        padding: 14px 30px;
        border-radius: 40px;
        background: linear-gradient(135deg, #00c6ff, #0072ff);
        color: #fff;
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
        box-shadow: 0 0 20px #00c6ff99;
    }

    .btn-subscribe:hover {
        transform: scale(1.05);
    }

    .btn-neon {
        margin-top: 40px;
        display: inline-block;
        padding: 14px 40px;
        border: 1px solid #00ffff;
        color: #fff;
        text-decoration: none;
        box-shadow: 0 0 15px #00ffff88;
    }

    @media(max-width:600px) {
        h1 {
            font-size: 2rem;
        }

        .plan-price {
            font-size: 1.7rem;
        }
    }

    @media (max-width: 992px) {
        .main-content {
            padding-top: 160px;
            /* mobile header height thodi zyada hoti hai */
        }
    }
    </style>
</head>

<body>

    <?php include 'particles.php'; ?>

    <?php include 'header.php'; ?>

    <div class="container">
        <h1>Choose Your Subscription Plan</h1>

        <div class="plans">

            <div class="plan-card">
                <div class="plan-title">Basic Plan</div>
                <div class="plan-price">₹199 / month</div>
                <a href="?plan=basic" class="btn-subscribe">Subscribe</a>
            </div>

            <div class="plan-card">
                <div class="plan-title">Standard Plan</div>
                <div class="plan-price">₹499 / month</div>
                <a href="?plan=standard" class="btn-subscribe">Subscribe</a>
            </div>

            <div class="plan-card">
                <div class="plan-title">Premium Plan</div>
                <div class="plan-price">₹999 / month</div>
                <a href="?plan=premium" class="btn-subscribe">Subscribe</a>
            </div>

        </div>

        <a href="dashboard.php" class="btn-neon">⬅ Back To Dashboard</a>
    </div>



</body>

</html>