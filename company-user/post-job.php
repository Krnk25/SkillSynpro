<?php
session_start();
require_once "../db.php";

$company_user_id = $_SESSION['company_user_id'];

if($_SERVER['REQUEST_METHOD']=='POST'){
    $title = $_POST['title'];
    $company = $_POST['company'];
    $location = $_POST['location'];
    $skills = $_POST['skills'];
    $salary = $_POST['salary'];

    $stmt = $conn->prepare("
        INSERT INTO jobs 
        (title, company, location, required_skills, salary, status, company_user_id)
        VALUES (?,?,?,?,?,'active',?)
    ");
    $stmt->bind_param("sssssi",
        $title,$company,$location,$skills,$salary,$company_user_id
    );
    $stmt->execute();
}
?>