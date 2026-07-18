<?php
session_start();

/* Sab session variables clear */
$_SESSION = [];

/* Session destroy */
session_destroy();

/* Session cookie bhi destroy (important) */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* Login page par redirect */
header("Location: login.php");
exit;