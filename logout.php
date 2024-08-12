<?php
session_name('user_session'); 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Unset all of the session variables
    $_SESSION = [];

    // If you want to destroy the session completely, also delete the session cookie
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();

    // Redirect to login page or homepage
    header("Location: index.php");
    exit;
} else {
    // If the request method is not POST, redirect to homepage or display an error
    header("Location: index.php");
    exit;
}
?>
