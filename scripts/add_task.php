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
    

    if (!isset($data['title']) ||!isset($data['description']) ||!isset($data['due_date']) ||!isset($data['priority']) ||!isset($data['category'])) {
        echo json_encode(["success" => false, "message" => "Invalid input"]);
        exit();
    }

    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        INSERT INTO tasks (user_id, title, description, due_date, priority, category, status)
        VALUES (:user_id, :title, :description, :due_date, :priority, :category, 'pending')
    ");
    $stmt->execute([
        'user_id' => $userId,
        'title' => $data['title'],
        'description' => $data['description'],
        'due_date' => $data['due_date'],
        'priority' => $data['priority'],
        'category' => $data['category']
    ]);

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>