<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

/* =====================
   PDF TEXT EXTRACT
===================== */
function extractPdfText($filePath) {
    $exe = "C:\\xampp3\\php\\pdftotext.exe";
    $outputFile = $filePath . ".txt";

    if (!file_exists($exe)) return '';

    $cmd = "\"$exe\" \"$filePath\" \"$outputFile\"";
    shell_exec($cmd);

    if (file_exists($outputFile)) {
        $text = file_get_contents($outputFile);
        unlink($outputFile);
        return strtolower(trim($text));
    }
    return '';
}
 // Extract text (basic — for txt read file contents, for others keep placeholder)
        $extracted_text = "";
        if ($file_ext === "txt") {
            $extracted_text = file_get_contents($file_path);
        } else {
            // For now placeholder; later integrate PDF/DOC extractor
            $extracted_text = "Extracted text from resume goes here...";
        }

/* =====================
   DOCX TEXT EXTRACT
===================== */
function extractDocxText($filePath) {
    $zip = new ZipArchive();
    $text = '';

    if ($zip->open($filePath) === TRUE) {
        $index = $zip->locateName('word/document.xml');
        if ($index !== false) {
            $data = $zip->getFromIndex($index);
            $xml = new DOMDocument();
            $xml->loadXML($data);
            $text = strip_tags($xml->saveXML());
        }
        $zip->close();
    }
    return strtolower(trim($text));
}

