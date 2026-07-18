<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSyncPro</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');


    :root {
        --primary-blue: #145da0;
        --dark-blue: #0c3b60;
        --light-blue: #93bce1;
        --button-red: #ff5733;
        --white: #ffffff;
        --text-color: #f0f0f0;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
    }

    html,
    body {
        width: 100%;
        overflow-x: hidden;
    }

    body {
        background: linear-gradient(120deg, #00111f, #003366, #00111f);
        background-size: 400% 400%;
        animation: gradientBG 20s ease infinite;
        color: var(--white);
        line-height: 1.6;
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

    /* particles */
    #tsparticles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -2;
    }

    .container {
        width: 90%;

        margin: 0 auto;
        padding: 10px;
    }

    /* Header */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        padding: 10px 0;
    }

    .logo {
        font-size: 1.6rem;
        font-weight: 700;
        background: linear-gradient(90deg, #ff00fbff, #f2ff00ff);
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

    .nav-links {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .nav-links a {
        color: var(--white);
        text-decoration: none;
        padding: 10px 20px;
        border: 1px solid #fff;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        background-color: transparent;
        background: linear-gradient(90deg, #ff00fbff, #f2ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;


    }

    .nav-links a:hover {

        color: #000;
        transform: scale(1.05);

    }

    .nav-links .register-btn {
        background-color: var(--white);
        color: var(--primary-blue);
        font-weight: 600;
    }

    .nav-links .register-btn:hover {
        background-color: #e6e6e6;
        transform: scale(1.05);
    }

    /* Hero Section */
    .hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        min-height: 50vh;
        margin-top: 10px;
        gap: 15px;
    }

    .hero-content {
        flex: 1;
        min-width: 280px;
        text-align: left;
        padding-right: 10px;
        margin-bottom: 30px;
    }

    .hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        line-height: 1.2;
        background: linear-gradient(90deg, #1aff00ff, #ff00ff);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientMove 5s ease infinite;
        font-family: 'Algerian', 'Times New Roman', serif;
    }


    .hero h1 span {
        background: linear-gradient(90deg, #a2ff00ff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero p {
        font-size: 1rem;
        color: var(--text-color);
        margin-bottom: 25px;
    }

    .get-started-btn {
        background-color: transparent;
        background: linear-gradient(90deg, #00ffe1ff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        border: solid #fff 1px;
        color: var(--white);
        text-decoration: none;
        font-size: 1rem;
        font-weight: 600;
        padding: 12px 24px;
        margin-left: 30px;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .get-started-btn:hover {

        transform: scale(1.05);
    }

    .hero-image {
        flex: 1;
        min-width: 300px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding-left: 10px;
    }

    .hero-image img {
        width: 400px;
        height: 400px;
        animation: moveUpAndDown 3s ease-in-out infinite;
    }

    @keyframes moveUpAndDown {
        0% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }

        100% {
            transform: translateY(0);
        }
    }

    /* Footer Boxes */
    .footer-boxes {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;


    }

    .box {

        padding: 20px;
        border-radius: 15px;
        border: 1px solid #ffffff1b;
        text-align: center;
        flex: 1;
        min-width: 220px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.4);

    }

    .box h3 {
        font-size: 1rem;
        font-weight: 600;
        margin-top: 10px;
        margin-bottom: 8px;
        background: linear-gradient(90deg, #1aff00ff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .box p {

        font-size: 0.8rem;
        color: var(--light-blue);

    }

    .box .icon {
        font-size: 1.5rem;
        color: var(--white);
        background: linear-gradient(90deg, #ff00fbff, #f2ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .copyright {
        text-align: center;
        font-size: 0.7rem;
        color: var(--light-blue);
        padding: 10px 0;
        margin-top: 60px;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .hero h1 {
            font-size: 2.2rem;
        }

        .hero p {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 768px) {
        .hero {
            flex-direction: column;
            text-align: center;
        }

        .hero-image {
            margin-top: 15px;
            padding-left: 0;
        }

        .footer-boxes {
            flex-direction: column;
            align-items: center;
        }

        .nav-links {
            width: 100%;
            justify-content: center;
            margin-top: 10px;
        }

        .nav-links a {
            margin: 5px;
        }
    }

    @media (max-width: 480px) {
        .hero h1 {
            font-size: 1.8rem;
        }

        .hero p {
            font-size: 0.9rem;
        }

        .get-started-btn {
            font-size: 0.95rem;
            padding: 10px 20px;
        }
    }
    </style>
</head>

<body>
    <div class="tbackground"></div>
    <div id="tsparticles"></div>

    <div class="container">
        <header class="header">
            <div class="logo">SkillSyncPro</div>
            <nav class="nav-links">
                <a href="company-user/login.php">Company User Login </a>
                <a href="student/login.php" class="register-btn">Student Login</a>
            </nav>
        </header>

        <section class="hero">
            <div class="hero-content">
                <h1>Match Your Skills For Job <span><i class="fa-solid fa-circle-check"></i></span></h1>
                <p>Upload your resume and find job roles tailored to your profile instantly.</p>
                <a href="get-started.php" class="get-started-btn">Get Started</a>
            </div>
            <div class="hero-image">
                <img src="image/girl.png" alt="A person sitting on a chair using a laptop.">
            </div>
        </section>

        <section class="footer-boxes">
            <div class="box">
                <span class="icon">&#x2318;</span>
                <h3>Easy Matching</h3>
                <p>Upload your resume and instantly find skill-based matches.</p>
            </div>
            <div class="box">
                <span class="icon">&#x2714;</span>
                <h3>Tailored Opportunities</h3>
                <p>Discover job openings curated for your skills.</p>
            </div>
            <div class="box">
                <span class="icon">&#x2388;</span>
                <h3>Career Growth</h3>
                <p>Advance faster with roles designed for your expertise.</p>
            </div>
            <div class="box">
                <span>&#x1F4CA;</span> <!-- changed icon -->
                <h3>Performance Insights</h3> <!-- changed title -->
                <p>Track your applications and improve your success rate.</p> <!-- changed description -->
            </div>
        </section>

        <div class="copyright">
            <p>© 2025 SkillSyncPro. All rights reserved.</p>
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