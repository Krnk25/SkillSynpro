<?php
session_start();
require '../db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

/* FETCH USER */
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("<h2 style='color:#fff;text-align:center;margin-top:100px'>User not found</h2>");
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $name    = trim($_POST['name']);
    $gender  = $_POST['gender'];
    $age     = intval($_POST['age']);
    $mobile  = trim($_POST['mobile']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare("UPDATE users SET name=?, gender=?, age=?, mobile=?, email=?, address=? WHERE id=?");
    $stmt->bind_param("ssisssi", $name, $gender, $age, $mobile, $email, $address, $user_id);

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        // Refresh user data after update
        $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    } else {
        $message = "Update failed: " . $stmt->error;
    }
}

$photoPath = !empty($user['photo'])
    ? "../uploads/photos/{$user['photo']}?t=" . time()
    : "default-avatar.png";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile | SkillSyncPro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Roboto', sans-serif;
    }

    body {
        min-height: 100vh;
        background: linear-gradient(120deg, #0f2027, #203a43, #2c5364);
        color: #fff;
        overflow-x: hidden;
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

    /* animated gradient bg */
    .tbackground {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(120deg, #00111f, #02182f, #00111f);
        background-size: 600% 600%;
        animation: gradientBG 20s ease infinite;
        z-index: -3;
    }

    /* particles */
    #tsparticles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -2;
    }

    /* HEADER FIX SPACE */
    .page-wrapper {
        padding-top: 100px;
        padding-bottom: 30px;
        min-height: 100vh;
        display: flex;
        justify-content: center;
    }

    /* PROFILE CARD */
    .profile-wrapper {
        width: 100%;
        max-width: 900px;
        padding: 30px;
        border-radius: 25px;
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(6px);
        box-shadow: 0 10px 40px rgba(0, 255, 255, .15);
        animation: fadeUp .8s ease;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(25px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    /* HEADER */
    .profile-header {
        display: flex;
        align-items: center;
        gap: 25px;
    }

    .avatar-wrapper {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        padding: 5px;
        background: conic-gradient(#0ff, #f0f, #0ff);
    }

    .avatar-wrapper img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #000;
    }

    h1 {
        font-size: 32px;
        background: linear-gradient(90deg, #0ff, #f0f);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .username {
        color: #aaa
    }

    /* INFO */
    .section-title {
        margin-top: 30px;
        margin-bottom: 15px;
        font-size: 22px;
        background: linear-gradient(90deg, #f0f, #0ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 160px 1fr;
        row-gap: 12px;
    }

    .info-grid div:first-child {
        color: #aaa
    }

    /* BUTTON */
    .edit-btn {
        margin-top: 30px;
        padding: 14px 30px;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        font-weight: 600;
        background: linear-gradient(90deg, #f0f, #0ff);
        color: #000;
        transition: 0.3s;
    }

    .edit-btn:hover {
        opacity: 0.8;
    }

    /* FORM */
    .edit-form {
        display: none;
        margin-top: 25px;
        animation: fadeUp .6s ease;
    }

    .edit-form input,
    .edit-form select {
        width: 100%;
        padding: 12px;
        margin-bottom: 12px;
        border-radius: 10px;
        border: none;
        background: rgba(255, 255, 255, .12);
        color: #fff;
    }

    .edit-form button {
        padding: 14px;
        width: 100%;
        border: none;
        border-radius: 30px;
        background: linear-gradient(90deg, #f0f, #0ff);
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    .edit-form button:hover {
        opacity: 0.9;
    }

    /* MOBILE */
    @media(max-width:600px) {
        .profile-header {
            flex-direction: column;
            align-items: flex-start
        }

        .info-grid {
            grid-template-columns: 1fr
        }
    }
    </style>
</head>

<body>

    <?php include 'particles.php'; ?>
    <?php include 'header.php'; ?>

    <div class="page-wrapper">
        <div class="profile-wrapper">

            <div class="profile-header">
                <div class="avatar-wrapper">
                    <img src="<?= $photoPath ?>" alt="Profile Photo">
                </div>
                <div>
                    <h1><?= htmlspecialchars($user['name']) ?></h1>
                    <div class="username">@<?= htmlspecialchars($user['username']) ?></div>
                </div>
            </div>

            <h2 class="section-title">Basic Info</h2>
            <div class="info-grid">
                <div>Gender</div>
                <div><?= $user['gender'] ?></div>
                <div>Age</div>
                <div><?= $user['age'] ?></div>
                <div>Degree</div>
                <div><?= $user['degree'] ?></div>
            </div>

            <h2 class="section-title">Contact</h2>
            <div class="info-grid">
                <div>Mobile</div>
                <div><?= $user['mobile'] ?></div>
                <div>Email</div>
                <div><?= $user['email'] ?></div>
                <div>Address</div>
                <div><?= $user['address'] ?></div>
            </div>

            <?php if($message): ?>
            <p style="margin-top:10px;color:#0ff;font-weight:600"><?= $message ?></p>
            <?php endif; ?>

            <button class="edit-btn"
                onclick="document.querySelector('.edit-form').style.display='block';this.style.display='none'">Edit
                Profile</button>

            <form class="edit-form" method="POST">
                <input name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                <select name="gender">
                    <option <?= $user['gender']=='Male'?'selected':'' ?>>Male</option>
                    <option <?= $user['gender']=='Female'?'selected':'' ?>>Female</option>
                    <option <?= $user['gender']=='Other'?'selected':'' ?>>Other</option>
                </select>
                <input name="age" value="<?= htmlspecialchars($user['age']) ?>" type="number">
                <input name="mobile" value="<?= htmlspecialchars($user['mobile']) ?>">
                <input name="email" value="<?= htmlspecialchars($user['email']) ?>" type="email">
                <input name="address" value="<?= htmlspecialchars($user['address']) ?>">
                <button name="update_profile">Save</button>
            </form>

        </div>
    </div>

    <!-- tsParticles library -->
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
    <script>
    tsParticles.load("tsparticles", {
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
            }
        },
        detectRetina: true
    });
    </script>

</body>

</html>