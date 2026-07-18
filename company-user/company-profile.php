<?php
session_start();
require_once "../db.php";

// Check login
if (!isset($_SESSION['company_user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['company_user_id']);

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM company_users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found!";
    exit;
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Company Profile - SkillSyncPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    html,
    body {
        height: 100%;
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(120deg, #00111f, #003366, #00111f);
        background-size: 400% 400%;
        animation: bgMove 20s ease infinite;
        color: #fff;
        overflow-x: hidden;
        /* horizontal scroll fix */
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

    .content-wrapper {
        min-height: 100%;
        display: flex;
        justify-content: center;
        align-items: flex-start;

        /* horizontal + general padding */
        padding-top: 130px;
        /* header ke niche gap, header ki height ke hisaab se adjust karo */
        box-sizing: border-box;
    }


    .card-profile,
    #editCard {
        width: 100%;
        max-width: 800px;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        box-shadow: 0 0 25px rgba(0, 255, 255, 0.2);
        padding: 30px;
        transition: all 0.3s ease;
        overflow-y: auto;
        /* vertical scroll if content exceeds */
    }

    .card-profile h2,
    #editCard h2 {
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: 'Algerian', 'Times New Roman', serif;
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-section h5 {
        border-bottom: 1px solid #00f7ff33;
        padding-bottom: 5px;
        margin-bottom: 15px;
        color: #00f7ff;
    }

    .profile-section p {
        font-size: 1rem;
        color: #fff;
    }


    .btn-update {
        display: inline-block;
        margin-top: 20px;
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        color: #000;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 30px;
        text-decoration: none;
        transition: 0.3s;
        margin-right: 10px;
        white-space: nowrap;
    }


    .btn-update:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px #ff00ff88, 0 0 40px #00ffff88;
    }

    label {
        color: #00f7ff;
    }

    input,
    textarea {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid #00f7ff33;
        color: #fff;
        margin-bottom: 8px;
        padding: 6px 10px;
    }

    textarea {
        resize: none;
    }

    /* ================= MOBILE ================= */
    @media (max-width: 768px) {

        .card-profile,
        #editCard {
            padding: 20px;
            max-width: 95%;
        }

        .card-profile h2,
        #editCard h2 {
            font-size: 1.5rem;
        }

        .profile-section h5 {
            font-size: 1rem;
        }

        .profile-section p {
            font-size: 0.9rem;
        }

        input,
        textarea {
            font-size: 0.85rem;
            padding: 5px 8px;
        }

        .btn-update {
            padding: 7px 14px;
            font-size: 0.85rem;
        }
    }
    </style>
</head>

<body>

    <?php include 'particles.php'; ?>
    <?php include 'header.php'; ?>

    <div class="content-wrapper">
        <!-- Profile Card -->
        <div class="card-profile" id="profileCard">
            <h2>Company & HR Profile</h2>

            <div class="profile-section">
                <h5>HR Details</h5>
                <p><strong>Name:</strong> <?= htmlspecialchars($user['hr_name']) ?></p>
                <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            </div>

            <div class="profile-section">
                <h5>Company Details</h5>
                <p><strong>Company Name:</strong> <?= htmlspecialchars($user['company_name']) ?></p>
                <p><strong>Industry:</strong> <?= htmlspecialchars($user['industry']) ?></p>
                <p><strong>Company Size:</strong> <?= htmlspecialchars($user['company_size']) ?></p>
                <p><strong>Website:</strong> <a href="<?= htmlspecialchars($user['website']) ?>"
                        target="_blank"><?= htmlspecialchars($user['website']) ?></a></p>
                <p><strong>City:</strong> <?= htmlspecialchars($user['city']) ?></p>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($user['description'])) ?></p>
            </div>

            <div class="text-center">
                <div class="dropdown mt-3">
                    <button class="btn-update dropdown-toggle" type="button" id="editDropdown" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Edit Profile
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="editDropdown">
                        <li><a class="dropdown-item" href="#" onclick="showEdit('hr')">Edit HR Details</a></li>
                        <li><a class="dropdown-item" href="#" onclick="showEdit('company')">Edit Company Details</a>
                        </li>
                        <li><a class="dropdown-item" href="#" onclick="showEdit('all')">Edit Full Profile</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Edit Card -->
        <div class="card-profile" id="editCard" style="display:none;">
            <h2>Edit Profile</h2>
            <form action="update_profile_process.php" method="POST">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">

                <div id="hrSection" style="display:none;">
                    <h5>HR Details</h5>
                    <label>HR Name</label>
                    <input type="text" name="hr_name" class="form-control"
                        value="<?= htmlspecialchars($user['hr_name']) ?>" required>
                    <label>Username</label>
                    <input type="text" name="username" class="form-control"
                        value="<?= htmlspecialchars($user['username']) ?>" required>
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div id="companySection" style="display:none;">
                    <h5>Company Details</h5>
                    <label>Company Name</label>
                    <input type="text" name="company_name" class="form-control"
                        value="<?= htmlspecialchars($user['company_name']) ?>">
                    <label>Industry</label>
                    <input type="text" name="industry" class="form-control"
                        value="<?= htmlspecialchars($user['industry']) ?>">
                    <label>Company Size</label>
                    <input type="text" name="company_size" class="form-control"
                        value="<?= htmlspecialchars($user['company_size']) ?>">
                    <label>Website</label>
                    <input type="url" name="website" class="form-control"
                        value="<?= htmlspecialchars($user['website']) ?>">
                    <label>City</label>
                    <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['city']) ?>">
                    <label>Description</label>
                    <textarea name="description" class="form-control"
                        rows="4"><?= htmlspecialchars($user['description']) ?></textarea>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn-update">Save Changes</button>
                    <button type="button" class="btn-update" onclick="showProfile()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function showEdit(section) {
        document.getElementById('profileCard').style.display = 'none';
        document.getElementById('editCard').style.display = 'block';
        document.getElementById('hrSection').style.display = (section === 'hr' || section === 'all') ? 'block' : 'none';
        document.getElementById('companySection').style.display = (section === 'company' || section === 'all') ?
            'block' : 'none';
    }

    function showProfile() {
        document.getElementById('editCard').style.display = 'none';
        document.getElementById('profileCard').style.display = 'block';
    }
    </script>
</body>

</html>