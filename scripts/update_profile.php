<?php
session_start();
require_once '../database/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, location = ? WHERE id = ?")
        ->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['location'],
            $_SESSION['user_id']
        ]);
    echo json_encode(['success' => true, 'message' => 'Profile updated']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>