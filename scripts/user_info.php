<?php
session_start();
require_once '../database/database.php';

// Fungsi bantu untuk cek akses dari browser
function isBrowserAccess() {
    return isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'html') !== false;
}

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if (isBrowserAccess()) {
        header('Content-Type: text/html');
        echo "<pre>" . json_encode([
            'success' => false,
            'message' => 'User not logged in'
        ], JSON_PRETTY_PRINT) . "</pre>";
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'User not logged in'
        ]);
    }
    exit;
}

// Ambil informasi user dari database berdasarkan ID di session
try {
    $stmt = $pdo->prepare("SELECT id, name, email, phone, location, created_at, profile_picture FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user) {
        $profilePicture = !empty($user['profile_picture']) 
            ? '../public/uploads/' . $user['profile_picture'] 
            : '../public/uploads/defaultProfilePicture.jpg';

        // Format tanggal member since
        $memberSince = date("F j, Y", strtotime($user['created_at']));

        $responseData = [
            'success' => true,
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '',
            'location' => $user['location'] ?? '',
            'member_since' => $memberSince,
            'profile_picture' => $profilePicture
        ];
    } else {
        $responseData = [
            'success' => false,
            'message' => 'User not found'
        ];
        
        // Hapus session karena user tidak valid
        session_destroy();
    }

    // Tentukan tipe output
    if (isBrowserAccess()) {
        header('Content-Type: text/html');
        echo "<pre>" . json_encode($responseData, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        header('Content-Type: application/json');
        echo json_encode($responseData);
    }

} catch (PDOException $e) {
    $errorResponse = [
        'success' => false,
        'message' => 'Database error'
    ];

    if (isBrowserAccess()) {
        header('Content-Type: text/html');
        echo "<pre>" . json_encode($errorResponse, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        header('Content-Type: application/json');
        echo json_encode($errorResponse);
    }
}
?>