/* =====================
   FORM SUBMISSION
===================== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['resume'])) {

    if ($_FILES['resume']['error'] !== 0) {
        $msg = "❌ Resume upload failed!";
    } else {

        $allowed = ['pdf','docx'];
        $fileName = $_FILES['resume']['name'];
        $fileTmp  = $_FILES['resume']['tmp_name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $msg = "❌ Only PDF or DOCX allowed!";
        } else {

            /* 🔥 REMOVE OLD RESUME */
            $conn->query("DELETE FROM resumes WHERE user_id = $user_id");

            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $newName = time() . "_" . basename($fileName);
            $uploadPath = $uploadDir . $newName;
            move_uploaded_file($fileTmp, $uploadPath);

            /* 🔥 EXTRACT TEXT */
            if ($ext === "pdf") {
                $text = extractPdfText($uploadPath);
            } else {
                $text = extractDocxText($uploadPath);
            }

            if (trim($text) === '') {
                $msg = "❌ Resume text extract failed (Scanned PDF not supported)";
            } else {

                /* =====================
                   MATCH SKILLS FROM JOBS TABLE
                ===================== */
                $jobSkills = [];
                $res = $conn->query("SELECT required_skills FROM jobs WHERE status='active'");
                while ($row = $res->fetch_assoc()) {
                    $skillsArr = explode(',', strtolower($row['required_skills']));
                    foreach ($skillsArr as $s) {
                        $jobSkills[] = trim($s);
                    }
                }
                $jobSkills = array_unique($jobSkills);

                $found_skills = [];
                foreach ($jobSkills as $skill) {
                    if (strpos($text, $skill) !== false) {
                        $found_skills[] = strtoupper($skill);
                    }
                }

                $skills = implode(", ", $found_skills);
                $eligibility = count($jobSkills) > 0
                    ? round((count($found_skills) / count($jobSkills)) * 100, 2)
                    : 0;

                /* =====================
                   SAVE RESUME
                ===================== */
                $stmt = $conn->prepare("
                    INSERT INTO resumes (user_id, filename, extracted_text, skills, eligibility, uploaded_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->bind_param(
                    "isssd",
                    $user_id,
                    $newName,
                    $text,
                    $skills,
                    $eligibility
                );
                $stmt->execute();
                $stmt->close();

                $msg = "✅ Resume uploaded & analyzed successfully!<br>
                        Matched Skills: <b>$skills</b><br>
                        Eligibility: <b>$eligibility%</b>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Resume - SkillSyncPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        color: #fff;
        background: linear-gradient(120deg, #00111f, #003366, #00111f);
        background-size: 600% 600%;
        animation: gradientBG 20s ease infinite;
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

    canvas#particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
    }

    .container-box {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        text-align: center;
        padding: 20px;
    }

    h1 {
        font-size: 2.5rem;
        font-family: 'Algerian', 'Times New Roman', serif;
        margin-bottom: 20px;
        background: linear-gradient(90deg, #ff00ff, #00f7ff, #00ff1a);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientMove 5s ease infinite;
        text-shadow: 0 0 8px #ff00ff, 0 0 15px #00f7ff;
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

    .upload-box {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 60px;
        max-width: 500px;
        width: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 2px solid #00f7ff;
        box-shadow: 0 0 10px #00f7ff, 0 0 20px #ff00ff;
    }

    .upload-box:hover {
        transform: scale(1.05);
        box-shadow: 0 0 40px #00f7ff, 0 0 90px #ff00ff;
    }

    input[type="file"] {
        display: block;
        margin: 20px auto;
        color: #fff;
        border: 2px solid transparent;
        background: rgba(255, 255, 255, 0.05);
        padding: 10px;
        border-radius: 8px;
        width: 100%;
        text-align: center;
    }

    input[type="file"]:hover {
        border: 2px solid #00f7ff;
        box-shadow: 0 0 10px #00f7ff, 0 0 20px #ff00ff;
    }

    .btn-custom,
    .btn-back {
        background: linear-gradient(90deg, #ff00ff, #00f7ff, #00ff1a);
        background-size: 300% 300%;
        color: #fff;
        padding: 8px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
        animation: gradientMove 5s ease infinite;
        text-shadow: 0 0 8px #ff00ff, 0 0 15px #00f7ff;
        margin-top: 10px;
        text-decoration: none;
    }

    .btn-custom:hover,
    .btn-back:hover {
        transform: scale(1.08);
        box-shadow: 0 0 20px #00f7ff, 0 0 40px #ff00ff;
    }

    .message {
        margin-top: 15px;
        font-size: 0.95rem;
    }

    @media(max-width:600px) {
        .upload-box {
            padding: 30px;
        }

        h1 {
            font-size: 2rem;
        }
    }
    </style>
</head>

<body>
    <canvas id="particles"></canvas>
    <div class="container-box">
        <h1>Upload Your Resume</h1>

        <?php if (!empty($message)): ?>
        <div class="alert alert-warning"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form class="upload-box" action="dash.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="resume" accept=".pdf,.doc,.docx,.txt,.rtf" required>
            <button type="submit" class="btn-custom">Upload Resume</button>
            <a href="dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
        </form>
    </div>

    <script>
    const canvas = document.getElementById('particles');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    class Particle {
        constructor(x, y, size, color, speedX, speedY) {
            this.x = x;
            this.y = y;
            this.size = size;
            this.color = color;
            this.speedX = speedX;
            this.speedY = speedY;
        }
        update() {
            this.x += this.speedX;
            this.y += this.speedY;
            if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
            if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
        }
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fillStyle = this.color;
            ctx.fill();
        }
    }

    let particlesArray = [];

    function initParticles() {
        particlesArray = [];
        for (let i = 0; i < 100; i++) {
            let size = Math.random() * 3 + 1;
            let x = Math.random() * canvas.width;
            let y = Math.random() * canvas.height;
            let colors = ["#ff00ff", "#00f7ff", "#ffffff"];
            let color = colors[Math.floor(Math.random() * colors.length)];
            let speedX = (Math.random() - 0.5);
            let speedY = (Math.random() - 0.5);
            particlesArray.push(new Particle(x, y, size, color, speedX, speedY));
        }
    }

    function animateParticles() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particlesArray.forEach(p => {
            p.update();
            p.draw();
        });
        requestAnimationFrame(animateParticles);
    }

    initParticles();
    animateParticles();
    window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        initParticles();
    });
    </script>
</body>

</html>