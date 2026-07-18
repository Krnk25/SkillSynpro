<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Get Started - SkillSyncPro</title>

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
        background: linear-gradient(120deg, #00111f, #02182f, #00111f);
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

    .box {
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(18px);
        padding: 35px 30px;
        border-radius: 25px;
        text-align: center;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 0 30px rgba(0, 255, 255, 0.25);
        animation: floatUp 4s ease-in-out infinite;
    }

    @keyframes floatUp {

        0%,
        100% {
            transform: translateY(0)
        }

        50% {
            transform: translateY(-6px)
        }
    }

    h2 {
        font-family: 'Algerian', 'Times New Roman', serif;
        font-size: 2.2rem;
        margin-bottom: 25px;
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .role-btn {
        display: block;
        width: 100%;
        padding: 14px 0;
        margin-bottom: 15px;
        font-size: 1.05rem;
        font-weight: 700;
        border-radius: 40px;
        border: none;
        text-decoration: none;
        color: #000;
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        transition: 0.3s;
    }

    .role-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px #ff00ff88, 0 0 35px #00ffff88;
    }

    .role-btn.student {
        background: linear-gradient(90deg, #1aff00, #00f7ff);
    }

    .back {
        display: inline-block;
        margin-top: 10px;
        font-size: 0.9rem;
        color: #00f7ff;
        text-decoration: none;
    }

    .back:hover {
        text-decoration: underline;
    }

    @media(max-width:480px) {
        h2 {
            font-size: 1.9rem;
        }

        .box {
            padding: 28px 22px;
        }
    }
    </style>
</head>

<body>

    <div id="tsparticles"></div>

    <div class="box">
        <h2>Get Started</h2>

        <a href="company-user/login.php" class="role-btn">
            Company User Login
        </a>

        <a href="student/login.php" class="role-btn student">
            Student Login
        </a>

        <a href="index.php" class="back">← Back to Home</a>
    </div>

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
                speed: 1.2
            }
        },
        detectRetina: true
    });
    </script>

</body>

</html>