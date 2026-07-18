<?php
session_start();
require '../db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$message = '';

if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $allowed = ['jpg','jpeg','png','gif'];
    $filename = $_FILES['photo']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $filesize = $_FILES['photo']['size'];

    if (!in_array($ext, $allowed)) {
        $message = "❌ Only JPG, PNG, GIF allowed.";
    } elseif ($filesize > 2*1024*1024) {
        $message = "❌ File size must be less than 2MB.";
    } else {
        $newname = "photo_".$user_id."_".time().".".$ext;
        $target = "../uploads/photos/".$newname;

        if (!is_dir("../uploads/photos")) {
            mkdir("../uploads/photos", 0777, true);
        }

        // Delete old photo
        $stmtOld = $conn->prepare("SELECT photo FROM users WHERE id=?");
        $stmtOld->bind_param("i",$user_id);
        $stmtOld->execute();
        $oldPhoto = $stmtOld->get_result()->fetch_assoc()['photo'] ?? '';
        if($oldPhoto && file_exists("../uploads/photos/".$oldPhoto)){
            unlink("../uploads/photos/".$oldPhoto);
        }

        // Move new photo
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            // Save photo name in DB
            $upd = $conn->prepare("UPDATE users SET photo=? WHERE id=?");
            $upd->bind_param("si",$newname,$user_id);
            $upd->execute();
            $message = "✅ Photo uploaded successfully!";
        } else {
            $message = "❌ Error uploading photo.";
        }
    }
}

header("Location: profile.php?msg=".urlencode($message));
exit;
?>