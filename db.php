<?php
// php/db.php
$host = "localhost";   // XAMPP default
$user = "root";        // XAMPP default
$pass = "";            // your MySQL password (empty if default)
$dbname = "skillsyncpro";


$conn = new mysqli($host, $user, $pass, $dbname);
date_default_timezone_set("Asia/Kolkata");

?>