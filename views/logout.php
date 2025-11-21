<?php
session_start();

// Clear all session variables
$_SESSION = [];

// Destroy the session entirely
session_destroy();

// Clear session cookie if exists
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

// Redirect to login page
header("Location: auth/login.php");
exit;
