<?php
session_start();

// Fungsi untuk mendeteksi apakah akses dari browser
function isBrowserAccess() {
    return isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'html') !== false;
}

require_once '../database/database.php';

header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$rememberMe = isset($_POST['remember-me']) ? true : false;

if (!$email || !$password) {
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
    // Cari user berdasarkan email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verifikasi password
    if ($user && password_verify($password, $user['password'])) {
        // Simpan data user ke session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['logged_in'] = true;

        // Set cookie "Remember Me" jika dicentang
        if ($rememberMe) {
            $cookieName = "remember_me";
            $cookieValue = $email; // Bisa diganti dengan token jika perlu
            $cookieExpire = time() + (60 * 60 * 24 * 7); // 1 minggu
            setcookie($cookieName, $cookieValue, $cookieExpire, "/");
        }

        $response = ['success' => true, 'message' => 'Login successful'];
    } else {
        $response = ['success' => false, 'message' => 'Invalid email or password'];
    }

} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Server error: ' . $e->getMessage()];
}

// Output respons sesuai jenis akses
if (isBrowserAccess()) {
    header('Content-Type: text/html');
    echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
} else {
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>