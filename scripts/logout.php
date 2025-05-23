<?php
session_start();

$_SESSION = [];

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

if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, '/');
}

session_destroy();

$isAjaxRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                 strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjaxRequest || $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Jika AJAX/Fetch atau POST request, kirim response JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Logout successful']);
} else {
    // Jika dari href/GET request, redirect ke halaman login
    header("Location: ../public/login.html");
}

exit;

?>