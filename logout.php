<?php
session_start();
header("Content-Type: application/json");

// Clear all session variables
$_SESSION = [];
session_unset();
session_destroy();

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/', '', false, true);
}

echo json_encode(["success" => true, "message" => "Logged out successfully."]);
?>
