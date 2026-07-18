<?php
session_start();
require_once "../db.php";

/* ===== LOGIN CHECK ===== */
if (!isset($_SESSION['company_user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['company_user_id']);

/* ===== FETCH USER ===== */
$stmt = $conn->prepare("SELECT * FROM company_users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found!");
}
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Company Profile - SkillSyncPro</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        min-height: 100vh;
        background: linear-gradient(120deg, #00111f, #003366, #00111f);
        background-size: 400% 400%;
        animation: bgMove 20s ease infinite;
        color: #fff;
        overflow-x: hidden;
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

    /* ===== GRADIENT BACKGROUND ===== */
    .tbackground {
        position: fixed;
        inset: 0;
        background: linear-gradient(120deg, #00111f, #02182f, #00111f);
        background-size: 600% 600%;
        animation: bgMove 20s ease infinite;
        z-index: -3;
    }

    /* ===== PARTICLES ===== */
    #tsparticles {
        position: fixed;
        inset: 0;
        z-index: -2;
        pointer-events: none;
    }

    /* ===== CONTENT ===== */
    .content-wrapper {
        min-height: 100vh;
        padding-top: 120px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .card-profile {
        width: 100%;
        max-width: 800px;
        background: rgba(0, 0, 0, 0.02);
        backdrop-filter: blur(3px);
        border-radius: 20px;
        box-shadow: 0 0 25px rgba(0, 255, 255, 0.25);
        padding: 30px;
        transition: 0.3s;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: Algerian, serif;
    }

    .profile-section h5 {
        color: #00f7ff;
        border-bottom: 1px solid #00f7ff33;
        padding-bottom: 6px;
        margin-bottom: 15px;
    }

    .profile-section p {
        font-size: 0.95rem;
    }

    label {
        color: #00f7ff;
        margin-top: 8px;
    }

    input,
    textarea {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid #00f7ff33;
        color: #fff;
    }

    .btn-update {
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        color: #000;
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 600;
        border: none;
        transition: 0.3s;
    }

    .btn-update:hover {
        transform: scale(1.05);
        box-shadow: 0 0 15px #00ffffaa;
    }

    /* ===== MOBILE ===== */
    @media (max-width: 768px) {
        .card-profile {
            margin: 0 10px;
            padding: 20px;
        }

        h2 {
            font-size: 1.4rem;
        }
    }
    </style>
</head>

<body>

    <!-- PARTICLES -->
    <div id="tsparticles"></div>

    <!-- GRADIENT BG -->
    <div class="tbackground"></div>

    <?php include "header.php"; ?>

    <div class="content-wrapper">

        <!-- PROFILE VIEW -->
        <div class="card-profile" id="profileCard">
            <h2>Company & HR Profile</h2>

            <div class="profile-section">
                <h5>HR Details</h5>
                <p><b>Name:</b> <?= htmlspecialchars($user['hr_name']) ?></p>
                <p><b>Username:</b> <?= htmlspecialchars($user['username']) ?></p>
                <p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>
            </div>

            <div class="profile-section">
                <h5>Company Details</h5>
                <p><b>Company:</b> <?= htmlspecialchars($user['company_name']) ?></p>
                <p><b>Industry:</b> <?= htmlspecialchars($user['industry']) ?></p>
                <p><b>Size:</b> <?= htmlspecialchars($user['company_size']) ?></p>
                <p><b>Website:</b>
                    <a href="<?= htmlspecialchars($user['website']) ?>" target="_blank">
                        <?= htmlspecialchars($user['website']) ?>
                    </a>
                </p>
                <p><b>City:</b> <?= htmlspecialchars($user['city']) ?></p>
                <p><b>Description:</b><br><?= nl2br(htmlspecialchars($user['description'])) ?></p>
            </div>

            <div class="text-center mt-3">
                <button class="btn-update" onclick="showEdit()">Edit Profile</button>
            </div>
        </div>

        <!-- EDIT -->
        <div class="card-profile" id="editCard" style="display:none;">
            <h2>Edit Profile</h2>

            <form method="post" action="update_profile.php">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">

                <label>HR Name</label>
                <input class="form-control" name="hr_name" value="<?= $user['hr_name'] ?>">

                <label>Username</label>
                <input class="form-control" name="username" value="<?= $user['username'] ?>">

                <label>Email</label>
                <input class="form-control" name="email" value="<?= $user['email'] ?>">

                <label>Company Name</label>
                <input class="form-control" name="company_name" value="<?= $user['company_name'] ?>">

                <label>Industry</label>
                <input class="form-control" name="industry" value="<?= $user['industry'] ?>">

                <label>Company Size</label>
                <input class="form-control" name="company_size" value="<?= $user['company_size'] ?>">

                <label>Website</label>
                <input class="form-control" name="website" value="<?= $user['website'] ?>">

                <label>City</label>
                <input class="form-control" name="city" value="<?= $user['city'] ?>">

                <label>Description</label>
                <textarea class="form-control" rows="4" name="description"><?= $user['description'] ?></textarea>

                <div class="text-center mt-3">
                    <button class="btn-update" type="submit">Save</button>
                    <button class="btn-update" type="button" onclick="showProfile()">Cancel</button>
                </div>
            </form>
        </div>

    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toggle -->
    <script>
    function showEdit() {
        profileCard.style.display = "none";
        editCard.style.display = "block";
    }

    function showProfile() {
        editCard.style.display = "none";
        profileCard.style.display = "block";
    }
    </script>

    <!-- tsParticles -->
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
    <script>
    tsParticles.load("tsparticles", {
        particles: {
            number: {
                value: 80
            },
            color: {
                value: ["#00f7ff", "#ff00ff"]
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
                opacity: 0.2
            },
            move: {
                enable: true,
                speed: 1
            }
        }
    });
    </script>

</body>

</html>