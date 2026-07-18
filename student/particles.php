<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
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
    </style>
</head>

<body>
    <div class="tbackground"></div>
    <div id="tsparticles"></div>

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