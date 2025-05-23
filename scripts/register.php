<?php
function isBrowserAccess() {
    return isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'html') !== false;
}

require_once '../database/database.php';

session_start();
header('Content-Type: application/json');

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$password) {
    $response = ['success' => false, 'message' => 'Please fill in all fields'];

    if (isBrowserAccess()) {
        header('Content-Type: text/html');
        echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        echo json_encode($response);
    }
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $response = ['success' => false, 'message' => 'Email is already registered'];

        if (isBrowserAccess()) {
            header('Content-Type: text/html');
            echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo json_encode($response);
        }
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $result = $stmt->execute([$name, $email, $hashedPassword]);

    if ($result) {
        $response = ['success' => true, 'message' => 'Registered successfully'];
    } else {
        $response = ['success' => false, 'message' => 'Registration failed'];
    }

} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Server error: ' . $e->getMessage()];
}

if (isBrowserAccess()) {
    header('Content-Type: text/html');
    echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
} else {
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>