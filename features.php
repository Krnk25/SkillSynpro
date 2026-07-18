<?php
session_start();

// Optional: redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$plan = $_GET['plan'] ?? 'unknown';
$userName = $_SESSION['user_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= ucfirst($plan) ?> Plan Features</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html,
    body {
        height: 100%;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background:
            radial-gradient(circle at 15% 15%, rgba(138, 43, 226, 0.18), transparent 40%),
            radial-gradient(circle at 85% 85%, rgba(0, 255, 255, 0.18), transparent 40%),
            linear-gradient(135deg, #07000f, #090018, #070021);
        background-size: 200% 200%;
        animation: movebg 10s ease infinite;
        color: #fff;
        padding: 50px 15px 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        overflow-x: hidden;
    }

    @keyframes movebg {
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

    header {
        text-align: center;
        margin-bottom: 40px;
        position: relative;
        width: 100%;
        max-width: 1100px;
    }

    .welcome {
        position: absolute;
        top: -10px;
        right: 0;
        font-size: 0.9rem;
        color: #ccc;
    }

    h1 {
        font-size: 3rem;
        background: linear-gradient(90deg, #9f6cff, #00ffff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 0 30px rgba(255, 255, 255, 0.06);
        margin-bottom: 8px;
    }

    p.sub {
        color: #cfcfcf;
        font-size: 1.1rem;
        text-align: center;
        margin-bottom: 40px;
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 28px;
        width: 100%;
        max-width: 1100px;
    }

    .feature-card {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 22px;
        padding: 30px 25px;
        backdrop-filter: blur(18px);
        box-shadow: 0 6px 40px rgba(0, 0, 0, 0.45);
        text-align: left;
        position: relative;
        transition: transform 0.4s cubic-bezier(.2, .9, .2, 1), box-shadow .4s ease;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -40%;
        width: 200%;
        height: 200%;
        background: conic-gradient(from 0deg, #9f6cff, #00ffff, #ff00ff, #9f6cff);
        opacity: 0.15;
        filter: blur(40px);
        z-index: 0;
        transform: rotate(0deg);
        animation: spin 8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .feature-card:hover {
        transform: translateY(-10px) scale(1.03);
        box-shadow: 0 20px 70px rgba(159, 108, 255, 0.25);
    }

    .feature-card h3 {
        font-size: 1.4rem;
        margin-bottom: 10px;
        color: #fff;
        position: relative;
        z-index: 1;
    }

    .feature-card p {
        color: #ddd;
        line-height: 1.6;
        position: relative;
        z-index: 1;
    }

    .btn-back {
        margin-top: 60px;
        display: inline-block;
        padding: 12px 34px;
        border-radius: 28px;
        background: linear-gradient(90deg, #9f6cff 0%, #00e5ff 60%, #ff00ff 100%);
        color: #fff;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border: none;
        cursor: pointer;
        box-shadow: 0 8px 30px rgba(159, 108, 255, 0.18);
        transition: all 0.25s ease;
        text-decoration: none;
    }

    .btn-back:hover {
        transform: translateY(-4px) scale(1.03);
        box-shadow: 0 18px 50px rgba(159, 108, 255, 0.3);
    }

    footer {
        text-align: center;
        color: #bfb8ff;
        margin-top: 60px;
        font-size: 0.9rem;
    }

    @media (max-width: 800px) {
        h1 {
            font-size: 2.2rem;
        }

        .feature-card {
            padding: 25px 20px;
        }
    }
    </style>
</head>

<body>
    <header>
        <div class="welcome">Welcome, @<?php echo htmlspecialchars($user['username']); ?></div>
        <h1><?= ucfirst($plan) ?> Plan Features</h1>
        <p class="sub">Explore everything included in your selected plan — designed for modern professionals.</p>
    </header>

    <section class="feature-grid">
        <div class="feature-card">
            <h3>⚡ Lightning Performance</h3>
            <p>Experience ultra-fast page loads and responsive interactions, optimized for all devices and browsers.</p>
        </div>
        <div class="feature-card">
            <h3>🔒 Secure Data Access</h3>
            <p>Your data is encrypted and handled with top-level SSL protection, ensuring complete privacy and security.
            </p>
        </div>
        <div class="feature-card">
            <h3>💬 Premium Support</h3>
            <p>24/7 dedicated assistance for your technical or account queries, available via chat, mail, or call.</p>
        </div>
        <div class="feature-card">
            <h3>🎨 Custom Dashboard</h3>
            <p>Personalized dashboard view with detailed analytics and quick navigation for all your account actions.
            </p>
        </div>
        <div class="feature-card">
            <h3>☁️ Cloud Storage</h3>
            <p>Get fast, reliable, and scalable storage space for your content with automated backups every 24 hours.
            </p>
        </div>
        <div class="feature-card">
            <h3>🚀 Instant Upgrades</h3>
            <p>Switch between plans seamlessly without data loss — your preferences and data remain intact.</p>
        </div>
    </section>

    <a href="subscription.php" class="btn-back">← Back to Plans</a>

    <footer>© <?= date('Y') ?> Karan Kabade — subscription Aurora Edition</footer>
</body>

</html>