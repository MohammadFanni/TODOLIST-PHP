<?php
require_once "../database/database.php";
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

try {
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);

    if (!isset($data['taskId'])) {
        echo json_encode(["success" => false, "message" => "Invalid input"]);
        exit();
    }

    $userId = $_SESSION['user_id'];
    $taskId = $data['taskId'];

    // Hapus tugas milik user tersebut
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $taskId, 'user_id' => $userId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Tugas tidak ditemukan atau akses ditolak"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>