<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
/* ===== GLOBAL ===== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

html,
body {
    overflow-x: hidden;


    color: #fff;
}

:root {
    --main-accent: #00ff00;
    --hover-glow: #00ff88;
    --dark-bg: #0a0f1e;
    --text-light: #fff;
}

/* ===== HEADER & NAVIGATION ===== */
header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    /* Logo left mein, Nav right mein */
    padding: 0 5%;
    z-index: 9999;
    background: rgba(10, 15, 30, 0.2);
    /* Darker background for readability */
    backdrop-filter: blur(3px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: 0.3s ease-in-out;
}

header:hover {
    box-shadow: 0 0 20px rgba(0, 255, 0, 0.2);
}

/* ===== LOGO ===== */
.logo {
    font-size: 1.8rem;
    font-weight: 700;
    cursor: pointer;
    flex-shrink: 0;
    /* Logo ko dabne se rokta hai */
}

.logospan {
    background: linear-gradient(90deg, #1fff06, #00f7ff, #ff00ff);
    background-size: 300% 300%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradientMove 5s ease infinite;
}

@keyframes gradientMove {
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

/* ===== NAVBAR ===== */
.navbar {
    display: flex;
    align-items: center;
}

.navbar ul {
    list-style: none;
    display: flex;
    gap: 30px;
    /* Menu items ke beech barabar gap */
    align-items: center;
    margin: 0;
    padding: 0;
}

.navbar li a {
    color: var(--text-light);
    text-decoration: none;
    font-size: 1rem;
    font-weight: 500;
    position: relative;
    transition: 0.3s;
    white-space: nowrap;
    /* Text ko ek line mein rakhta hai */
}

/* Hover Underline Effect */
.navbar li a::after {
    content: "";
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0%;
    height: 2px;
    background: var(--main-accent);
    transition: width 0.3s ease;
}

.navbar li a:hover::after,
.navbar li.active a::after {
    width: 100%;
}

.navbar li a:hover,
.navbar li.active a {
    color: var(--main-accent);
}

/* Logout Button */
.btn-logout {
    border: 1px solid var(--main-accent);
    padding: 8px 20px;
    border-radius: 25px;
    transition: 0.4s;
}

.btn-logout:hover {
    background: var(--main-accent);
    color: #000 !important;
    box-shadow: 0 0 15px var(--main-accent);
}

/* ===== MOBILE MENU ===== */
.hamburger {
    display: none;
    font-size: 1.8rem;
    cursor: pointer;
    color: var(--main-accent);
}

@media (max-width: 992px) {
    .hamburger {
        display: block;
    }

    .navbar {
        position: fixed;
        top: 80px;
        left: -100%;
        /* Default hidden */
        width: 100%;
        height: calc(100vh - 80px);
        background: rgba(10, 15, 30, 0.98);
        transition: 0.4s;
        justify-content: center;
    }

    .navbar.active {
        left: 0;
    }

    .navbar ul {
        flex-direction: column;
        gap: 25px;
    }
}

/* Page content spacer (taaki content header ke niche na chhup jaye) */
main,
section,
.dashboard-wrapper {
    padding-top: 100px;
}
</style>

<header>
    <div class="logo">
        <span class="logospan">SkillSyncPro</span>
    </div>

    <div class="hamburger" onclick="toggleMenu()">☰</div>
    <nav class="navbar" id="nav-menu">
        <ul>
            <li class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>"><a
                    href="dashboard.php">Home</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF'])=='manage-job.php'?'active':'' ?>"><a
                    href="manage-job.php">Jobs</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF'])=='view-applicants.php'?'active':'' ?>"><a
                    href="view-applicants.php">Applicants</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF'])=='profile.php'?'active':'' ?>"><a
                    href="profile.php">Profile</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF'])=='analytics.php'?'active':'' ?>"><a
                    href="analytics.php">Insights</a></li>
            <li><a href="logout.php" class="btn-logout">Logout</a></li>
        </ul>
    </nav>
</header>

<script>
function toggleMenu() {
    document.getElementById("nav-menu").classList.toggle("active");
}
</script>