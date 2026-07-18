<?php
require_once "../db.php"; // path ko apne db.php ke location ke hisab se adjust karo

$email = "karankabade7@gmail.com"; // DB me exactly jo email hai wahi yaha dal do

$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if($user){
    echo "Found user: ".$user['name']." - ".$user['email'];
}else{
    echo "Email not found!";
}