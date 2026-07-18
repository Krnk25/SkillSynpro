<?php
include 'db.php';
$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id=?");
$stmt->execute([$id]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));